<?php

namespace App\Observers;

use App\Models\Message;
use App\Services\NotificationService;

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
        // Determine message type and send appropriate notifications
        if ($this->isFromClient($message)) {
            // Client sent message to admin
            $this->notificationService->send('message.created', $message);
            
            // Check if urgent
            if ($this->isUrgent($message)) {
                $this->notificationService->send('message.urgent', $message);
            }
        } elseif ($this->isFromAdmin($message)) {
            // Admin sent message to client
            $this->notificationService->send('message.reply', $message);
        }

        // Send auto-reply if configured
        if ($this->shouldSendAutoReply($message)) {
            $this->notificationService->send('message.auto_reply', $message);
        }
    }

    /**
     * Handle the Message "updated" event.
     */
    public function updated(Message $message): void
    {
        // Check if message was marked as read
        if ($message->isDirty('is_read') && $message->is_read) {
            $this->handleMessageRead($message);
        }

        // Check if message was replied to
        if ($message->isDirty('is_replied') && $message->is_replied) {
            $this->handleMessageReplied($message);
        }

        // Check if priority changed
        if ($message->isDirty('priority')) {
            $this->handlePriorityChange($message);
        }
    }

    /**
     * Check if message is from client
     */
    protected function isFromClient(Message $message): bool
    {
        return $message->type === 'client_to_admin' || 
               ($message->user_id && !$message->admin_id);
    }

    /**
     * Check if message is from admin
     */
    protected function isFromAdmin(Message $message): bool
    {
        return $message->type === 'admin_to_client' || 
               $message->admin_id;
    }

    /**
     * Check if message is urgent
     */
    protected function isUrgent(Message $message): bool
    {
        return $message->priority === 'urgent' ||
               str_contains(strtolower($message->subject), 'urgent') ||
               str_contains(strtolower($message->subject), 'emergency');
    }

    /**
     * Check if should send auto-reply
     */
    protected function shouldSendAutoReply(Message $message): bool
    {
        return config('notifications.auto_notifications.message.auto_reply', true) &&
               $this->isFromClient($message) &&
               !$message->parent_id; // Only for new messages, not replies
    }

    /**
     * Handle message read event
     */
    protected function handleMessageRead(Message $message): void
    {
        // Update read timestamp if not set
        if (!$message->read_at) {
            $message->update(['read_at' => now()]);
        }
    }

    /**
     * Handle message replied event
     */
    protected function handleMessageReplied(Message $message): void
    {
        // Update replied timestamp if not set
        if (!$message->replied_at) {
            $message->update(['replied_at' => now()]);
        }
    }

    /**
     * Handle priority changes
     */
    protected function handlePriorityChange(Message $message): void
    {
        $oldPriority = $message->getOriginal('priority');
        $newPriority = $message->priority;

        // If changed to urgent, notify immediately
        if ($newPriority === 'urgent' && $oldPriority !== 'urgent') {
            $this->notificationService->send('message.urgent', $message);
        }
    }
}