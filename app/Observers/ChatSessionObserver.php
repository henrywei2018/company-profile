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
                'visitor_name' => $chatSession->getVisitorName(),
                'visitor_email' => $chatSession->getVisitorEmail()
            ]);

            // Notify available operators about new chat session
            $this->sendIfEnabled('chat.session_started', $chatSession);

            // Set initial activity timestamp
            $chatSession->update(['last_activity_at' => now()]);

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
            // Check if session status changed
            if ($chatSession->isDirty('status')) {
                $this->handleStatusChange($chatSession);
            }

            // Check if operator was assigned
            if ($chatSession->isDirty('operator_id')) {
                $this->handleOperatorAssignment($chatSession);
            }

            // Check if session has been waiting too long
            if ($chatSession->status === 'waiting' && $this->isWaitingTooLong($chatSession)) {
                $this->handleLongWait($chatSession);
            }

            // Check if session is inactive
            if ($this->isSessionInactive($chatSession)) {
                $this->handleInactiveSession($chatSession);
            }

        } catch (\Exception $e) {
            Log::error('Failed to send chat session update notifications', [
                'session_id' => $chatSession->session_id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle session status changes
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
                $this->handleSessionActivation($chatSession);
                break;
                
            case 'closed':
                $this->handleSessionClosure($chatSession);
                break;
                
            case 'waiting':
                $this->handleSessionWaiting($chatSession);
                break;
                
            case 'transferred':
                $this->handleSessionTransfer($chatSession);
                break;
        }
    }

    /**
     * Handle session activation
     */
    protected function handleSessionActivation(ChatSession $chatSession): void
    {
        // Update started timestamp
        if (!$chatSession->started_at) {
            $chatSession->update(['started_at' => now()]);
        }

        // Notify operator about active session
        if ($chatSession->operator) {
            $this->sendNotification('chat.session_active', $chatSession, $chatSession->operator);
        }

        Log::info('Chat session activated', [
            'session_id' => $chatSession->session_id,
            'operator_id' => $chatSession->operator_id
        ]);
    }

    /**
     * Handle session closure
     */
    protected function handleSessionClosure(ChatSession $chatSession): void
    {
        // Update ended timestamp
        if (!$chatSession->ended_at) {
            $chatSession->update(['ended_at' => now()]);
        }

        // Generate session summary
        $summary = $this->generateSessionSummary($chatSession);

        // Send closure notification with summary
        $this->sendNotification('chat.session_closed', $chatSession);

        // Notify operator
        if ($chatSession->operator) {
            $this->sendNotification('chat.session_closed', $chatSession, $chatSession->operator);
        }

        Log::info('Chat session closed', [
            'session_id' => $chatSession->session_id,
            'duration' => $this->getSessionDuration($chatSession),
            'message_count' => $chatSession->messages()->count()
        ]);
    }

    /**
     * Handle session waiting
     */
    protected function handleSessionWaiting(ChatSession $chatSession): void
    {
        // Send waiting notification to operators
        $this->sendIfEnabled('chat.session_waiting', $chatSession);

        Log::info('Chat session waiting for operator', [
            'session_id' => $chatSession->session_id
        ]);
    }

    /**
     * Handle session transfer
     */
    protected function handleSessionTransfer(ChatSession $chatSession): void
    {
        $oldOperatorId = $chatSession->getOriginal('operator_id');
        $newOperatorId = $chatSession->operator_id;

        // Notify old operator
        if ($oldOperatorId) {
            $oldOperator = \App\Models\User::find($oldOperatorId);
            if ($oldOperator) {
                $this->sendNotification('chat.session_transferred_from', $chatSession, $oldOperator);
            }
        }

        // Notify new operator
        if ($newOperatorId) {
            $newOperator = \App\Models\User::find($newOperatorId);
            if ($newOperator) {
                $this->sendNotification('chat.session_transferred_to', $chatSession, $newOperator);
            }
        }

        Log::info('Chat session transferred', [
            'session_id' => $chatSession->session_id,
            'old_operator_id' => $oldOperatorId,
            'new_operator_id' => $newOperatorId
        ]);
    }

    /**
     * Handle operator assignment
     */
    protected function handleOperatorAssignment(ChatSession $chatSession): void
    {
        if ($chatSession->operator_id) {
            // Operator assigned
            $this->sendNotification('chat.operator_assigned', $chatSession, $chatSession->operator);
            
            Log::info('Operator assigned to chat session', [
                'session_id' => $chatSession->session_id,
                'operator_id' => $chatSession->operator_id
            ]);
        }
    }

    /**
     * Handle long waiting sessions
     */
    protected function handleLongWait(ChatSession $chatSession): void
    {
        $waitingMinutes = now()->diffInMinutes($chatSession->created_at);
        
        // Send urgent notification for long waits
        $this->sendNotification('chat.session_waiting_long', $chatSession);
        
        Log::warning('Chat session waiting too long', [
            'session_id' => $chatSession->session_id,
            'waiting_minutes' => $waitingMinutes
        ]);
    }

    /**
     * Handle inactive sessions
     */
    protected function handleInactiveSession(ChatSession $chatSession): void
    {
        $inactiveMinutes = now()->diffInMinutes($chatSession->last_activity_at);
        
        // Send inactive notification
        $this->sendNotification('chat.session_inactive', $chatSession);
        
        // Consider auto-closing after extended inactivity
        if ($inactiveMinutes > 60) { // 1 hour
            $this->considerAutoClose($chatSession);
        }
        
        Log::info('Chat session inactive', [
            'session_id' => $chatSession->session_id,
            'inactive_minutes' => $inactiveMinutes
        ]);
    }

    /**
     * Consider auto-closing inactive session
     */
    protected function considerAutoClose(ChatSession $chatSession): void
    {
        if ($chatSession->status === 'active') {
            // Send warning before auto-close
            $this->sendNotification('chat.session_auto_close_warning', $chatSession);
            
            // Schedule auto-close in 15 minutes if still inactive
            Log::info('Auto-close warning sent for inactive session', [
                'session_id' => $chatSession->session_id
            ]);
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

    /**
     * Generate session summary
     */
    protected function generateSessionSummary(ChatSession $chatSession): string
    {
        $messageCount = $chatSession->messages()->count();
        $duration = $this->getSessionDuration($chatSession);
        $visitorName = $chatSession->getVisitorName();
        
        return "Chat session with {$visitorName} completed. Duration: {$duration}, Messages: {$messageCount}";
    }

    /**
     * Get session duration
     */
    protected function getSessionDuration(ChatSession $chatSession): string
    {
        if (!$chatSession->started_at || !$chatSession->ended_at) {
            return 'Unknown duration';
        }
        
        $minutes = $chatSession->started_at->diffInMinutes($chatSession->ended_at);
        
        if ($minutes < 1) {
            return 'Less than 1 minute';
        } elseif ($minutes < 60) {
            return "{$minutes} minute(s)";
        } else {
            $hours = floor($minutes / 60);
            $remainingMinutes = $minutes % 60;
            return "{$hours}h {$remainingMinutes}m";
        }
    }

    /**
     * Handle the ChatSession "deleted" event.
     */
    public function deleted(ChatSession $chatSession): void
    {
        try {
            // Notify admins about session deletion
            $this->notifyAdmins('chat.session_deleted', $chatSession);

            Log::info('Chat session deleted notification sent', [
                'session_id' => $chatSession->session_id
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send chat session deletion notification', [
                'session_id' => $chatSession->session_id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Check for abandoned chat sessions (called by scheduled job)
     */
    public static function checkAbandonedSessions(): void
    {
        $abandonedSessions = ChatSession::where('status', 'waiting')
            ->where('created_at', '<', now()->subHours(2))
            ->get();

        foreach ($abandonedSessions as $session) {
            $session->update(['status' => 'abandoned']);
            Notifications::send('chat.session_abandoned', $session);
        }

        Log::info('Abandoned chat sessions check completed', [
            'abandoned_count' => $abandonedSessions->count()
        ]);
    }

    /**
     * Check for inactive sessions (called by scheduled job)
     */
    public static function checkInactiveSessions(): void
    {
        $inactiveSessions = ChatSession::where('status', 'active')
            ->where('last_activity_at', '<', now()->subHours(1))
            ->get();

        foreach ($inactiveSessions as $session) {
            Notifications::send('chat.session_inactive_alert', $session);
        }

        // Auto-close sessions inactive for more than 2 hours
        $veryInactiveSessions = ChatSession::where('status', 'active')
            ->where('last_activity_at', '<', now()->subHours(2))
            ->get();

        foreach ($veryInactiveSessions as $session) {
            $session->update([
                'status' => 'closed',
                'ended_at' => now(),
                'close_reason' => 'auto_closed_inactive'
            ]);
        }

        Log::info('Inactive chat sessions check completed', [
            'inactive_count' => $inactiveSessions->count(),
            'auto_closed_count' => $veryInactiveSessions->count()
        ]);
    }

    /**
     * Generate daily chat report
     */
    public static function generateDailyChatReport(): void
    {
        $yesterday = now()->subDay();
        
        $report = [
            'total_sessions' => ChatSession::whereDate('created_at', $yesterday)->count(),
            'completed_sessions' => ChatSession::whereDate('created_at', $yesterday)
                ->where('status', 'closed')->count(),
            'abandoned_sessions' => ChatSession::whereDate('created_at', $yesterday)
                ->where('status', 'abandoned')->count(),
            'average_duration' => ChatSession::whereDate('created_at', $yesterday)
                ->where('status', 'closed')
                ->whereNotNull('started_at')
                ->whereNotNull('ended_at')
                ->get()
                ->avg(function ($session) {
                    return $session->started_at->diffInMinutes($session->ended_at);
                }),
            'total_messages' => \App\Models\ChatMessage::whereDate('created_at', $yesterday)->count(),
            'unique_visitors' => ChatSession::whereDate('created_at', $yesterday)
                ->distinct('visitor_id')
                ->count('visitor_id')
        ];

        Notifications::send('chat.daily_report', $report);

        Log::info('Daily chat report generated', $report);
    }

    /**
     * Send operator performance summary
     */
    public static function sendOperatorPerformanceSummary(): void
    {
        $operators = \App\Models\User::whereHas('chatSessions', function ($query) {
            $query->whereDate('created_at', '>=', now()->subDays(7));
        })->get();

        foreach ($operators as $operator) {
            $performance = [
                'operator_name' => $operator->name,
                'sessions_handled' => $operator->chatSessions()
                    ->whereDate('created_at', '>=', now()->subDays(7))
                    ->count(),
                'average_response_time' => $operator->getAverageResponseTime(),
                'customer_satisfaction' => $operator->getCustomerSatisfactionRating()
            ];

            Notifications::send('chat.operator_performance', $performance, $operator);
        }

        Log::info('Operator performance summaries sent', [
            'operator_count' => $operators->count()
        ]);
    }
}