<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use App\Services\ClientAccessService;
use App\Services\ClientNotificationService;
use App\Facades\Notifications;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class DashboardController extends Controller
{
    protected DashboardService $dashboardService;
    protected ClientAccessService $clientAccessService;
    protected ClientNotificationService $clientNotificationService;

    public function __construct(
        DashboardService $dashboardService,
        ClientAccessService $clientAccessService,
        ClientNotificationService $clientNotificationService
    ) {
        $this->dashboardService = $dashboardService;
        $this->clientAccessService = $clientAccessService;
        $this->clientNotificationService = $clientNotificationService;

        // Apply client middleware
        $this->middleware(['auth', 'verified', 'role:client']);
    }

    /**
     * Display the client dashboard with comprehensive data.
     */
    public function index()
    {
        try {
            $user = auth()->user();

            // Get comprehensive dashboard data
            $dashboardData = $this->dashboardService->getDashboardData($user);

            // Get notification counts for header
            $notificationCounts = $this->dashboardService->getClientNotificationCounts($user);

            // Get client permissions
            $permissions = $this->clientAccessService->getClientPermissions($user);

            // Check for any important alerts
            $alerts = $this->getClientAlerts($user);

            // Get recent notifications for dropdown
            $recentNotifications = $this->dashboardService->getRecentNotifications($user, 10);

            return view('client.dashboard', [
                'user' => $user,
                'statistics' => $dashboardData['statistics'] ?? [],
                'recentActivities' => $dashboardData['recent_activities'] ?? [],
                'upcomingDeadlines' => $dashboardData['upcoming_deadlines'] ?? [],
                'performance' => $dashboardData['performance'] ?? [],
                'quickActions' => $dashboardData['quick_actions'] ?? [],
                'notificationCounts' => $notificationCounts,
                'recentNotifications' => $recentNotifications,
                'permissions' => $permissions,
                'alerts' => $alerts,
                'chartData' => $this->getClientChartData($user),
            ]);
        } catch (\Exception $e) {
            Log::error('Error loading client dashboard', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);

            return view('client.dashboard', [
                'user' => auth()->user(),
                'statistics' => [],
                'recentActivities' => [],
                'upcomingDeadlines' => [],
                'performance' => [],
                'quickActions' => [],
                'notificationCounts' => [],
                'recentNotifications' => [],
                'permissions' => [],
                'alerts' => [],
                'chartData' => [],
                'error' => 'Unable to load dashboard data. Please try again.'
            ]);
        }
    }

    /**
     * Get real-time statistics for AJAX updates.
     */
    public function getRealtimeStats(): JsonResponse
    {
        try {
            $user = auth()->user();

            $stats = [
                'projects' => [
                    'total' => $this->clientAccessService->getClientProjects($user)->count(),
                    'active' => $this->clientAccessService->getClientProjects($user)
                        ->whereIn('status', ['in_progress', 'on_hold'])->count(),
                    'completed' => $this->clientAccessService->getClientProjects($user)
                        ->where('status', 'completed')->count(),
                    'overdue' => $this->clientAccessService->getClientProjects($user)
                        ->where('status', 'in_progress')
                        ->where('end_date', '<', now())
                        ->whereNotNull('end_date')
                        ->count(),
                ],
                'quotations' => [
                    'total' => $this->clientAccessService->getClientQuotations($user)->count(),
                    'pending' => $this->clientAccessService->getClientQuotations($user)
                        ->where('status', 'pending')->count(),
                    'approved' => $this->clientAccessService->getClientQuotations($user)
                        ->where('status', 'approved')->count(),
                    'awaiting_approval' => $this->clientAccessService->getClientQuotations($user)
                        ->where('status', 'approved')
                        ->whereNull('client_approved')
                        ->count(),
                ],
                'messages' => [
                    'total' => $this->clientAccessService->getClientMessages($user)->count(),
                    'unread' => $this->clientAccessService->getClientMessages($user)
                        ->where('is_read', false)->count(),
                    'urgent' => $this->clientAccessService->getClientMessages($user)
                        ->where('priority', 'urgent')
                        ->where('is_read', false)
                        ->count(),
                ],
                'notifications' => [
                    'unread' => $user->unreadNotifications()->count(),
                ],
            ];

            return response()->json([
                'success' => true,
                'data' => $stats,
                'timestamp' => now()->toISOString(),
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting realtime stats', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to load statistics',
            ], 500);
        }
    }

    /**
     * Get chart data for dashboard widgets.
     */
    public function getChartData(Request $request): JsonResponse
    {
        try {
            $user = auth()->user();
            $chartType = $request->get('type', 'projects');
            $dateRange = $request->get('range', 'last_30_days');

            $chartData = match ($chartType) {
                'projects' => $this->getProjectsChartData($user, $dateRange),
                'quotations' => $this->getQuotationsChartData($user, $dateRange),
                'messages' => $this->getMessagesChartData($user, $dateRange),
                'timeline' => $this->getTimelineChartData($user, $dateRange),
                default => []
            };

            return response()->json([
                'success' => true,
                'data' => $chartData,
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting chart data', [
                'user_id' => auth()->id(),
                'type' => $request->get('type'),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to load chart data',
            ], 500);
        }
    }

    /**
     * Get performance metrics for the client.
     */
    public function getPerformanceMetrics(): JsonResponse
    {
        try {
            $user = auth()->user();

            $projects = $this->clientAccessService->getClientProjects($user)->get();

            $performance = [
                'project_completion_rate' => $this->calculateCompletionRate($projects),
                'on_time_delivery_rate' => $this->calculateOnTimeDeliveryRate($projects),
                'average_project_duration' => $this->calculateAverageProjectDuration($projects),
                'client_satisfaction_score' => $this->getClientSatisfactionScore($user),
                'response_rate' => $this->calculateResponseRate($user),
                'total_project_value' => $this->calculateTotalProjectValue($projects),
            ];

            return response()->json([
                'success' => true,
                'data' => $performance,
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting performance metrics', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to load performance metrics',
            ], 500);
        }
    }

    /**
     * Get upcoming deadlines for the client.
     */
    public function getUpcomingDeadlines(): JsonResponse
    {
        try {
            $user = auth()->user();

            $deadlines = $this->clientAccessService->getClientProjects($user)
                ->where('status', 'in_progress')
                ->where('end_date', '>', now())
                ->where('end_date', '<=', now()->addDays(30))
                ->orderBy('end_date')
                ->get()
                ->map(function ($project) {
                    return [
                        'id' => $project->id,
                        'title' => $project->title,
                        'deadline' => $project->end_date->format('Y-m-d'),
                        'deadline_formatted' => $project->end_date->format('M d, Y'),
                        'days_remaining' => now()->diffInDays($project->end_date, false),
                        'status' => $project->status,
                        'priority' => $project->priority ?? 'normal',
                        'progress_percentage' => $project->progress_percentage ?? 0,
                        'url' => route('client.projects.show', $project),
                        'urgency' => $this->getDeadlineUrgency($project->end_date),
                    ];
                })
                ->toArray();

            return response()->json([
                'success' => true,
                'data' => $deadlines,
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting upcoming deadlines', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to load deadlines',
            ], 500);
        }
    }

    /**
     * Get recent activities for the client.
     */
    public function getRecentActivities(): JsonResponse
    {
        try {
            $user = auth()->user();

            $activities = collect();

            // Recent project updates
            $recentProjects = $this->clientAccessService->getClientProjects($user)
                ->orderBy('updated_at', 'desc')
                ->limit(5)
                ->get()
                ->map(function ($project) {
                    return [
                        'type' => 'project',
                        'icon' => 'folder',
                        'color' => $this->getProjectStatusColor($project->status),
                        'title' => $project->title,
                        'description' => "Status: {$project->formatted_status}",
                        'timestamp' => $project->updated_at,
                        'url' => route('client.projects.show', $project),
                    ];
                });

            // Recent quotation updates
            $recentQuotations = $this->clientAccessService->getClientQuotations($user)
                ->orderBy('updated_at', 'desc')
                ->limit(5)
                ->get()
                ->map(function ($quotation) {
                    return [
                        'type' => 'quotation',
                        'icon' => 'document-text',
                        'color' => $this->getQuotationStatusColor($quotation->status),
                        'title' => $quotation->project_type,
                        'description' => "Status: {$quotation->formatted_status}",
                        'timestamp' => $quotation->updated_at,
                        'url' => route('client.quotations.show', $quotation),
                    ];
                });

            // Recent messages
            $recentMessages = $this->clientAccessService->getClientMessages($user)
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get()
                ->map(function ($message) {
                    return [
                        'type' => 'message',
                        'icon' => 'mail',
                        'color' => $message->is_read ? 'gray' : 'blue',
                        'title' => $message->subject,
                        'description' => $message->is_replied ? 'Replied' : 'New message',
                        'timestamp' => $message->created_at,
                        'url' => route('client.messages.show', $message),
                    ];
                });

            // Combine and sort all activities
            $activities = $activities
                ->concat($recentProjects)
                ->concat($recentQuotations)
                ->concat($recentMessages)
                ->sortByDesc('timestamp')
                ->take(15)
                ->values()
                ->map(function ($activity) {
                    $activity['formatted_time'] = $activity['timestamp']->diffForHumans();
                    return $activity;
                });

            return response()->json([
                'success' => true,
                'data' => $activities,
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting recent activities', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to load activities',
            ], 500);
        }
    }

    /**
     * Get notification summary for the client.
     */
    public function getNotifications(): JsonResponse
    {
        try {
            $user = auth()->user();

            $notifications = $user->notifications()
                ->orderBy('created_at', 'desc')
                ->limit(20)
                ->get()
                ->map(function ($notification) {
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
                });

            return response()->json([
                'success' => true,
                'data' => $notifications,
                'unread_count' => $user->unreadNotifications()->count(),
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting notifications', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to load notifications',
            ], 500);
        }
    }

    /**
     * Mark notification as read.
     */
    public function markNotificationRead(Request $request): JsonResponse
    {
        try {
            $user = auth()->user();
            $notificationId = $request->get('notification_id');

            if ($notificationId === 'all') {
                $user->unreadNotifications->markAsRead();
                $message = 'All notifications marked as read';
            } else {
                $notification = $user->notifications()->find($notificationId);
                if ($notification) {
                    $notification->markAsRead();
                    $message = 'Notification marked as read';
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Notification not found',
                    ], 404);
                }
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'unread_count' => $user->unreadNotifications()->count(),
            ]);
        } catch (\Exception $e) {
            Log::error('Error marking notification as read', [
                'user_id' => auth()->id(),
                'notification_id' => $request->get('notification_id'),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to mark notification as read',
            ], 500);
        }
    }

    /**
     * Test client notifications.
     */
    public function testNotification(): JsonResponse
    {
        try {
            $user = auth()->user();

            $success = $this->clientNotificationService->sendTestNotification($user);

            if ($success) {
                return response()->json([
                    'success' => true,
                    'message' => 'Test notification sent successfully! Check your email and notifications.',
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to send test notification. Please check your settings.',
                ], 500);
            }
        } catch (\Exception $e) {
            Log::error('Error sending test notification', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to send test notification',
            ], 500);
        }
    }

    /**
     * Clear dashboard cache.
     */
    public function clearCache(): JsonResponse
    {
        try {
            $user = auth()->user();

            // Clear user-specific cache
            $this->clientAccessService->clearClientCache($user);

            // Clear dashboard cache
            Cache::forget("dashboard_data_{$user->id}_" . $user->getRoleNames()->implode('_'));

            return response()->json([
                'success' => true,
                'message' => 'Dashboard cache cleared successfully',
            ]);
        } catch (\Exception $e) {
            Log::error('Error clearing dashboard cache', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to clear cache',
            ], 500);
        }
    }

    // Helper methods below...

    /**
     * Get client-specific alerts.
     */
    protected function getClientAlerts($user): array
    {
        try {
            return [
                'profile_incomplete' => !$this->isProfileComplete($user),
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
        } catch (\Exception $e) {
            Log::error('Error getting client alerts', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }

    /**
     * Get chart data for client dashboard.
     */
    protected function getClientChartData($user): array
    {
        try {
            return [
                'projects_by_status' => $this->getProjectsByStatusChart($user),
                'quotations_by_status' => $this->getQuotationsByStatusChart($user),
                'project_timeline' => $this->getProjectTimelineChart($user),
                'monthly_activity' => $this->getMonthlyActivityChart($user),
            ];
        } catch (\Exception $e) {
            Log::error('Error getting chart data', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }

    /**
     * Calculate project completion rate.
     */
    protected function calculateCompletionRate($projects): float
    {
        if ($projects->isEmpty())
            return 0;

        $completed = $projects->where('status', 'completed')->count();
        return round(($completed / $projects->count()) * 100, 1);
    }

    /**
     * Calculate on-time delivery rate.
     */
    protected function calculateOnTimeDeliveryRate($projects): float
    {
        $completedWithDates = $projects->where('status', 'completed')
            ->filter(fn($p) => $p->end_date && $p->actual_completion_date);

        if ($completedWithDates->isEmpty())
            return 0;

        $onTime = $completedWithDates->filter(fn($p) => $p->actual_completion_date <= $p->end_date)->count();

        return round(($onTime / $completedWithDates->count()) * 100, 1);
    }

    /**
     * Calculate average project duration.
     */
    protected function calculateAverageProjectDuration($projects): int
    {
        $completedWithDates = $projects->where('status', 'completed')
            ->filter(fn($p) => $p->start_date && $p->actual_completion_date);

        if ($completedWithDates->isEmpty())
            return 0;

        $totalDays = $completedWithDates->sum(function ($project) {
            return $project->start_date->diffInDays($project->actual_completion_date);
        });

        return round($totalDays / $completedWithDates->count());
    }

    /**
     * Get client satisfaction score.
     */
    protected function getClientSatisfactionScore($user): float
    {
        // This would typically be calculated from testimonials/ratings
        // For now, return a mock value
        return 4.5;
    }

    /**
     * Calculate response rate.
     */
    protected function calculateResponseRate($user): float
    {
        $messages = $this->clientAccessService->getClientMessages($user)->get();
        if ($messages->isEmpty())
            return 0;

        $replied = $messages->where('is_replied', true)->count();
        return round(($replied / $messages->count()) * 100, 1);
    }

    /**
     * Calculate total project value.
     */
    protected function calculateTotalProjectValue($projects): int
    {
        return $projects->sum('value') ?? 0;
    }

    /**
     * Check if user profile is complete.
     */
    protected function isProfileComplete($user): bool
    {
        $requiredFields = ['name', 'email', 'phone', 'company', 'address'];

        foreach ($requiredFields as $field) {
            if (empty($user->$field)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get deadline urgency level.
     */
    protected function getDeadlineUrgency($deadline): string
    {
        $daysUntilDeadline = now()->diffInDays($deadline, false);

        return match (true) {
            $daysUntilDeadline <= 1 => 'critical',
            $daysUntilDeadline <= 3 => 'high',
            $daysUntilDeadline <= 7 => 'medium',
            default => 'low'
        };
    }

    /**
     * Get project status color.
     */
    protected function getProjectStatusColor($status): string
    {
        return match ($status) {
            'completed' => 'green',
            'in_progress' => 'blue',
            'on_hold' => 'yellow',
            'cancelled' => 'red',
            default => 'gray'
        };
    }

    /**
     * Get quotation status color.
     */
    protected function getQuotationStatusColor($status): string
    {
        return match ($status) {
            'approved' => 'green',
            'pending' => 'yellow',
            'reviewed' => 'blue',
            'rejected' => 'red',
            default => 'gray'
        };
    }

    /**
     * Get notification icon.
     */
    protected function getNotificationIcon($type): string
    {
        return match ($type) {
            'project.created', 'project.updated', 'project.completed' => 'folder',
            'project.deadline_approaching', 'project.overdue' => 'exclamation-triangle',
            'quotation.created', 'quotation.approved', 'quotation.rejected' => 'document-text',
            'message.created', 'message.reply' => 'mail',
            'user.welcome', 'user.email_verified' => 'user',
            'testimonial.created' => 'star',
            default => 'bell',
        };
    }

    /**
     * Get notification color.
     */
    protected function getNotificationColor($type): string
    {
        return match ($type) {
            'project.completed', 'quotation.approved', 'user.welcome' => 'green',
            'project.overdue', 'message.urgent', 'quotation.rejected' => 'red',
            'project.deadline_approaching' => 'yellow',
            'quotation.created', 'message.created', 'message.reply' => 'blue',
            default => 'gray',
        };
    }

    // Chart data methods (simplified versions)

    protected function getProjectsChartData($user, $dateRange): array
    {
        // Simplified implementation - would contain actual chart data
        return [
            'labels' => ['Planning', 'In Progress', 'On Hold', 'Completed', 'Cancelled'],
            'datasets' => [
                [
                    'label' => 'Projects by Status',
                    'data' => [1, 3, 0, 5, 1],
                    'backgroundColor' => ['#3B82F6', '#F59E0B', '#EF4444', '#10B981', '#6B7280']
                ]
            ]
        ];
    }

    protected function getQuotationsChartData($user, $dateRange): array
    {
        // Simplified implementation
        return [
            'labels' => ['Pending', 'Reviewed', 'Approved', 'Rejected'],
            'datasets' => [
                [
                    'label' => 'Quotations by Status',
                    'data' => [2, 1, 4, 1],
                    'backgroundColor' => ['#F59E0B', '#3B82F6', '#10B981', '#EF4444']
                ]
            ]
        ];
    }

    protected function getMessagesChartData($user, $dateRange): array
    {
        // Simplified implementation
        return [
            'labels' => ['Read', 'Unread', 'Replied'],
            'datasets' => [
                [
                    'label' => 'Messages Status',
                    'data' => [8, 2, 6],
                    'backgroundColor' => ['#10B981', '#F59E0B', '#3B82F6']
                ]
            ]
        ];
    }

    protected function getTimelineChartData($user, $dateRange): array
    {
        // Simplified implementation
        return [
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            'datasets' => [
                [
                    'label' => 'Projects',
                    'data' => [1, 2, 1, 3, 2, 1],
                    'borderColor' => '#3B82F6',
                    'tension' => 0.4
                ],
                [
                    'label' => 'Quotations',
                    'data' => [2, 3, 2, 4, 3, 2],
                    'borderColor' => '#10B981',
                    'tension' => 0.4
                ]
            ]
        ];
    }

    protected function getProjectsByStatusChart($user): array
    {
        $projects = $this->clientAccessService->getClientProjects($user)->get();

        $statusCounts = $projects
            ->groupBy('status')
            ->map(fn($items) => count($items));

        return [
            'labels' => $statusCounts->keys()->toArray(),
            'data' => $statusCounts->values()->toArray(),
        ];
    }

    protected function getQuotationsByStatusChart($user): array
    {
        $quotations = $this->clientAccessService->getClientQuotations($user)->get();

        $statusCounts = $quotations
            ->groupBy('status')
            ->map(fn($items) => count($items));

        return [
            'labels' => $statusCounts->keys()->toArray(),
            'data' => $statusCounts->values()->toArray(),
        ];
    }

    protected function getProjectTimelineChart($user): array
    {
        $projects = $this->clientAccessService->getClientProjects($user)
            ->whereBetween('created_at', [now()->subMonths(6), now()])
            ->get()
            ->groupBy(fn($p) => $p->created_at->format('M Y'))
            ->map(fn($items) => count($items));

        return [
            'labels' => $projects->keys()->toArray(),
            'data' => $projects->values()->toArray(),
        ];
    }


    protected function getMonthlyActivityChart($user): array
    {
        // Combined activity chart
        $months = collect();
        for ($i = 5; $i >= 0; $i--) {
            $months->push(now()->subMonths($i)->format('M Y'));
        }

        // This would contain actual activity data
        return [
            'labels' => $months->toArray(),
            'projects' => [1, 2, 1, 3, 2, 1],
            'quotations' => [2, 3, 2, 4, 3, 2],
            'messages' => [5, 7, 4, 8, 6, 5],
        ];
    }
}