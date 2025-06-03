<?php
// File: app/Observers/ChatSessionObserver.php

namespace App\Observers;

use App\Models\ChatSession;
use App\Traits\SendsNotifications;
use App\Facades\Notifications;
use Illuminate\Support\Facades\Log;

class ChatSessionObserver
{
    use SendsNotifications;

    /**
     * Handle the ChatSession "created" event.
     */
    public function created(ChatSession $chatSession): void
    {
        try {
            Log::info('Chat session created, sending notifications', [
                'session_id' => $chatSession->session_id,
                'user_id' => $chatSession->user_id,
                'status' => $chatSession->status
            ]);

            // Send notification to available operators
            $this->sendIfEnabled('chat.session_started', $chatSession);

            // If session is waiting, notify about waiting status
            if ($chatSession->status === 'waiting') {
                $this->sendIfEnabled('chat.session_waiting', $chatSession);
            }

        } catch (\Exception $e) {
            Log::error('Failed to send chat session created notifications', [
                'session_id' => $chatSession->session_id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle the ChatSession "updated" event.
     */
    public function updated(ChatSession $chatSession): void
    {
        try {
            // Check what was updated
            if ($chatSession->isDirty('status')) {
                $this->handleStatusChange($chatSession);
            }

            if ($chatSession->isDirty('assigned_operator_id')) {
                $this->handleOperatorAssignment($chatSession);
            }

            if ($chatSession->isDirty('priority')) {
                $this->handlePriorityChange($chatSession);
            }

        } catch (\Exception $e) {
            Log::error('Failed to send chat session update notifications', [
                'session_id' => $chatSession->session_id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle status changes
     */
    protected function handleStatusChange(ChatSession $chatSession): void
    {
        $oldStatus = $chatSession->getOriginal('status');
        $newStatus = $chatSession->status;

        Log::info('Chat session status changed', [
            'session_id' => $chatSession->session_id,
            'old_status' => $oldStatus,
            'new_status' => $newStatus
        ]);

        switch ($newStatus) {
            case 'active':
                if ($oldStatus === 'waiting') {
                    // Session was picked up by operator
                    $this->sendIfEnabled('chat.session_started', $chatSession);
                }
                break;

            case 'closed':
                // Session was closed
                $this->sendIfEnabled('chat.session_closed', $chatSession);
                break;

            case 'waiting':
                // Session is waiting for operator
                $this->sendIfEnabled('chat.session_waiting', $chatSession);
                break;
        }
    }

    /**
     * Handle operator assignment
     */
    protected function handleOperatorAssignment(ChatSession $chatSession): void
    {
        $oldOperatorId = $chatSession->getOriginal('assigned_operator_id');
        $newOperatorId = $chatSession->assigned_operator_id;

        if ($oldOperatorId && $newOperatorId && $oldOperatorId !== $newOperatorId) {
            // Session was transferred
            $this->sendIfEnabled('chat.session_transferred', $chatSession);
        } elseif (!$oldOperatorId && $newOperatorId) {
            // Session was assigned to operator
            $this->sendIfEnabled('chat.operator_joined', $chatSession);
        }
    }

    /**
     * Handle priority changes
     */
    protected function handlePriorityChange(ChatSession $chatSession): void
    {
        if ($chatSession->priority === 'urgent') {
            // High priority session - notify immediately
            $this->sendIfEnabled('chat.session_urgent', $chatSession);
        }
    }

    /**
     * Handle the ChatSession "deleted" event.
     */
    public function deleted(ChatSession $chatSession): void
    {
        try {
            Log::info('Chat session deleted', [
                'session_id' => $chatSession->session_id
            ]);

            // Could send cleanup notifications if needed
            
        } catch (\Exception $e) {
            Log::error('Failed to handle chat session deletion', [
                'session_id' => $chatSession->session_id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Check for sessions that need attention (called by scheduled job)
     */
    public static function checkAbandonedSessions(): void
    {
        $abandonedSessions = ChatSession::where('status', 'waiting')
            ->where('created_at', '<', now()->subMinutes(10))
            ->get();

        foreach ($abandonedSessions as $session) {
            Notifications::send('chat.session_abandoned', $session);
        }

        Log::info('Abandoned sessions check completed', [
            'abandoned_count' => $abandonedSessions->count()
        ]);
    }

    /**
     * Check for inactive sessions (called by scheduled job)
     */
    public static function checkInactiveSessions(): void
    {
        $inactiveSessions = ChatSession::where('status', 'active')
            ->where('last_activity_at', '<', now()->subMinutes(30))
            ->get();

        foreach ($inactiveSessions as $session) {
            Notifications::send('chat.session_inactive', $session);
        }

        Log::info('Inactive sessions check completed', [
            'inactive_count' => $inactiveSessions->count()
        ]);
    }
}