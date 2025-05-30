<?php
// File: app/Services/DashboardService.php

namespace App\Services;

use App\Models\User;
use App\Models\Project;
use App\Models\Quotation;
use App\Models\Message;
use App\Models\Testimonial;
use App\Models\Service;
use App\Models\ProjectCategory;
use App\Models\ChatSession;
use App\Models\Certification;
use App\Services\ClientAccessService;
use App\Services\ClientNotificationService;
use App\Facades\Notifications;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class DashboardService
{
    protected ClientAccessService $clientAccessService;
    protected ClientNotificationService $notificationService;

    public function __construct(
        ClientAccessService $clientAccessService,
        ClientNotificationService $notificationService
    ) {
        $this->clientAccessService = $clientAccessService;
        $this->notificationService = $notificationService;
    }

    /**
     * Get comprehensive dashboard data based on user role.
     */
    public function getDashboardData(User $user): array
    {
        $cacheKey = "dashboard_data_{$user->id}_" . $user->getRoleNames()->implode('_');
        
        return Cache::remember($cacheKey, 300, function () use ($user) {
            if ($user->hasAnyRole(['super-admin', 'admin', 'manager'])) {
                return $this->getAdminDashboardData($user);
            } else {
                return $this->getClientDashboardData($user);
            }
        });
    }

    /**
     * Get admin dashboard data with notification counts.
     */
    protected function getAdminDashboardData(User $user): array
    {
        return [
            'statistics' => $this->getAdminStatistics(),
            'recent_activities' => $this->getAdminRecentActivities(),
            'alerts' => $this->getAdminAlerts(),
            'notifications' => $this->getAdminNotifications(),
            'notification_counts' => $this->getAdminNotificationCounts(),
            'charts' => $this->getAdminChartData(),
            'performance' => $this->getSystemPerformance(),
            'quick_actions' => $this->getAdminQuickActions($user),
            'pending_items' => $this->getPendingItems(),
            'recent_notifications' => $this->getRecentNotifications($user),
        ];
    }

    /**
     * Get client dashboard data with notification counts.
     */
    protected function getClientDashboardData(User $user): array
    {
        return [
            'statistics' => $this->getClientStatistics($user),
            'recent_activities' => $this->getClientRecentActivities($user),
            'alerts' => $this->getClientAlerts($user),
            'notifications' => $this->getClientNotifications($user),
            'notification_counts' => $this->getClientNotificationCounts($user),
            'upcoming_deadlines' => $this->getUpcomingDeadlines($user),
            'quick_actions' => $this->getClientQuickActions($user),
            'performance' => $this->getClientPerformance($user),
            'recent_notifications' => $this->getRecentNotifications($user),
        ];
    }

    /**
     * Get admin notification counts for header display.
     */
    public function getAdminNotificationCounts(): array
    {
        return [
            'unread_database_notifications' => auth()->user()->unreadNotifications()->count(),
            'unread_messages' => Message::where('is_read', false)->count(),
            'pending_quotations' => Quotation::where('status', 'pending')->count(),
            'overdue_projects' => Project::where('status', 'in_progress')
                ->where('end_date', '<', now())
                ->whereNotNull('end_date')
                ->count(),
            'waiting_chats' => ChatSession::where('status', 'waiting')->count(),
            'urgent_items' => Message::where('priority', 'urgent')
                ->where('is_read', false)
                ->count(),
            'total_notifications' => $this->getTotalNotificationCount(),
        ];
    }

    /**
     * Get client notification counts for header display.
     */
    public function getClientNotificationCounts(User $user): array
    {
        return [
            'unread_database_notifications' => $user->unreadNotifications()->count(),
            'unread_messages' => $this->clientAccessService->getClientMessages($user)
                ->where('is_read', false)
                ->count(),
            'pending_approvals' => $this->clientAccessService->getClientQuotations($user)
                ->where('status', 'approved')
                ->whereNull('client_approved')
                ->count(),
            'overdue_projects' => $this->clientAccessService->getClientProjects($user)
                ->where('status', 'in_progress')
                ->where('end_date', '<', now())
                ->whereNotNull('end_date')
                ->count(),
            'upcoming_deadlines' => $this->clientAccessService->getClientProjects($user)
                ->where('status', 'in_progress')
                ->where('end_date', '>', now())
                ->where('end_date', '<=', now()->addDays(7))
                ->count(),
            'total_notifications' => $this->getTotalNotificationCount($user),
        ];
    }

    /**
     * Get recent notifications for dropdown display.
     */
    public function getRecentNotifications(User $user, int $limit = 10): array
    {
        try {
            $notifications = $user->notifications()
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();

            return $notifications->map(function ($notification) {
                $data = $notification->data;
                
                return [
                    'id' => $notification->id,
                    'type' => $data['type'] ?? 'notification',
                    'title' => $data['title'] ?? 'Notification',
                    'message' => $data['message'] ?? '',
                    'url' => $data['action_url'] ?? '#',
                    'created_at' => $notification->created_at,
                    'read_at' => $notification->read_at,
                    'is_read' => !is_null($notification->read_at),
                    'formatted_time' => $notification->created_at->diffForHumans(),
                    'icon' => $this->getNotificationIcon($data['type'] ?? 'notification'),
                    'color' => $this->getNotificationColor($data['type'] ?? 'notification'),
                ];
            })->toArray();
        } catch (\Exception $e) {
            Log::error('Failed to get recent notifications: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Mark notification as read and return updated counts.
     */
    public function markNotificationAsRead(User $user, string $notificationId): array
    {
        try {
            $notification = $user->notifications()->where('id', $notificationId)->first();
            
            if ($notification && is_null($notification->read_at)) {
                $notification->markAsRead();
                
                // Log the action
                Log::info('Notification marked as read', [
                    'user_id' => $user->id,
                    'notification_id' => $notificationId,
                    'notification_type' => $notification->data['type'] ?? 'unknown'
                ]);
            }

            // Return updated counts
            return $user->hasAnyRole(['super-admin', 'admin', 'manager']) 
                ? $this->getAdminNotificationCounts()
                : $this->getClientNotificationCounts($user);

        } catch (\Exception $e) {
            Log::error('Failed to mark notification as read: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Mark all notifications as read for user.
     */
    public function markAllNotificationsAsRead(User $user): bool
    {
        try {
            $unreadCount = $user->unreadNotifications()->count();
            $user->unreadNotifications()->update(['read_at' => now()]);
            
            Log::info('All notifications marked as read', [
                'user_id' => $user->id,
                'count' => $unreadCount
            ]);
            
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to mark all notifications as read: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send notification alerts based on system status.
     */
    public function sendSystemAlerts(): void
    {
        try {
            $this->checkOverdueProjects();
            $this->checkPendingQuotations();
            $this->checkUrgentMessages();
            $this->checkWaitingChats();
            $this->checkSystemHealth();
        } catch (\Exception $e) {
            Log::error('Failed to send system alerts: ' . $e->getMessage());
        }
    }

    /**
     * Check for overdue projects and send alerts.
     */
    protected function checkOverdueProjects(): void
    {
        $overdueProjects = Project::where('status', 'in_progress')
            ->where('end_date', '<', now())
            ->whereNotNull('end_date')
            ->with(['client'])
            ->get();

        foreach ($overdueProjects as $project) {
            // Send to client if registered
            if ($project->client) {
                Notifications::send('project.overdue', $project, $project->client);
            }

            // Send to admins
            $admins = User::role(['super-admin', 'admin', 'manager'])->get();
            Notifications::send('project.overdue', $project, $admins);
        }
    }

    /**
     * Check for pending quotations requiring attention.
     */
    protected function checkPendingQuotations(): void
    {
        $pendingQuotations = Quotation::where('status', 'pending')
            ->where('created_at', '<', now()->subHours(24))
            ->get();

        if ($pendingQuotations->count() > 0) {
            $admins = User::role(['super-admin', 'admin', 'manager'])->get();
            
            foreach ($pendingQuotations as $quotation) {
                Notifications::send('quotation.pending_review', $quotation, $admins);
            }
        }
    }

    /**
     * Check for urgent messages.
     */
    protected function checkUrgentMessages(): void
    {
        $urgentMessages = Message::where('priority', 'urgent')
            ->where('is_read', false)
            ->where('created_at', '>', now()->subHours(1))
            ->get();

        if ($urgentMessages->count() > 0) {
            $admins = User::role(['super-admin', 'admin', 'manager'])->get();
            
            foreach ($urgentMessages as $message) {
                Notifications::send('message.urgent', $message, $admins);
            }
        }
    }

    /**
     * Check for waiting chat sessions.
     */
    protected function checkWaitingChats(): void
    {
        $waitingChats = ChatSession::where('status', 'waiting')
            ->where('created_at', '<', now()->subMinutes(5))
            ->get();

        if ($waitingChats->count() > 0) {
            $operators = User::whereHas('chatOperator', function($query) {
                $query->where('is_online', true);
            })->get();

            foreach ($waitingChats as $chat) {
                Notifications::send('chat.session_waiting', $chat, $operators);
            }
        }
    }

    /**
     * Check system health and send alerts.
     */
    protected function checkSystemHealth(): void
    {
        // Check for expired certificates
        $expiredCerts = Certification::where('expiry_date', '<', now())
            ->where('is_active', true)
            ->count();

        if ($expiredCerts > 0) {
            $admins = User::role(['super-admin', 'admin'])->get();
            Notifications::send('system.certificates_expired', [
                'count' => $expiredCerts,
                'message' => "{$expiredCerts} certificate(s) have expired and need renewal."
            ], $admins);
        }

        // Check for certificates expiring soon
        $expiringCerts = Certification::whereBetween('expiry_date', [
            now(),
            now()->addDays(30)
        ])->where('is_active', true)->count();

        if ($expiringCerts > 0) {
            $admins = User::role(['super-admin', 'admin'])->get();
            Notifications::send('system.certificates_expiring', [
                'count' => $expiringCerts,
                'message' => "{$expiringCerts} certificate(s) will expire within 30 days."
            ], $admins);
        }
    }

    /**
     * Get total notification count for a user.
     */
    protected function getTotalNotificationCount(?User $user = null): int
    {
        $user = $user ?? auth()->user();
        
        if (!$user) return 0;

        return $user->unreadNotifications()->count();
    }

    /**
     * Get notification icon based on type.
     */
    protected function getNotificationIcon(string $type): string
    {
        return match($type) {
            'project.created', 'project.updated', 'project.completed' => 'folder',
            'project.overdue', 'project.deadline_approaching' => 'exclamation-triangle',
            'quotation.created', 'quotation.approved', 'quotation.rejected' => 'document-text',
            'message.created', 'message.reply', 'message.urgent' => 'mail',
            'chat.session_started', 'chat.message_received' => 'chat',
            'user.welcome', 'user.email_verified' => 'user',
            'system.maintenance', 'system.alert' => 'cog',
            'testimonial.created' => 'star',
            default => 'bell',
        };
    }

    /**
     * Get notification color based on type.
     */
    protected function getNotificationColor(string $type): string
    {
        return match($type) {
            'project.completed' => 'green',
            'project.overdue', 'message.urgent' => 'red',
            'project.deadline_approaching' => 'yellow',
            'quotation.approved' => 'green',
            'quotation.rejected' => 'red',
            'quotation.created', 'quotation.pending' => 'blue',
            'message.created', 'message.reply' => 'blue',
            'chat.session_started' => 'green',
            'chat.session_waiting' => 'yellow',
            'user.welcome' => 'green',
            'system.alert' => 'red',
            'system.maintenance' => 'yellow',
            default => 'gray',
        };
    }

    /**
     * Get real-time notification updates for AJAX.
     */
    public function getRealTimeNotificationUpdates(User $user): array
    {
        try {
            $counts = $user->hasAnyRole(['super-admin', 'admin', 'manager']) 
                ? $this->getAdminNotificationCounts()
                : $this->getClientNotificationCounts($user);

            $recentNotifications = $this->getRecentNotifications($user, 5);

            return [
                'success' => true,
                'counts' => $counts,
                'recent_notifications' => $recentNotifications,
                'has_new_notifications' => $counts['total_notifications'] > 0,
                'timestamp' => now()->toISOString(),
            ];
        } catch (\Exception $e) {
            Log::error('Failed to get real-time notification updates: ' . $e->getMessage());
            
            return [
                'success' => false,
                'error' => 'Failed to fetch notification updates',
                'counts' => [],
                'recent_notifications' => [],
            ];
        }
    }

    /**
     * Process notification preferences update.
     */
    public function updateNotificationPreferences(User $user, array $preferences): bool
    {
        try {
            // Update user notification preferences
            $user->update([
                'project_update_notifications' => $preferences['project_updates'] ?? true,
                'quotation_update_notifications' => $preferences['quotation_updates'] ?? true,
                'message_reply_notifications' => $preferences['message_replies'] ?? true,
                'deadline_notifications' => $preferences['deadline_reminders'] ?? true,
                'system_notifications' => $preferences['system_alerts'] ?? true,
                'email_notifications' => $preferences['email_notifications'] ?? true,
                'sms_notifications' => $preferences['sms_notifications'] ?? false,
            ]);

            Log::info('Notification preferences updated', [
                'user_id' => $user->id,
                'preferences' => $preferences
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to update notification preferences: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send test notification to verify system.
     */
    public function sendTestNotification(User $user, string $type = 'system.test'): bool
    {
        try {
            $testData = [
                'title' => 'Test Notification',
                'message' => 'This is a test notification to verify the system is working correctly.',
                'timestamp' => now()->toISOString(),
            ];

            Notifications::send($type, $testData, $user);

            Log::info('Test notification sent', [
                'user_id' => $user->id,
                'type' => $type
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send test notification: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get notification statistics for admin dashboard.
     */
    public function getNotificationStatistics(): array
    {
        try {
            $today = now()->startOfDay();
            $thisWeek = now()->startOfWeek();
            $thisMonth = now()->startOfMonth();

            return [
                'total_sent' => DB::table('notifications')->count(),
                'sent_today' => DB::table('notifications')
                    ->where('created_at', '>=', $today)
                    ->count(),
                'sent_this_week' => DB::table('notifications')
                    ->where('created_at', '>=', $thisWeek)
                    ->count(),
                'sent_this_month' => DB::table('notifications')
                    ->where('created_at', '>=', $thisMonth)
                    ->count(),
                'unread_total' => DB::table('notifications')
                    ->whereNull('read_at')
                    ->count(),
                'read_rate' => $this->calculateReadRate(),
                'most_active_types' => $this->getMostActiveNotificationTypes(),
                'delivery_success_rate' => $this->getDeliverySuccessRate(),
            ];
        } catch (\Exception $e) {
            Log::error('Failed to get notification statistics: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Calculate notification read rate.
     */
    protected function calculateReadRate(): float
    {
        $total = DB::table('notifications')->count();
        if ($total === 0) return 0;

        $read = DB::table('notifications')->whereNotNull('read_at')->count();
        return round(($read / $total) * 100, 2);
    }

    /**
     * Get most active notification types.
     */
    protected function getMostActiveNotificationTypes(): array
    {
        return DB::table('notifications')
            ->select('type', DB::raw('count(*) as count'))
            ->groupBy('type')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get()
            ->toArray();
    }

    /**
     * Get delivery success rate (mock implementation).
     */
    protected function getDeliverySuccessRate(): float
    {
        // This would be implemented based on your email/SMS delivery tracking
        return 98.5; // Mock success rate
    }

    // Keep all existing methods from the original DashboardService...
    // (getAdminStatistics, getClientStatistics, getAdminRecentActivities, etc.)
    // These remain unchanged as they don't directly relate to notifications

    /**
     * Get admin statistics.
     */
    protected function getAdminStatistics(): array
    {
        $currentMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth()->startOfMonth();

        return [
            'projects' => [
                'total' => Project::count(),
                'active' => Project::whereIn('status', ['in_progress', 'on_hold'])->count(),
                'completed' => Project::where('status', 'completed')->count(),
                'pending' => Project::where('status', 'planning')->count(),
                'overdue' => Project::where('status', 'in_progress')
                    ->where('end_date', '<', now())
                    ->whereNotNull('end_date')
                    ->count(),
                'this_month' => Project::where('created_at', '>=', $currentMonth)->count(),
                'change_percentage' => $this->calculateChangePercentage(
                    Project::where('created_at', '>=', $currentMonth)->count(),
                    Project::whereBetween('created_at', [$lastMonth, $currentMonth])->count()
                ),
            ],
            'quotations' => [
                'total' => Quotation::count(),
                'pending' => Quotation::where('status', 'pending')->count(),
                'reviewed' => Quotation::where('status', 'reviewed')->count(),
                'approved' => Quotation::where('status', 'approved')->count(),
                'rejected' => Quotation::where('status', 'rejected')->count(),
                'awaiting_approval' => Quotation::where('status', 'approved')
                    ->whereNull('client_approved')
                    ->count(),
                'this_month' => Quotation::where('created_at', '>=', $currentMonth)->count(),
                'conversion_rate' => $this->calculateConversionRate(),
            ],
            'clients' => [
                'total' => User::role('client')->count(),
                'active' => User::role('client')->where('is_active', true)->count(),
                'verified' => User::role('client')->whereNotNull('email_verified_at')->count(),
                'new_this_month' => User::role('client')
                    ->where('created_at', '>=', $currentMonth)
                    ->count(),
            ],
            'messages' => [
                'total' => Message::count(),
                'unread' => Message::where('is_read', false)->count(),
                'unreplied' => Message::where('is_replied', false)->count(),
                'urgent' => Message::where('priority', 'urgent')
                    ->where('is_read', false)
                    ->count(),
                'today' => Message::whereDate('created_at', today())->count(),
            ],
            'chat' => [
                'total_sessions' => ChatSession::count(),
                'active_sessions' => ChatSession::where('status', 'active')->count(),
                'waiting_sessions' => ChatSession::where('status', 'waiting')->count(),
                'avg_response_time' => $this->getChatAverageResponseTime(),
                'today_sessions' => ChatSession::whereDate('created_at', today())->count(),
            ],
            'content' => [
                'services' => Service::count(),
                'active_services' => Service::where('is_active', true)->count(),
                'categories' => ProjectCategory::count(),
                'testimonials' => Testimonial::count(),
                'certifications' => Certification::count(),
            ],
        ];
    }

    // Include other helper methods as needed...
    // (calculateChangePercentage, calculateConversionRate, etc.)
    
    protected function calculateChangePercentage(int $current, int $previous): float
    {
        if ($previous === 0) return $current > 0 ? 100 : 0;
        return round((($current - $previous) / $previous) * 100, 1);
    }

    protected function calculateConversionRate(): float
    {
        $totalQuotations = Quotation::count();
        if ($totalQuotations === 0) return 0;
        
        $approvedQuotations = Quotation::where('status', 'approved')->count();
        return round(($approvedQuotations / $totalQuotations) * 100, 1);
    }

    protected function getChatAverageResponseTime(): float
    {
        $sessions = ChatSession::where('status', 'closed')
            ->whereNotNull('ended_at')
            ->take(50)
            ->get();

        if ($sessions->isEmpty()) return 0;

        $totalMinutes = $sessions->sum(function ($session) {
            return $session->started_at->diffInMinutes($session->ended_at);
        });

        return round($totalMinutes / $sessions->count(), 2);
    }

    // Additional helper methods for other functionality...
}