<?php

namespace App\Services;

use App\Models\Message;
use App\Models\User;
use App\Services\TempNotifiable;
use App\Facades\Notifications;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class MessageService
{
    public function getClientMessages(User $user, array $filters = [], int $perPage = 15)
    {
        $query = Message::query();

        // Apply client access control
        if (!$user->hasAnyRole(['super-admin', 'admin', 'manager'])) {
            $query->where('user_id', $user->id);
        }

        // Apply filters
        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('subject', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('message', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('name', 'like', '%' . $filters['search'] . '%');
            });
        }

        if (isset($filters['read']) && $filters['read'] !== '') {
            $isRead = $filters['read'] === 'read' || $filters['read'] === '1';
            $query->where('is_read', $isRead);
        }

        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (!empty($filters['priority'])) {
            $query->where('priority', $filters['priority']);
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        return $query->with(['attachments', 'project', 'parent'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function createMessage(array $messageData, array $attachments = []): Message
    {
        // Set default values
        $messageData['type'] = $messageData['type'] ?? 'contact_form';
        $messageData['priority'] = $messageData['priority'] ?? 'normal';
        $messageData['requires_response'] = $messageData['requires_response'] ?? true;

        // Set response deadline for urgent messages
        if ($messageData['priority'] === 'urgent' && !isset($messageData['response_deadline'])) {
            $messageData['response_deadline'] = now()->addHours(4);
        }

        // Create the message
        $message = Message::create($messageData);

        // Handle attachments if provided
        if (!empty($attachments)) {
            $this->handleAttachments($message, $attachments);
        }

        // Send notifications
        $this->sendMessageNotifications($message);

        // Send auto-reply if enabled
        $this->sendAutoReplyIfEnabled($message);

        // Load relationships for return
        $message->load(['attachments', 'project']);

        return $message;
    }

    public function createReply(Message $originalMessage, array $replyData, array $attachments = []): Message
    {
        // Ensure the reply is linked to the root message
        $rootMessage = $originalMessage->getRootMessage();

        $replyData['parent_id'] = $rootMessage->id;
        $replyData['project_id'] = $originalMessage->project_id;
        $replyData['type'] = 'admin_to_client';
        $replyData['user_id'] = $originalMessage->user_id;

        // Create the reply
        $reply = $this->createMessage($replyData, $attachments);

        // Mark original message as replied
        $originalMessage->update([
            'is_replied' => true,
            'replied_at' => now(),
            'replied_by' => auth()->id(),
        ]);

        // Send reply notification
        $this->sendReplyNotification($reply, $originalMessage);

        return $reply;
    }

    public function markAsRead(Message $message, ?User $user = null): void
    {
        if (!$message->is_read) {
            $message->update([
                'is_read' => true,
                'read_at' => now(), // ✅ Timestamp when message was read
            ]);

            // Log the read action for audit trail
            \Log::info('Message marked as read', [
                'message_id' => $message->id,
                'user_id' => $user?->id ?? 'system',
                'read_at' => now()->toISOString()
            ]);
        }
    }

    public function markAsUnread(Message $message): void
    {
        if ($message->is_read) {
            $message->update([
                'is_read' => false,
                'read_at' => null, // ✅ Clear the read timestamp
            ]);

            // Log the unread action for audit trail
            \Log::info('Message marked as unread', [
                'message_id' => $message->id,
                'read_at_cleared' => now()->toISOString()
            ]);
        }
    }
    public function autoMarkThreadAsRead($thread, User $user): int
{
    $markedCount = 0;
    $currentTime = now();
    
    foreach ($thread as $message) {
        // Only mark admin-to-client messages that are unread
        if (!$message->is_read && 
            in_array($message->type, ['admin_to_client', 'support_response']) &&
            $message->user_id === $user->id) {
            
            try {
                $message->update([
                    'is_read' => true,
                    'read_at' => $currentTime, // ✅ Consistent timestamp
                ]);
                
                $markedCount++;
                
            } catch (\Exception $e) {
                \Log::warning('Failed to auto-mark message as read', [
                    'message_id' => $message->id,
                    'user_id' => $user->id,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }
    
    if ($markedCount > 0) {
        \Log::info("Auto-marked {$markedCount} messages as read in thread", [
            'user_id' => $user->id,
            'marked_at' => $currentTime->toISOString()
        ]);
    }
    
    return $markedCount;
}

    public function setPriority(Message $message, string $priority): Message
    {
        $validPriorities = ['low', 'normal', 'high', 'urgent'];

        if (!in_array($priority, $validPriorities)) {
            throw new \InvalidArgumentException('Invalid priority level');
        }

        $oldPriority = $message->priority;
        $message->update(['priority' => $priority]);

        // Send notification if priority increased to urgent
        if ($oldPriority !== 'urgent' && $priority === 'urgent') {
            Notifications::send('message.urgent', $message);
        }

        return $message;
    }

    public function addFollowUp(Message $message, array $followUpData): Message
    {
        $followUpData['parent_id'] = $message->id;
        $followUpData['type'] = 'follow_up';
        $followUpData['user_id'] = $message->user_id;

        $followUp = $this->createMessage($followUpData);

        // Send follow-up notification
        Notifications::send('message.follow_up', $followUp);

        return $followUp;
    }

    public function escalateMessage(Message $message, string $reason = null): Message
    {
        $message->update([
            'priority' => 'urgent',
            'escalated_at' => now(),
            'escalation_reason' => $reason,
        ]);

        // Send escalation notification to senior staff
        Notifications::send('message.escalated', $message);

        return $message;
    }
    public function bulkSetPriority(array $messageIds, string $priority, User $user): int
    {
        $validPriorities = ['low', 'normal', 'high', 'urgent'];

        if (!in_array($priority, $validPriorities)) {
            throw new \InvalidArgumentException('Invalid priority level');
        }

        $query = Message::whereIn('id', $messageIds);

        // Apply access control for non-admin users
        if (!$user->hasAnyRole(['super-admin', 'admin', 'manager'])) {
            return 0; // Non-admin users can't bulk change priority
        }

        $updated = $query->update(['priority' => $priority]);

        // Send bulk notification
        if ($updated > 0) {
            Notifications::send('message.bulk_priority_updated', [
                'count' => $updated,
                'priority' => $priority,
                'updated_by' => $user->name
            ]);
        }

        return $updated;
    }

    public function deleteMessage(Message $message): bool
    {
        // Send notification before deletion
        Notifications::send('message.deleted', $message);

        // Delete attachments from storage
        foreach ($message->attachments as $attachment) {
            if (Storage::disk('public')->exists($attachment->file_path)) {
                Storage::disk('public')->delete($attachment->file_path);
            }
        }

        return $message->delete();
    }

    public function archiveMessage(Message $message): Message
    {
        $message->update([
            'archived_at' => now(),
            'status' => 'archived'
        ]);

        // Send archive notification
        Notifications::send('message.archived', $message);

        return $message;
    }


    public function getUrgentCount(): int
    {
        return Message::where('priority', 'urgent')
            ->where('is_read', false)
            ->count();
    }

    public function getOverdueMessages(): \Illuminate\Database\Eloquent\Collection
    {
        return Message::where('requires_response', true)
            ->where('is_replied', false)
            ->where('response_deadline', '<', now())
            ->whereNotNull('response_deadline')
            ->with(['user', 'project'])
            ->get();
    }

    public function sendOverdueNotifications(): int
    {
        $overdueMessages = $this->getOverdueMessages();
        $sent = 0;

        foreach ($overdueMessages as $message) {
            // Only send if not already notified today
            if (
                !$message->overdue_notification_sent_at ||
                !$message->overdue_notification_sent_at->isToday()
            ) {

                Notifications::send('message.overdue', $message);
                $message->update(['overdue_notification_sent_at' => now()]);
                $sent++;
            }
        }

        return $sent;
    }

    protected function sendMessageNotifications(Message $message): void
    {
        try {
            // Determine notification type based on message properties
            if ($message->priority === 'urgent') {
                Notifications::send('message.urgent', $message);
            } else {
                Notifications::send('message.created', $message);
            }

            // Update notification timestamp
            $message->update(['notification_sent_at' => now()]);
        } catch (\Exception $e) {
            \Log::error('Failed to send message notification', [
                'message_id' => $message->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    protected function sendReplyNotification(Message $reply, Message $originalMessage): void
    {
        try {
            // Create appropriate notifiable
            $recipient = $originalMessage->user
                ? $originalMessage->user
                : TempNotifiable::forMessage($originalMessage->email, $originalMessage->name);

            Notifications::send('message.reply', $reply, $recipient);
        } catch (\Exception $e) {
            \Log::error('Failed to send reply notification', [
                'reply_id' => $reply->id,
                'original_message_id' => $originalMessage->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    protected function sendAutoReplyIfEnabled(Message $message): void
    {
        // Only send auto-reply for initial contact messages
        if ($message->type !== 'contact_form' || $message->parent_id) {
            return;
        }

        // Check if auto-reply is enabled
        if (!settings('message_auto_reply_enabled', true)) {
            return;
        }

        // Check if we already sent an auto-reply
        if ($message->auto_reply_sent) {
            return;
        }

        try {
            // Create appropriate notifiable
            $recipient = $message->user
                ? $message->user
                : TempNotifiable::forMessage($message->email, $message->name);

            Notifications::send('message.auto_reply', $message, $recipient);

            // Mark auto-reply as sent
            $message->update(['auto_reply_sent' => true]);
        } catch (\Exception $e) {
            \Log::error('Failed to send auto-reply', [
                'message_id' => $message->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    protected function handleAttachments(Message $message, array $attachments): void
    {
        foreach ($attachments as $file) {
            if ($file instanceof UploadedFile && $file->isValid()) {
                $this->storeAttachment($message, $file);
            }
        }
    }

    protected function storeAttachment(Message $message, UploadedFile $file): void
    {
        // Generate unique filename
        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

        // Store file
        $path = $file->storeAs('message_attachments/' . $message->id, $filename, 'public');

        // Create attachment record
        $message->attachments()->create([
            'file_path' => $path,
            'file_name' => $file->getClientOriginalName(),
            'file_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
        ]);
    }

    protected function calculateResponseRate($query): float
    {
        $totalRequiringResponse = (clone $query)->where('requires_response', true)->count();
        if ($totalRequiringResponse === 0)
            return 0;

        $replied = (clone $query)->where('requires_response', true)
            ->where('is_replied', true)
            ->count();

        return round(($replied / $totalRequiringResponse) * 100, 1);
    }

    protected function calculateAverageResponseTime($query): float
    {
        $repliedMessages = (clone $query)->where('is_replied', true)
            ->whereNotNull('replied_at')
            ->get();

        if ($repliedMessages->isEmpty())
            return 0;

        $totalHours = $repliedMessages->sum(function ($message) {
            return $message->created_at->diffInHours($message->replied_at);
        });

        return round($totalHours / $repliedMessages->count(), 1);
    }
    /**
     * Get message statistics for client dashboard
     */
    public function getMessageStatistics(User $user): array
    {
        $query = $this->getClientMessagesQuery($user);

        return [
            'total' => $query->count(),
            'unread' => $query->where('is_read', false)->count(),
            'pending_replies' => $query->where('is_replied', false)
                ->whereIn('type', ['general', 'support', 'project_inquiry', 'complaint'])
                ->count(),
            'urgent' => $query->where('priority', 'urgent')->count(),
            'this_month' => $query->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)->count(),
            'avg_response_time' => $this->getAverageResponseTime($user),
            'by_type' => $query->select('type', \DB::raw('count(*) as count'))
                ->groupBy('type')
                ->pluck('count', 'type')
                ->toArray(),
            'by_priority' => $query->select('priority', \DB::raw('count(*) as count'))
                ->groupBy('priority')
                ->pluck('count', 'priority')
                ->toArray(),
        ];
    }

    /**
     * Get unread count for client
     */
    public function getUnreadCount(User $user): int
    {
        return $this->getClientMessagesQuery($user)
            ->where('is_read', false)
            ->count();
    }

    /**
     * Get message thread for conversation view
     */
    public function getMessageThread(Message $message): \Illuminate\Database\Eloquent\Collection
    {
        // Get the root message
        $rootMessage = $message->parent_id ? $message->parent : $message;

        // Get all messages in this thread (root + all its replies)
        $thread = Message::where(function ($query) use ($rootMessage) {
            $query->where('id', $rootMessage->id)
                ->orWhere('parent_id', $rootMessage->id);
        })
            ->with(['attachments', 'user'])
            ->orderBy('created_at')
            ->get();

        return $thread;
    }

    /**
     * Bulk mark messages as read
     */
    public function bulkMarkAsRead(array $messageIds, User $user): int
{
    if (empty($messageIds)) {
        return 0;
    }

    $count = $this->getClientMessagesQuery($user)
        ->whereIn('id', $messageIds)
        ->where('is_read', false)
        ->update([
            'is_read' => true,
            'read_at' => now(),
            'updated_at' => now()
        ]);
    
    if ($count > 0) {
        Log::info("Bulk marked {$count} messages as read", [
            'user_id' => $user->id,
            'message_ids' => $messageIds,
            'marked_at' => now()->toISOString()
        ]);
    }
    
    return $count;
}
public function bulkMarkAsUnread(array $messageIds, User $user): int
{
    if (empty($messageIds)) {
        return 0;
    }

    $count = $this->getClientMessagesQuery($user)
        ->whereIn('id', $messageIds)
        ->where('is_read', true)
        ->update([
            'is_read' => false,
            'read_at' => null,
            'updated_at' => now()
        ]);
    
    if ($count > 0) {
        Log::info("Bulk marked {$count} messages as unread", [
            'user_id' => $user->id,
            'message_ids' => $messageIds,
            'unmarked_at' => now()->toISOString()
        ]);
    }
    
    return $count;
}
public function bulkDeleteMessages(array $messageIds, User $user): int
{
    if (empty($messageIds)) {
        return 0;
    }

    // Get messages that can be deleted (security check)
    $deletableMessages = $this->getClientMessagesQuery($user)
        ->whereIn('id', $messageIds)
        ->whereNotIn('type', ['admin_to_client', 'admin_reply'])
        ->get();

    $deletedCount = 0;

    foreach ($deletableMessages as $message) {
        try {
            // Attachments will be cleaned up by model events
            $message->delete();
            $deletedCount++;
        } catch (\Exception $e) {
            Log::warning('Failed to delete message in bulk operation', [
                'message_id' => $message->id,
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    if ($deletedCount > 0) {
        Log::info("Bulk deleted {$deletedCount} messages", [
            'user_id' => $user->id,
            'deleted_message_ids' => $deletableMessages->pluck('id')->toArray(),
            'deleted_at' => now()->toISOString()
        ]);
    }

    return $deletedCount;
}

    /**
     * Get recent client activity
     */
    public function getRecentActivity(User $user, int $days = 7): array
    {
        $messages = $this->getClientMessagesQuery($user)
            ->where('created_at', '>=', now()->subDays($days))
            ->with(['project'])
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        return $messages->map(function ($message) {
            return [
                'id' => $message->id,
                'type' => 'message',
                'action' => $this->getActivityAction($message),
                'title' => $message->subject,
                'description' => Str::limit($message->message, 100),
                'url' => route('client.messages.show', $message),
                'created_at' => $message->created_at,
                'priority' => $message->priority,
                'is_read' => $message->is_read,
                'project' => $message->project ? [
                    'id' => $message->project->id,
                    'title' => $message->project->title,
                ] : null,
            ];
        })->toArray();
    }

    /**
     * Get client messages query with proper access control
     */
    protected function getClientMessagesQuery(User $user): Builder
    {
        return Message::query()->where('user_id', $user->id);
    }

    /**
     * Get average response time in hours
     */
    protected function getAverageResponseTime(User $user): float
    {
        $repliedMessages = $this->getClientMessagesQuery($user)
            ->whereNotNull('replied_at')
            ->where('is_replied', true)
            ->select('created_at', 'replied_at')
            ->get();

        if ($repliedMessages->isEmpty()) {
            return 0;
        }

        $totalHours = $repliedMessages->sum(function ($message) {
            return $message->created_at->diffInHours($message->replied_at);
        });

        return round($totalHours / $repliedMessages->count(), 1);
    }

    /**
     * Get activity action description
     */
    protected function getActivityAction(Message $message): string
    {
        switch ($message->type) {
            case 'admin_to_client':
                return 'Received reply from support';
            case 'client_reply':
                return 'Sent reply';
            case 'support':
                return 'Requested technical support';
            case 'project_inquiry':
                return 'Asked about project';
            case 'complaint':
                return 'Filed complaint';
            case 'feedback':
                return 'Provided feedback';
            default:
                return 'Sent message';
        }
    }

    /**
     * Check if client can reply to message
     */
    public function canReplyToMessage(Message $message, User $user): bool
    {
        // Get the root message for the thread
        $rootMessage = $message->parent_id ? $message->parent : $message;

        // Client must own the original conversation
        if ($rootMessage->user_id !== $user->id) {
            return false;
        }

        // Can reply to any message in their own thread
        return true;
    }

    /**
     * Get message summary for dashboard
     */
    public function getMessageSummary(User $user): array
    {
        $query = $this->getClientMessagesQuery($user);

        $summary = [
            'total' => $query->count(),
            'unread' => $query->where('is_read', false)->count(),
            'urgent' => $query->where('priority', 'urgent')->count(),
            'awaiting_reply' => $query->where('is_replied', false)
                ->whereIn('type', ['general', 'support', 'project_inquiry', 'complaint'])
                ->count(),
        ];

        // Recent activity (last 7 days)
        $recentCount = $query->where('created_at', '>=', now()->subDays(7))->count();
        $summary['recent_activity'] = $recentCount;

        return $summary;
    }

    /**
     * Create client reply to admin message
     */
    public function createClientReply(Message $originalMessage, string $replyText, array $attachments = []): Message
    {
        $user = $originalMessage->user;

        // Ensure we're always linking to the root message
        $rootMessage = $originalMessage->parent_id ? $originalMessage->parent : $originalMessage;

        $replyData = [
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'company' => $user->company,
            'subject' => 'Re: ' . $rootMessage->subject,
            'message' => $replyText,
            'type' => 'client_reply',
            'user_id' => $user->id,
            'project_id' => $rootMessage->project_id,
            'parent_id' => $rootMessage->id, // Always link to root
            'priority' => $rootMessage->priority,
            'is_read' => false,
            'is_replied' => false,
        ];

        return $this->createMessage($replyData, $attachments);
    }
}