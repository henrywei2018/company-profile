<?php
// File: app/Observers/ChatSessionObserver.php

namespace App\Observers;

use App\Models\ChatSession;
use App\Services\NotificationService;

class ChatSessionObserver
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Handle the ChatSession "created" event.
     */
    public function created(ChatSession $chatSession): void
    {
        // Notify operators about new chat session
        $this->notificationService->send('chat.session_started', $chatSession);
    }

    /**
     * Handle the ChatSession "updated" event.
     */
    public function updated(ChatSession $chatSession): void
    {
        // Check if session status changed
        if ($chatSession->isDirty('status')) {
            $this->handleStatusChange($chatSession);
        }

        // Check if session has been waiting too long
        if ($chatSession->status === 'waiting' && $this->isWaitingTooLong($chatSession)) {
            $this->notificationService->send('chat.session_waiting', $chatSession);
        }

        // Check if session is inactive
        if ($this->isSessionInactive($chatSession)) {
            $this->notificationService->send('chat.session_inactive', $chatSession);
        }
    }

    /**
     * Handle session status changes
     */
    protected function handleStatusChange(ChatSession $chatSession): void
    {
        switch ($chatSession->status) {
            case 'active':
                // Session became active, might want to notify
                break;
            case 'closed':
                $this->notificationService->send('chat.session_closed', $chatSession);
                break;
            case 'waiting':
                $this->notificationService->send('chat.session_waiting', $chatSession);
                break;
        }
    }

    /**
     * Check if session has been waiting too long
     */
    protected function isWaitingTooLong(ChatSession $chatSession): bool
    {
        if (!$chatSession->created_at) return false;
        
        $waitingMinutes = now()->diffInMinutes($chatSession->created_at);
        return $waitingMinutes > 10; // Alert if waiting more than 10 minutes
    }

    /**
     * Check if session is inactive
     */
    protected function isSessionInactive(ChatSession $chatSession): bool
    {
        if (!$chatSession->last_activity_at) return false;
        
        $inactiveMinutes = now()->diffInMinutes($chatSession->last_activity_at);
        return $inactiveMinutes > 30; // Alert if inactive more than 30 minutes
    }
}