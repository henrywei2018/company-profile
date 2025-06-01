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
    public function getDashboardSessions(): array
    {
        return [
            'active' => ChatSession::with(['user', 'operator', 'latestMessage'])
                ->where('status', 'active')
                ->orderBy('last_activity_at', 'desc')
                ->get(),
            'waiting' => ChatSession::with(['user', 'latestMessage'])
                ->where('status', 'waiting')
                ->orderBy('priority', 'desc')
                ->orderBy('created_at')
                ->get(),
            'recent_closed' => ChatSession::with(['user', 'operator'])
                ->where('status', 'closed')
                ->orderBy('ended_at', 'desc')
                ->limit(10)
                ->get(),
        ];
    }
    
    public function getSessionMessages(ChatSession $session, int $page = 1, int $limit = 50): array
    {
        $offset = ($page - 1) * $limit;
        
        $messages = $session->messages()
            ->with('sender')
            ->orderBy('created_at', 'asc')
            ->offset($offset)
            ->limit($limit)
            ->get();
        
        $totalMessages = $session->messages()->count();
        $hasMore = $totalMessages > ($offset + $limit);
        
        return [
            'messages' => $messages,
            'total' => $totalMessages,
            'current_page' => $page,
            'has_more' => $hasMore,
            'per_page' => $limit,
        ];
    }

    public function markSessionMessagesAsRead(ChatSession $session, string $readerType = 'operator'): int
    {
        $query = $session->messages()->where('is_read', false);
        
        // Only mark messages not sent by the reader
        if ($readerType === 'operator') {
            $query->where('sender_type', '!=', 'operator');
        } else {
            $query->where('sender_type', '!=', 'visitor');
        }
        
        return $query->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    public function getUnreadMessagesCount(User $operator): int
    {
        return ChatMessage::whereHas('chatSession', function ($query) use ($operator) {
            $query->where('assigned_operator_id', $operator->id)
                ->whereIn('status', ['active', 'waiting']);
        })
        ->where('sender_type', 'visitor')
        ->where('is_read', false)
        ->count();
    }

    public function getSessionsNeedingAttention(): \Illuminate\Database\Eloquent\Collection
    {
        $now = now();
        
        return ChatSession::where(function ($query) use ($now) {
            // Sessions waiting too long (>10 minutes)
            $query->where('status', 'waiting')
                ->where('created_at', '<', $now->subMinutes(10));
        })
        ->orWhere(function ($query) use ($now) {
            // Active sessions with no recent activity (>30 minutes)
            $query->where('status', 'active')
                ->where('last_activity_at', '<', $now->subMinutes(30));
        })
        ->with(['user', 'operator'])
        ->get();
    }

    public function sendAutomatedResponse(ChatSession $session, string $templateType): ?ChatMessage
    {
        $template = \App\Models\ChatTemplate::where('type', $templateType)
            ->where('is_active', true)
            ->first();
        
        if (!$template) {
            return null;
        }
        
        $message = $session->messages()->create([
            'sender_type' => 'bot',
            'sender_id' => null,
            'message' => $template->message,
            'message_type' => 'template',
            'metadata' => ['template_id' => $template->id],
        ]);
        
        // Update session activity
        $session->update(['last_activity_at' => now()]);
        
        // Increment template usage
        $template->incrementUsage();
        
        return $message;
    }

    public function getPerformanceMetrics(array $dateRange = null): array
    {
        $query = ChatSession::query();
        
        if ($dateRange) {
            $query->whereBetween('created_at', $dateRange);
        } else {
            // Default to last 30 days
            $query->where('created_at', '>=', now()->subDays(30));
        }
        
        $sessions = $query->with('messages')->get();
        $closedSessions = $sessions->where('status', 'closed');
        
        // Calculate average response time (first operator response)
        $avgFirstResponseTime = $closedSessions->map(function ($session) {
            $firstVisitorMessage = $session->messages
                ->where('sender_type', 'visitor')
                ->first();
            
            if (!$firstVisitorMessage) return null;
            
            $firstOperatorMessage = $session->messages
                ->where('sender_type', 'operator')
                ->where('created_at', '>', $firstVisitorMessage->created_at)
                ->first();
            
            if (!$firstOperatorMessage) return null;
            
            return $firstVisitorMessage->created_at
                ->diffInMinutes($firstOperatorMessage->created_at);
        })
        ->filter()
        ->avg();
        
        // Calculate session resolution rate
        $totalSessions = $sessions->count();
        $resolvedSessions = $closedSessions->count();
        $resolutionRate = $totalSessions > 0 ? ($resolvedSessions / $totalSessions) * 100 : 0;
        
        // Calculate average session duration
        $avgSessionDuration = $closedSessions->map(function ($session) {
            return $session->getDuration();
        })
        ->filter()
        ->avg();
        
        return [
            'total_sessions' => $totalSessions,
            'resolved_sessions' => $resolvedSessions,
            'resolution_rate' => round($resolutionRate, 1),
            'avg_first_response_time' => round($avgFirstResponseTime ?? 0, 1),
            'avg_session_duration' => round($avgSessionDuration ?? 0, 1),
            'customer_satisfaction' => $this->calculateCustomerSatisfaction($sessions),
        ];
    }

    protected function calculateCustomerSatisfaction($sessions): float
    {
        // This would be based on actual customer feedback
        $satisfactionScore = 4.2; // Base score
        
        // Adjust based on session completion rate
        $completionRate = $sessions->where('status', 'closed')->count() / max($sessions->count(), 1);
        $satisfactionScore += ($completionRate - 0.8) * 2; // Bonus for high completion rate
        
        return max(1, min(5, round($satisfactionScore, 1)));
    }

    public function getQueuePosition(ChatSession $session): int
    {
        if ($session->status !== 'waiting') {
            return 0;
        }
        
        return ChatSession::where('status', 'waiting')
            ->where('created_at', '<', $session->created_at)
            ->count() + 1;
    }

    /**
     * Auto-assign sessions to available operators
     */
    public function autoAssignWaitingSessions(): int
    {
        $waitingSessions = ChatSession::where('status', 'waiting')
            ->orderBy('priority', 'desc')
            ->orderBy('created_at')
            ->get();
        
        $assigned = 0;
        
        foreach ($waitingSessions as $session) {
            if ($this->autoAssignSession($session)) {
                $assigned++;
            }
        }
        
        return $assigned;
    }

    public function sendSessionSummary(ChatSession $session, string $email): bool
    {
        try {
            $summary = $this->generateSessionSummary($session);
            
            \Mail::to($email)->send(new \App\Mail\ChatSessionSummaryMail($session, $summary));
            
            return true;
        } catch (\Exception $e) {
            \Log::error('Failed to send chat session summary: ' . $e->getMessage());
            return false;
        }
    }

    public function generateSessionSummary(ChatSession $session): array
    {
        $messages = $session->messages()->orderBy('created_at')->get();
        
        return [
            'session_id' => $session->session_id,
            'visitor_name' => $session->getVisitorName(),
            'started_at' => $session->started_at,
            'ended_at' => $session->ended_at,
            'duration' => $session->getDuration(),
            'message_count' => $messages->count(),
            'operator_name' => $session->operator?->name,
            'status' => $session->status,
            'summary' => $session->summary,
            'messages' => $messages->map(function ($message) {
                return [
                    'time' => $message->created_at->format('H:i'),
                    'sender' => $message->getSenderName(),
                    'message' => $message->message,
                    'type' => $message->sender_type,
                ];
            }),
        ];
    }

    public function getOperatorWorkload(): array
    {
        $operators = ChatOperator::with('user')
            ->where('is_online', true)
            ->get();
        
        return $operators->map(function ($operator) {
            $activeSessions = ChatSession::where('assigned_operator_id', $operator->user_id)
                ->whereIn('status', ['active', 'waiting'])
                ->count();
            
            return [
                'operator_id' => $operator->user_id,
                'operator_name' => $operator->user->name,
                'active_sessions' => $activeSessions,
                'max_sessions' => $operator->max_concurrent_chats,
                'availability' => $operator->is_available,
                'workload_percentage' => ($activeSessions / $operator->max_concurrent_chats) * 100,
            ];
        })->toArray();
    }

    /**
     * Clean up stale sessions
     */
    public function cleanupStaleSessions(): int
    {
        $cutoffTime = now()->subHours(24);
        
        $staleSessions = ChatSession::whereIn('status', ['active', 'waiting'])
            ->where('last_activity_at', '<', $cutoffTime)
            ->get();
        
        $cleaned = 0;
        
        foreach ($staleSessions as $session) {
            $session->update([
                'status' => 'closed',
                'ended_at' => now(),
                'close_reason' => 'Automatically closed due to inactivity',
            ]);
            
            // Add system message
            $session->messages()->create([
                'sender_type' => 'system',
                'message' => 'Session automatically closed due to prolonged inactivity.',
                'message_type' => 'system',
            ]);
            
            $cleaned++;
        }
        
        return $cleaned;
    }

    /**
     * Get real-time chat metrics for dashboard
     */
    public function getRealTimeMetrics(): array
    {
        $now = now();
        
        return [
            'active_sessions' => ChatSession::where('status', 'active')->count(),
            'waiting_sessions' => ChatSession::where('status', 'waiting')->count(),
            'online_operators' => ChatOperator::where('is_online', true)->count(),
            'available_operators' => ChatOperator::where('is_online', true)
                ->where('is_available', true)->count(),
            'sessions_today' => ChatSession::whereDate('created_at', $now->toDateString())->count(),
            'messages_today' => ChatMessage::whereDate('created_at', $now->toDateString())->count(),
            'avg_wait_time' => $this->getEstimatedWaitTime(),
            'needs_attention' => $this->getSessionsNeedingAttention()->count(),
        ];
    }

    /**
     * Export chat sessions to CSV
     */
    public function exportSessionsToCSV(array $filters = []): string
    {
        $query = ChatSession::with(['user', 'operator', 'messages']);
        
        // Apply filters
        if (isset($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }
        
        if (isset($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }
        
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        
        $sessions = $query->orderBy('created_at', 'desc')->get();
        
        $filename = 'chat_sessions_' . now()->format('Y-m-d_H-i-s') . '.csv';
        $filepath = storage_path('app/exports/' . $filename);
        
        // Ensure directory exists
        if (!file_exists(dirname($filepath))) {
            mkdir(dirname($filepath), 0755, true);
        }
        
        $file = fopen($filepath, 'w');
        
        // Headers
        fputcsv($file, [
            'Session ID',
            'Visitor Name',
            'Visitor Email',
            'Operator',
            'Status',
            'Priority',
            'Started At',
            'Ended At',
            'Duration (minutes)',
            'Messages Count',
            'Summary'
        ]);
        
        // Data
        foreach ($sessions as $session) {
            fputcsv($file, [
                $session->session_id,
                $session->getVisitorName(),
                $session->getVisitorEmail(),
                $session->operator?->name ?? 'Unassigned',
                $session->status,
                $session->priority,
                $session->started_at->format('Y-m-d H:i:s'),
                $session->ended_at?->format('Y-m-d H:i:s') ?? '',
                $session->getDuration() ?? '',
                $session->messages->count(),
                $session->summary ?? ''
            ]);
        }
        
        fclose($file);
        
        return $filepath;
    }

    /**
     * Update operator settings
     */
    public function updateOperatorSettings(User $user, array $settings): bool
    {
        $operator = ChatOperator::firstOrCreate(['user_id' => $user->id]);
        
        $allowedSettings = [
            'max_concurrent_chats',
            'is_available',
            'settings' // JSON field for additional settings
        ];
        
        $updateData = array_intersect_key($settings, array_flip($allowedSettings));
        
        return $operator->update($updateData);
    }

    /**
     * Get chat session analytics
     */
    public function getSessionAnalytics(string $period = '30d'): array
    {
        $startDate = match($period) {
            '7d' => now()->subDays(7),
            '30d' => now()->subDays(30),
            '90d' => now()->subDays(90),
            default => now()->subDays(30)
        };
        
        $sessions = ChatSession::where('created_at', '>=', $startDate)
            ->with('messages')
            ->get();
        
        // Group by date
        $dailyStats = $sessions->groupBy(function ($session) {
            return $session->created_at->format('Y-m-d');
        })->map(function ($daySessions) {
            return [
                'sessions_count' => $daySessions->count(),
                'messages_count' => $daySessions->sum(function ($session) {
                    return $session->messages->count();
                }),
                'avg_duration' => $daySessions->where('status', 'closed')
                    ->avg(function ($session) {
                        return $session->getDuration();
                    }) ?? 0,
            ];
        });
        
        return [
            'period' => $period,
            'total_sessions' => $sessions->count(),
            'daily_stats' => $dailyStats,
            'peak_hours' => $this->getPeakHours($sessions),
            'popular_days' => $this->getPopularDays($sessions),
        ];
    }

    /**
     * Get peak hours from sessions
     */
    protected function getPeakHours($sessions): array
    {
        return $sessions->groupBy(function ($session) {
            return $session->created_at->format('H');
        })->map(function ($hourSessions) {
            return $hourSessions->count();
        })->sortDesc()->take(5)->toArray();
    }

    /**
     * Get popular days from sessions
     */
    protected function getPopularDays($sessions): array
    {
        return $sessions->groupBy(function ($session) {
            return $session->created_at->format('l'); // Day name
        })->map(function ($daySessions) {
            return $daySessions->count();
        })->sortDesc()->toArray();
    }
    
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