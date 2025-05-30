<?php

namespace App\Services;

use App\Facades\Notifications;
use App\Models\ChatOperator;
use App\Models\ChatSession;
use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Support\Str;

class ChatService
{
    /**
     * Start a new chat session
     */
    public function startSession(User $user): ChatSession
    {
        return ChatSession::create([
            'user_id' => $user->id,
            'session_id' => Str::uuid(),
            'status' => 'waiting',
            'started_at' => now(),
            'last_activity_at' => now(),
            'priority' => 'normal',
        ]);
    }

    /**
     * End a chat session
     */
    public function endSession(ChatSession $session): void
    {
        $session->update([
            'ended_at' => now(),
            'status' => 'closed'
        ]);
    }

    /**
     * Assign operator to session
     */
    public function assignOperator(ChatSession $session): void
    {
        $operator = ChatOperator::available()->first();

        if ($operator) {
            $session->update([
                'assigned_operator_id' => $operator->user_id,
                'status' => 'active'
            ]);
        }
    }

    /**
     * Send a message in chat session
     */
    public function sendMessage(ChatSession $session, string $message, string $senderType = 'visitor', ?User $sender = null): ChatMessage
    {
        // Create the message
        $chatMessage = $session->messages()->create([
            'sender_type' => $senderType,
            'sender_id' => $sender ? $sender->id : ($session->user_id ?? null),
            'message' => $message,
            'message_type' => 'text',
            'created_at' => now(),
        ]);

        // Update session activity
        $session->update([
            'last_activity_at' => now()
        ]);

        // Send notifications to operators if message is from visitor
        if ($senderType === 'visitor') {
            $this->notifyOperators($session, $message);
        }

        return $chatMessage;
    }

    /**
     * Get a chat session by session ID
     */
    public function getSession(string $sessionId): ?ChatSession
    {
        return ChatSession::where('session_id', $sessionId)->first();
    }

    /**
     * Get messages for a session
     */
    public function getMessages(ChatSession $session, int $limit = null): \Illuminate\Database\Eloquent\Collection
    {
        $query = $session->messages()->with('sender')->orderBy('created_at');
        
        if ($limit) {
            $query->limit($limit);
        }

        return $query->get();
    }

    /**
     * Close a chat session
     */
    public function closeSession(ChatSession $session, string $reason = null): void
    {
        $session->update([
            'status' => 'closed',
            'ended_at' => now(),
            'close_reason' => $reason,
        ]);
    }

    /**
     * Get chat statistics
     */
    public function getStatistics(): array
    {
        return [
            'total_sessions' => ChatSession::count(),
            'active_sessions' => ChatSession::where('status', 'active')->count(),
            'waiting_sessions' => ChatSession::where('status', 'waiting')->count(),
            'closed_sessions_today' => ChatSession::whereDate('ended_at', today())
                ->where('status', 'closed')
                ->count(),
            'total_messages' => ChatMessage::count(),
            'messages_today' => ChatMessage::whereDate('created_at', today())->count(),
            'online_operators' => ChatOperator::where('is_online', true)->count(),
            'available_operators' => ChatOperator::where('is_online', true)
                ->where('is_available', true)
                ->count(),
            'avg_response_time' => $this->calculateAverageResponseTime(),
            'satisfaction_rate' => $this->calculateSatisfactionRate(),
        ];
    }

    /**
     * Calculate average response time
     */
    protected function calculateAverageResponseTime(): float
    {
        // Get closed sessions from last 7 days
        $sessions = ChatSession::where('status', 'closed')
            ->where('created_at', '>=', now()->subDays(7))
            ->get();

        if ($sessions->isEmpty()) {
            return 0;
        }

        $totalDuration = 0;
        $count = 0;

        foreach ($sessions as $session) {
            if ($session->started_at && $session->ended_at) {
                $duration = $session->started_at->diffInMinutes($session->ended_at);
                $totalDuration += $duration;
                $count++;
            }
        }

        return $count > 0 ? round($totalDuration / $count, 1) : 0;
    }

    /**
     * Calculate satisfaction rate
     */
    protected function calculateSatisfactionRate(): float
    {
        // This is a placeholder - you can implement actual satisfaction tracking
        // For now, return a mock value
        return 85.5;
    }

    /**
     * Notify operators about new messages
     */
    protected function notifyOperators(ChatSession $session, string $message): void
    {
        // Get available operators
        $operators = ChatOperator::where('is_online', true)
            ->where('is_available', true)
            ->with('user')
            ->get();

        // Send notification to each available operator
        foreach ($operators as $operator) {
            if ($operator->user) {
                Notifications::send('chat.message_received', $session, $operator->user, ['database']);
            }
        }

        // Also send email notification if configured
        $notificationEmail = settings('chat_notification_email');
        if ($notificationEmail && settings('chat_email_notifications', false)) {
            $tempNotifiable = new \App\Services\TempNotifiable(
                $notificationEmail,
                'Chat Support Team'
            );
            Notifications::send('chat.message_received', $session, $tempNotifiable, ['mail']);
        }
    }

    /**
     * Update session priority
     */
    public function updatePriority(ChatSession $session, string $priority): void
    {
        $validPriorities = ['low', 'normal', 'high', 'urgent'];
        
        if (in_array($priority, $validPriorities)) {
            $session->update(['priority' => $priority]);
        }
    }

    /**
     * Add system message to chat
     */
    public function addSystemMessage(ChatSession $session, string $message): ChatMessage
    {
        return $session->messages()->create([
            'sender_type' => 'system',
            'sender_id' => null,
            'message' => $message,
            'message_type' => 'system',
            'created_at' => now(),
        ]);
    }

    /**
     * Check if operators are available
     */
    public function areOperatorsAvailable(): bool
    {
        return ChatOperator::where('is_online', true)
            ->where('is_available', true)
            ->exists();
    }

    /**
     * Get waiting time for new sessions
     */
    public function getEstimatedWaitTime(): int
    {
        $waitingSessions = ChatSession::where('status', 'waiting')->count();
        $availableOperators = ChatOperator::where('is_online', true)
            ->where('is_available', true)
            ->count();

        if ($availableOperators === 0) {
            return 0; // No operators available
        }

        // Estimate 5 minutes per session ahead in queue
        return ($waitingSessions / $availableOperators) * 5;
    }

    /**
     * Archive old closed sessions
     */
    public function archiveOldSessions(int $daysOld = 30): int
    {
        $cutoffDate = now()->subDays($daysOld);
        
        return ChatSession::where('status', 'closed')
            ->where('ended_at', '<', $cutoffDate)
            ->update(['status' => 'archived']);
    }

    /**
     * Get session duration in minutes
     */
    public function getSessionDuration(ChatSession $session): ?int
    {
        if (!$session->started_at || !$session->ended_at) {
            return null;
        }

        return $session->started_at->diffInMinutes($session->ended_at);
    }

    /**
     * Mark operator as online
     */
    public function setOperatorOnline(User $user): ChatOperator
    {
        return ChatOperator::updateOrCreate(
            ['user_id' => $user->id],
            [
                'is_online' => true,
                'is_available' => true,
                'last_seen_at' => now(),
            ]
        );
    }

    /**
     * Mark operator as offline
     */
    public function setOperatorOffline(User $user): void
    {
        ChatOperator::where('user_id', $user->id)
            ->update([
                'is_online' => false,
                'last_seen_at' => now(),
            ]);
    }

    /**
     * Update operator availability
     */
    public function setOperatorAvailability(User $user, bool $available): void
    {
        ChatOperator::where('user_id', $user->id)
            ->update([
                'is_available' => $available,
                'last_seen_at' => now(),
            ]);
    }

    /**
     * Get operator by user
     */
    public function getOperator(User $user): ?ChatOperator
    {
        return ChatOperator::where('user_id', $user->id)->first();
    }

    /**
     * Auto-assign session to available operator
     */
    public function autoAssignSession(ChatSession $session): bool
    {
        $operator = ChatOperator::where('is_online', true)
            ->where('is_available', true)
            ->whereDoesntHave('activeSessions')
            ->first();

        if ($operator) {
            $session->update([
                'assigned_operator_id' => $operator->user_id,
                'status' => 'active'
            ]);

            // Add system message
            $this->addSystemMessage(
                $session,
                "Chat assigned to {$operator->user->name}"
            );

            return true;
        }

        return false;
    }

    /**
     * Transfer session to another operator
     */
    public function transferSession(ChatSession $session, User $newOperator): void
    {
        $oldOperatorName = $session->operator ? $session->operator->name : 'System';
        
        $session->update([
            'assigned_operator_id' => $newOperator->id
        ]);

        // Add system message about transfer
        $this->addSystemMessage(
            $session,
            "Chat transferred from {$oldOperatorName} to {$newOperator->name}"
        );

        // Notify the new operator
        Notifications::send('chat.session_transferred', $session, $newOperator);
    }

    /**
     * Get active sessions for operator
     */
    public function getOperatorActiveSessions(User $operator): \Illuminate\Database\Eloquent\Collection
    {
        return ChatSession::where('assigned_operator_id', $operator->id)
            ->whereIn('status', ['active', 'waiting'])
            ->with(['user', 'messages' => function($query) {
                $query->orderBy('created_at', 'desc')->limit(1);
            }])
            ->orderBy('last_activity_at', 'desc')
            ->get();
    }

    /**
     * Send auto-reply message
     */
    public function sendAutoReply(ChatSession $session, string $templateType = 'greeting'): ?ChatMessage
    {
        $template = \App\Models\ChatTemplate::where('type', $templateType)
            ->where('is_active', true)
            ->first();

        if (!$template) {
            return null;
        }

        return $this->sendMessage($session, $template->message, 'bot');
    }

    /**
     * Handle session timeout
     */
    public function handleSessionTimeout(ChatSession $session): void
    {
        if ($session->status === 'active' && $session->last_activity_at) {
            $inactiveMinutes = now()->diffInMinutes($session->last_activity_at);
            
            if ($inactiveMinutes >= 30) {
                $this->addSystemMessage(
                    $session,
                    'Session closed due to inactivity'
                );
                
                $this->closeSession($session, 'Timeout due to inactivity');
                
                Notifications::send('chat.session_timeout', $session);
            }
        }
    }
}