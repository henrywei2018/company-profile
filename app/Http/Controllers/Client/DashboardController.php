<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use App\Services\ClientAccessService;
use App\Services\NotificationService;
use App\Services\NotificationDataService;
use App\Facades\Notifications;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    protected DashboardService $dashboardService;
    protected ClientAccessService $clientAccessService;
    protected NotificationService $notificationService;

    public function __construct(
        DashboardService $dashboardService,
        ClientAccessService $clientAccessService,
        NotificationService $notificationService
    ) {
        $this->dashboardService = $dashboardService;
        $this->clientAccessService = $clientAccessService;
        $this->notificationService = $notificationService;
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

            // FIXED: Get notification counts for header
            $notificationCounts = $this->getClientNotificationCounts($user);

            // FIXED: Get recent notifications for dropdown - properly formatted
            $recentNotifications = $this->getFormattedRecentNotifications($user, 10);

            // Get client permissions
            $permissions = [];
            if (method_exists($this->clientAccessService, 'getClientPermissions')) {
                $permissions = $this->clientAccessService->getClientPermissions($user);
            }

            // Check for any important alerts
            $alerts = $this->getClientAlerts($user);

            // FIXED: Return view with all required data for header component
            return view('client.dashboard', [
                'user' => $user,
                'statistics' => $dashboardData['statistics'] ?? [],
                'recentActivities' => $dashboardData['recent_activities'] ?? [],
                'upcomingDeadlines' => $dashboardData['upcoming_deadlines'] ?? [],
                'quickActions' => $this->getClientQuickActions(),
                'notifications' => $notificationCounts,
                'permissions' => $permissions,
                'alerts' => $alerts,
                
                // FIXED: These are required by the client header component
                'recentNotifications' => $recentNotifications,
                'unreadNotificationsCount' => $notificationCounts['unread_notifications'] ?? 0,
                'unreadMessagesCount' => $notificationCounts['unread_messages'] ?? 0,
                'pendingApprovalsCount' => $notificationCounts['pending_approvals'] ?? 0,
                'overdueProjectsCount' => $notificationCounts['overdue_projects'] ?? 0,
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error loading client dashboard', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // FIXED: Return safe fallback with empty but properly structured data
            return view('client.dashboard', [
                'user' => auth()->user(),
                'statistics' => [],
                'recentActivities' => [],
                'upcomingDeadlines' => [],
                'quickActions' => $this->getClientQuickActions(),
                'notifications' => [],
                'permissions' => [],
                'alerts' => [],
                'error' => 'Unable to load dashboard data. Please try again.',
                
                // FIXED: Empty but properly typed data for header
                'recentNotifications' => collect(),
                'unreadNotificationsCount' => 0,
                'unreadMessagesCount' => 0,
                'pendingApprovalsCount' => 0,
                'overdueProjectsCount' => 0,
            ]);
        }
    }
    

    /**
     * Get real-time statistics for AJAX updates.
     */
    public function getRealtimeStats(): JsonResponse
    {
        try {
            $user = Auth::user();
            $notificationCounts = $this->dashboardService->getClientNotificationCounts($user);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'notifications' => [
                        'unread' => $notificationCounts['unread_database_notifications'],
                        'total' => $notificationCounts['total_notifications'],
                    ],
                    'messages' => [
                        'unread' => $notificationCounts['unread_messages'],
                    ],
                    'quotations' => [
                        'awaiting_approval' => $notificationCounts['pending_approvals'],
                    ],
                    'projects' => [
                        'overdue' => $notificationCounts['overdue_projects'],
                        'upcoming_deadlines' => $notificationCounts['upcoming_deadlines'],
                    ],
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get client realtime stats: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'data' => []
            ], 500);
        }
    }

    protected function getClientQuickActions(): array
    {
        return [
            [
                'title' => 'Request Quote',
                'description' => 'Submit new quotation request',
                'icon' => 'document-add',
                'color' => 'blue',
                'url' => route('client.quotations.create'),
            ],
            [
                'title' => 'Send Message',
                'description' => 'Contact support team',
                'icon' => 'mail',
                'color' => 'green',
                'url' => route('client.messages.create'),
            ],
            [
                'title' => 'View Projects',
                'description' => 'Check your projects',
                'icon' => 'folder',
                'color' => 'purple',
                'url' => route('client.projects.index'),
            ],
            [
                'title' => 'Leave Review',
                'description' => 'Share your experience',
                'icon' => 'star',
                'color' => 'amber',
                'url' => route('client.testimonials.create'),
            ],
        ];
    }
    protected function getFormattedRecentNotifications($user, int $limit = 10)
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
            });
            
        } catch (\Exception $e) {
            Log::error('Error getting formatted recent notifications', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            return collect();
        }
    }
    
    protected function getClientNotificationCounts($user): array
    {
        try {
            return [
                'unread_messages' => $this->clientAccessService->getClientMessages($user)
                    ->where('is_read', false)->count(),
                'pending_approvals' => $this->clientAccessService->getClientQuotations($user)
                    ->where('status', 'approved')
                    ->whereNull('client_approved')
                    ->count(),
                'overdue_projects' => $this->clientAccessService->getClientProjects($user)
                    ->where('status', 'in_progress')
                    ->where('end_date', '<', now())
                    ->whereNotNull('end_date')
                    ->count(),
                'unread_notifications' => $user->unreadNotifications()->count(),
            ];
        } catch (\Exception $e) {
            Log::error('Error getting client notification counts', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            return [
                'unread_messages' => 0,
                'pending_approvals' => 0,
                'overdue_projects' => 0,
                'unread_notifications' => 0,
            ];
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
    protected function getRecentActivities(): JsonResponse
    {
        try {
            $user = auth()->user();

            $activities = collect();

            // Recent project updates dengan color yang aman
            $recentProjects = $this->clientAccessService->getClientProjects($user)
                ->orderBy('updated_at', 'desc')
                ->limit(5)
                ->get()
                ->map(function ($project) {
                    return [
                        'type' => 'project',
                        'icon' => 'folder',
                        'color' => $this->getProjectStatusColor($project->status), // Pastikan method ini ada
                        'title' => $project->title,
                        'description' => "Status: " . $this->formatProjectStatus($project->status),
                        'timestamp' => $project->updated_at,
                        'url' => route('client.projects.show', $project),
                    ];
                });

            // Recent quotation updates dengan color yang aman
            $recentQuotations = $this->clientAccessService->getClientQuotations($user)
                ->orderBy('updated_at', 'desc')
                ->limit(5)
                ->get()
                ->map(function ($quotation) {
                    return [
                        'type' => 'quotation',
                        'icon' => 'document-text',
                        'color' => $this->getQuotationStatusColor($quotation->status), // Pastikan method ini ada
                        'title' => $quotation->project_type,
                        'description' => "Status: " . $this->formatQuotationStatus($quotation->status),
                        'timestamp' => $quotation->updated_at,
                        'url' => route('client.quotations.show', $quotation),
                    ];
                });

            // Recent messages dengan color yang aman
            $recentMessages = $this->clientAccessService->getClientMessages($user)
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get()
                ->map(function ($message) {
                    return [
                        'type' => 'message',
                        'icon' => 'mail',
                        'color' => $this->getMessageStatusColor($message), // Pastikan method ini ada
                        'title' => $message->subject,
                        'description' => $message->is_replied ? 'Replied' : 'New message',
                        'timestamp' => $message->created_at,
                        'url' => route('client.messages.show', $message),
                    ];
                });

            // Combine dan sort all activities
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
            $recentNotifications = $this->getFormattedRecentNotifications($user, 20);

            return response()->json([
                'success' => true,
                'data' => $recentNotifications,
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
                'data' => [],
                'unread_count' => 0,
            ], 500);
        }
    }

    /**
     * Mark notification as read.
     */
    public function markNotificationRead(Request $request): JsonResponse
    {
        try {
            $notificationId = $request->input('notification_id');
            
            if ($notificationId === 'all') {
                // Mark all as read
                $count = Auth::user()->unreadNotifications()->update(['read_at' => now()]);
                
                return response()->json([
                    'success' => true,
                    'message' => "{$count} notifications marked as read",
                    'count' => $count
                ]);
            } else {
                // Mark single notification as read
                $notification = Auth::user()->notifications()->find($notificationId);
                
                if (!$notification) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Notification not found'
                    ], 404);
                }
                
                if (!$notification->read_at) {
                    $notification->markAsRead();
                }
                
                return response()->json([
                    'success' => true,
                    'message' => 'Notification marked as read'
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Failed to mark client notification as read: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark notification as read'
            ], 500);
        }
    }

    /**
     * Test client notifications.
     */
    public function testNotification(): JsonResponse
    {
        try {
            $user = Auth::user();
            
            $success = $this->notificationService->send('user.welcome', $user, $user);

            return response()->json([
                'success' => $success,
                'message' => $success 
                    ? 'Test notification sent successfully! Check your email and notifications.' 
                    : 'Failed to send test notification'
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send client test notification: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to send test notification: ' . $e->getMessage()
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
    protected function formatActivitiesData($activities): array
    {
        return collect($activities)->map(function ($activity) {
            // Ensure semua required keys ada dengan default values
            return [
                'type' => $activity['type'] ?? 'unknown',
                'icon' => $activity['icon'] ?? 'folder',
                'color' => $activity['color'] ?? 'gray',
                'title' => $activity['title'] ?? 'Unknown Activity',
                'description' => $activity['description'] ?? '',
                'date' => $activity['date'] ?? now(),
                'url' => $activity['url'] ?? '#',
                'formatted_time' => isset($activity['date']) && $activity['date']
                    ? $activity['date']->diffForHumans()
                    : 'Recently',
            ];
        })->toArray();
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
        $iconMap = [
            'project.created' => 'folder',
            'project.updated' => 'folder',
            'project.completed' => 'folder',
            'project.overdue' => 'exclamation-triangle',
            'project.deadline_approaching' => 'exclamation-triangle',
            'quotation.created' => 'document-text',
            'quotation.approved' => 'document-text',
            'quotation.rejected' => 'document-text',
            'message.created' => 'mail',
            'message.reply' => 'mail',
            'message.urgent' => 'mail',
            'chat.session_started' => 'chat',
            'chat.message_received' => 'chat',
            'user.welcome' => 'user',
            'user.email_verified' => 'user',
            'system.maintenance' => 'cog',
            'system.alert' => 'cog',
            'testimonial.created' => 'star',
        ];

        return $iconMap[$type] ?? 'bell';
    }

    /**
     * Get notification color.
     */
    protected function getNotificationColor(string $type): string
    {
        return match(true) {
            str_contains($type, 'completed') => 'green',
            str_contains($type, 'overdue') || str_contains($type, 'urgent') => 'red',
            str_contains($type, 'deadline') => 'yellow',
            str_contains($type, 'approved') => 'green',
            str_contains($type, 'rejected') => 'red',
            str_contains($type, 'created') || str_contains($type, 'pending') => 'blue',
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

    protected function getMessageStatusColor($message): string
    {
        if ($message->priority === 'urgent') {
            return 'red';
        }

        return match (true) {
            $message->is_read && $message->is_replied => 'green',
            $message->is_read && !$message->is_replied => 'blue',
            !$message->is_read => 'yellow',
            default => 'gray'
        };
    }

    protected function formatProjectStatus($status): string
    {
        return match ($status) {
            'in_progress' => 'In Progress',
            'on_hold' => 'On Hold',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
            'planning' => 'Planning',
            default => ucfirst($status)
        };
    }

    protected function formatQuotationStatus($status): string
    {
        return match ($status) {
            'pending' => 'Pending Review',
            'reviewed' => 'Under Review',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            default => ucfirst($status)
        };
    }


}