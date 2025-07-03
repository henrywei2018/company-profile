<?php

namespace App\Services;

use App\Models\Message;
use App\Models\Project;
use App\Models\User;
use App\Services\TempNotifiable;
use App\Facades\Notifications;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;


class MessageService
{
    public function getAdminMessages(User $admin, array $filters = [], int $perPage = 20)
    {
        $query = Message::excludeAdminMessages()
            ->with(['user', 'project', 'attachments', 'parent']);

        // Apply admin-specific filters
        $this->applyAdminFilters($query, $filters);

        // Apply sorting
        $sortBy = $filters['sort'] ?? 'created_at';
        $direction = $filters['direction'] ?? 'desc';

        $allowedSorts = ['created_at', 'subject', 'name', 'priority', 'is_read', 'is_replied'];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $direction);
        } else {
            $query->latest();
        }

        return $query->paginate($perPage);
    }
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
    public function getAdminFilterOptions(): array
    {
        return [
            'types' => [
                'contact_form' => 'Contact Form',
                'client_to_admin' => 'Client Message',
                'client_reply' => 'Client Reply',
                'general' => 'General Inquiry',
                'support' => 'Support Request',
                'project_inquiry' => 'Project Inquiry',
                'complaint' => 'Complaint',
                'feedback' => 'Feedback',
            ],
            'priorities' => [
                'low' => 'Low',
                'normal' => 'Normal',
                'high' => 'High',
                'urgent' => 'Urgent',
            ],
            'statuses' => [
                'unread' => 'Unread',
                'read' => 'Read',
                'pending' => 'Pending Reply',
                'replied' => 'Replied',
                'urgent' => 'Urgent',
            ],
            'clients' => User::whereHas('roles', function ($query) {
                $query->where('name', 'client');
            })->select('id', 'name', 'email')->orderBy('name')->get(),
            'projects' => Project::select('id', 'title')->orderBy('title')->get(),
        ];
    }

    public function createMessage(array $messageData, array $attachments = []): Message
    {
        // Set default values
        $messageData['type'] = $messageData['type'] ?? 'contact_form';
        $messageData['priority'] = $messageData['priority'] ?? 'normal';
        $messageData['requires_response'] = $messageData['requires_response'] ?? true;

        // Set response deadline for urgent messages
        if ($messageData['priority'] === 'urgent' && !isset($messageData['response_deadline'])) {
            $messageData['response_deadline'] = now()->addHours(2);
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
    public function forwardMessage(Message $originalMessage, array $recipientData, User $admin): Message
    {
        // Prepare forwarded message content
        $forwardedContent = $this->prepareForwardedContent($originalMessage, $recipientData['forward_message'] ?? null);

        // Prepare message data
        $messageData = [
            'type' => 'admin_to_client',
            'name' => $admin->name ?? 'Admin',
            'email' => settings('mail_from_address', config('mail.from.address', 'admin@company.com')),
            'subject' => 'Fwd: ' . $originalMessage->subject,
            'message' => $forwardedContent,
            'priority' => $originalMessage->priority,
            'project_id' => $originalMessage->project_id,
            'is_read' => true,
            'is_replied' => false,
            'read_at' => now(),
            'replied_by' => $admin->id,
        ];

        // Set recipient details
        if ($recipientData['type'] === 'client') {
            $client = User::findOrFail($recipientData['client_id']);
            $messageData['user_id'] = $client->id;
        }

        // Create forwarded message
        $forwardedMessage = Message::create($messageData);

        // Copy attachments from original message
        $this->copyMessageAttachments($originalMessage, $forwardedMessage);

        return $forwardedMessage;
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
            if (
                !$message->is_read &&
                in_array($message->type, ['admin_to_client', 'support_response']) &&
                $message->user_id === $user->id
            ) {

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
    public function updateMessagePriority(Message $message, string $priority, User $admin): bool
    {
        $oldPriority = $message->priority;
        $message->update(['priority' => $priority]);

        // Send urgent notification if priority changed to urgent
        if ($priority === 'urgent' && $oldPriority !== 'urgent') {
            $this->sendUrgentNotification($message);
        }

        Log::info('Message priority updated', [
            'message_id' => $message->id,
            'old_priority' => $oldPriority,
            'new_priority' => $priority,
            'admin_id' => $admin->id,
        ]);

        return true;
    }
    public function assignMessageToProject(Message $message, int $projectId, User $admin): Project
    {
        $project = Project::findOrFail($projectId);

        // Verify project belongs to the message sender
        if ($message->user_id && $project->client_id !== $message->user_id) {
            throw new \Exception('Project does not belong to the message sender');
        }

        $message->update(['project_id' => $projectId]);

        Log::info('Message assigned to project', [
            'message_id' => $message->id,
            'project_id' => $projectId,
            'admin_id' => $admin->id,
        ]);

        return $project;
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

    public function deleteMessage(Message $message, User $admin, array $options = []): bool
    {
        $forceDelete = $options['force'] ?? false;
        $deleteThread = $options['delete_thread'] ?? false;

        try {
            DB::beginTransaction();

            if ($deleteThread || $this->shouldDeleteAsThread($message)) {
                // Delete entire thread
                $deleted = $this->deleteMessageThread($message, $admin);
            } else {
                // Delete single message with validation
                $deleted = $this->deleteSingleMessage($message, $admin, $forceDelete);
            }

            DB::commit();
            return $deleted;

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    private function deleteIndividualMessage(Message $message, User $admin, bool $forceDelete = false): bool
{
    try {
        // Delete attachments first
        foreach ($message->attachments as $attachment) {
            $attachment->delete();
        }

        // Log the deletion
        Log::info('Message deleted by admin', [
            'message_id' => $message->id,
            'message_subject' => $message->subject,
            'client_id' => $message->user_id,
            'admin_id' => $admin->id,
            'force_delete' => $forceDelete
        ]);

        if ($forceDelete) {
            $message->forceDelete();
        } else {
            $message->delete();
        }

        return true;

    } catch (\Exception $e) {
        Log::error('Failed to delete message', [
            'message_id' => $message->id,
            'admin_id' => $admin->id,
            'error' => $e->getMessage()
        ]);
        return false;
    }
}
    public function deleteMessageThread(Message $message, User $admin): bool
    {
        // Get root message
        $rootMessage = $message->parent_id ? $message->parent : $message;

        // Get all messages in thread
        $threadMessages = Message::where(function ($query) use ($rootMessage) {
            $query->where('id', $rootMessage->id)
                ->orWhere('parent_id', $rootMessage->id);
        })->get();

        $deletedCount = 0;
        $messageIds = [];

        foreach ($threadMessages as $threadMessage) {
            try {
                // Delete attachments first
                $this->deleteMessageAttachments($threadMessage);

                $messageIds[] = $threadMessage->id;
                $threadMessage->delete();
                $deletedCount++;

            } catch (\Exception $e) {
                Log::error('Failed to delete message in thread', [
                    'message_id' => $threadMessage->id,
                    'thread_root_id' => $rootMessage->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        Log::info('Message thread deleted by admin', [
            'root_message_id' => $rootMessage->id,
            'deleted_count' => $deletedCount,
            'message_ids' => $messageIds,
            'admin_id' => $admin->id,
        ]);

        return $deletedCount > 0;
    }
    public function deleteSingleMessage(Message $message, User $admin, bool $force = false): bool
    {
        // Check constraints unless forced
        if (!$force) {
            if ($message->replies()->count() > 0) {
                throw new \Exception('Cannot delete message that has replies. Use deleteMessageThread() or force=true.');
            }

            if ($message->parent_id) {
                throw new \Exception('Cannot delete reply message individually. Use deleteMessageThread() or force=true.');
            }
        }

        // Delete attachments first
        $this->deleteMessageAttachments($message);

        $messageId = $message->id;
        $message->delete();

        Log::info('Single message deleted by admin', [
            'message_id' => $messageId,
            'admin_id' => $admin->id,
            'forced' => $force,
        ]);

        return true;
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
    public function getAdminMessageStatistics(): array
    {
        $baseQuery = Message::excludeAdminMessages();

        return [
            'total' => $baseQuery->count(),
            'unread' => $baseQuery->where('is_read', false)->count(),
            'urgent' => $baseQuery->where('priority', 'urgent')->count(),
            'pending_replies' => $baseQuery->where('is_replied', false)->count(),
            'today' => $baseQuery->whereDate('created_at', today())->count(),
            'this_week' => $baseQuery->where('created_at', '>=', now()->startOfWeek())->count(),
            'this_month' => $baseQuery->where('created_at', '>=', now()->startOfMonth())->count(),
            'by_type' => $baseQuery->selectRaw('type, COUNT(*) as count')
                ->groupBy('type')
                ->pluck('count', 'type')
                ->toArray(),
            'by_priority' => $baseQuery->selectRaw('priority, COUNT(*) as count')
                ->groupBy('priority')
                ->pluck('count', 'priority')
                ->toArray(),
            'average_response_time' => $this->getAverageResponseTimeForAdmin(),
        ];
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
    public function bulkDeleteMessages(array $messageIds, User $user, array $options = []): int
    {
        if (empty($messageIds)) {
            return 0;
        }

        // Detect if user is admin or client
        if ($user->hasAnyRole(['super-admin', 'admin', 'manager'])) {
            return $this->bulkDeleteMessagesForAdmin($messageIds, $user, $options);
        } else {
            return $this->bulkDeleteMessagesForClient($messageIds, $user, $options);
        }
    }

    /**
     * Bulk delete for admin users (more permissive)
     */
    public function bulkDeleteMessagesForAdmin(array $messageIds, User $admin, array $options = []): int
    {
        if (empty($messageIds)) {
            return 0;
        }

        $forceDelete = $options['force'] ?? false;
        $deleteThreads = $options['delete_threads'] ?? true; // Default to thread deletion for admin

        $messages = Message::whereIn('id', $messageIds)->get();
        $processedThreads = $this->createSet();
        $deletedCount = 0;

        foreach ($messages as $message) {
            try {
                // Get root message ID for this thread
                $rootId = $message->parent_id ?? $message->id;

                // Skip if we already processed this thread
                if ($processedThreads->contains($rootId)) {
                    continue;
                }

                if ($deleteThreads) {
                    // Delete entire thread
                    if ($this->deleteMessageThread($message, $admin)) {
                        $threadSize = $this->getThreadSize($message);
                        $deletedCount += $threadSize;
                        $processedThreads->add($rootId);
                    }
                } else {
                    // Delete single message
                    if ($this->deleteSingleMessage($message, $admin, $forceDelete)) {
                        $deletedCount++;
                    }
                }

            } catch (\Exception $e) {
                Log::error('Failed to delete message in admin bulk operation', [
                    'message_id' => $message->id,
                    'admin_id' => $admin->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        Log::info('Admin bulk delete completed', [
            'admin_id' => $admin->id,
            'requested_count' => count($messageIds),
            'deleted_count' => $deletedCount,
            'delete_threads' => $deleteThreads,
            'forced' => $forceDelete,
        ]);

        return $deletedCount;
    }


    public function getRecentAdminActivity(): array
    {
        return [
            'new_messages' => Message::excludeAdminMessages()
                ->where('created_at', '>=', now()->subHours(24))
                ->with(['user'])
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get(),
            'recent_replies' => Message::where('type', 'admin_to_client')
                ->where('created_at', '>=', now()->subHours(24))
                ->with(['parent', 'user'])
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get(),
        ];
    }
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
        $rootMessage = $message->parent_id ? $message->parent : $message;

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
    public function getBulkOperationOptions(User $user): array
    {
        $isAdmin = $user->hasAnyRole(['super-admin', 'admin', 'manager']);

        if ($isAdmin) {
            return [
                'available_operations' => [
                    'delete_single' => 'Delete Selected Messages',
                    'delete_threads' => 'Delete Entire Conversations',
                    'archive' => 'Archive Messages',
                    'mark_read' => 'Mark as Read',
                    'mark_unread' => 'Mark as Unread',
                    'change_priority' => 'Change Priority',
                ],
                'default_delete_mode' => 'delete_threads',
                'can_force_delete' => true,
                'can_delete_admin_messages' => true,
            ];
        } else {
            return [
                'available_operations' => [
                    'delete_single' => 'Delete Selected Messages',
                    'archive' => 'Archive Messages (Recommended)',
                    'mark_read' => 'Mark as Read',
                    'mark_unread' => 'Mark as Unread',
                ],
                'default_delete_mode' => 'delete_single',
                'can_force_delete' => false,
                'can_delete_admin_messages' => false,
                'recommended_operation' => 'archive',
            ];
        }
    }

    /**
     * Validate bulk operation permissions
     */
    public function validateBulkOperation(string $operation, array $messageIds, User $user): array
    {
        $isAdmin = $user->hasAnyRole(['super-admin', 'admin', 'manager']);
        $errors = [];
        $warnings = [];

        // Check if operation is allowed for user type
        $allowedOps = $this->getBulkOperationOptions($user)['available_operations'];
        if (!isset($allowedOps[$operation])) {
            $errors[] = "Operation '{$operation}' is not allowed for your user type";
        }

        // Check message permissions
        if (!$isAdmin) {
            $invalidMessages = Message::whereIn('id', $messageIds)
                ->where(function ($query) use ($user) {
                    $query->where('user_id', '!=', $user->id)
                        ->orWhereIn('type', ['admin_to_client', 'admin_reply']);
                })
                ->count();

            if ($invalidMessages > 0) {
                $errors[] = "You can only perform operations on your own messages";
            }
        }

        // Operation-specific validations
        switch ($operation) {
            case 'delete_threads':
                if (!$isAdmin) {
                    $warnings[] = "Thread deletion will only remove your messages from conversations";
                }
                break;

            case 'delete_single':
                $repliedMessages = Message::whereIn('id', $messageIds)
                    ->where('is_replied', true)
                    ->count();

                if ($repliedMessages > 0) {
                    $warnings[] = "Some messages have been replied to and may not be deletable";
                }
                break;
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'warnings' => $warnings,
        ];
    }

    /**
     * Check if client can delete a replied message
     */
    protected function canDeleteRepliedMessage(Message $message, User $client): bool
    {
        // Allow deletion if reply is older than X hours (business rule)
        $replyGracePeriod = 24; // hours

        if (
            $message->replied_at &&
            now()->diffInHours($message->replied_at) < $replyGracePeriod
        ) {
            return false;
        }

        return true;
    }

    public function deleteMessageForClient(Message $message, User $client, array $options = []): bool
    {
        // Security check - client can only delete their own messages
        if (!$this->canClientDeleteMessage($message, $client)) {
            throw new \Exception('You can only delete your own messages');
        }

        $deleteThread = $options['delete_thread'] ?? false;

        try {
            DB::beginTransaction();

            if ($deleteThread && $this->shouldDeleteAsThread($message)) {
                // Delete entire client's thread participation
                $deleted = $this->deleteClientThreadParticipation($message, $client);
            } else {
                // Delete single client message
                $deleted = $this->deleteSingleClientMessage($message, $client);
            }

            DB::commit();
            return $deleted;

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Delete client's participation in a thread (safer approach)
     */
    public function deleteClientThreadParticipation(Message $message, User $client): bool
    {
        $rootMessage = $message->parent_id ? $message->parent : $message;

        // Get only client's messages in this thread
        $clientMessages = Message::where(function ($query) use ($rootMessage) {
            $query->where('id', $rootMessage->id)
                ->orWhere('parent_id', $rootMessage->id);
        })
            ->where('user_id', $client->id)
            ->whereNotIn('type', ['admin_to_client', 'admin_reply']) // Never delete admin messages
            ->get();

        $deletedCount = 0;
        $messageIds = [];

        foreach ($clientMessages as $clientMessage) {
            try {
                // Only delete attachments from client's own messages
                $this->deleteMessageAttachments($clientMessage);

                $messageIds[] = $clientMessage->id;
                $clientMessage->delete();
                $deletedCount++;

            } catch (\Exception $e) {
                Log::error('Failed to delete client message in thread', [
                    'message_id' => $clientMessage->id,
                    'client_id' => $client->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        Log::info('Client thread participation deleted', [
            'root_message_id' => $rootMessage->id,
            'client_id' => $client->id,
            'deleted_count' => $deletedCount,
            'message_ids' => $messageIds,
        ]);

        return $deletedCount > 0;
    }

    /**
     * Delete single client message with validation
     */
    public function deleteSingleClientMessage(Message $message, User $client): bool
    {
        // Additional client-specific validations
        if ($message->type === 'admin_to_client') {
            throw new \Exception('Cannot delete admin messages');
        }

        if ($message->is_replied && !$this->canDeleteRepliedMessage($message, $client)) {
            throw new \Exception('Cannot delete message that has been replied to by admin');
        }

        // Delete attachments first
        $this->deleteMessageAttachments($message);

        $messageId = $message->id;
        $message->delete();

        Log::info('Single client message deleted', [
            'message_id' => $messageId,
            'client_id' => $client->id,
        ]);

        return true;
    }

    /**
     * Bulk delete for client with safety checks
     */
    public function bulkDeleteMessagesForClient(array $messageIds, User $client, array $options = []): int
    {
        if (empty($messageIds)) {
            return 0;
        }

        // Security filter - only client's own deletable messages
        $deletableMessages = Message::whereIn('id', $messageIds)
            ->where('user_id', $client->id)
            ->whereNotIn('type', ['admin_to_client', 'admin_reply'])
            ->get();

        $deleteThreads = $options['delete_threads'] ?? false;
        $processedThreads = $this->createSet();
        $deletedCount = 0;

        foreach ($deletableMessages as $message) {
            try {
                if (!$this->canClientDeleteMessage($message, $client)) {
                    continue;
                }

                $rootId = $message->parent_id ?? $message->id;

                // Skip if we already processed this thread
                if ($deleteThreads && $processedThreads->contains($rootId)) {
                    continue;
                }

                if ($deleteThreads) {
                    // Delete client's participation in thread
                    if ($this->deleteClientThreadParticipation($message, $client)) {
                        $threadSize = $this->getClientThreadSize($message, $client);
                        $deletedCount += $threadSize;
                        $processedThreads->add($rootId);
                    }
                } else {
                    // Delete single message
                    if ($this->deleteSingleClientMessage($message, $client)) {
                        $deletedCount++;
                    }
                }

            } catch (\Exception $e) {
                Log::error('Failed to delete client message in bulk operation', [
                    'message_id' => $message->id,
                    'client_id' => $client->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        Log::info('Client bulk delete completed', [
            'client_id' => $client->id,
            'requested_count' => count($messageIds),
            'deleted_count' => $deletedCount,
            'delete_threads' => $deleteThreads,
        ]);

        return $deletedCount;
    }

    public function bulkChangePriority(array $messageIds, string $priority, User $admin): int
{
    if (empty($messageIds)) {
        return 0;
    }

    // Validate priority
    if (!in_array($priority, ['low', 'normal', 'high', 'urgent'])) {
        throw new \InvalidArgumentException("Invalid priority: {$priority}");
    }

    $count = Message::excludeAdminMessages()
        ->whereIn('id', $messageIds)
        ->update([
            'priority' => $priority,
            'priority_updated_by' => $admin->id,
            'priority_updated_at' => now(),
            'updated_at' => now()
        ]);

    if ($count > 0) {
        Log::info("Admin bulk changed priority to {$priority} for {$count} messages", [
            'admin_id' => $admin->id,
            'admin_name' => $admin->name,
            'priority' => $priority,
            'message_ids' => $messageIds,
            'updated_at' => now()->toISOString()
        ]);

        // If urgent priority is set, create notifications
        if ($priority === 'urgent') {
            $this->notifyUrgentMessages($messageIds, $admin);
        }
    }

    return $count;
}

/**
 * Bulk assign messages to admin - for message management workflows
 */
public function bulkAssignToAdmin(array $messageIds, int $assignedAdminId, User $admin): int
{
    if (empty($messageIds)) {
        return 0;
    }

    // Verify the assigned admin exists and has admin role
    $assignedAdmin = User::find($assignedAdminId);
    if (!$assignedAdmin || !$assignedAdmin->hasAnyRole(['super-admin', 'admin', 'manager'])) {
        throw new \InvalidArgumentException("Invalid admin for assignment");
    }

    $count = Message::excludeAdminMessages()
        ->whereIn('id', $messageIds)
        ->update([
            'assigned_to' => $assignedAdminId,
            'assigned_by' => $admin->id,
            'assigned_at' => now(),
            'updated_at' => now()
        ]);

    if ($count > 0) {
        Log::info("Admin bulk assigned {$count} messages to admin {$assignedAdminId}", [
            'assigning_admin_id' => $admin->id,
            'assigned_admin_id' => $assignedAdminId,
            'assigned_admin_name' => $assignedAdmin->name,
            'message_ids' => $messageIds,
            'assigned_at' => now()->toISOString()
        ]);

        // Notify the assigned admin
        $this->notifyAssignedAdmin($assignedAdmin, $messageIds, $admin);
    }

    return $count;
}

    /**
     * Archive message for client (recommended approach)
     */
    public function archiveMessageForClient(Message $message, User $client): bool
    {
        // Security check
        if (!$this->canClientArchiveMessage($message, $client)) {
            throw new \Exception('You can only archive your own messages');
        }

        $message->update([
            'archived_at' => now(),
            'archived_by' => $client->id,
            'archived_by_client' => true, // Flag to distinguish client vs admin archive
        ]);

        Log::info('Message archived by client', [
            'message_id' => $message->id,
            'client_id' => $client->id,
        ]);

        return true;
    }

    /**
     * Get client deletion options (for UI)
     */
    public function getClientDeletionOptions(Message $message, User $client): array
    {
        $canDelete = $this->canClientDeleteMessage($message, $client);
        $canArchive = $this->canClientArchiveMessage($message, $client);
        $hasReplies = $message->replies()->where('type', 'admin_to_client')->count() > 0;
        $isReply = !is_null($message->parent_id);
        $threadSize = $this->getClientThreadSize($message, $client);

        return [
            'can_delete' => $canDelete,
            'can_archive' => $canArchive,
            'can_delete_thread' => $canDelete && ($hasReplies || $isReply),
            'has_admin_replies' => $hasReplies,
            'is_reply' => $isReply,
            'thread_size' => $threadSize,
            'recommended_action' => $this->getClientRecommendedAction($message, $client),
            'warning_message' => $this->getClientDeletionWarning($message, $client),
        ];
    }
    public function getClientArchivedMessages(User $client, int $perPage = 15)
    {
        return Message::where('user_id', $client->id)
            ->whereNotNull('archived_at')
            ->where('archived_by_client', true)
            ->with(['attachments', 'project', 'parent'])
            ->orderBy('archived_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Restore client archived message
     */
    public function restoreClientArchivedMessage(Message $message, User $client): bool
    {
        // Security check
        if ($message->user_id !== $client->id || !$message->archived_by_client) {
            throw new \Exception('You can only restore your own archived messages');
        }

        $message->update([
            'archived_at' => null,
            'archived_by' => null,
            'archived_by_client' => false,
        ]);

        Log::info('Message restored from archive by client', [
            'message_id' => $message->id,
            'client_id' => $client->id,
        ]);

        return true;
    }
    protected function applyAdminFilters($query, array $filters): void
    {
        // Search filter
        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('subject', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('message', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('name', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('email', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('company', 'like', '%' . $filters['search'] . '%');
            });
        }

        // Read status filter
        if (isset($filters['read'])) {
            if ($filters['read'] === 'read') {
                $query->where('is_read', true);
            } elseif ($filters['read'] === 'unread') {
                $query->where('is_read', false);
            }
        }

        // Replied status filter
        if (isset($filters['replied'])) {
            $query->where('is_replied', $filters['replied']);
        }

        // Type filter
        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        // Priority filter
        if (!empty($filters['priority'])) {
            $query->where('priority', $filters['priority']);
        }

        // Project filter
        if (!empty($filters['project_id'])) {
            $query->where('project_id', $filters['project_id']);
        }

        // Client filter
        if (!empty($filters['client_id'])) {
            $query->where('user_id', $filters['client_id']);
        }

        // Date range filters
        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }
    }

    /**
     * Prepare forwarded message content
     */
    protected function prepareForwardedContent(Message $originalMessage, ?string $forwardMessage): string
    {
        $content = '';

        if ($forwardMessage) {
            $content .= $forwardMessage . "\n\n";
            $content .= "--- Forwarded Message ---\n\n";
        } else {
            $content .= "--- Forwarded Message ---\n\n";
        }

        $content .= "From: {$originalMessage->name} <{$originalMessage->email}>\n";
        $content .= "Date: {$originalMessage->created_at->format('Y-m-d H:i:s')}\n";
        $content .= "Subject: {$originalMessage->subject}\n\n";
        $content .= $originalMessage->message;

        return $content;
    }

    /**
     * Copy attachments from original message to forwarded message
     */
    protected function copyMessageAttachments(Message $originalMessage, Message $forwardedMessage): void
    {
        foreach ($originalMessage->attachments as $attachment) {
            try {
                $originalPath = $attachment->file_path;
                $newPath = 'message-attachments/' . $forwardedMessage->id . '/' . $attachment->file_name;

                // Ensure directory exists
                $newDir = dirname($newPath);
                if (!Storage::disk('public')->exists($newDir)) {
                    Storage::disk('public')->makeDirectory($newDir);
                }

                // Copy file
                if (Storage::disk('public')->copy($originalPath, $newPath)) {
                    // Create new attachment record
                    $forwardedMessage->attachments()->create([
                        'file_path' => $newPath,
                        'file_name' => $attachment->file_name,
                        'file_type' => $attachment->file_type,
                        'file_size' => $attachment->file_size,
                    ]);
                }

            } catch (\Exception $e) {
                Log::error('Failed to copy attachment for forwarded message', [
                    'original_attachment_id' => $attachment->id,
                    'forwarded_message_id' => $forwardedMessage->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * Send urgent notification for priority changes
     */
    protected function sendUrgentNotification(Message $message): void
    {
        try {
            Notifications::send('message.urgent', $message);

            Log::info('Urgent priority notification sent', [
                'message_id' => $message->id,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send urgent notification', [
                'message_id' => $message->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Check if message should be deleted as thread
     */
    protected function shouldDeleteAsThread(Message $message): bool
    {
        // If it has replies or is a reply, suggest thread deletion
        return $message->replies()->count() > 0 || !is_null($message->parent_id);
    }

    /**
     * Delete all attachments for a message
     */
    protected function deleteMessageAttachments(Message $message): void
    {
        foreach ($message->attachments as $attachment) {
            try {
                if (Storage::disk('public')->exists($attachment->file_path)) {
                    Storage::disk('public')->delete($attachment->file_path);
                }
                $attachment->delete();
            } catch (\Exception $e) {
                Log::error('Failed to delete attachment', [
                    'attachment_id' => $attachment->id,
                    'message_id' => $message->id,
                    'file_path' => $attachment->file_path,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }
    public function clientBulkArchive(array $messageIds, User $client): int
    {
        if (empty($messageIds)) {
            return 0;
        }

        $archivableMessages = $this->getClientMessagesQuery($client)
            ->whereIn('id', $messageIds)
            ->whereNull('archived_at')
            ->get();

        $archivedCount = 0;
        foreach ($archivableMessages as $message) {
            try {
                if ($this->canClientArchiveMessage($message, $client)) {
                    $this->archiveMessageForClient($message, $client);
                    $archivedCount++;
                }
            } catch (\Exception $e) {
                Log::error('Archive failed', ['message_id' => $message->id, 'error' => $e->getMessage()]);
            }
        }

        return $archivedCount;
    }

    /**
     * Get average response time for admin statistics
     */
    protected function getAverageResponseTimeForAdmin(): float
    {
        $repliedMessages = Message::excludeAdminMessages()
            ->whereNotNull('replied_at')
            ->where('is_replied', true)
            ->select('created_at', 'replied_at')
            ->get();

        if ($repliedMessages->isEmpty()) {
            return 0;
        }

        $totalHours = $repliedMessages->sum(function ($message) {
            // Fixed: Use now() to get current time and diff from replied_at
            return $message->created_at->diffInHours($message->replied_at);
        });

        return round($totalHours / $repliedMessages->count(), 1);
    }

    /**
     * Create a simple Set class for tracking processed items
     */
    protected function createSet(): object
    {
        return new class {
            private array $items = [];

            public function add($item): void
            {
                $this->items[$item] = true;
            }

            public function contains($item): bool
            {
                return isset($this->items[$item]);
            }

            public function toArray(): array
            {
                return array_keys($this->items);
            }
        };
    }

    /**
     * Get thread size for logging/reporting
     */
    protected function getThreadSize(Message $message): int
    {
        $rootMessage = $message->parent_id ? $message->parent : $message;

        return Message::where(function ($query) use ($rootMessage) {
            $query->where('id', $rootMessage->id)
                ->orWhere('parent_id', $rootMessage->id);
        })->count();
    }

    /**
     * Check if message can be deleted
     */
    protected function canDeleteMessage(Message $message): bool
    {
        return $message->replies()->count() === 0 && !$message->parent_id;
    }

    /**
     * Get message status text for export
     */
    protected function getMessageStatusText(Message $message): string
    {
        if ($message->priority === 'urgent') {
            return 'Urgent';
        }

        if (!$message->is_read) {
            return 'Unread';
        }

        if (!$message->is_replied) {
            return 'Pending Reply';
        }

        return 'Replied';
    }

    /**
     * Get recommended deletion action
     */
    protected function getRecommendedDeletionAction(Message $message): string
    {
        $hasReplies = $message->replies()->count() > 0;
        $isReply = !is_null($message->parent_id);

        if ($hasReplies) {
            return 'delete_thread'; // Has replies, delete whole thread
        }

        if ($isReply) {
            return 'delete_thread'; // Is a reply, delete whole thread
        }

        return 'delete_single'; // Standalone message, can delete individually
    }

    // ========================================
    // CLIENT-SPECIFIC HELPER METHODS
    // ========================================

    /**
     * Check if client can delete a message
     */
    protected function canClientDeleteMessage(Message $message, User $client): bool
    {
        // Must be client's own message
        if ($message->user_id !== $client->id) {
            return false;
        }

        // Cannot delete admin messages
        if (in_array($message->type, ['admin_to_client', 'admin_reply'])) {
            return false;
        }

        // Additional business rules can be added here
        return true;
    }

    /**
     * Check if client can archive a message
     */
    protected function canClientArchiveMessage(Message $message, User $client): bool
    {
        // More permissive than deletion - can archive even admin messages
        return $message->user_id === $client->id ||
            ($message->email === $client->email && !$message->user_id);
    }


    /**
     * Get client's thread size (only their messages)
     */
    protected function getClientThreadSize(Message $message, User $client): int
    {
        $rootMessage = $message->parent_id ? $message->parent : $message;

        return Message::where(function ($query) use ($rootMessage) {
            $query->where('id', $rootMessage->id)
                ->orWhere('parent_id', $rootMessage->id);
        })
            ->where('user_id', $client->id)
            ->whereNotIn('type', ['admin_to_client', 'admin_reply'])
            ->count();
    }

    /**
     * Get recommended action for client
     */
    protected function getClientRecommendedAction(Message $message, User $client): string
    {
        $hasAdminReplies = $message->replies()->where('type', 'admin_to_client')->count() > 0;
        $isImportant = in_array($message->priority, ['high', 'urgent']);

        if ($hasAdminReplies || $isImportant) {
            return 'archive'; // Safer for important conversations
        }

        if ($message->parent_id) {
            return 'delete_thread'; // Delete conversation participation
        }

        return 'delete_single'; // Safe to delete individual message
    }

    /**
     * Get deletion warning message for client
     */
    protected function getClientDeletionWarning(Message $message, User $client): ?string
    {
        $hasAdminReplies = $message->replies()->where('type', 'admin_to_client')->count() > 0;

        if ($hasAdminReplies) {
            return 'This conversation has admin replies. Consider archiving instead of deleting.';
        }

        if ($message->priority === 'urgent') {
            return 'This is an urgent message. Are you sure you want to delete it?';
        }

        if ($message->project_id) {
            return 'This message is linked to a project. Deleting may affect project records.';
        }

        return null;
    }
    private function notifyUrgentMessages(array $messageIds, User $admin): void
{
    // Implementation depends on your notification system
    // This is a placeholder for urgent message notifications
}

/**
 * Notify assigned admin about new message assignments
 */
private function notifyAssignedAdmin(User $assignedAdmin, array $messageIds, User $assigningAdmin): void
{
    // Implementation depends on your notification system
    // This is a placeholder for assignment notifications
}
}