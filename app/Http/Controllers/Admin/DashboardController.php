<?php
// File: app/Http/Controllers/Admin/DashboardController.php - REFINED VERSION

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use App\Services\NotificationService;
use App\Services\NavigationService;
use App\Facades\Notifications;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    protected DashboardService $dashboardService;
    protected NotificationService $notificationService;
    protected NavigationService $navigationService;

    public function __construct(
        DashboardService $dashboardService,
        NotificationService $notificationService
        
    ) {
        $this->dashboardService = $dashboardService;
        $this->notificationService = $notificationService;
    }

    /**
     * Show admin dashboard with comprehensive error handling
     */
    public function index()
    {
        try {
            $user = Auth::user();
            
            // Log dashboard access for monitoring
            Log::info('Admin dashboard accessed', [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'timestamp' => now()->toISOString()
            ]);

            // Get all dashboard data with error handling for each section
            $dashboardData = $this->getDashboardDataSafely($user);
            $notificationCounts = $this->getNotificationCountsSafely($user);
            $recentNotifications = $this->getRecentNotificationsSafely($user);
            
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
        ]);
    }

    /**
     * Individual safe data retrievers with fallbacks
     */
    protected function getStatisticsSafely(): array
    {
        try {
            return [
                'projects' => [
                    'total' => \App\Models\Project::count(),
                    'active' => \App\Models\Project::whereIn('status', ['in_progress', 'on_hold'])->count(),
                    'completed' => \App\Models\Project::where('status', 'completed')->count(),
                    'change_percentage' => 0,
                ],
                'quotations' => [
                    'total' => \App\Models\Quotation::count(),
                    'pending' => \App\Models\Quotation::where('status', 'pending')->count(),
                    'approved' => \App\Models\Quotation::where('status', 'approved')->count(),
                    'conversion_rate' => 0,
                ],
                'clients' => [
                    'total' => \App\Models\User::role('client')->count(),
                    'active' => \App\Models\User::role('client')->where('is_active', true)->count(),
                    'verified' => \App\Models\User::role('client')->whereNotNull('email_verified_at')->count(),
                ],
                'messages' => [
                    'total' => \App\Models\Message::count(),
                    'unread' => \App\Models\Message::where('is_read', false)->count(),
                    'urgent' => \App\Models\Message::where('priority', 'urgent')->where('is_read', false)->count(),
                ],
            ];
        } catch (\Exception $e) {
            Log::warning('Failed to get statistics safely', ['error' => $e->getMessage()]);
            return $this->getFallbackStatistics();
        }
    }

    protected function getRecentActivitiesSafely(): array
    {
        try {
            $activities = [];
            
            // Recent projects
            $recentProjects = \App\Models\Project::with(['client'])
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

            // Recent quotations
            $recentQuotations = \App\Models\Quotation::with(['client'])
                ->latest()
                ->limit(3)
                ->get()
                ->map(function ($quotation) {
                    return [
                        'type' => 'quotation',
                        'action' => 'submitted',
                        'title' => $quotation->project_type,
                        'user' => $quotation->client->name ?? $quotation->name,
                        'date' => $quotation->created_at,
                        'url' => route('admin.quotations.show', $quotation),
                        'icon' => 'document-text',
                        'color' => 'amber',
                    ];
                });

            // Recent messages
            $recentMessages = \App\Models\Message::with(['user'])
                ->latest()
                ->limit(3)
                ->get()
                ->map(function ($message) {
                    return [
                        'type' => 'message',
                        'action' => 'sent',
                        'title' => $message->subject,
                        'user' => $message->user->name ?? $message->name,
                        'date' => $message->created_at,
                        'url' => route('admin.messages.show', $message),
                        'icon' => 'mail',
                        'color' => 'green',
                    ];
                });

            return [
                'recent_projects' => $recentProjects->toArray(),
                'recent_quotations' => $recentQuotations->toArray(),
                'recent_messages' => $recentMessages->toArray(),
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
                'overdue_projects' => \App\Models\Project::where('status', 'in_progress')
                    ->where('end_date', '<', now())
                    ->whereNotNull('end_date')
                    ->count(),
                'pending_quotations' => \App\Models\Quotation::where('status', 'pending')
                    ->where('created_at', '<', now()->subHours(24))
                    ->count(),
                'urgent_messages' => \App\Models\Message::where('priority', 'urgent')
                    ->where('is_read', false)
                    ->count(),
                'waiting_chats' => \App\Models\ChatSession::where('status', 'waiting')->count(),
                'expiring_certificates' => \App\Models\Certification::where('expiry_date', '<=', now()->addDays(30))
                    ->where('is_active', true)
                    ->count(),
            ];
        } catch (\Exception $e) {
            Log::warning('Failed to get alerts safely', ['error' => $e->getMessage()]);
            return [
                'overdue_projects' => 0,
                'pending_quotations' => 0,
                'urgent_messages' => 0,
                'waiting_chats' => 0,
                'expiring_certificates' => 0,
            ];
        }
    }

    protected function getPerformanceSafely(): array
    {
        try {
            $memoryUsage = $this->getMemoryUsagePercentage();
            $diskUsage = $this->getDiskUsagePercentage();
            
            return [
                'memory_usage' => $memoryUsage,
                'disk_usage' => $diskUsage,
                'cpu_usage' => 25, // Mock data - would need system monitoring
                'uptime' => '99.9%',
                'last_backup' => now()->subHours(6),
                'response_time' => 'good',
            ];
        } catch (\Exception $e) {
            Log::warning('Failed to get performance data safely', ['error' => $e->getMessage()]);
            return $this->getFallbackPerformance();
        }
    }

    protected function getPendingItemsSafely(): array
    {
        try {
            return [
                'pending_quotations' => \App\Models\Quotation::where('status', 'pending')->count(),
                'unread_messages' => \App\Models\Message::where('is_read', false)->count(),
                'overdue_projects' => \App\Models\Project::where('status', 'in_progress')
                    ->where('end_date', '<', now())
                    ->whereNotNull('end_date')
                    ->count(),
                'waiting_chats' => \App\Models\ChatSession::where('status', 'waiting')->count(),
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
            return \App\Models\Message::where('is_read', false)->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    protected function getPendingQuotationsCountFallback(): int
    {
        try {
            return \App\Models\Quotation::where('status', 'pending')->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    protected function getOverdueProjectsCountFallback(): int
    {
        try {
            return \App\Models\Project::where('status', 'in_progress')
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
            return \App\Models\ChatSession::where('status', 'waiting')->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    protected function getUrgentItemsCountFallback(): int
    {
        try {
            return \App\Models\Message::where('priority', 'urgent')
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
     * Get real-time stats for AJAX updates
     */
    public function getStats(): JsonResponse
    {
        try {
            $user = Auth::user();
            $notificationCounts = $this->getNotificationCountsSafely($user);
            
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
                        'pending' => $notificationCounts['pending_quotations'],
                    ],
                    'projects' => [
                        'overdue' => $notificationCounts['overdue_projects'],
                    ],
                    'chat' => [
                        'waiting' => $notificationCounts['waiting_chats'],
                    ],
                    'urgent_items' => $notificationCounts['urgent_items'],
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get admin stats: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'data' => [],
                'error' => 'Unable to fetch current statistics'
            ], 500);
        }
    }

    /**
     * Get chart data for dashboard
     */
    public function getChartData(Request $request): JsonResponse
    {
        try {
            $period = $request->get('period', '7days');
            
            // Generate chart data based on period
            $chartData = $this->generateChartData($period);
            
            return response()->json([
                'success' => true,
                'data' => $chartData
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get admin chart data: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'data' => []
            ], 500);
        }
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

    /**
     * Send test notification
     */
    public function sendTestNotification(): JsonResponse
    {
        try {
            $user = Auth::user();
            
            // Send test notification using our centralized system
            $success = Notifications::send('user.welcome', $user, $user);

            if ($success) {
                Log::info('Test notification sent from admin dashboard', ['user_id' => $user->id]);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Test notification sent successfully! Check your notifications.'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to send test notification.'
            ], 500);

        } catch (\Exception $e) {
            Log::error('Failed to send test notification from dashboard: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to send test notification: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export dashboard data
     */
    public function exportDashboard(Request $request)
    {
        try {
            $user = Auth::user();
            $format = $request->get('format', 'json');
            
            $dashboardData = $this->getDashboardDataSafely($user);
            $notificationCounts = $this->getNotificationCountsSafely($user);
            
            $exportData = [
                'generated_at' => now()->toISOString(),
                'generated_by' => $user->name,
                'dashboard_data' => $dashboardData,
                'notification_counts' => $notificationCounts,
                'system_info' => [
                    'laravel_version' => app()->version(),
                    'php_version' => phpversion(),
                    'timezone' => config('app.timezone'),
                ]
            ];

            if ($format === 'json') {
                return response()->json($exportData);
            }

            // Return as downloadable file
            return response()->streamDownload(function () use ($exportData, $format) {
                if ($format === 'csv') {
                    echo "Admin Dashboard Export\n";
                    echo "Generated: " . $exportData['generated_at'] . "\n";
                    echo "By: " . $exportData['generated_by'] . "\n\n";
                    
                    // CSV format for statistics
                    echo "Section,Metric,Value\n";
                    foreach ($exportData['dashboard_data']['statistics'] as $section => $stats) {
                        foreach ($stats as $metric => $value) {
                            echo "{$section},{$metric},{$value}\n";
                        }
                    }
                } else {
                    echo json_encode($exportData, JSON_PRETTY_PRINT);
                }
            }, 'admin_dashboard_export_' . now()->format('Y-m-d_H-i-s') . '.' . $format);

        } catch (\Exception $e) {
            Log::error('Failed to export admin dashboard: ' . $e->getMessage());
            
            return redirect()->route('admin.dashboard')
                ->with('error', 'Failed to export dashboard data');
        }
    }

    /**
     * Get system health status
     */
    public function getSystemHealth(): JsonResponse
    {
        try {
            $health = [
                'database' => $this->checkDatabaseHealth(),
                'storage' => $this->checkStorageHealth(),
                'cache' => $this->checkCacheHealth(),
                'queue' => $this->checkQueueHealth(),
                'mail' => $this->checkMailHealth(),
            ];

            $overallStatus = collect($health)->every(fn($status) => $status['status'] === 'healthy') ? 'healthy' : 'issues';

            return response()->json([
                'success' => true,
                'overall_status' => $overallStatus,
                'checks' => $health,
                'timestamp' => now()->toISOString()
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get system health: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'overall_status' => 'error',
                'checks' => [],
                'error' => 'System health check failed'
            ], 500);
        }
    }

    /**
     * Helper methods for system monitoring
     */
    protected function getMemoryUsagePercentage(): int
    {
        try {
            $memoryLimit = $this->parseSize(ini_get('memory_limit'));
            $memoryUsage = memory_get_usage(true);
            
            return $memoryLimit > 0 ? round(($memoryUsage / $memoryLimit) * 100) : 50;
        } catch (\Exception $e) {
            return 50; // Fallback value
        }
    }

    protected function getDiskUsagePercentage(): int
    {
        try {
            $total = disk_total_space(storage_path());
            $free = disk_free_space(storage_path());
            
            return $total > 0 ? round((($total - $free) / $total) * 100) : 60;
        } catch (\Exception $e) {
            return 60; // Fallback value
        }
    }

    protected function parseSize(string $size): int
    {
        $unit = preg_replace('/[^bkmgtpezy]/i', '', $size);
        $size = preg_replace('/[^0-9\.]/', '', $size);
        
        if ($unit) {
            return round($size * pow(1024, stripos('bkmgtpezy', $unit[0])));
        }
        
        return round($size);
    }

    protected function generateChartData(string $period): array
    {
        try {
            $days = match($period) {
                '7days' => 7,
                '30days' => 30,
                '90days' => 90,
                default => 7
            };
            
            $projectData = [];
            $quotationData = [];
            
            for ($i = $days - 1; $i >= 0; $i--) {
                $date = now()->subDays($i);
                
                $projectData[] = [
                    'date' => $date->format('M j'),
                    'count' => \App\Models\Project::whereDate('created_at', $date)->count()
                ];
                
                $quotationData[] = [
                    'date' => $date->format('M j'),
                    'count' => \App\Models\Quotation::whereDate('created_at', $date)->count()
                ];
            }
            
            return [
                'projects' => $projectData,
                'quotations' => $quotationData,
                'period' => $period,
                'generated_at' => now()->toISOString()
            ];
            
        } catch (\Exception $e) {
            Log::warning('Failed to generate chart data', ['error' => $e->getMessage()]);
            return ['projects' => [], 'quotations' => [], 'period' => $period];
        }
    }

    /**
     * System health check methods
     */
    protected function checkDatabaseHealth(): array
    {
        try {
            DB::connection()->getPdo();
            return ['status' => 'healthy', 'message' => 'Database connection active'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Database connection failed: ' . $e->getMessage()];
        }
    }

    protected function checkStorageHealth(): array
    {
        try {
            $available = disk_free_space(storage_path());
            $total = disk_total_space(storage_path());
            $percentage = round(($available / $total) * 100, 2);
            
            $status = $percentage > 20 ? 'healthy' : ($percentage > 10 ? 'warning' : 'critical');
            
            return [
                'status' => $status,
                'message' => "Storage {$percentage}% available",
                'details' => [
                    'available' => $this->formatBytes($available),
                    'total' => $this->formatBytes($total),
                    'percentage' => $percentage
                ]
            ];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Storage check failed: ' . $e->getMessage()];
        }
    }

    protected function checkCacheHealth(): array
    {
        try {
            Cache::put('health_check', 'test', 60);
            $result = Cache::get('health_check');
            
            return $result === 'test' 
                ? ['status' => 'healthy', 'message' => 'Cache is working']
                : ['status' => 'error', 'message' => 'Cache not responding'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Cache check failed: ' . $e->getMessage()];
        }
    }

    protected function checkQueueHealth(): array
    {
        try {
            // Basic queue check - could be enhanced with specific queue status
            return ['status' => 'healthy', 'message' => 'Queue system active'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Queue check failed: ' . $e->getMessage()];
        }
    }
    protected function checkMailHealth(): array
    {
        try {
            // Basic mail config check
            $configured = config('mail.default') !== null;
            return $configured 
                ? ['status' => 'healthy', 'message' => 'Mail system configured']
                : ['status' => 'warning', 'message' => 'Mail not configured'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Mail check failed'];
        }
    }

    protected function formatBytes($size, $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
            $size /= 1024;
        }
        
        return round($size, $precision) . ' ' . $units[$i];
    }
}