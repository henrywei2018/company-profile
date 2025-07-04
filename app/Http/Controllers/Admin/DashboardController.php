<?php
// File: app/Http/Controllers/Admin/DashboardController.php - CLEAN BUILD

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use App\Services\NotificationService;
use App\Services\GoogleAnalyticsService;
use App\Facades\Notifications;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Project;
use App\Models\Quotation;
use App\Models\Message;
use App\Models\Testimonial;
use App\Models\Service;
use App\Models\ChatSession;
use App\Models\Certification;

class DashboardController extends Controller
{
    protected DashboardService $dashboardService;
    protected NotificationService $notificationService;
    protected GoogleAnalyticsService $googleAnalyticsService;

    public function __construct(
        DashboardService $dashboardService,
        NotificationService $notificationService,
        GoogleAnalyticsService $googleAnalyticsService
    ) {
        $this->dashboardService = $dashboardService;
        $this->notificationService = $notificationService;
        $this->googleAnalyticsService = $googleAnalyticsService;
    }

    /**
     * Show admin dashboard with comprehensive error handling
     */
    public function index()
    {
        try {
        $user = Auth::user();

        if (!$user) {
            Log::warning('Dashboard access attempted without authentication', [
                'timestamp' => now()->toISOString(),
                'ip' => request()->ip(),
                // Optionally log headers/cookies for more trace
            ]);
            // Return a 401 response if not authenticated
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        // Log user info for debug
        Log::info('Admin dashboard accessed', [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'timestamp' => now()->toISOString(),
            'roles' => $user->roles->pluck('name'), // if using roles
        ]);

            // Get all dashboard data with error handling for each section
            $dashboardData = $this->getDashboardDataSafely($user);
            $notificationCounts = $this->getNotificationCountsSafely($user);
            $recentNotifications = $this->getRecentNotificationsSafely($user);
            
            // Use new GoogleAnalyticsService for analytics data
            $analytics = $this->googleAnalyticsService->getKPIDashboard(30);
            
            // Prepare view data with all required variables
            $viewData = array_merge($dashboardData, [
                'user' => $user,
                'enableCharts' => true, // Enable charts for admin dashboard
                
                // Notification data for header component
                'recentNotifications' => collect($recentNotifications),
                'unreadNotificationsCount' => $notificationCounts['unread_database_notifications'],
                'unreadMessagesCount' => $notificationCounts['unread_messages'],
                'pendingQuotationsCount' => $notificationCounts['pending_quotations'],
                'waitingChatsCount' => $notificationCounts['waiting_chats'],
                'urgentItemsCount' => $notificationCounts['urgent_items'],
                
                // Additional counts for dashboard display
                'notificationCounts' => $notificationCounts,
                'totalNotifications' => $notificationCounts['total_notifications'],
                
                // Google Analytics KPI data
                'analytics' => $analytics,
            ]);

            return view('admin.dashboard', $viewData);
            
        } catch (\Exception $e) {
            Log::error('Critical error loading admin dashboard', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Return dashboard with safe fallback data
            return $this->getFallbackDashboard($e->getMessage());
        }
    }

    /**
     * Get KPI dashboard data
     */
    public function getKPIDashboard(Request $request): JsonResponse
    {
        try {
            $period = $request->get('period', 30);
            $period = max(1, min(365, (int)$period)); // Validate period (1-365 days)
            
            $user = Auth::user();
            
            Log::info('KPI Dashboard accessed', [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'period' => $period,
                'timestamp' => now()->toISOString()
            ]);

            // Get KPI dashboard data from GoogleAnalyticsService
            $kpiData = $this->googleAnalyticsService->getKPIDashboard($period);
            
            return response()->json([
                'success' => true,
                'data' => $kpiData,
                'meta' => [
                    'period' => $period,
                    'user' => $user->name,
                    'generated_at' => now()->toISOString(),
                    'api_version' => '1.0'
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('KPI Dashboard API error: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'period' => $request->get('period'),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'KPI dashboard data temporarily unavailable',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error',
                'data' => $this->getEmptyKPIDashboard($request->get('period', 30))
            ], 500);
        }
    }

    /**
     * Get real-time KPI summary
     */
    public function getRealTimeKPISummary(): JsonResponse
    {
        try {
            $user = Auth::user();
            
            $realTimeData = $this->googleAnalyticsService->getRealTimeKPISummary();
            
            return response()->json([
                'success' => true,
                'data' => $realTimeData,
                'meta' => [
                    'type' => 'realtime',
                    'user' => $user->name,
                    'fetched_at' => now()->toISOString(),
                    'next_update' => now()->addMinutes(2)->toISOString()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Real-time KPI summary error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Real-time data temporarily unavailable',
                'error' => config('app.debug') ? $e->getMessage() : 'Service unavailable',
                'data' => [
                    'status' => 'error',
                    'today_vs_yesterday' => [],
                    'real_time_alerts' => []
                ]
            ], 500);
        }
    }

    /**
     * Get specific KPI category data
     */
    public function getKPICategory(Request $request, string $category): JsonResponse
    {
        try {
            $period = $request->get('period', 30);
            $period = max(1, min(365, (int)$period));
            
            $validCategories = [
                'overview', 'traffic', 'engagement', 'conversion', 
                'audience', 'acquisition', 'behavior', 'technical'
            ];
            
            if (!in_array($category, $validCategories)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid KPI category',
                    'valid_categories' => $validCategories
                ], 400);
            }
            
            $user = Auth::user();
            
            // Get specific category data
            $methodName = 'get' . ucfirst($category) . 'KPIs';
            
            if (!method_exists($this->googleAnalyticsService, $methodName)) {
                throw new \Exception("Method {$methodName} not found in GoogleAnalyticsService");
            }
            
            $categoryData = $this->googleAnalyticsService->$methodName($period);
            
            return response()->json([
                'success' => true,
                'data' => $categoryData,
                'meta' => [
                    'category' => $category,
                    'period' => $period,
                    'user' => $user->name,
                    'generated_at' => now()->toISOString()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error("KPI category '{$category}' error: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => "Failed to load {$category} KPIs",
                'error' => config('app.debug') ? $e->getMessage() : 'Data unavailable',
                'data' => []
            ], 500);
        }
    }

    /**
     * Refresh analytics data
     */
    public function refreshAnalytics(): JsonResponse
    {
        try {
            $user = Auth::user();
            
            // Clear analytics cache
            $this->googleAnalyticsService->clearKPICache();
            
            // Get fresh data
            $analytics = $this->googleAnalyticsService->getKPIDashboard(30);
            
            Log::info('Analytics data manually refreshed', [
                'user_id' => $user->id,
                'refreshed_at' => now()->toISOString(),
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Analytics data refreshed successfully!',
                'data' => [
                    'refreshed_at' => now()->toISOString(),
                    'next_auto_refresh' => now()->addMinutes(15)->toISOString(),
                    'analytics' => $analytics,
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Manual analytics refresh failed: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to refresh analytics data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get current analytics status and health
     */
    public function getAnalyticsStatus(): JsonResponse
    {
        try {
            $connectionTest = $this->googleAnalyticsService->testAnalyticsConnection();
            
            return response()->json([
                'success' => true,
                'status' => $connectionTest['status'] === 'connected' ? 'operational' : 'degraded',
                'data' => [
                    'connection' => $connectionTest,
                    'cache_info' => [
                        'last_refresh' => Cache::get('analytics.last_refresh', 'Unknown'),
                        'strategy' => 'Smart caching based on data type',
                    ],
                    'health' => [
                        'api_status' => $connectionTest['status'],
                        'last_error' => Cache::get('analytics.last_error', null),
                        'uptime' => '99.9%',
                    ]
                ],
                'timestamp' => now()->toISOString()
            ]);

        } catch (\Exception $e) {
            Log::error('Analytics status check failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'status' => 'error',
                'message' => 'Analytics status check failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Clear analytics cache manually
     */
    public function clearAnalyticsCache(): JsonResponse
    {
        try {
            $user = Auth::user();
            
            // Clear all analytics cache
            $this->googleAnalyticsService->clearKPICache();
            
            Log::info('Analytics cache manually cleared', [
                'user_id' => $user->id,
                'cleared_at' => now()->toISOString()
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Analytics cache cleared successfully!',
                'data' => [
                    'cleared_at' => now()->toISOString(),
                    'cache_keys_cleared' => 'All analytics cache',
                    'next_refresh' => 'Data will be fetched on next request'
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Analytics cache clear failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear analytics cache',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export KPI data in various formats
     */
    public function exportKPIData(Request $request): \Symfony\Component\HttpFoundation\StreamedResponse|JsonResponse
    {
        try {
            $format = $request->get('format', 'csv');
            $period = $request->get('period', 30);
            $categories = $request->get('categories', ['overview']);
            
            $validFormats = ['csv', 'excel', 'pdf', 'json'];
            $validCategories = [
                'overview', 'traffic', 'engagement', 'conversion', 
                'audience', 'acquisition', 'behavior', 'technical'
            ];
            
            if (!in_array($format, $validFormats)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid export format',
                    'valid_formats' => $validFormats
                ], 400);
            }
            
            $categories = array_intersect((array)$categories, $validCategories);
            if (empty($categories)) {
                $categories = ['overview'];
            }
            
            $user = Auth::user();
            
            // Get KPI data for export
            $exportData = [
                'exported_by' => $user->name,
                'exported_at' => now()->toISOString(),
                'period_days' => $period,
                'categories' => $categories,
                'data' => []
            ];
            
            foreach ($categories as $category) {
                $methodName = 'get' . ucfirst($category) . 'KPIs';
                if (method_exists($this->googleAnalyticsService, $methodName)) {
                    $exportData['data'][$category] = $this->googleAnalyticsService->$methodName($period);
                }
            }
            
            // Log export activity
            Log::info('KPI data exported', [
                'user_id' => $user->id,
                'format' => $format,
                'categories' => $categories,
                'period' => $period
            ]);
            
            return $this->generateExportResponse($exportData, $format, $period);

        } catch (\Exception $e) {
            Log::error('KPI export error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Export failed',
                'error' => config('app.debug') ? $e->getMessage() : 'Export service unavailable'
            ], 500);
        }
    }

    /**
     * Get KPI alerts for specific metrics
     */
    public function getKPIAlerts(Request $request): JsonResponse
    {
        try {
            $period = $request->get('period', 30);
            $severity = $request->get('severity'); // optional filter
            
            $user = Auth::user();
            
            $alerts = $this->googleAnalyticsService->getKPIAlerts($period);
            
            // Filter by severity if requested
            if ($severity && in_array($severity, ['critical', 'warning', 'info'])) {
                $alerts['alerts'] = array_filter($alerts['alerts'], function($alert) use ($severity) {
                    return $alert['severity'] === $severity;
                });
                $alerts['alert_count'] = count($alerts['alerts']);
            }
            
            return response()->json([
                'success' => true,
                'data' => $alerts,
                'meta' => [
                    'period' => $period,
                    'severity_filter' => $severity,
                    'user' => $user->name,
                    'generated_at' => now()->toISOString()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('KPI alerts error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to load KPI alerts',
                'error' => config('app.debug') ? $e->getMessage() : 'Alerts service unavailable',
                'data' => ['alerts' => [], 'alert_count' => 0]
            ], 500);
        }
    }

    /**
     * Show KPI dashboard page
     */
    public function showKPIDashboard()
    {
        try {
            $user = Auth::user();
            
            Log::info('KPI Dashboard page accessed', [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'timestamp' => now()->toISOString()
            ]);
            
            // Get initial KPI data for the page
            $initialData = $this->googleAnalyticsService->getKPIDashboard(30);
            
            return view('admin.kpi-dashboard', [
                'user' => $user,
                'initialData' => $initialData,
                'enableCharts' => true,
                'pageTitle' => 'KPI Analytics Dashboard',
                
                // Header notification data
                'recentNotifications' => $this->getRecentNotificationsSafely($user, 5),
                'unreadNotificationsCount' => $this->getUnreadNotificationsCountFallback($user),
                'unreadMessagesCount' => $this->getUnreadMessagesCountFallback(),
                'pendingQuotationsCount' => $this->getPendingQuotationsCountFallback(),
                'waitingChatsCount' => $this->getWaitingChatsCountFallback(),
                'urgentItemsCount' => $this->getUrgentItemsCountFallback(),
            ]);
            
        } catch (\Exception $e) {
            Log::error('KPI Dashboard page error: ' . $e->getMessage());
            
            return view('admin.kpi-dashboard', [
                'user' => Auth::user(),
                'initialData' => $this->getEmptyKPIDashboard(30),
                'enableCharts' => false,
                'pageTitle' => 'KPI Analytics Dashboard',
                'error' => 'KPI dashboard is temporarily unavailable',
                
                // Safe fallback data
                'recentNotifications' => collect([]),
                'unreadNotificationsCount' => 0,
                'unreadMessagesCount' => 0,
                'pendingQuotationsCount' => 0,
                'waitingChatsCount' => 0,
                'urgentItemsCount' => 0,
            ]);
        }
    }

    /**
     * Get analytics dashboard data with freshness info
     */
    public function getAnalyticsData(): JsonResponse
    {
        try {
            $dashboardData = $this->googleAnalyticsService->getKPIDashboard(30);
            
            return response()->json([
                'success' => true,
                'data' => $dashboardData,
                'meta' => [
                    'generated_at' => now()->toISOString(),
                    'cache_strategy' => 'Smart caching with period-based refresh',
                    'data_freshness' => $dashboardData['meta'] ?? [],
                    'api_version' => 'GA4 Data API v1',
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Analytics data API error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Analytics data temporarily unavailable',
                'error' => $e->getMessage(),
                'fallback_data' => $this->getEmptyKPIDashboard(30)
            ], 500);
        }
    }

    /**
     * Safely get dashboard data with individual error handling
     */
    protected function getDashboardDataSafely($user): array
    {
        $data = [
            'statistics' => [],
            'recentActivities' => [],
            'alerts' => [],
            'performance' => [],
            'pendingItems' => [],
        ];

        // Get statistics with error handling
        try {
            $fullDashboardData = $this->dashboardService->getDashboardData($user);
            $data = array_merge($data, $fullDashboardData);
        } catch (\Exception $e) {
            Log::warning('Failed to get full dashboard data, attempting individual sections', [
                'error' => $e->getMessage()
            ]);
            
            // Try to get individual sections
            $data['statistics'] = $this->getStatisticsSafely();
            $data['recentActivities'] = $this->getRecentActivitiesSafely();
            $data['alerts'] = $this->getAlertsSafely();
            $data['performance'] = $this->getPerformanceSafely();
            $data['pendingItems'] = $this->getPendingItemsSafely();
        }

        return $data;
    }

    /**
     * Safely get notification counts
     */
    protected function getNotificationCountsSafely($user): array
    {
        try {
            return $this->dashboardService->getAdminNotificationCounts();
        } catch (\Exception $e) {
            Log::warning('Failed to get notification counts, using fallback', [
                'error' => $e->getMessage()
            ]);
            
            return [
                'unread_database_notifications' => $this->getUnreadNotificationsCountFallback($user),
                'unread_messages' => $this->getUnreadMessagesCountFallback(),
                'pending_quotations' => $this->getPendingQuotationsCountFallback(),
                'overdue_projects' => $this->getOverdueProjectsCountFallback(),
                'waiting_chats' => $this->getWaitingChatsCountFallback(),
                'urgent_items' => $this->getUrgentItemsCountFallback(),
                'total_notifications' => $this->getTotalNotificationsCountFallback($user),
            ];
        }
    }

    /**
     * Safely get recent notifications
     */
    protected function getRecentNotificationsSafely($user, int $limit = 10): array
    {
        try {
            return $this->dashboardService->getRecentNotifications($user, $limit);
        } catch (\Exception $e) {
            Log::warning('Failed to get recent notifications', [
                'error' => $e->getMessage()
            ]);
            
            // Try direct database approach as fallback
            try {
                return $user->notifications()
                    ->orderBy('created_at', 'desc')
                    ->limit($limit)
                    ->get()
                    ->map(function ($notification) {
                        return [
                            'id' => $notification->id,
                            'type' => 'system.notification',
                            'title' => $notification->data['title'] ?? 'Notification',
                            'message' => $notification->data['message'] ?? '',
                            'url' => $notification->data['action_url'] ?? '#',
                            'created_at' => $notification->created_at,
                            'is_read' => !is_null($notification->read_at),
                            'formatted_time' => $notification->created_at->diffForHumans(),
                            'icon' => 'bell',
                            'color' => 'gray',
                            'category' => 'system',
                        ];
                    })
                    ->toArray();
            } catch (\Exception $e2) {
                Log::error('Fallback notification retrieval also failed', [
                    'error' => $e2->getMessage()
                ]);
                return [];
            }
        }
    }

    /**
     * Get real-time stats for AJAX updates
     */
    public function getStats(): JsonResponse
{
    try {
        $user = Auth::user();
        
        // Get fresh statistics
        $stats = $this->getAdminStatistics();
        
        // Additional real-time calculations
        $stats['active_users'] = User::where('last_login_at', '>=', now()->subDays(7))->count();
        $stats['active_projects'] = Project::where('status', 'active')->count();
        $stats['pending_quotations'] = Quotation::where('status', 'pending')->count();
        $stats['unread_messages'] = Message::where('is_read', false)
            ->count();
        
        // Log the stats request
        Log::info('Dashboard stats requested', [
            'user_id' => $user->id,
            'stats' => $stats
        ]);
        
        return response()->json([
            'success' => true,
            'data' => $stats,
            'timestamp' => now()->toISOString(),
            'cached' => false
        ]);
        
    } catch (\Exception $e) {
        Log::error('Failed to get dashboard stats: ' . $e->getMessage());
        
        return response()->json([
            'success' => false,
            'message' => 'Unable to fetch statistics',
            'error' => $e->getMessage()
        ], 500);
    }
}

/**
 * Get admin statistics with caching
 */
protected function getAdminStatistics(): array
{
    $cacheKey = 'admin_stats_' . Auth::id();
    
    return Cache::remember($cacheKey, 300, function () {
        return [
            'users_count' => User::count(),
            'projects_count' => Project::count(),
            'quotations_count' => Quotation::count(),
            'messages_count' => Message::count(),
            'testimonials_count' => Testimonial::where('is_active', true)->count(),
            'services_count' => Service::where('is_active', true)->count(),
            'certifications_count' => Certification::count(),
            'chat_sessions_count' => ChatSession::count(),
            
            // Growth metrics
            'new_users_this_month' => User::where('created_at', '>=', now()->startOfMonth())->count(),
            'new_projects_this_month' => Project::where('created_at', '>=', now()->startOfMonth())->count(),
            'new_quotations_this_month' => Quotation::where('created_at', '>=', now()->startOfMonth())->count(),
            
            // Status breakdowns
            'project_status_breakdown' => Project::selectRaw('status, count(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray(),
            
            'quotation_status_breakdown' => Quotation::selectRaw('status, count(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray(),
        ];
    });
}


    /**
     * Clear dashboard cache
     */
    public function clearCache(): JsonResponse
    {
        try {
            $user = Auth::user();
            
            // Clear dashboard service cache
            $this->dashboardService->clearCache($user);
            
            // Clear additional caches
            Cache::forget("admin_stats_{$user->id}");
            Cache::forget("admin_dashboard_data_{$user->id}");
            
            Log::info('Admin dashboard cache cleared', ['user_id' => $user->id]);
            
            return response()->json([
                'success' => true,
                'message' => 'Dashboard cache cleared successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to clear admin cache: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear cache'
            ], 500);
        }
    }

    // Helper methods for safe data retrieval and fallbacks
    
    protected function getStatisticsSafely(): array
    {
        try {
            return [
                'projects' => [
                    'total' => Project::count(),
                    'active' => Project::whereIn('status', ['in_progress', 'on_hold'])->count(),
                    'completed' => Project::where('status', 'completed')->count(),
                    'change_percentage' => 0,
                ],
                'quotations' => [
                    'total' => Quotation::count(),
                    'pending' => Quotation::where('status', 'pending')->count(),
                    'approved' => Quotation::where('status', 'approved')->count(),
                    'conversion_rate' => 0,
                ],
                'clients' => [
                    'total' => User::role('client')->count(),
                    'active' => User::role('client')->where('is_active', true)->count(),
                    'verified' => User::role('client')->whereNotNull('email_verified_at')->count(),
                ],
                'messages' => [
                    'total' => Message::count(),
                    'unread' => Message::where('is_read', false)->count(),
                    'urgent' => Message::where('priority', 'urgent')->where('is_read', false)->count(),
                ],
            ];
        } catch (\Exception $e) {
            Log::warning('Failed to get statistics safely', ['error' => $e->getMessage()]);
            return [
                'projects' => ['total' => 0, 'active' => 0, 'completed' => 0, 'change_percentage' => 0],
                'quotations' => ['total' => 0, 'pending' => 0, 'approved' => 0, 'conversion_rate' => 0],
                'clients' => ['total' => 0, 'active' => 0, 'verified' => 0],
                'messages' => ['total' => 0, 'unread' => 0, 'urgent' => 0],
            ];
        }
    }

    protected function getRecentActivitiesSafely(): array
    {
        try {
            $activities = [];
            
            // Recent projects
            $recentProjects = Project::with(['client'])
                ->latest()
                ->limit(3)
                ->get()
                ->map(function ($project) {
                    return [
                        'type' => 'project',
                        'action' => 'created',
                        'title' => $project->title,
                        'user' => $project->client->name ?? 'Unknown',
                        'date' => $project->created_at,
                        'url' => route('admin.projects.show', $project),
                        'icon' => 'folder',
                        'color' => 'blue',
                    ];
                });

            return [
                'recent_projects' => $recentProjects->toArray(),
                'recent_quotations' => [],
                'recent_messages' => [],
            ];

        } catch (\Exception $e) {
            Log::warning('Failed to get recent activities safely', ['error' => $e->getMessage()]);
            return [
                'recent_projects' => [],
                'recent_quotations' => [],
                'recent_messages' => [],
            ];
        }
    }

    protected function getAlertsSafely(): array
    {
        try {
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
            ];
        } catch (\Exception $e) {
            Log::warning('Failed to get alerts safely', ['error' => $e->getMessage()]);
            return [
                'overdue_projects' => 0,
                'pending_quotations' => 0,
                'urgent_messages' => 0,
                'waiting_chats' => 0,
            ];
        }
    }

    protected function getPerformanceSafely(): array
    {
        try {
            return [
                'memory_usage' => 45,
                'disk_usage' => 62,
                'cpu_usage' => 25,
                'uptime' => '99.9%',
                'last_backup' => now()->subHours(6),
                'response_time' => 'good',
            ];
        } catch (\Exception $e) {
            return [
                'memory_usage' => 50,
                'disk_usage' => 60,
                'cpu_usage' => 25,
                'uptime' => '99.9%',
                'last_backup' => now()->subHours(6),
                'response_time' => 'unknown',
            ];
        }
    }

    protected function getPendingItemsSafely(): array
    {
        try {
            return [
                'pending_quotations' => Quotation::where('status', 'pending')->count(),
                'unread_messages' => Message::where('is_read', false)->count(),
                'overdue_projects' => Project::where('status', 'in_progress')
                    ->where('end_date', '<', now())
                    ->whereNotNull('end_date')
                    ->count(),
                'waiting_chats' => ChatSession::where('status', 'waiting')->count(),
            ];
        } catch (\Exception $e) {
            Log::warning('Failed to get pending items safely', ['error' => $e->getMessage()]);
            return [
                'pending_quotations' => 0,
                'unread_messages' => 0,
                'overdue_projects' => 0,
                'waiting_chats' => 0,
            ];
        }
    }

    /**
     * Get fallback dashboard when everything fails
     */
    protected function getFallbackDashboard(string $errorMessage): \Illuminate\View\View
    {
        $user = Auth::user();
        
        return view('admin.dashboard', [
            'user' => $user,
            'enableCharts' => false, // Disable charts in fallback mode
            'error' => 'Some dashboard features are temporarily unavailable. Please try refreshing the page.',
            
            // Safe fallback data
            'statistics' => $this->getFallbackStatistics(),
            'recentActivities' => [],
            'alerts' => [],
            'performance' => $this->getFallbackPerformance(),
            'pendingItems' => [],
            
            // Notification data for header component
            'recentNotifications' => collect([]),
            'unreadNotificationsCount' => 0,
            'unreadMessagesCount' => 0,
            'pendingQuotationsCount' => 0,
            'waitingChatsCount' => 0,
            'urgentItemsCount' => 0,
            'notificationCounts' => [],
            'totalNotifications' => 0,
            
            // Empty analytics data
            'analytics' => $this->getEmptyKPIDashboard(30),
        ]);
    }

    /**
     * Fallback count methods
     */
    protected function getUnreadNotificationsCountFallback($user): int
    {
        try {
            return $user->unreadNotifications()->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    protected function getUnreadMessagesCountFallback(): int
    {
        try {
            return Message::where('is_read', false)->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    protected function getPendingQuotationsCountFallback(): int
    {
        try {
            return Quotation::where('status', 'pending')->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    protected function getOverdueProjectsCountFallback(): int
    {
        try {
            return Project::where('status', 'in_progress')
                ->where('end_date', '<', now())
                ->whereNotNull('end_date')
                ->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    protected function getWaitingChatsCountFallback(): int
    {
        try {
            return ChatSession::where('status', 'waiting')->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    protected function getUrgentItemsCountFallback(): int
    {
        try {
            return Message::where('priority', 'urgent')
                ->where('is_read', false)
                ->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    protected function getTotalNotificationsCountFallback($user): int
    {
        try {
            return $user->notifications()->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Fallback data structures
     */
    protected function getFallbackStatistics(): array
    {
        return [
            'projects' => ['total' => 0, 'active' => 0, 'completed' => 0, 'change_percentage' => 0],
            'quotations' => ['total' => 0, 'pending' => 0, 'approved' => 0, 'conversion_rate' => 0],
            'clients' => ['total' => 0, 'active' => 0, 'verified' => 0],
            'messages' => ['total' => 0, 'unread' => 0, 'urgent' => 0],
        ];
    }

    protected function getFallbackPerformance(): array
    {
        return [
            'memory_usage' => 50,
            'disk_usage' => 60,
            'cpu_usage' => 25,
            'uptime' => '99.9%',
            'last_backup' => now()->subHours(6),
            'response_time' => 'unknown',
        ];
    }

    /**
     * Generate export response based on format
     */
    protected function generateExportResponse(array $data, string $format, int $period): \Symfony\Component\HttpFoundation\StreamedResponse|JsonResponse
    {
        $filename = 'kpi-analytics-' . now()->format('Y-m-d_H-i-s');
        
        switch ($format) {
            case 'csv':
                return response()->streamDownload(function () use ($data) {
                    $this->generateCSVExport($data);
                }, $filename . '.csv', ['Content-Type' => 'text/csv']);
                
            case 'excel':
                return response()->streamDownload(function () use ($data) {
                    $this->generateExcelExport($data);
                }, $filename . '.xlsx', ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']);
                
            case 'pdf':
                return response()->streamDownload(function () use ($data) {
                    $this->generatePDFExport($data);
                }, $filename . '.pdf', ['Content-Type' => 'application/pdf']);
                
            case 'json':
            default:
                return response()->json([
                    'success' => true,
                    'data' => $data,
                    'export_info' => [
                        'format' => 'json',
                        'generated_at' => now()->toISOString(),
                        'filename' => $filename . '.json'
                    ]
                ]);
        }
    }

    /**
     * Generate CSV export
     */
    protected function generateCSVExport(array $data): void
    {
        $output = fopen('php://output', 'w');
        
        // Write header
        fputcsv($output, ['KPI Analytics Export']);
        fputcsv($output, ['Exported by: ' . $data['exported_by']]);
        fputcsv($output, ['Exported at: ' . $data['exported_at']]);
        fputcsv($output, ['Period: ' . $data['period_days'] . ' days']);
        fputcsv($output, []);
        
        // Write data for each category
        foreach ($data['data'] as $category => $categoryData) {
            fputcsv($output, [strtoupper($category) . ' KPIs']);
            fputcsv($output, []);
            
            // Flatten and write category data
            $this->writeArrayToCSV($output, $categoryData, ucfirst($category));
            fputcsv($output, []);
        }
        
        fclose($output);
    }

    /**
     * Generate Excel export (simplified)
     */
    protected function generateExcelExport(array $data): void
    {
        // For now, generate CSV format (implement proper Excel export with PhpSpreadsheet if needed)
        $this->generateCSVExport($data);
    }

    /**
     * Generate PDF export (simplified)
     */
    protected function generatePDFExport(array $data): void
    {
        // Simplified PDF export - implement with proper PDF library
        echo "KPI Analytics Report\n";
        echo "Exported by: " . $data['exported_by'] . "\n";
        echo "Exported at: " . $data['exported_at'] . "\n";
        echo "Period: " . $data['period_days'] . " days\n\n";
        
        foreach ($data['data'] as $category => $categoryData) {
            echo strtoupper($category) . " KPIs\n";
            echo str_repeat("-", 50) . "\n";
            print_r($categoryData);
            echo "\n\n";
        }
    }

    /**
     * Helper to write array data to CSV recursively
     */
    protected function writeArrayToCSV($output, array $data, string $prefix = ''): void
    {
        foreach ($data as $key => $value) {
            $fullKey = $prefix ? $prefix . '.' . $key : $key;
            
            if (is_array($value)) {
                if ($this->isAssociativeArray($value)) {
                    $this->writeArrayToCSV($output, $value, $fullKey);
                } else {
                    fputcsv($output, [$fullKey, implode(', ', $value)]);
                }
            } else {
                fputcsv($output, [$fullKey, $value]);
            }
        }
    }

    /**
     * Check if array is associative
     */
    protected function isAssociativeArray(array $array): bool
    {
        if (empty($array)) return false;
        return array_keys($array) !== range(0, count($array) - 1);
    }

    /**
     * Get empty KPI dashboard for error cases
     */
    protected function getEmptyKPIDashboard(int $period): array
    {
        return [
            'overview' => [],
            'traffic' => [],
            'engagement' => [],
            'conversion' => [],
            'audience' => [],
            'acquisition' => [],
            'behavior' => [],
            'technical' => [],
            'trends' => [],
            'alerts' => ['alerts' => [], 'alert_count' => 0],
            'meta' => [
                'period_days' => $period,
                'error' => true,
                'message' => 'KPI data temporarily unavailable'
            ]
        ];
    }

    public function getSystemHealth(): JsonResponse
{
    try {
        $user = Auth::user();
        
        // System health checks
        $health = [
            'status' => 'healthy',
            'database' => $this->checkDatabaseHealth(),
            'cache' => $this->checkCacheHealth(),
            'storage' => $this->checkStorageHealth(),
            'analytics' => $this->checkAnalyticsHealth(),
            'queue' => $this->checkQueueHealth(),
            'memory' => $this->getMemoryUsage(),
            'timestamp' => now()->toISOString()
        ];

        // Determine overall health status
        $issues = collect($health)->filter(function ($check) {
            return is_array($check) && isset($check['status']) && $check['status'] !== 'healthy';
        });

        if ($issues->count() > 0) {
            $health['status'] = $issues->contains('status', 'critical') ? 'critical' : 'warning';
        }

        return response()->json([
            'success' => true,
            'health' => $health,
            'summary' => [
                'total_checks' => 6,
                'healthy' => 6 - $issues->count(),
                'issues' => $issues->count(),
                'status' => $health['status']
            ]
        ]);

    } catch (\Exception $e) {
        Log::error('System health check failed: ' . $e->getMessage());
        
        return response()->json([
            'success' => false,
            'health' => [
                'status' => 'critical',
                'error' => 'Health check system failure',
                'timestamp' => now()->toISOString()
            ]
        ], 500);
    }
}

/**
 * Check database health
 */
private function checkDatabaseHealth(): array
{
    try {
        $start = microtime(true);
        DB::select('SELECT 1');
        $responseTime = round((microtime(true) - $start) * 1000, 2);
        
        return [
            'status' => 'healthy',
            'response_time' => $responseTime . 'ms',
            'connection' => 'active'
        ];
    } catch (\Exception $e) {
        return [
            'status' => 'critical',
            'error' => 'Database connection failed',
            'details' => $e->getMessage()
        ];
    }
}

/**
 * Check cache health
 */
private function checkCacheHealth(): array
{
    try {
        $testKey = 'health_check_' . time();
        $testValue = 'test_data';
        
        Cache::put($testKey, $testValue, 60);
        $retrieved = Cache::get($testKey);
        Cache::forget($testKey);
        
        if ($retrieved === $testValue) {
            return [
                'status' => 'healthy',
                'driver' => config('cache.default'),
                'operations' => 'write/read successful'
            ];
        } else {
            return [
                'status' => 'warning',
                'error' => 'Cache read/write mismatch'
            ];
        }
    } catch (\Exception $e) {
        return [
            'status' => 'critical',
            'error' => 'Cache system failure',
            'details' => $e->getMessage()
        ];
    }
}

/**
 * Check storage health
 */
private function checkStorageHealth(): array
{
    try {
        $storagePath = storage_path();
        $freeSpace = disk_free_space($storagePath);
        $totalSpace = disk_total_space($storagePath);
        $usedSpace = $totalSpace - $freeSpace;
        $usagePercent = round(($usedSpace / $totalSpace) * 100, 2);
        
        $status = 'healthy';
        if ($usagePercent > 90) $status = 'critical';
        elseif ($usagePercent > 80) $status = 'warning';
        
        return [
            'status' => $status,
            'free_space' => $this->formatBytes($freeSpace),
            'total_space' => $this->formatBytes($totalSpace),
            'usage_percent' => $usagePercent,
            'writable' => is_writable($storagePath)
        ];
    } catch (\Exception $e) {
        return [
            'status' => 'critical',
            'error' => 'Storage check failed',
            'details' => $e->getMessage()
        ];
    }
}

/**
 * Check analytics health
 */
private function checkAnalyticsHealth(): array
{
    try {
        $connectionTest = $this->googleAnalyticsService->testAnalyticsConnection();
        
        return [
            'status' => $connectionTest['status'] === 'connected' ? 'healthy' : 'warning',
            'connection' => $connectionTest['status'],
            'message' => $connectionTest['message'] ?? 'Unknown status',
            'last_check' => $connectionTest['timestamp'] ?? now()->toISOString()
        ];
    } catch (\Exception $e) {
        return [
            'status' => 'warning',
            'error' => 'Analytics check failed',
            'details' => $e->getMessage()
        ];
    }
}

/**
 * Check queue health
 */
private function checkQueueHealth(): array
{
    try {
        // Check if queue workers are running (basic check)
        $queueConnection = config('queue.default');
        
        return [
            'status' => 'healthy',
            'connection' => $queueConnection,
            'driver' => config("queue.connections.{$queueConnection}.driver"),
            'note' => 'Basic queue configuration check'
        ];
    } catch (\Exception $e) {
        return [
            'status' => 'warning',
            'error' => 'Queue check failed',
            'details' => $e->getMessage()
        ];
    }
}

/**
 * Get memory usage information
 */
private function getMemoryUsage(): array
{
    $memoryUsage = memory_get_usage(true);
    $memoryLimit = $this->parseMemoryLimit(ini_get('memory_limit'));
    $usagePercent = $memoryLimit > 0 ? round(($memoryUsage / $memoryLimit) * 100, 2) : 0;
    
    $status = 'healthy';
    if ($usagePercent > 90) $status = 'critical';
    elseif ($usagePercent > 80) $status = 'warning';
    
    return [
        'status' => $status,
        'current_usage' => $this->formatBytes($memoryUsage),
        'memory_limit' => $this->formatBytes($memoryLimit),
        'usage_percent' => $usagePercent,
        'peak_usage' => $this->formatBytes(memory_get_peak_usage(true))
    ];
}

/**
 * Format bytes to human readable format
 */
private function formatBytes(int $bytes, int $precision = 2): string
{
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    
    for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
        $bytes /= 1024;
    }
    
    return round($bytes, $precision) . ' ' . $units[$i];
}

/**
 * Parse memory limit string to bytes
 */
private function parseMemoryLimit(string $memoryLimit): int
{
    if ($memoryLimit === '-1') {
        return PHP_INT_MAX;
    }
    
    $unit = strtolower(substr($memoryLimit, -1));
    $value = (int) substr($memoryLimit, 0, -1);
    
    switch ($unit) {
        case 'g':
            $value *= 1024;
        case 'm':
            $value *= 1024;
        case 'k':
            $value *= 1024;
    }
    
    return $value;
}
}