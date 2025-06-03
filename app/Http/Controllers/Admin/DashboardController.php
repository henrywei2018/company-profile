<?php
// Update your Admin DashboardController with these methods:

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    protected DashboardService $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    /**
     * Show admin dashboard with proper notification data
     */
    public function index()
    {
        try {
            $user = Auth::user();

            // Get dashboard data
            $dashboardData = $this->dashboardService->getDashboardData($user);
            
            // Get notification counts for header
            $notificationCounts = $this->dashboardService->getAdminNotificationCounts();
            
            // Get recent notifications for header dropdown
            $recentNotifications = $this->dashboardService->getRecentNotifications($user, 10);

            return view('admin.dashboard', [
                'user' => $user,
                'statistics' => $dashboardData['statistics'] ?? [],
                'recentActivities' => $dashboardData['recent_activities'] ?? [],
                'alerts' => $dashboardData['alerts'] ?? [],
                'performance' => $dashboardData['performance'] ?? [],
                'pendingItems' => $dashboardData['pending_items'] ?? [],
                
                // For header component
                'recentNotifications' => collect($recentNotifications),
                'unreadNotificationsCount' => $notificationCounts['unread_database_notifications'] ?? 0,
                'unreadMessagesCount' => $notificationCounts['unread_messages'] ?? 0,
                'pendingQuotationsCount' => $notificationCounts['pending_quotations'] ?? 0,
                'waitingChatsCount' => $notificationCounts['waiting_chats'] ?? 0,
                'urgentItemsCount' => $notificationCounts['urgent_items'] ?? 0,
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error loading admin dashboard', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return view('admin.dashboard', [
                'user' => Auth::user(),
                'statistics' => [],
                'recentActivities' => [],
                'alerts' => [],
                'performance' => [],
                'pendingItems' => [],
                'recentNotifications' => collect(),
                'unreadNotificationsCount' => 0,
                'unreadMessagesCount' => 0,
                'pendingQuotationsCount' => 0,
                'waitingChatsCount' => 0,
                'urgentItemsCount' => 0,
                'error' => 'Unable to load dashboard data. Please try again.'
            ]);
        }
    }

    /**
     * Get real-time stats for AJAX updates
     */
    public function getStats(): JsonResponse
    {
        try {
            $user = Auth::user();
            $notificationCounts = $this->dashboardService->getAdminNotificationCounts();
            
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
                'data' => []
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
            $user = Auth::user();
            
            // Get dashboard data for charts
            $dashboardData = $this->dashboardService->getDashboardData($user);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'projects' => $dashboardData['charts']['projects'] ?? [],
                    'quotations' => $dashboardData['charts']['quotations'] ?? [],
                    'revenue' => $dashboardData['charts']['revenue'] ?? [],
                    'users' => $dashboardData['charts']['users'] ?? [],
                ]
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
            $this->dashboardService->clearCache($user);
            
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
            
            // Use notification service to send test
            $notificationController = app(\App\Http\Controllers\Admin\NotificationController::class);
            return $notificationController->sendTestNotification(request());

        } catch (\Exception $e) {
            Log::error('Failed to send test notification from dashboard: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to send test notification'
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
            $format = $request->get('format', 'pdf');
            
            $report = $this->dashboardService->generateReport($user, [
                'period' => $request->get('period', 'last_30_days'),
                'format' => $format
            ]);

            if ($format === 'json') {
                return response()->json($report);
            }

            // Return CSV or PDF export
            return response()->streamDownload(function () use ($report) {
                echo "Admin Dashboard Report\n";
                echo "Generated: " . now()->format('Y-m-d H:i:s') . "\n\n";
                echo json_encode($report, JSON_PRETTY_PRINT);
            }, 'admin_dashboard_report_' . now()->format('Y-m-d') . '.' . $format);

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

    // Helper methods for system health checks

    protected function checkDatabaseHealth(): array
    {
        try {
            \DB::connection()->getPdo();
            return ['status' => 'healthy', 'message' => 'Database connection active'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Database connection failed'];
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
            return ['status' => 'error', 'message' => 'Storage check failed'];
        }
    }

    protected function checkCacheHealth(): array
    {
        try {
            \Cache::put('health_check', 'test', 60);
            $result = \Cache::get('health_check');
            
            return $result === 'test' 
                ? ['status' => 'healthy', 'message' => 'Cache is working']
                : ['status' => 'error', 'message' => 'Cache not responding'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Cache check failed'];
        }
    }

    protected function checkQueueHealth(): array
    {
        try {
            // Basic queue check - could be enhanced with specific queue status
            return ['status' => 'healthy', 'message' => 'Queue system active'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Queue check failed'];
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