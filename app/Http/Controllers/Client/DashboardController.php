<?php
// File: app/Http/Controllers/Client/DashboardController.php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use App\Services\ClientAccessService;
use App\Services\NotificationAlertService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    protected DashboardService $dashboardService;
    protected ClientAccessService $clientAccessService;
    protected NotificationAlertService $notificationService;

    public function __construct(
        DashboardService $dashboardService,
        ClientAccessService $clientAccessService,
        NotificationAlertService $notificationService
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
        $user = auth()->user();
        
        // Get comprehensive dashboard data
        $dashboardData = $this->dashboardService->getDashboardData($user);
        
        // Get notification summary
        $notificationSummary = $this->notificationService->getNotificationSummary($user);
        
        // Combine data for view
        $data = array_merge($dashboardData, [
            'notification_summary' => $notificationSummary,
            'user' => $user,
        ]);

        return view('client.dashboard', $data);
    }

    /**
     * Get real-time statistics for AJAX updates.
     */
    public function getRealtimeStats(): JsonResponse
    {
        $user = auth()->user();
        
        $stats = $this->dashboardService->getRealTimeStats($user);
        
        return response()->json([
            'success' => true,
            'data' => $stats,
            'timestamp' => now()->toISOString(),
        ]);
    }

    /**
     * Get chart data for dashboard widgets.
     */
    public function getChartData(Request $request): JsonResponse
    {
        $user = auth()->user();
        $chartType = $request->get('type', 'projects');
        $dateRange = $request->get('range', 'last_30_days');
        
        try {
            $chartData = $this->dashboardService->getChartData($user, $chartType, $dateRange);
            
            return response()->json([
                'success' => true,
                'data' => $chartData,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load chart data',
            ], 500);
        }
    }

    /**
     * Get performance metrics.
     */
    public function getPerformanceMetrics(): JsonResponse
    {
        $user = auth()->user();
        
        $dashboardData = $this->dashboardService->getDashboardData($user);
        $performance = $dashboardData['performance'] ?? [];
        
        return response()->json([
            'success' => true,
            'data' => $performance,
        ]);
    }

    /**
     * Get upcoming deadlines.
     */
    public function getUpcomingDeadlines(): JsonResponse
    {
        $user = auth()->user();
        
        $dashboardData = $this->dashboardService->getDashboardData($user);
        $deadlines = $dashboardData['upcoming_deadlines'] ?? [];
        
        return response()->json([
            'success' => true,
            'data' => $deadlines,
        ]);
    }

    /**
     * Get recent activities.
     */
    public function getRecentActivities(): JsonResponse
    {
        $user = auth()->user();
        
        $dashboardData = $this->dashboardService->getDashboardData($user);
        $activities = $dashboardData['recent_activities'] ?? [];
        
        return response()->json([
            'success' => true,
            'data' => $activities,
        ]);
    }

    /**
     * Generate and download client report.
     */
    public function generateReport(Request $request)
    {
        $user = auth()->user();
        
        $filters = $request->validate([
            'date_range' => 'string|in:today,this_week,this_month,last_month,last_30_days,last_90_days,custom',
            'start_date' => 'date|required_if:date_range,custom',
            'end_date' => 'date|required_if:date_range,custom|after_or_equal:start_date',
            'format' => 'string|in:pdf,excel,csv',
        ]);
        
        $reportData = $this->dashboardService->generateReport($user, $filters);
        
        return $this->downloadReport($reportData, $filters['format'] ?? 'pdf');
    }

    /**
     * Clear dashboard cache.
     */
    public function clearCache(): JsonResponse
    {
        $user = auth()->user();
        
        $this->dashboardService->clearCache($user);
        
        return response()->json([
            'success' => true,
            'message' => 'Dashboard cache cleared successfully',
        ]);
    }

    /**
     * Download report in specified format.
     */
    protected function downloadReport(array $reportData, string $format)
    {
        $filename = 'client_report_' . now()->format('Y-m-d_H-i-s');
        
        switch ($format) {
            case 'excel':
                return $this->downloadExcelReport($reportData, $filename);
            case 'csv':
                return $this->downloadCsvReport($reportData, $filename);
            default:
                return $this->downloadPdfReport($reportData, $filename);
        }
    }

    /**
     * Download CSV report.
     */
    protected function downloadCsvReport(array $reportData, string $filename)
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}.csv\"",
        ];

        $callback = function() use ($reportData) {
            $file = fopen('php://output', 'w');
            
            // Report header
            fputcsv($file, ['CLIENT PERFORMANCE REPORT']);
            fputcsv($file, ['Generated at', now()->format('Y-m-d H:i:s')]);
            fputcsv($file, ['Period', $reportData['period']['start'] . ' to ' . $reportData['period']['end']]);
            fputcsv($file, ['']);
            
            // Projects summary
            if (isset($reportData['projects'])) {
                fputcsv($file, ['PROJECTS SUMMARY']);
                fputcsv($file, ['Total Projects', $reportData['projects']['total']]);
                fputcsv($file, ['Completed Projects', $reportData['projects']['completed']]);
                fputcsv($file, ['Active Projects', $reportData['projects']['active']]);
                fputcsv($file, ['Project Value', number_format($reportData['projects']['value'])]);
                fputcsv($file, ['']);
            }
            
            // Quotations summary
            if (isset($reportData['quotations'])) {
                fputcsv($file, ['QUOTATIONS SUMMARY']);
                fputcsv($file, ['Total Quotations', $reportData['quotations']['total']]);
                fputcsv($file, ['Approved Quotations', $reportData['quotations']['approved']]);
                fputcsv($file, ['Pending Quotations', $reportData['quotations']['pending']]);
                fputcsv($file, ['']);
            }
            
            // Messages summary
            if (isset($reportData['messages'])) {
                fputcsv($file, ['MESSAGES SUMMARY']);
                fputcsv($file, ['Total Messages', $reportData['messages']['total']]);
                fputcsv($file, ['Replied Messages', $reportData['messages']['replied']]);
                fputcsv($file, ['Response Rate', $reportData['messages']['response_rate'] . '%']);
                fputcsv($file, ['']);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Download PDF report (placeholder).
     */
    protected function downloadPdfReport(array $reportData, string $filename)
    {
        // This would integrate with a PDF library like TCPDF or DomPDF
        // For now, redirect to CSV download
        return $this->downloadCsvReport($reportData, $filename);
    }

    /**
     * Download Excel report (placeholder).
     */
    protected function downloadExcelReport(array $reportData, string $filename)
    {
        // This would integrate with PhpSpreadsheet
        // For now, redirect to CSV download
        return $this->downloadCsvReport($reportData, $filename);
    }
}