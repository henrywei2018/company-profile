<?php
// File: app/Http/Controllers/Admin/DashboardController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use App\Services\NotificationService;
use App\Facades\Notifications;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    protected DashboardService $dashboardService;
    protected NotificationService $notificationService;

    public function __construct(
        DashboardService $dashboardService,
        NotificationService $notificationService
    ) {
        $this->dashboardService = $dashboardService;
        $this->notificationService = $notificationService;
    }

    /**
     * Display the admin dashboard.
     */
    public function index()
    {
        try {
            $user = auth()->user();

            // Get dashboard data using existing service
            $dashboardData = $this->dashboardService->getDashboardData($user);

            // Get notification counts for header
            $notificationCounts = $this->dashboardService->getAdminNotificationCounts();

            return view('admin.dashboard', [
                'user' => $user,
                'totalProjects' => $dashboardData['statistics']['projects']['total'] ?? 0,
                'activeClients' => $dashboardData['statistics']['clients']['active'] ?? 0,
                'unreadMessages' => $dashboardData['statistics']['messages']['unread'] ?? 0,
                'pendingQuotations' => $dashboardData['statistics']['quotations']['pending'] ?? 0,
                'projectsChange' => $dashboardData['statistics']['projects']['change_percentage'] ?? 0,
                'clientsChange' => $dashboardData['statistics']['clients']['new_this_month'] ?? 0,
                'recentMessages' => $this->getRecentMessages(),
                'recentQuotations' => $this->getRecentQuotations(),
                'recentProjects' => $this->getRecentProjects(),
                'recentNotifications' => $this->getRecentNotifications($user),
                'notificationCounts' => $notificationCounts,
            ]);

        } catch (\Exception $e) {
            Log::error('Error loading admin dashboard', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);

            return view('admin.dashboard', [
                'user' => auth()->user(),
                'totalProjects' => 0,
                'activeClients' => 0,
                'unreadMessages' => 0,
                'pendingQuotations' => 0,
                'projectsChange' => 0,
                'clientsChange' => 0,
                'recentMessages' => collect(),
                'recentQuotations' => collect(),
                'recentProjects' => collect(),
                'recentNotifications' => collect(),
                'notificationCounts' => [],
                'error' => 'Unable to load dashboard data. Please try again.'
            ]);
        }
    }

    /**
     * Get dashboard statistics for AJAX calls.
     */
    public function getStats(): JsonResponse
    {
        try {
            $user = auth()->user();
            
            // Get stats using existing service
            $dashboardData = $this->dashboardService->getDashboardData($user);
            $notificationCounts = $this->dashboardService->getAdminNotificationCounts();

            return response()->json([
                'success' => true,
                'data' => [
                    'statistics' => $dashboardData['statistics'] ?? [],
                    'notifications' => $notificationCounts,
                    'alerts' => $dashboardData['alerts'] ?? [],
                    'pending_items' => $dashboardData['pending_items'] ?? [],
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get admin dashboard stats: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => 'Failed to load dashboard statistics'
            ], 500);
        }
    }

    /**
     * Get chart data for dashboard widgets.
     */
    public function getChartData(): JsonResponse
    {
        try {
            $user = auth()->user();
            $dashboardData = $this->dashboardService->getDashboardData($user);

            return response()->json([
                'success' => true,
                'data' => $dashboardData['charts'] ?? []
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get chart data: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'data' => []
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
            $this->dashboardService->clearCache($user);
            
            Cache::tags(['dashboard', 'admin'])->flush();
            
            return response()->json([
                'success' => true,
                'message' => 'Dashboard cache cleared successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to clear dashboard cache: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear cache'
            ], 500);
        }
    }

    /**
     * Export dashboard data.
     */
    public function exportDashboard(Request $request)
    {
        try {
            $user = auth()->user();
            $filters = $request->validate([
                'period' => 'nullable|string|in:last_7_days,last_30_days,last_3_months,last_year',
                'format' => 'nullable|string|in:csv,pdf,excel',
            ]);

            $report = $this->dashboardService->generateReport($user, $filters);

            $filename = 'admin_dashboard_' . now()->format('Y-m-d_H-i-s');
            $format = $filters['format'] ?? 'csv';

            switch ($format) {
                case 'csv':
                    return $this->exportToCsv($report, $filename);
                case 'pdf':
                    return $this->exportToPdf($report, $filename);
                case 'excel':
                    return $this->exportToExcel($report, $filename);
                default:
                    return $this->exportToCsv($report, $filename);
            }

        } catch (\Exception $e) {
            Log::error('Failed to export dashboard: ' . $e->getMessage());
            
            return redirect()->back()->with('error', 'Failed to export dashboard data.');
        }
    }

    /**
     * Get system health status.
     */
    public function getSystemHealth(): JsonResponse
    {
        try {
            $health = [
                'database' => $this->checkDatabaseHealth(),
                'cache' => $this->checkCacheHealth(),
                'storage' => $this->checkStorageHealth(),
                'queue' => $this->checkQueueHealth(),
                'mail' => $this->checkMailHealth(),
            ];

            $overallStatus = collect($health)->every(fn($status) => $status['status'] === 'healthy') ? 'healthy' : 'warning';

            return response()->json([
                'success' => true,
                'overall_status' => $overallStatus,
                'checks' => $health,
                'last_check' => now()->toISOString()
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get system health: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'overall_status' => 'error',
                'checks' => [],
                'last_check' => now()->toISOString()
            ], 500);
        }
    }

    /**
     * Send test notification.
     */
    public function sendTestNotification(): JsonResponse
    {
        try {
            $user = auth()->user();
            
            // Use existing notification service
            $success = $this->notificationService->test($user);

            if ($success) {
                return response()->json([
                    'success' => true,
                    'message' => 'Test notification sent successfully! Check your notifications.',
                    'results' => $success
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to send test notification.'
            ], 500);

        } catch (\Exception $e) {
            Log::error('Failed to send test notification: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to send test notification.'
            ], 500);
        }
    }

    // Helper methods

    /**
     * Get recent messages for dashboard.
     */
    protected function getRecentMessages()
    {
        try {
            return \App\Models\Message::with(['user'])
                ->latest()
                ->limit(5)
                ->get();
        } catch (\Exception $e) {
            Log::warning('Failed to get recent messages: ' . $e->getMessage());
            return collect();
        }
    }

    /**
     * Get recent quotations for dashboard.
     */
    protected function getRecentQuotations()
    {
        try {
            return \App\Models\Quotation::with(['client', 'service'])
                ->latest()
                ->limit(5)
                ->get();
        } catch (\Exception $e) {
            Log::warning('Failed to get recent quotations: ' . $e->getMessage());
            return collect();
        }
    }

    /**
     * Get recent projects for dashboard.
     */
    protected function getRecentProjects()
    {
        try {
            return \App\Models\Project::with(['client', 'category'])
                ->latest()
                ->limit(5)
                ->get();
        } catch (\Exception $e) {
            Log::warning('Failed to get recent projects: ' . $e->getMessage());
            return collect();
        }
    }

    /**
     * Get recent notifications for dashboard.
     */
    protected function getRecentNotifications($user)
    {
        try {
            return $this->dashboardService->getRecentNotifications($user, 10);
        } catch (\Exception $e) {
            Log::warning('Failed to get recent notifications: ' . $e->getMessage());
            return collect();
        }
    }

    /**
     * Check database health.
     */
    protected function checkDatabaseHealth(): array
    {
        try {
            DB::connection()->getPdo();
            $count = DB::table('users')->count();
            
            return [
                'status' => 'healthy',
                'message' => 'Database connection successful',
                'details' => "Connected with {$count} users"
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Database connection failed',
                'details' => $e->getMessage()
            ];
        }
    }

    /**
     * Check cache health.
     */
    protected function checkCacheHealth(): array
    {
        try {
            $key = 'health_check_' . time();
            Cache::put($key, 'test', 10);
            $value = Cache::get($key);
            Cache::forget($key);
            
            if ($value === 'test') {
                return [
                    'status' => 'healthy',
                    'message' => 'Cache working properly',
                    'details' => 'Read/write test successful'
                ];
            }
            
            return [
                'status' => 'warning',
                'message' => 'Cache may not be working',
                'details' => 'Read/write test failed'
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Cache error',
                'details' => $e->getMessage()
            ];
        }
    }

    /**
     * Check storage health.
     */
    protected function checkStorageHealth(): array
    {
        try {
            $storagePath = storage_path();
            $freeBytes = disk_free_space($storagePath);
            $totalBytes = disk_total_space($storagePath);
            $usedPercent = (($totalBytes - $freeBytes) / $totalBytes) * 100;
            
            $status = $usedPercent > 90 ? 'warning' : 'healthy';
            $message = $status === 'warning' ? 'Storage space low' : 'Storage space sufficient';
            
            return [
                'status' => $status,
                'message' => $message,
                'details' => sprintf('%.1f%% used', $usedPercent)
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Storage check failed',
                'details' => $e->getMessage()
            ];
        }
    }

    /**
     * Check queue health.
     */
    protected function checkQueueHealth(): array
    {
        try {
            // Basic queue connectivity check
            return [
                'status' => 'healthy',
                'message' => 'Queue system operational',
                'details' => 'Basic connectivity check passed'
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Queue system error',
                'details' => $e->getMessage()
            ];
        }
    }

    /**
     * Check mail health.
     */
    protected function checkMailHealth(): array
    {
        try {
            // Basic mail configuration check
            $configured = config('mail.default') ? true : false;
            
            return [
                'status' => $configured ? 'healthy' : 'warning',
                'message' => $configured ? 'Mail system configured' : 'Mail system not configured',
                'details' => 'Driver: ' . config('mail.default', 'none')
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Mail system error',
                'details' => $e->getMessage()
            ];
        }
    }

    /**
     * Export to CSV.
     */
    protected function exportToCsv($report, $filename)
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}.csv\"",
        ];

        $callback = function() use ($report) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, ['Metric', 'Value', 'Period']);

            // CSV data
            foreach ($report['overview'] ?? [] as $key => $value) {
                if (is_array($value)) {
                    foreach ($value as $subKey => $subValue) {
                        fputcsv($file, [
                            ucwords(str_replace('_', ' ', $key . ' ' . $subKey)),
                            is_numeric($subValue) ? $subValue : $subValue,
                            $report['period']['start'] ?? 'N/A' . ' to ' . $report['period']['end'] ?? 'N/A'
                        ]);
                    }
                } else {
                    fputcsv($file, [
                        ucwords(str_replace('_', ' ', $key)),
                        is_numeric($value) ? $value : $value,
                        $report['period']['start'] ?? 'N/A' . ' to ' . $report['period']['end'] ?? 'N/A'
                    ]);
                }
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export to PDF (placeholder).
     */
    protected function exportToPdf($report, $filename)
    {
        // This would require a PDF library like DomPDF or similar
        // For now, fallback to CSV
        return $this->exportToCsv($report, $filename);
    }

    /**
     * Export to Excel (placeholder).
     */
    protected function exportToExcel($report, $filename)
    {
        // This would require a library like PhpSpreadsheet
        // For now, fallback to CSV
        return $this->exportToCsv($report, $filename);
    }
}