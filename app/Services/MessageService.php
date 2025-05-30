<?php

namespace App\Services;

use App\Models\Message;
use App\Models\User;
use App\Services\TempNotifiable;
use App\Facades\Notifications;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;

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
            $query->where(function($q) use ($filters) {
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
                'read_at' => now(),
            ]);
        }
    }
    
    public function markAsUnread(Message $message): void
    {
        if ($message->is_read) {
            $message->update([
                'is_read' => false,
                'read_at' => null,
            ]);
        }
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

    public function bulkMarkAsRead(array $messageIds, User $user): int
    {
        $query = Message::whereIn('id', $messageIds);
        
        // Apply access control
        if (!$user->hasAnyRole(['super-admin', 'admin', 'manager'])) {
            $query->where('user_id', $user->id);
        }
        
        return $query->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
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

    public function getMessageThread(Message $message): \Illuminate\Database\Eloquent\Collection
    {
        $rootMessage = $message->getRootMessage();
        
        return Message::where(function($query) use ($rootMessage) {
            $query->where('id', $rootMessage->id)
                  ->orWhere('parent_id', $rootMessage->id);
        })
        ->with(['attachments', 'sender'])
        ->orderBy('created_at')
        ->get();
    }
    
    public function getUnreadCount(User $user): int
    {
        $query = Message::query();
        
        if (!$user->hasAnyRole(['super-admin', 'admin', 'manager'])) {
            $query->where('user_id', $user->id);
        }
        
        return $query->where('is_read', false)->count();
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
            if (!$message->overdue_notification_sent_at || 
                !$message->overdue_notification_sent_at->isToday()) {
                
                Notifications::send('message.overdue', $message);
                $message->update(['overdue_notification_sent_at' => now()]);
                $sent++;
            }
        }

        return $sent;
    }
    
    public function getMessageStatistics(User $user = null): array
    {
        $query = Message::query();
        
        if ($user && !$user->hasAnyRole(['super-admin', 'admin', 'manager'])) {
            $query->where('user_id', $user->id);
        }
        
        return [
            'total' => (clone $query)->count(),
            'unread' => (clone $query)->where('is_read', false)->count(),
            'replied' => (clone $query)->where('is_replied', true)->count(),
            'urgent' => (clone $query)->where('priority', 'urgent')->count(),
            'overdue' => (clone $query)->where('requires_response', true)
                ->where('is_replied', false)
                ->where('response_deadline', '<', now())
                ->whereNotNull('response_deadline')
                ->count(),
            'this_week' => (clone $query)->whereBetween('created_at', [
                now()->startOfWeek(),
                now()->endOfWeek()
            ])->count(),
            'this_month' => (clone $query)->whereMonth('created_at', now()->month)->count(),
            'by_type' => (clone $query)
                ->selectRaw('type, COUNT(*) as count')
                ->groupBy('type')
                ->pluck('count', 'type')
                ->toArray(),
            'by_priority' => (clone $query)
                ->selectRaw('priority, COUNT(*) as count')
                ->groupBy('priority')
                ->pluck('count', 'priority')
                ->toArray(),
            'response_rate' => $this->calculateResponseRate($query),
            'average_response_time' => $this->calculateAverageResponseTime($query),
        ];
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
        if ($totalRequiringResponse === 0) return 0;

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

        if ($repliedMessages->isEmpty()) return 0;

        $totalHours = $repliedMessages->sum(function($message) {
            return $message->created_at->diffInHours($message->replied_at);
        });

        return round($totalHours / $repliedMessages->count(), 1);
    }
}