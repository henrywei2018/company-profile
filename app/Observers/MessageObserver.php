<?php
// File: app/Observers/MessageObserver.php

namespace App\Observers;

use App\Models\Message;
use App\Models\User;
use App\Services\NotificationService;
use App\Services\TempNotifiable;
use App\Facades\Notifications;
use Illuminate\Support\Facades\Log;

class MessageObserver
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

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
                'priority' => $message->priority
            ]);

            // Determine notification recipients and type
            if ($this->isFromClient($message)) {
                $this->handleClientMessage($message);
            } elseif ($this->isFromAdmin($message)) {
                $this->handleAdminMessage($message);
            }

            // Send auto-reply if configured
            if ($this->shouldSendAutoReply($message)) {
                $this->sendAutoReply($message);
            }

        } catch (\Exception $e) {
            Log::error('Failed to send message notification in observer', [
                'message_id' => $message->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Handle the Message "updated" event.
     */
    public function updated(Message $message): void
    {
        try {
            // Check if message was marked as read
            if ($message->isDirty('is_read') && $message->is_read) {
                $this->handleMessageRead($message);
            }

            // Check if message was replied to
            if ($message->isDirty('is_replied') && $message->is_replied) {
                $this->handleMessageReplied($message);
            }

            // Check if priority changed to urgent
            if ($message->isDirty('priority') && $message->priority === 'urgent') {
                $this->handleUrgentPriorityChange($message);
            }

        } catch (\Exception $e) {
            Log::error('Failed to handle message update notification', [
                'message_id' => $message->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle client-to-admin messages
     */
    protected function handleClientMessage(Message $message): void
    {
        // Determine notification type
        $notificationType = $this->isUrgent($message) ? 'message.urgent' : 'message.created';

        // Send to admins
        $this->notificationService->send($notificationType, $message);

        Log::info('Client message notification sent', [
            'message_id' => $message->id,
            'type' => $notificationType,
            'subject' => $message->subject
        ]);
    }

    /**
     * Handle admin-to-client messages (replies)
     */
    protected function handleAdminMessage(Message $message): void
    {
        // This is likely a reply from admin to client
        $recipient = $this->getClientRecipient($message);
        
        if ($recipient) {
            $this->notificationService->send('message.reply', $message, $recipient);
            
            Log::info('Admin reply notification sent', [
                'message_id' => $message->id,
                'recipient_type' => get_class($recipient),
                'subject' => $message->subject
            ]);
        }
    }

    /**
     * Send auto-reply notification
     */
    protected function sendAutoReply(Message $message): void
    {
        $recipient = TempNotifiable::forMessage($message->email, $message->name);
        
        $this->notificationService->send('message.auto_reply', $message, $recipient);
        
        Log::info('Auto-reply notification sent', [
            'message_id' => $message->id,
            'recipient_email' => $message->email
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
    }

    /**
     * Handle message replied event
     */
    protected function handleMessageReplied(Message $message): void
    {
        if (!$message->replied_at) {
            $message->update(['replied_at' => now()]);
        }
    }

    /**
     * Handle urgent priority change
     */
    protected function handleUrgentPriorityChange(Message $message): void
    {
        // Re-send as urgent if priority was just changed to urgent
        $this->notificationService->send('message.urgent', $message);
        
        Log::info('Urgent priority notification sent', [
            'message_id' => $message->id
        ]);
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
                // Original sender is registered
                return $originalMessage->user;
            } elseif ($originalMessage->email) {
                // Original sender is not registered
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
     * Check if notification should be skipped
     */
    protected function shouldSkipNotification(Message $message): bool
    {
        return $message->type === 'system' 
            || $message->is_auto_reply 
            || $message->skip_notifications ?? false;
    }

    /**
     * Check if message is from client
     */
    protected function isFromClient(Message $message): bool
    {
        return $message->type === 'client_to_admin' 
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
    // INTEGRATION METHODS FOR YOUR EXISTING OBSERVER
    // =============================================================

    /**
     * Method to manually trigger notifications from existing observer
     */
    public function triggerNotification(Message $message, string $type, $recipient = null): bool
    {
        try {
            return $this->notificationService->send($type, $message, $recipient);
        } catch (\Exception $e) {
            Log::error('Manual notification trigger failed', [
                'message_id' => $message->id,
                'type' => $type,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Method to send notification to specific user type
     */
    public function notifyAdmins(Message $message, string $type = 'message.created'): bool
    {
        return $this->notificationService->send($type, $message);
    }

    /**
     * Method to send notification to message sender
     */
    public function notifyMessageSender(Message $message, string $type = 'message.reply'): bool
    {
        $recipient = null;

        if ($message->user) {
            $recipient = $message->user;
        } elseif ($message->email) {
            $recipient = TempNotifiable::forMessage($message->email, $message->name);
        }

        if ($recipient) {
            return $this->notificationService->send($type, $message, $recipient);
        }

        return false;
    }

    /**
     * Bulk notification method for multiple messages
     */
    public function bulkNotify(array $messages, string $type): array
    {
        $results = [];
        
        foreach ($messages as $message) {
            $results[$message->id] = $this->triggerNotification($message, $type);
        }

        return $results;
    }
}