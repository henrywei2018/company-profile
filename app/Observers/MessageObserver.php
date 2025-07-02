<?php

namespace App\Observers;

use App\Models\Message;
use App\Services\TempNotifiable;
use App\Facades\Notifications;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

/**
 * Enhanced Message Observer using existing centralized notification system
 * Works with the existing NotificationService and Notifications facade
 */
class MessageObserver
{
    /**
     * Handle the Message "created" event.
     */
    public function created(Message $message): void
    {
        try {
            // Skip auto-notifications for system messages or auto-replies
            if ($this->shouldSkipNotification($message)) {
                return;
            }

            Log::info('Message created, processing notifications', [
                'message_id' => $message->id,
                'type' => $message->type,
                'priority' => $message->priority,
                'subject' => $message->subject,
            ]);

            // Handle notifications based on message type
            if ($this->isFromClient($message)) {
                $this->handleClientMessage($message);
            } elseif ($this->isFromAdmin($message)) {
                $this->handleAdminMessage($message);
            }

            // Send auto-reply if configured
            if ($this->shouldSendAutoReply($message)) {
                $this->sendAutoReply($message);
            }

            // Clear relevant caches
            $this->clearMessageCaches($message);

        } catch (\Exception $e) {
            Log::error('Failed to send message notification in observer', [
                'message_id' => $message->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    /**
     * Handle the Message "updated" event.
     */
    public function updated(Message $message): void
    {
        $changes = $message->getChanges();

        if (empty($changes)) {
            return;
        }

        try {
            // Handle specific status changes
            if (isset($changes['is_read']) && $changes['is_read']) {
                $this->handleMessageRead($message);
            }

            if (isset($changes['is_replied']) && $changes['is_replied']) {
                $this->handleMessageReplied($message);
            }

            if (isset($changes['priority']) && $changes['priority'] === 'urgent') {
                $this->handleUrgentPriorityChange($message);
            }

            // Clear relevant caches
            $this->clearMessageCaches($message);

            Log::info('Message updated', [
                'message_id' => $message->id,
                'changes' => array_keys($changes),
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to handle message update notification', [
                'message_id' => $message->id,
                'changes' => array_keys($changes),
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Handle client-to-admin messages using centralized notifications
     */
    protected function handleClientMessage(Message $message): void
    {
        // Determine notification type based on priority
        $notificationType = $this->isUrgent($message) 
            ? 'message.urgent' 
            : 'message.created';

        // Send to admins using centralized notification system
        $sent = Notifications::send($notificationType, $message);

        Log::info('Client message notification sent via centralized system', [
            'message_id' => $message->id,
            'type' => $notificationType,
            'sent' => $sent,
            'subject' => $message->subject,
        ]);
    }

    /**
     * Handle admin-to-client messages (replies) using centralized notifications
     */
    protected function handleAdminMessage(Message $message): void
    {
        $recipient = $this->getClientRecipient($message);
        
        if (!$recipient) {
            Log::warning('No recipient found for admin message', [
                'message_id' => $message->id,
            ]);
            return;
        }

        // Send reply notification using centralized system
        $sent = Notifications::send('message.reply', $message, $recipient);
        
        Log::info('Admin reply notification sent via centralized system', [
            'message_id' => $message->id,
            'recipient_type' => get_class($recipient),
            'sent' => $sent,
            'subject' => $message->subject,
        ]);
    }

    /**
     * Send auto-reply notification using centralized system
     */
    protected function sendAutoReply(Message $message): void
    {
        $recipient = TempNotifiable::forMessage($message->email, $message->name);
        
        $sent = Notifications::send('message.auto_reply', $message, $recipient);
        
        Log::info('Auto-reply notification sent via centralized system', [
            'message_id' => $message->id,
            'recipient_email' => $message->email,
            'sent' => $sent,
        ]);
    }

    /**
     * Handle message read event
     */
    protected function handleMessageRead(Message $message): void
    {
        if (!$message->read_at) {
            $message->update(['read_at' => now()]);
        }

        // Mark related thread messages as read if this is a client
        if ($message->user_id) {
            $this->markThreadAsReadForClient($message);
        }

        Log::debug('Message marked as read', [
            'message_id' => $message->id,
            'user_id' => $message->user_id,
        ]);
    }

    /**
     * Handle message replied event
     */
    protected function handleMessageReplied(Message $message): void
    {
        if (!$message->replied_at) {
            $message->update([
                'replied_at' => now(),
                'replied_by' => auth()->id(),
            ]);
        }

        Log::debug('Message marked as replied', [
            'message_id' => $message->id,
            'replied_by' => auth()->id(),
        ]);
    }

    /**
     * Handle urgent priority change using centralized notifications
     */
    protected function handleUrgentPriorityChange(Message $message): void
    {
        // Re-send as urgent notification using centralized system
        $sent = Notifications::send('message.urgent', $message);
        
        Log::info('Urgent priority notification sent via centralized system', [
            'message_id' => $message->id,
            'sent' => $sent,
        ]);
    }

    /**
     * Mark entire thread as read for client
     */
    protected function markThreadAsReadForClient(Message $message): void
    {
        if (!$message->user_id) {
            return;
        }

        $rootMessage = $message->parent_id ? $message->parent : $message;
        
        // Get all unread messages in thread for this client
        $updated = Message::where(function ($query) use ($rootMessage) {
            $query->where('id', $rootMessage->id)
                  ->orWhere('parent_id', $rootMessage->id);
        })
        ->where('user_id', $message->user_id)
        ->where('is_read', false)
        ->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        if ($updated > 0) {
            Log::debug('Thread marked as read for client', [
                'root_message_id' => $rootMessage->id,
                'client_id' => $message->user_id,
                'updated_count' => $updated,
            ]);
        }
    }

    /**
     * Get client recipient for admin messages
     */
    protected function getClientRecipient(Message $message)
    {
        // Try to find the original message recipient
        if ($message->parent_id && $message->parent) {
            $originalMessage = $message->parent;
            
            if ($originalMessage->user) {
                return $originalMessage->user;
            } elseif ($originalMessage->email) {
                return TempNotifiable::forMessage(
                    $originalMessage->email,
                    $originalMessage->name
                );
            }
        }

        // Try current message user
        if ($message->user) {
            return $message->user;
        }

        // Try email from current message
        if ($message->email) {
            return TempNotifiable::forMessage($message->email, $message->name);
        }

        return null;
    }

    /**
     * Clear message-related caches
     */
    protected function clearMessageCaches(Message $message): void
    {
        $cacheKeys = [
            'messages_unread_count',
            'admin_dashboard_stats',
        ];

        // Add client-specific caches
        if ($message->user_id) {
            $cacheKeys = array_merge($cacheKeys, [
                "dashboard_data_{$message->user_id}_client",
                "client_stats_{$message->user_id}",
                "messages_unread_count_{$message->user_id}",
            ]);
        }

        // Add project-specific caches
        if ($message->project_id) {
            $cacheKeys[] = "project_messages_{$message->project_id}";
        }

        foreach ($cacheKeys as $key) {
            Cache::forget($key);
        }

        Log::debug('Message caches cleared', [
            'message_id' => $message->id,
            'cache_keys_count' => count($cacheKeys),
        ]);
    }

    /**
     * Check if notification should be skipped
     */
    protected function shouldSkipNotification(Message $message): bool
    {
        return $message->type === 'system' 
            || $message->is_auto_reply 
            || ($message->skip_notifications ?? false);
    }

    /**
     * Check if message is from client
     */
    protected function isFromClient(Message $message): bool
    {
        return $message->type === 'client_to_admin' 
            || $message->type === 'client_reply'
            || (!$message->admin_id && $message->user_id);
    }

    /**
     * Check if message is from admin
     */
    protected function isFromAdmin(Message $message): bool
    {
        return $message->type === 'admin_to_client' 
            || $message->admin_id 
            || $message->parent_id; // Replies are usually from admin
    }

    /**
     * Check if message is urgent
     */
    protected function isUrgent(Message $message): bool
    {
        return $message->priority === 'urgent'
            || str_contains(strtolower($message->subject), 'urgent')
            || str_contains(strtolower($message->subject), 'emergency');
    }

    /**
     * Check if should send auto-reply
     */
    protected function shouldSendAutoReply(Message $message): bool
    {
        return config('notifications.auto_notifications.message.auto_reply', true)
            && settings('message_auto_reply_enabled', true)
            && $this->isFromClient($message)
            && !$message->parent_id // Only for new messages, not replies
            && !$message->is_auto_reply;
    }

    // =============================================================
    // MANUAL TRIGGER METHODS FOR CONTROLLERS
    // =============================================================

    /**
     * Manual trigger for reply notifications (called from controllers)
     */
    public function triggerReplyNotification(Message $originalMessage, Message $reply): bool
    {
        try {
            // Ensure proper threading
            $this->ensureProperThreading($originalMessage, $reply);

            // Update original message status
            $this->updateOriginalMessageStatus($originalMessage, $reply);

            // Send reply notification using centralized system
            if ($this->isFromAdmin($reply)) {
                // Admin replied to client
                $recipient = $this->getClientRecipient($reply);
                if ($recipient) {
                    $sent = Notifications::send('message.reply', $reply, $recipient);
                    
                    Log::info('Manual reply notification sent', [
                        'original_message_id' => $originalMessage->id,
                        'reply_id' => $reply->id,
                        'sent' => $sent,
                    ]);
                    
                    return $sent;
                }
            } elseif ($this->isFromClient($reply)) {
                // Client replied - notify admins
                $sent = Notifications::send('message.client_reply', $reply);
                
                Log::info('Manual client reply notification sent', [
                    'original_message_id' => $originalMessage->id,
                    'reply_id' => $reply->id,
                    'sent' => $sent,
                ]);
                
                return $sent;
            }

            return false;

        } catch (\Exception $e) {
            Log::error('Manual reply notification trigger failed', [
                'original_message_id' => $originalMessage->id,
                'reply_id' => $reply->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Manual trigger for urgent message notifications
     */
    public function triggerUrgentNotification(Message $message): bool
    {
        try {
            $sent = Notifications::send('message.urgent', $message);
            
            Log::info('Manual urgent notification sent', [
                'message_id' => $message->id,
                'sent' => $sent,
            ]);
            
            return $sent;

        } catch (\Exception $e) {
            Log::error('Manual urgent notification trigger failed', [
                'message_id' => $message->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Bulk notification method for multiple messages
     */
    public function bulkNotify(array $messages, string $action = 'created'): array
    {
        $results = [];
        
        foreach ($messages as $message) {
            try {
                switch ($action) {
                    case 'created':
                        $results[$message->id] = Notifications::send('message.created', $message);
                        break;
                        
                    case 'urgent':
                        $results[$message->id] = Notifications::send('message.urgent', $message);
                        break;
                        
                    default:
                        $results[$message->id] = false;
                }
            } catch (\Exception $e) {
                Log::error('Bulk notification failed for message', [
                    'message_id' => $message->id,
                    'action' => $action,
                    'error' => $e->getMessage(),
                ]);
                $results[$message->id] = false;
            }
        }

        return $results;
    }

    /**
     * Ensure proper message threading
     */
    protected function ensureProperThreading(Message $originalMessage, Message $reply): void
    {
        $rootMessage = $originalMessage->parent_id ? $originalMessage->parent : $originalMessage;
        
        if ($reply->parent_id !== $rootMessage->id) {
            $reply->update(['parent_id' => $rootMessage->id]);
            
            Log::debug('Reply threading corrected', [
                'reply_id' => $reply->id,
                'root_message_id' => $rootMessage->id,
            ]);
        }
    }

    /**
     * Update original message status when replied
     */
    protected function updateOriginalMessageStatus(Message $originalMessage, Message $reply): void
    {
        if (!$originalMessage->is_replied) {
            $originalMessage->update([
                'is_replied' => true,
                'replied_at' => now(),
                'replied_by' => $reply->user_id ?? auth()->id(),
            ]);
        }
    }
}