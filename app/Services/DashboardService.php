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
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
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
     * Get admin dashboard data.
     */
    protected function getAdminDashboardData(User $user): array
    {
        return [
            'statistics' => $this->getAdminStatistics(),
            'recent_activities' => $this->getAdminRecentActivities(),
            'alerts' => $this->getAdminAlerts(),
            'notifications' => $this->getAdminNotifications(),
            'charts' => $this->getAdminChartData(),
            'performance' => $this->getSystemPerformance(),
            'quick_actions' => $this->getAdminQuickActions($user),
            'pending_items' => $this->getPendingItems(),
        ];
    }

    /**
     * Get client dashboard data.
     */
    protected function getClientDashboardData(User $user): array
    {
        return [
            'statistics' => $this->getClientStatistics($user),
            'recent_activities' => $this->getClientRecentActivities($user),
            'alerts' => $this->getClientAlerts($user),
            'notifications' => $this->getClientNotifications($user),
            'upcoming_deadlines' => $this->getUpcomingDeadlines($user),
            'quick_actions' => $this->getClientQuickActions($user),
            'performance' => $this->getClientPerformance($user),
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
     * Get client statistics using ClientAccessService.
     */
    protected function getClientStatistics(User $user): array
    {
        $projectsQuery = $this->clientAccessService->getClientProjects($user);
        $quotationsQuery = $this->clientAccessService->getClientQuotations($user);
        $messagesQuery = $this->clientAccessService->getClientMessages($user);

        return [
            'projects' => [
                'total' => (clone $projectsQuery)->count(),
                'active' => (clone $projectsQuery)->whereIn('status', ['in_progress', 'on_hold'])->count(),
                'completed' => (clone $projectsQuery)->where('status', 'completed')->count(),
                'planning' => (clone $projectsQuery)->where('status', 'planning')->count(),
                'overdue' => (clone $projectsQuery)
                    ->where('status', 'in_progress')
                    ->where('end_date', '<', now())
                    ->whereNotNull('end_date')
                    ->count(),
                'this_year' => (clone $projectsQuery)->whereYear('created_at', now()->year)->count(),
                'completion_rate' => $this->calculateCompletionRate($projectsQuery),
            ],
            'quotations' => [
                'total' => (clone $quotationsQuery)->count(),
                'pending' => (clone $quotationsQuery)->where('status', 'pending')->count(),
                'reviewed' => (clone $quotationsQuery)->where('status', 'reviewed')->count(),
                'approved' => (clone $quotationsQuery)->where('status', 'approved')->count(),
                'awaiting_approval' => (clone $quotationsQuery)
                    ->where('status', 'approved')
                    ->whereNull('client_approved')
                    ->count(),
                'this_month' => (clone $quotationsQuery)->whereMonth('created_at', now()->month)->count(),
            ],
            'messages' => [
                'total' => (clone $messagesQuery)->count(),
                'unread' => (clone $messagesQuery)->where('is_read', false)->count(),
                'replied' => (clone $messagesQuery)->where('is_replied', true)->count(),
                'this_week' => (clone $messagesQuery)->whereBetween('created_at', [
                    Carbon::now()->startOfWeek(),
                    Carbon::now()->endOfWeek()
                ])->count(),
            ],
            'performance' => [
                'project_value' => $this->calculateProjectValue($projectsQuery),
                'on_time_delivery' => $this->calculateOnTimeDelivery($projectsQuery),
                'satisfaction_rate' => $this->calculateSatisfactionRate($user),
            ],
        ];
    }

    /**
     * Get admin recent activities.
     */
    protected function getAdminRecentActivities(): array
    {
        $activities = collect();

        // Recent projects
        $recentProjects = Project::with(['client', 'category'])
            ->orderBy('updated_at', 'desc')
            ->limit(5)
            ->get();

        foreach ($recentProjects as $project) {
            $activities->push([
                'type' => 'project',
                'title' => 'Project: ' . $project->title,
                'description' => 'Client: ' . ($project->client->name ?? 'N/A') . ' | Status: ' . ucfirst($project->status),
                'date' => $project->updated_at,
                'url' => route('admin.projects.show', $project),
                'icon' => 'folder',
                'color' => $this->getProjectStatusColor($project->status),
                'priority' => $this->getActivityPriority($project->status),
            ]);
        }

        // Recent quotations
        $recentQuotations = Quotation::with('service')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        foreach ($recentQuotations as $quotation) {
            $activities->push([
                'type' => 'quotation',
                'title' => 'New Quotation: ' . ($quotation->project_type ?? 'Quote #' . $quotation->id),
                'description' => 'From: ' . $quotation->name . ' | Status: ' . ucfirst($quotation->status),
                'date' => $quotation->created_at,
                'url' => route('admin.quotations.show', $quotation),
                'icon' => 'document-text',
                'color' => $this->getQuotationStatusColor($quotation->status),
                'priority' => $this->getQuotationPriority($quotation),
            ]);
        }

        // Recent messages
        $recentMessages = Message::orderBy('created_at', 'desc')->limit(5)->get();

        foreach ($recentMessages as $message) {
            $activities->push([
                'type' => 'message',
                'title' => 'Message: ' . $message->subject,
                'description' => 'From: ' . $message->name . ' | ' . ($message->is_read ? 'Read' : 'Unread'),
                'date' => $message->created_at,
                'url' => route('admin.messages.show', $message),
                'icon' => 'mail',
                'color' => $message->is_read ? 'gray' : 'blue',
                'priority' => $message->is_read ? 'low' : 'medium',
            ]);
        }

        // Recent chat sessions
        $recentChats = ChatSession::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();

        foreach ($recentChats as $chat) {
            $activities->push([
                'type' => 'chat',
                'title' => 'Chat Session: ' . $chat->getVisitorName(),
                'description' => 'Status: ' . ucfirst($chat->status) . ' | Messages: ' . $chat->messages()->count(),
                'date' => $chat->created_at,
                'url' => route('admin.chat.show', $chat),
                'icon' => 'chat',
                'color' => $chat->status === 'active' ? 'green' : ($chat->status === 'waiting' ? 'yellow' : 'gray'),
                'priority' => $chat->status === 'waiting' ? 'high' : 'low',
            ]);
        }

        return $activities->sortByDesc('date')->take(15)->values()->toArray();
    }

    /**
     * Get client recent activities.
     */
    protected function getClientRecentActivities(User $user): array
    {
        $activities = collect();

        // Recent project updates
        $recentProjects = $this->clientAccessService->getClientProjects($user)
            ->with(['category', 'images'])
            ->orderBy('updated_at', 'desc')
            ->limit(5)
            ->get();

        foreach ($recentProjects as $project) {
            $activities->push([
                'type' => 'project',
                'title' => 'Project: ' . $project->title,
                'description' => 'Status: ' . ucfirst(str_replace('_', ' ', $project->status)),
                'date' => $project->updated_at,
                'url' => route('client.projects.show', $project),
                'icon' => 'folder',
                'color' => $this->getProjectStatusColor($project->status),
            ]);
        }

        // Recent quotation updates
        $recentQuotations = $this->clientAccessService->getClientQuotations($user)
            ->with('service')
            ->orderBy('updated_at', 'desc')
            ->limit(5)
            ->get();

        foreach ($recentQuotations as $quotation) {
            $activities->push([
                'type' => 'quotation',
                'title' => 'Quotation: ' . ($quotation->project_type ?? 'Quote #' . $quotation->id),
                'description' => 'Status: ' . ucfirst($quotation->status),
                'date' => $quotation->updated_at,
                'url' => route('client.quotations.show', $quotation),
                'icon' => 'document-text',
                'color' => $this->getQuotationStatusColor($quotation->status),
            ]);
        }

        // Recent messages
        $recentMessages = $this->clientAccessService->getClientMessages($user)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        foreach ($recentMessages as $message) {
            $activities->push([
                'type' => 'message',
                'title' => 'Message: ' . $message->subject,
                'description' => $message->is_read ? 'Read' : 'Unread',
                'date' => $message->created_at,
                'url' => route('client.messages.show', $message),
                'icon' => 'mail',
                'color' => $message->is_read ? 'gray' : 'blue',
            ]);
        }

        return $activities->sortByDesc('date')->take(10)->values()->toArray();
    }

    /**
     * Get admin alerts.
     */
    protected function getAdminAlerts(): array
    {
        $alerts = [];

        // Overdue projects
        $overdueProjects = Project::where('status', 'in_progress')
            ->where('end_date', '<', now())
            ->whereNotNull('end_date')
            ->count();

        if ($overdueProjects > 0) {
            $alerts[] = [
                'type' => 'error',
                'title' => 'Overdue Projects',
                'message' => "{$overdueProjects} project(s) are overdue and need attention.",
                'action' => [
                    'text' => 'View Projects',
                    'url' => route('admin.projects.index', ['status' => 'overdue']),
                ],
                'priority' => 'high',
                'count' => $overdueProjects,
            ];
        }

        // Pending quotations
        $pendingQuotations = Quotation::where('status', 'pending')->count();
        if ($pendingQuotations > 5) {
            $alerts[] = [
                'type' => 'warning',
                'title' => 'Pending Quotations',
                'message' => "{$pendingQuotations} quotations are pending review.",
                'action' => [
                    'text' => 'Review Quotations',
                    'url' => route('admin.quotations.index', ['status' => 'pending']),
                ],
                'priority' => 'medium',
                'count' => $pendingQuotations,
            ];
        }

        // Unread urgent messages
        $urgentMessages = Message::where('is_read', false)
            ->where('priority', 'urgent')
            ->count();

        if ($urgentMessages > 0) {
            $alerts[] = [
                'type' => 'error',
                'title' => 'Urgent Messages',
                'message' => "{$urgentMessages} urgent message(s) require immediate attention.",
                'action' => [
                    'text' => 'View Messages',
                    'url' => route('admin.messages.index', ['priority' => 'urgent', 'read' => 'unread']),
                ],
                'priority' => 'critical',
                'count' => $urgentMessages,
            ];
        }

        // System performance alerts
        $systemAlerts = $this->getSystemAlerts();
        $alerts = array_merge($alerts, $systemAlerts);

        return $alerts;
    }

    /**
     * Get client alerts.
     */
    protected function getClientAlerts(User $user): array
    {
        $alerts = [];

        // Overdue projects
        $overdueProjects = $this->clientAccessService->getClientProjects($user)
            ->where('status', 'in_progress')
            ->where('end_date', '<', now())
            ->whereNotNull('end_date')
            ->count();

        if ($overdueProjects > 0) {
            $alerts[] = [
                'type' => 'warning',
                'title' => 'Overdue Projects',
                'message' => "You have {$overdueProjects} project(s) that are overdue.",
                'action' => [
                    'text' => 'View Projects',
                    'url' => route('client.projects.index', ['status' => 'overdue']),
                ],
                'priority' => 'high',
            ];
        }

        // Pending approvals
        $pendingApprovals = $this->clientAccessService->getClientQuotations($user)
            ->where('status', 'approved')
            ->whereNull('client_approved')
            ->count();

        if ($pendingApprovals > 0) {
            $alerts[] = [
                'type' => 'info',
                'title' => 'Pending Approvals',
                'message' => "You have {$pendingApprovals} quotation(s) awaiting your approval.",
                'action' => [
                    'text' => 'Review Quotations',
                    'url' => route('client.quotations.index', ['status' => 'approved']),
                ],
                'priority' => 'medium',
            ];
        }

        // Incomplete profile
        if (!$this->hasCompleteProfile($user)) {
            $alerts[] = [
                'type' => 'info',
                'title' => 'Complete Your Profile',
                'message' => 'Please complete your profile for better service.',
                'action' => [
                    'text' => 'Update Profile',
                    'url' => route('client.profile.edit'),
                ],
                'priority' => 'low',
            ];
        }

        return $alerts;
    }

    /**
     * Get admin chart data.
     */
    protected function getAdminChartData(): array
    {
        return [
            'projects_by_status' => $this->getProjectsByStatusChart(),
            'quotations_by_month' => $this->getQuotationsByMonthChart(),
            'messages_trends' => $this->getMessagesTrendsChart(),
            'client_growth' => $this->getClientGrowthChart(),
            'revenue_trends' => $this->getRevenueTrendsChart(),
        ];
    }

    // Helper methods for calculations and data processing

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

    protected function calculateCompletionRate($projectsQuery): float
    {
        $total = (clone $projectsQuery)->count();
        if ($total === 0) return 0;
        
        $completed = (clone $projectsQuery)->where('status', 'completed')->count();
        return round(($completed / $total) * 100, 1);
    }

    protected function calculateProjectValue($projectsQuery): float
    {
        return (clone $projectsQuery)
            ->whereNotNull('value')
            ->sum('value') ?? 0;
    }

    protected function calculateOnTimeDelivery($projectsQuery): float
    {
        $completedProjects = (clone $projectsQuery)
            ->where('status', 'completed')
            ->whereNotNull('end_date')
            ->whereNotNull('actual_completion_date')
            ->get();

        if ($completedProjects->isEmpty()) return 0;

        $onTimeCount = $completedProjects->filter(function($project) {
            return $project->actual_completion_date <= $project->end_date;
        })->count();

        return round(($onTimeCount / $completedProjects->count()) * 100, 1);
    }

    protected function calculateSatisfactionRate(User $user): float
    {
        $testimonials = Testimonial::whereHas('project', function($query) use ($user) {
            $query->where('client_id', $user->id);
        })->get();

        if ($testimonials->isEmpty()) return 0;

        $averageRating = $testimonials->avg('rating');
        return round(($averageRating / 5) * 100, 1);
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

    protected function hasCompleteProfile(User $user): bool
    {
        return !empty($user->phone) && 
               !empty($user->address) && 
               !empty($user->company);
    }

    protected function getProjectStatusColor(string $status): string
    {
        return match($status) {
            'planning' => 'yellow',
            'in_progress' => 'blue',
            'on_hold' => 'orange',
            'completed' => 'green',
            'cancelled' => 'red',
            default => 'gray',
        };
    }

    protected function getQuotationStatusColor(string $status): string
    {
        return match($status) {
            'pending' => 'yellow',
            'reviewed' => 'blue',
            'approved' => 'green',
            'rejected' => 'red',
            default => 'gray',
        };
    }

    protected function getActivityPriority(string $status): string
    {
        return match($status) {
            'completed' => 'high',
            'in_progress' => 'medium',
            'on_hold' => 'high',
            'cancelled' => 'low',
            default => 'medium',
        };
    }

    protected function getQuotationPriority(Quotation $quotation): string
    {
        if ($quotation->status === 'pending') return 'high';
        if ($quotation->status === 'approved' && !$quotation->client_approved) return 'high';
        return $quotation->priority ?? 'medium';
    }

    // Chart generation methods
    protected function getProjectsByStatusChart(): array
    {
        $data = Project::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        return [
            'labels' => array_keys($data),
            'data' => array_values($data),
            'colors' => array_map([$this, 'getProjectStatusColor'], array_keys($data)),
        ];
    }

    protected function getQuotationsByMonthChart(): array
    {
        $months = [];
        $counts = [];
        
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $months[] = $date->format('M Y');
            $counts[] = Quotation::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
        }

        return [
            'labels' => $months,
            'data' => $counts,
        ];
    }

    protected function getMessagesTrendsChart(): array
    {
        $days = [];
        $counts = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $days[] = $date->format('M j');
            $counts[] = Message::whereDate('created_at', $date->toDateString())->count();
        }

        return [
            'labels' => $days,
            'data' => $counts,
        ];
    }

    protected function getClientGrowthChart(): array
    {
        $months = [];
        $counts = [];
        
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $months[] = $date->format('M Y');
            $counts[] = User::role('client')
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
        }

        return [
            'labels' => $months,
            'data' => $counts,
        ];
    }

    protected function getRevenueTrendsChart(): array
    {
        $months = [];
        $revenue = [];
        
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $months[] = $date->format('M Y');
            $revenue[] = Project::where('status', 'completed')
                ->whereYear('actual_completion_date', $date->year)
                ->whereMonth('actual_completion_date', $date->month)
                ->sum('value') ?? 0;
        }

        return [
            'labels' => $months,
            'data' => $revenue,
        ];
    }

    protected function getSystemAlerts(): array
    {
        $alerts = [];

        // Check disk space (mock implementation)
        $diskUsage = 75; // This would be actual disk usage check
        if ($diskUsage > 80) {
            $alerts[] = [
                'type' => 'warning',
                'title' => 'Disk Space Warning',
                'message' => "Disk usage is at {$diskUsage}%. Consider cleaning up old files.",
                'priority' => 'medium',
            ];
        }

        // Check for expired certificates
        $expiredCerts = Certification::where('expiry_date', '<', now())
            ->where('is_active', true)
            ->count();

        if ($expiredCerts > 0) {
            $alerts[] = [
                'type' => 'warning',
                'title' => 'Expired Certificates',
                'message' => "{$expiredCerts} certificate(s) have expired.",
                'action' => [
                    'text' => 'View Certificates',
                    'url' => route('admin.certifications.index'),
                ],
                'priority' => 'medium',
            ];
        }

        return $alerts;
    }

    protected function getAdminQuickActions(User $user): array
    {
        return [
            [
                'title' => 'Create Project',
                'description' => 'Start a new project',
                'url' => route('admin.projects.create'),
                'icon' => 'plus-circle',
                'color' => 'blue',
            ],
            [
                'title' => 'Review Quotations',
                'description' => 'Check pending quotations',
                'url' => route('admin.quotations.index', ['status' => 'pending']),
                'icon' => 'document-text',
                'color' => 'yellow',
                'badge' => Quotation::where('status', 'pending')->count(),
            ],
            [
                'title' => 'Messages',
                'description' => 'View unread messages',
                'url' => route('admin.messages.index', ['read' => 'unread']),
                'icon' => 'mail',
                'color' => 'green',
                'badge' => Message::where('is_read', false)->count(),
            ],
            [
                'title' => 'System Settings',
                'description' => 'Configure system',
                'url' => route('admin.settings.index'),
                'icon' => 'cog',
                'color' => 'gray',
            ],
        ];
    }

    protected function getClientQuickActions(User $user): array
    {
        return [
            [
                'title' => 'Request Quote',
                'description' => 'Submit a new quotation request',
                'url' => route('client.quotations.create'),
                'icon' => 'document-add',
                'color' => 'blue',
            ],
            [
                'title' => 'Send Message',
                'description' => 'Contact support team',
                'url' => route('client.messages.create'),
                'icon' => 'mail',
                'color' => 'green',
                'badge' => $this->clientAccessService->getClientMessages($user)
                    ->where('is_read', false)
                    ->count(),
            ],
            [
                'title' => 'View Projects',
                'description' => 'Browse your projects',
                'url' => route('client.projects.index'),
                'icon' => 'folder',
                'color' => 'purple',
            ],
            [
                'title' => 'Leave Review',
                'description' => 'Share your experience',
                'url' => route('client.testimonials.create'),
                'icon' => 'star',
                'color' => 'yellow',
                'badge' => $this->clientAccessService->getClientProjects($user)
                    ->where('status', 'completed')
                    ->whereDoesntHave('testimonial')
                    ->count(),
            ],
        ];
    }

    protected function getPendingItems(): array
    {
        return [
            'quotations' => [
                'count' => Quotation::where('status', 'pending')->count(),
                'url' => route('admin.quotations.index', ['status' => 'pending']),
                'title' => 'Pending Quotations',
            ],
            'messages' => [
                'count' => Message::where('is_read', false)->count(),
                'url' => route('admin.messages.index', ['read' => 'unread']),
                'title' => 'Unread Messages',
            ],
            'projects' => [
                'count' => Project::where('status', 'planning')->count(),
                'url' => route('admin.projects.index', ['status' => 'planning']),
                'title' => 'Projects in Planning',
            ],
            'chats' => [
                'count' => ChatSession::where('status', 'waiting')->count(),
                'url' => route('admin.chat.index'),
                'title' => 'Waiting Chats',
            ],
        ];
    }

    protected function getUpcomingDeadlines(User $user): array
    {
        return $this->clientAccessService->getClientProjects($user)
            ->where('status', 'in_progress')
            ->whereNotNull('end_date')
            ->where('end_date', '>', now())
            ->where('end_date', '<=', now()->addDays(30))
            ->with(['category'])
            ->orderBy('end_date')
            ->get()
            ->map(function ($project) {
                $daysUntil = now()->diffInDays($project->end_date, false);
                
                return [
                    'id' => $project->id,
                    'title' => $project->title,
                    'date' => $project->end_date,
                    'days_until' => $daysUntil,
                    'url' => route('client.projects.show', $project),
                    'urgency' => $this->calculateUrgency($daysUntil),
                    'category' => $project->category?->name,
                    'location' => $project->location,
                ];
            })
            ->toArray();
    }

    protected function getAdminNotifications(): array
    {
        return [
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
        ];
    }

    protected function getClientNotifications(User $user): array
    {
        return [
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
        ];
    }

    protected function getSystemPerformance(): array
    {
        return [
            'response_time' => [
                'average' => $this->getChatAverageResponseTime(),
                'unit' => 'minutes',
                'status' => $this->getChatAverageResponseTime() < 30 ? 'good' : 'warning',
            ],
            'uptime' => [
                'percentage' => 99.9, // This would be calculated from actual monitoring
                'status' => 'excellent',
            ],
            'active_users' => [
                'count' => User::where('is_active', true)->count(),
                'online' => ChatSession::where('status', 'active')->count(),
            ],
            'storage' => [
                'used_percentage' => 65, // This would be actual disk usage
                'status' => 'good',
            ],
        ];
    }

    protected function getClientPerformance(User $user): array
    {
        $projectsQuery = $this->clientAccessService->getClientProjects($user);
        $messagesQuery = $this->clientAccessService->getClientMessages($user);

        return [
            'project_completion' => [
                'rate' => $this->calculateCompletionRate($projectsQuery),
                'status' => $this->calculateCompletionRate($projectsQuery) > 80 ? 'excellent' : 'good',
            ],
            'response_rate' => [
                'rate' => $this->calculateResponseRate($messagesQuery),
                'status' => $this->calculateResponseRate($messagesQuery) > 90 ? 'excellent' : 'good',
            ],
            'satisfaction' => [
                'rate' => $this->calculateSatisfactionRate($user),
                'status' => $this->calculateSatisfactionRate($user) > 90 ? 'excellent' : 'good',
            ],
            'on_time_delivery' => [
                'rate' => $this->calculateOnTimeDelivery($projectsQuery),
                'status' => $this->calculateOnTimeDelivery($projectsQuery) > 85 ? 'excellent' : 'good',
            ],
        ];
    }

    protected function calculateResponseRate($messagesQuery): float
    {
        $total = (clone $messagesQuery)->count();
        if ($total === 0) return 0;
        
        $replied = (clone $messagesQuery)->where('is_replied', true)->count();
        return round(($replied / $total) * 100, 1);
    }

    protected function calculateUrgency(int $daysUntil): string
    {
        return match(true) {
            $daysUntil <= 1 => 'critical',
            $daysUntil <= 3 => 'high',
            $daysUntil <= 7 => 'medium',
            default => 'low',
        };
    }

    /**
     * Clear all cached data for a user.
     */
    public function clearCache(User $user): void
    {
        $roleKey = $user->getRoleNames()->implode('_');
        $keys = [
            "dashboard_data_{$user->id}_{$roleKey}",
            "client_dashboard_data_{$user->id}",
            "client_statistics_{$user->id}",
            "client_activities_{$user->id}",
        ];

        foreach ($keys as $key) {
            Cache::forget($key);
        }
    }

    /**
     * Get real-time statistics for AJAX updates.
     */
    public function getRealTimeStats(User $user): array
    {
        if ($user->hasAnyRole(['super-admin', 'admin', 'manager'])) {
            return [
                'unread_messages' => Message::where('is_read', false)->count(),
                'pending_quotations' => Quotation::where('status', 'pending')->count(),
                'active_chats' => ChatSession::where('status', 'active')->count(),
                'waiting_chats' => ChatSession::where('status', 'waiting')->count(),
                'urgent_items' => Message::where('priority', 'urgent')
                    ->where('is_read', false)
                    ->count(),
            ];
        } else {
            return [
                'unread_messages' => $this->clientAccessService->getClientMessages($user)
                    ->where('is_read', false)
                    ->count(),
                'pending_approvals' => $this->clientAccessService->getClientQuotations($user)
                    ->where('status', 'approved')
                    ->whereNull('client_approved')
                    ->count(),
                'active_projects' => $this->clientAccessService->getClientProjects($user)
                    ->whereIn('status', ['in_progress', 'on_hold'])
                    ->count(),
            ];
        }
    }

    /**
     * Generate comprehensive report data.
     */
    public function generateReport(User $user, array $filters = []): array
    {
        $dateRange = $filters['date_range'] ?? 'last_30_days';
        $startDate = $this->getStartDate($dateRange, $filters);
        $endDate = $filters['end_date'] ?? now();

        if ($user->hasAnyRole(['super-admin', 'admin', 'manager'])) {
            return $this->generateAdminReport($startDate, $endDate, $filters);
        } else {
            return $this->generateClientReport($user, $startDate, $endDate, $filters);
        }
    }

    protected function generateAdminReport(Carbon $startDate, Carbon $endDate, array $filters): array
    {
        return [
            'period' => [
                'start' => $startDate->toDateString(),
                'end' => $endDate->toDateString(),
                'days' => $startDate->diffInDays($endDate),
            ],
            'projects' => [
                'total' => Project::whereBetween('created_at', [$startDate, $endDate])->count(),
                'completed' => Project::where('status', 'completed')
                    ->whereBetween('actual_completion_date', [$startDate, $endDate])
                    ->count(),
                'revenue' => Project::where('status', 'completed')
                    ->whereBetween('actual_completion_date', [$startDate, $endDate])
                    ->sum('value'),
            ],
            'quotations' => [
                'total' => Quotation::whereBetween('created_at', [$startDate, $endDate])->count(),
                'approved' => Quotation::where('status', 'approved')
                    ->whereBetween('approved_at', [$startDate, $endDate])
                    ->count(),
                'conversion_rate' => $this->calculatePeriodConversionRate($startDate, $endDate),
            ],
            'clients' => [
                'new' => User::role('client')
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->count(),
                'active' => $this->getActiveClientsInPeriod($startDate, $endDate),
            ],
            'messages' => [
                'total' => Message::whereBetween('created_at', [$startDate, $endDate])->count(),
                'response_rate' => $this->calculatePeriodResponseRate($startDate, $endDate),
            ],
        ];
    }

    protected function generateClientReport(User $user, Carbon $startDate, Carbon $endDate, array $filters): array
    {
        return [
            'period' => [
                'start' => $startDate->toDateString(),
                'end' => $endDate->toDateString(),
                'days' => $startDate->diffInDays($endDate),
            ],
            'projects' => $this->getClientProjectReport($user, $startDate, $endDate),
            'quotations' => $this->getClientQuotationReport($user, $startDate, $endDate),
            'messages' => $this->getClientMessageReport($user, $startDate, $endDate),
            'performance' => $this->getClientPerformanceReport($user, $startDate, $endDate),
        ];
    }

    protected function getStartDate(string $range, array $filters): Carbon
    {
        return match($range) {
            'today' => now()->startOfDay(),
            'yesterday' => now()->subDay()->startOfDay(),
            'this_week' => now()->startOfWeek(),
            'last_week' => now()->subWeek()->startOfWeek(),
            'this_month' => now()->startOfMonth(),
            'last_month' => now()->subMonth()->startOfMonth(),
            'last_30_days' => now()->subDays(30),
            'last_90_days' => now()->subDays(90),
            'this_year' => now()->startOfYear(),
            'custom' => Carbon::parse($filters['start_date'] ?? now()->subDays(30)),
            default => now()->subDays(30),
        };
    }

    // Additional helper methods for report generation
    protected function calculatePeriodConversionRate(Carbon $startDate, Carbon $endDate): float
    {
        $totalQuotations = Quotation::whereBetween('created_at', [$startDate, $endDate])->count();
        if ($totalQuotations === 0) return 0;
        
        $approvedQuotations = Quotation::where('status', 'approved')
            ->whereBetween('approved_at', [$startDate, $endDate])
            ->count();
            
        return round(($approvedQuotations / $totalQuotations) * 100, 1);
    }

    protected function getActiveClientsInPeriod(Carbon $startDate, Carbon $endDate): int
    {
        return User::role('client')
            ->where(function($query) use ($startDate, $endDate) {
                $query->whereHas('projects', function($q) use ($startDate, $endDate) {
                    $q->whereBetween('updated_at', [$startDate, $endDate]);
                })
                ->orWhereHas('quotations', function($q) use ($startDate, $endDate) {
                    $q->whereBetween('updated_at', [$startDate, $endDate]);
                })
                ->orWhereHas('messages', function($q) use ($startDate, $endDate) {
                    $q->whereBetween('created_at', [$startDate, $endDate]);
                });
            })
            ->distinct()
            ->count();
    }

    protected function calculatePeriodResponseRate(Carbon $startDate, Carbon $endDate): float
    {
        $totalMessages = Message::whereBetween('created_at', [$startDate, $endDate])->count();
        if ($totalMessages === 0) return 0;
        
        $repliedMessages = Message::where('is_replied', true)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();
            
        return round(($repliedMessages / $totalMessages) * 100, 1);
    }

    protected function getClientProjectReport(User $user, Carbon $startDate, Carbon $endDate): array
    {
        $query = $this->clientAccessService->getClientProjects($user)
            ->whereBetween('updated_at', [$startDate, $endDate]);
            
        return [
            'total' => (clone $query)->count(),
            'completed' => (clone $query)->where('status', 'completed')->count(),
            'active' => (clone $query)->whereIn('status', ['in_progress', 'on_hold'])->count(),
            'value' => (clone $query)->sum('value') ?? 0,
        ];
    }

    protected function getClientQuotationReport(User $user, Carbon $startDate, Carbon $endDate): array
    {
        $query = $this->clientAccessService->getClientQuotations($user)
            ->whereBetween('created_at', [$startDate, $endDate]);
            
        return [
            'total' => (clone $query)->count(),
            'approved' => (clone $query)->where('status', 'approved')->count(),
            'pending' => (clone $query)->where('status', 'pending')->count(),
        ];
    }

    protected function getClientMessageReport(User $user, Carbon $startDate, Carbon $endDate): array
    {
        $query = $this->clientAccessService->getClientMessages($user)
            ->whereBetween('created_at', [$startDate, $endDate]);
            
        return [
            'total' => (clone $query)->count(),
            'replied' => (clone $query)->where('is_replied', true)->count(),
            'response_rate' => $this->calculateResponseRate($query),
        ];
    }

    protected function getClientPerformanceReport(User $user, Carbon $startDate, Carbon $endDate): array
    {
        return [
            'satisfaction_rate' => $this->calculateSatisfactionRate($user),
            'on_time_delivery' => $this->calculateOnTimeDelivery(
                $this->clientAccessService->getClientProjects($user)
            ),
            'communication_score' => $this->calculateResponseRate(
                $this->clientAccessService->getClientMessages($user)
            ),
        ];
    }
}