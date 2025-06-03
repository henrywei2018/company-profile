<?php
// File: app/Observers/ChatMessageObserver.php

namespace App\Observers;

use App\Models\ChatMessage;
use App\Traits\SendsNotifications;
use App\Facades\Notifications;
use Illuminate\Support\Facades\Log;

class ChatMessageObserver
{
    use SendsNotifications;

    /**
     * Handle the ChatMessage "created" event.
     */
    public function created(ChatMessage $chatMessage): void
    {
        try {
            Log::info('Chat message created, sending notifications', [
                'message_id' => $chatMessage->id,
                'sender_type' => $chatMessage->sender_type,
                'session_id' => $chatMessage->chatSession->session_id ?? 'unknown'
            ]);

            $this->handleNewMessage($chatMessage);

        } catch (\Exception $e) {
            Log::error('Failed to send chat message created notifications', [
                'message_id' => $chatMessage->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle new message notifications
     */
    protected function handleNewMessage(ChatMessage $chatMessage): void
    {
        $session = $chatMessage->chatSession;
        
        if (!$session) {
            return;
        }

        // Update session activity
        $session->touch('last_activity_at');

        switch ($chatMessage->sender_type) {
            case 'visitor':
                // Visitor sent message - notify operators
                $this->sendIfEnabled('chat.message_received', $session);
                break;

            case 'operator':
                // Operator sent message - notify client
                if ($session->user) {
                    $this->sendIfEnabled('chat.operator_reply', $session, $session->user);
                }
                break;

            case 'system':
                // System message - usually no notification needed
                Log::info('System message created', [
                    'message' => $chatMessage->message,
                    'session_id' => $session->session_id
                ]);
                break;
        }

        // Check if this is an urgent message
        if ($this->isUrgentMessage($chatMessage)) {
            $this->sendIfEnabled('chat.urgent_message', $session);
        }
    }

    /**
     * Determine if message is urgent
     */
    protected function isUrgentMessage(ChatMessage $chatMessage): bool
    {
        $urgentKeywords = ['urgent', 'emergency', 'asap', 'immediately', 'help'];
        $message = strtolower($chatMessage->message);

        foreach ($urgentKeywords as $keyword) {
            if (str_contains($message, $keyword)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Handle the ChatMessage "updated" event.
     */
    public function updated(ChatMessage $chatMessage): void
    {
        try {
            // Check if message was marked as read
            if ($chatMessage->isDirty('is_read') && $chatMessage->is_read) {
                Log::info('Chat message marked as read', [
                    'message_id' => $chatMessage->id,
                    'sender_type' => $chatMessage->sender_type
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Failed to handle chat message update', [
                'message_id' => $chatMessage->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle the ChatMessage "deleted" event.
     */
    public function deleted(ChatMessage $chatMessage): void
    {
        try {
            Log::info('Chat message deleted', [
                'message_id' => $chatMessage->id,
                'sender_type' => $chatMessage->sender_type
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to handle chat message deletion', [
                'message_id' => $chatMessage->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}