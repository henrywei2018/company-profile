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
use App\Facades\Notifications;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class DashboardService
{
    protected ClientAccessService $clientAccessService;

    public function __construct(ClientAccessService $clientAccessService)
    {
        $this->clientAccessService = $clientAccessService;
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

    /**
     * Get client statistics.
     */
    protected function getClientStatistics(User $user): array
    {
        return $this->clientAccessService->getClientStatistics($user);
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
     * Get admin recent activities.
     */
    protected function getAdminRecentActivities(): array
    {
        return [
            'recent_projects' => Project::with(['client', 'category'])
                ->latest()
                ->limit(5)
                ->get()
                ->map(function ($project) {
                    return [
                        'type' => 'project',
                        'action' => 'created',
                        'title' => $project->title,
                        'user' => $project->client->name ?? 'Unknown',
                        'date' => $project->created_at,
                        'url' => route('admin.projects.show', $project),
                    ];
                }),
            'recent_quotations' => Quotation::with(['client', 'service'])
                ->latest()
                ->limit(5)
                ->get()
                ->map(function ($quotation) {
                    return [
                        'type' => 'quotation',
                        'action' => 'submitted',
                        'title' => $quotation->project_type,
                        'user' => $quotation->client->name ?? $quotation->name,
                        'date' => $quotation->created_at,
                        'url' => route('admin.quotations.show', $quotation),
                    ];
                }),
            'recent_messages' => Message::with(['user'])
                ->latest()
                ->limit(5)
                ->get()
                ->map(function ($message) {
                    return [
                        'type' => 'message',
                        'action' => 'sent',
                        'title' => $message->subject,
                        'user' => $message->user->name ?? $message->name,
                        'date' => $message->created_at,
                        'url' => route('admin.messages.show', $message),
                    ];
                }),
        ];
    }

    /**
     * Get client recent activities.
     */
    protected function getClientRecentActivities(User $user): array
{
    try {
        $activities = [];

        // Recent project updates dengan validasi yang aman
        $recentProjects = $this->clientAccessService->getClientProjects($user)
            ->latest()
            ->limit(5)
            ->get()
            ->map(function ($project) {
                return [
                    'type' => 'project',
                    'action' => 'updated',
                    'title' => $project->title,
                    'description' => "Status: " . $this->formatStatus($project->status),
                    'status' => $project->status,
                    'date' => $project->updated_at,
                    'url' => route('client.projects.show', $project),
                    'icon' => 'folder',
                    'color' => $this->getActivityColor('project', $project->status),
                ];
            })->toArray();

        // Recent quotation updates dengan validasi yang aman
        $recentQuotations = $this->clientAccessService->getClientQuotations($user)
            ->latest()
            ->limit(5)
            ->get()
            ->map(function ($quotation) {
                return [
                    'type' => 'quotation',
                    'action' => 'status_updated',
                    'title' => $quotation->project_type,
                    'description' => "Status: " . $this->formatStatus($quotation->status),
                    'status' => $quotation->status,
                    'date' => $quotation->updated_at,
                    'url' => route('client.quotations.show', $quotation),
                    'icon' => 'document-text',
                    'color' => $this->getActivityColor('quotation', $quotation->status),
                ];
            })->toArray();

        // Recent messages dengan validasi yang aman
        $recentMessages = $this->clientAccessService->getClientMessages($user)
            ->latest()
            ->limit(5)
            ->get()
            ->map(function ($message) {
                $messageStatus = $this->getMessageStatus($message);
                return [
                    'type' => 'message',
                    'action' => $message->is_replied ? 'replied' : 'sent',
                    'title' => $message->subject,
                    'description' => $messageStatus['description'],
                    'status' => $messageStatus['status'],
                    'date' => $message->updated_at,
                    'url' => route('client.messages.show', $message),
                    'icon' => 'mail',
                    'color' => $this->getActivityColor('message', $messageStatus['status']),
                ];
            })->toArray();

        return [
            'recent_projects' => $recentProjects,
            'recent_quotations' => $recentQuotations,
            'recent_messages' => $recentMessages,
        ];

    } catch (\Exception $e) {
        Log::error('Error getting client recent activities', [
            'user_id' => $user->id,
            'error' => $e->getMessage()
        ]);
        
        return [
            'recent_projects' => [],
            'recent_quotations' => [],
            'recent_messages' => [],
        ];
    }
}

protected function getActivityColor(string $type, string $status): string
{
    $colorMap = [
        'project' => [
            'completed' => 'green',
            'in_progress' => 'blue',
            'on_hold' => 'yellow',
            'cancelled' => 'red',
            'planning' => 'purple',
            'default' => 'gray'
        ],
        'quotation' => [
            'approved' => 'green',
            'pending' => 'yellow',
            'reviewed' => 'blue',
            'rejected' => 'red',
            'default' => 'gray'
        ],
        'message' => [
            'urgent' => 'red',
            'replied' => 'green',
            'read' => 'blue',
            'unread' => 'yellow',
            'default' => 'gray'
        ]
    ];

    return $colorMap[$type][$status] ?? $colorMap[$type]['default'] ?? 'gray';
}

/**
 * Format status untuk display
 */
protected function formatStatus(string $status): string
{
    return match ($status) {
        'in_progress' => 'In Progress',
        'on_hold' => 'On Hold',
        'pending' => 'Pending',
        'reviewed' => 'Under Review',
        'approved' => 'Approved',
        'rejected' => 'Rejected',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled',
        'planning' => 'Planning',
        default => ucfirst(str_replace('_', ' ', $status))
    };
}

/**
 * Get message status safely
 */
protected function getMessageStatus($message): array
{
    if ($message->priority === 'urgent') {
        return [
            'status' => 'urgent',
            'description' => 'Urgent message'
        ];
    }

    if ($message->is_replied) {
        return [
            'status' => 'replied',
            'description' => 'Replied'
        ];
    }

    if ($message->is_read) {
        return [
            'status' => 'read',
            'description' => 'Read'
        ];
    }

    return [
        'status' => 'unread',
        'description' => 'Unread'
    ];
}

    /**
     * Get admin alerts.
     */
    protected function getAdminAlerts(): array
    {
        return [
            'overdue_projects' => Project::where('status', 'in_progress')
                ->where('end_date', '<', now())
                ->whereNotNull('end_date')
                ->count(),
            'pending_quotations' => Quotation::where('status', 'pending')
                ->where('created_at', '<', now()->subHours(24))
                ->count(),
            'urgent_messages' => Message::where('priority', 'urgent')
                ->where('is_read', false)
                ->count(),
            'waiting_chats' => ChatSession::where('status', 'waiting')->count(),
            'expiring_certificates' => Certification::where('expiry_date', '<=', now()->addDays(30))
                ->where('is_active', true)
                ->count(),
        ];
    }

    /**
     * Get client alerts.
     */
    protected function getClientAlerts(User $user): array
    {
        return [
            'overdue_projects' => $this->clientAccessService->getClientProjects($user)
                ->where('status', 'in_progress')
                ->where('end_date', '<', now())
                ->whereNotNull('end_date')
                ->count(),
            'pending_approvals' => $this->clientAccessService->getClientQuotations($user)
                ->where('status', 'approved')
                ->whereNull('client_approved')
                ->count(),
            'unread_messages' => $this->clientAccessService->getClientMessages($user)
                ->where('is_read', false)
                ->count(),
            'upcoming_deadlines' => $this->clientAccessService->getClientProjects($user)
                ->where('status', 'in_progress')
                ->where('end_date', '>', now())
                ->where('end_date', '<=', now()->addDays(7))
                ->count(),
        ];
    }

    /**
     * Get admin notifications.
     */
    protected function getAdminNotifications(): array
    {
        return [
            'system_health' => 'good',
            'backup_status' => 'completed',
            'security_alerts' => 0,
            'maintenance_mode' => false,
        ];
    }

    /**
     * Get client notifications.
     */
    protected function getClientNotifications(User $user): array
    {
        return [
            'profile_complete' => $this->isProfileComplete($user),
            'email_verified' => !is_null($user->email_verified_at),
            'has_active_projects' => $this->clientAccessService->getClientProjects($user)
                ->whereIn('status', ['in_progress', 'on_hold'])
                ->count() > 0,
        ];
    }

    /**
     * Get admin chart data.
     */
    protected function getAdminChartData(): array
    {
        return [
            'project_trends' => $this->getProjectTrends(),
            'quotation_conversion' => $this->getQuotationConversionData(),
            'revenue_trends' => $this->getRevenueTrends(),
            'client_growth' => $this->getClientGrowthData(),
        ];
    }

    /**
     * Get system performance data.
     */
    protected function getSystemPerformance(): array
    {
        return [
            'response_time' => 'good',
            'memory_usage' => 45,
            'disk_usage' => 62,
            'cpu_usage' => 23,
            'uptime' => '99.9%',
            'last_backup' => now()->subHours(6),
        ];
    }

    /**
     * Get admin quick actions.
     */
    protected function getAdminQuickActions(User $user): array
    {
        return [
            ['label' => 'Add Project', 'url' => route('admin.projects.create'), 'icon' => 'plus'],
            ['label' => 'View Quotations', 'url' => route('admin.quotations.index'), 'icon' => 'document'],
            ['label' => 'Messages', 'url' => route('admin.messages.index'), 'icon' => 'mail'],
            ['label' => 'Reports', 'url' => route('admin.reports.index'), 'icon' => 'chart'],
        ];
    }

    /**
     * Get client quick actions.
     */
    protected function getClientQuickActions(User $user): array
    {
        return [
            ['label' => 'Request Quotation', 'url' => route('quotation.create'), 'icon' => 'plus'],
            ['label' => 'My Projects', 'url' => route('client.projects.index'), 'icon' => 'folder'],
            ['label' => 'Messages', 'url' => route('client.messages.index'), 'icon' => 'mail'],
            ['label' => 'Profile', 'url' => route('client.profile.edit'), 'icon' => 'user'],
        ];
    }

    /**
     * Get client performance.
     */
    protected function getClientPerformance(User $user): array
    {
        $projects = $this->clientAccessService->getClientProjects($user)->get();
        
        return [
            'completion_rate' => $projects->count() > 0 ? 
                round(($projects->where('status', 'completed')->count() / $projects->count()) * 100, 1) : 0,
            'on_time_delivery' => $this->calculateOnTimeDelivery($projects),
            'satisfaction_score' => $this->getClientSatisfactionScore($user),
            'total_projects' => $projects->count(),
        ];
    }

    /**
     * Get pending items for admin.
     */
    protected function getPendingItems(): array
    {
        return [
            'pending_quotations' => Quotation::where('status', 'pending')->count(),
            'overdue_projects' => Project::where('status', 'in_progress')
                ->where('end_date', '<', now())
                ->whereNotNull('end_date')
                ->count(),
            'unread_messages' => Message::where('is_read', false)->count(),
            'waiting_chats' => ChatSession::where('status', 'waiting')->count(),
        ];
    }

    /**
     * Get upcoming deadlines for client.
     */
    protected function getUpcomingDeadlines(User $user): array
    {
        return $this->clientAccessService->getClientProjects($user)
            ->where('status', 'in_progress')
            ->where('end_date', '>', now())
            ->where('end_date', '<=', now()->addDays(30))
            ->orderBy('end_date')
            ->get()
            ->map(function ($project) {
                return [
                    'title' => $project->title,
                    'deadline' => $project->end_date,
                    'days_remaining' => now()->diffInDays($project->end_date, false),
                    'url' => route('client.projects.show', $project),
                ];
            })
            ->toArray();
    }

    /**
     * Generate comprehensive report data.
     */
    public function generateReport(User $user, array $filters = []): array
    {
        $period = $this->getReportPeriod($filters);
        
        if ($user->hasAnyRole(['super-admin', 'admin', 'manager'])) {
            return $this->generateAdminReport($period, $filters);
        } else {
            return $this->generateClientReport($user, $period, $filters);
        }
    }

    /**
     * Generate admin report.
     */
    protected function generateAdminReport(array $period, array $filters): array
    {
        return [
            'period' => $period,
            'overview' => $this->getAdminStatistics(),
            'projects' => $this->getProjectReportData($period),
            'quotations' => $this->getQuotationReportData($period),
            'clients' => $this->getClientReportData($period),
            'revenue' => $this->getRevenueReportData($period),
            'performance' => $this->getPerformanceMetrics($period),
        ];
    }

    /**
     * Generate client report.
     */
    protected function generateClientReport(User $user, array $period, array $filters): array
    {
        return [
            'period' => $period,
            'overview' => $this->getClientStatistics($user),
            'projects' => $this->getClientProjectReport($user, $period),
            'quotations' => $this->getClientQuotationReport($user, $period),
            'performance' => $this->getClientPerformance($user),
        ];
    }

    // Additional helper methods...

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

    // Private helper methods...

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

    protected function getTotalNotificationCount(?User $user = null): int
    {
        $user = $user ?? auth()->user();
        
        if (!$user) return 0;

        return $user->unreadNotifications()->count();
    }

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

    protected function isProfileComplete(User $user): bool
    {
        $requiredFields = ['name', 'email', 'phone', 'company', 'address'];
        
        foreach ($requiredFields as $field) {
            if (empty($user->$field)) {
                return false;
            }
        }
        
        return true;
    }

    protected function calculateOnTimeDelivery($projects): float
    {
        $completedProjects = $projects->where('status', 'completed')
            ->filter(fn($p) => $p->end_date && $p->actual_completion_date);
        
        if ($completedProjects->isEmpty()) return 0;
        
        $onTime = $completedProjects->filter(fn($p) => $p->actual_completion_date <= $p->end_date)->count();
        
        return round(($onTime / $completedProjects->count()) * 100, 1);
    }

    protected function getClientSatisfactionScore(User $user): float
    {
        // Mock implementation - would calculate based on testimonials/ratings
        return 85.5;
    }

    protected function getReportPeriod(array $filters): array
    {
        $period = $filters['period'] ?? 'last_30_days';
        
        return match($period) {
            'last_7_days' => ['start' => now()->subDays(7), 'end' => now()],
            'last_30_days' => ['start' => now()->subDays(30), 'end' => now()],
            'last_3_months' => ['start' => now()->subMonths(3), 'end' => now()],
            'last_year' => ['start' => now()->subYear(), 'end' => now()],
            default => ['start' => now()->subDays(30), 'end' => now()],
        };
    }

    protected function getProjectTrends(): array
    {
        // Mock implementation
        return [];
    }

    protected function getQuotationConversionData(): array
    {
        // Mock implementation
        return [];
    }

    protected function getRevenueTrends(): array
    {
        // Mock implementation
        return [];
    }

    protected function getClientGrowthData(): array
    {
        // Mock implementation
        return [];
    }

    protected function getProjectReportData(array $period): array
    {
        // Mock implementation
        return [];
    }

    protected function getQuotationReportData(array $period): array
    {
        // Mock implementation
        return [];
    }

    protected function getClientReportData(array $period): array
    {
        // Mock implementation
        return [];
    }

    protected function getRevenueReportData(array $period): array
    {
        // Mock implementation
        return [];
    }

    protected function getPerformanceMetrics(array $period): array
    {
        // Mock implementation
        return [];
    }

    protected function getClientProjectReport(User $user, array $period): array
    {
        // Mock implementation
        return [];
    }

    protected function getClientQuotationReport(User $user, array $period): array
    {
        // Mock implementation
        return [];
    }
}