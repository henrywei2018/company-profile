<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use App\Services\ClientAccessService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    protected DashboardService $dashboardService;
    protected ClientAccessService $clientAccessService;

    public function __construct(
        DashboardService $dashboardService,
        ClientAccessService $clientAccessService
    ) {
        $this->dashboardService = $dashboardService;
        $this->clientAccessService = $clientAccessService;
    }

    /**
     * Display the client dashboard
     */
    public function index()
    {
        try {
            $user = auth()->user();

            // Get main dashboard data (without notification processing)
            $dashboardData = $this->dashboardService->getDashboardData($user);

            // Get basic counts for dashboard widgets
            $basicCounts = $this->getBasicCounts($user);

            return view('client.dashboard', [
                'user' => $user,
                'statistics' => $dashboardData['statistics'] ?? [],
                'recentActivities' => $dashboardData['recent_activities'] ?? [],
                'upcomingDeadlines' => $dashboardData['upcoming_deadlines'] ?? [],
                'quickActions' => $this->getClientQuickActions(),
                
                // Basic counts for widgets - notifications handled separately
                'unreadNotificationsCount' => $user->unreadNotifications()->count(),
                'unreadMessagesCount' => $basicCounts['unread_messages'] ?? 0,
                'pendingApprovalsCount' => $basicCounts['pending_approvals'] ?? 0,
                'overdueProjectsCount' => $basicCounts['overdue_projects'] ?? 0,
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
                'quickActions' => $this->getClientQuickActions(),
                'unreadNotificationsCount' => 0,
                'unreadMessagesCount' => 0,
                'pendingApprovalsCount' => 0,
                'overdueProjectsCount' => 0,
                'error' => 'Unable to load dashboard data. Please refresh the page.'
            ]);
        }
    }

    /**
     * Get real-time dashboard statistics
     */
    public function getRealtimeStats(): JsonResponse
    {
        try {
            $user = auth()->user();
            $dashboardData = $this->dashboardService->getDashboardData($user);
            
            // Extract statistics and add notification count
            $stats = $dashboardData['statistics'] ?? [];
            $stats['unread_notifications'] = $user->unreadNotifications()->count();
            
            return response()->json([
                'success' => true,
                'stats' => $stats
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error getting realtime stats', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'stats' => []
            ], 500);
        }
    }

    /**
     * Get chart data for dashboard widgets
     */
    public function getChartData(Request $request): JsonResponse
    {
        try {
            $user = auth()->user();
            $chartType = $request->input('type', 'projects');
            $period = $request->input('period', '30d');
            
            // Use existing dashboard data or generate specific chart data
            $dashboardData = $this->dashboardService->getDashboardData($user);
            
            // Extract relevant chart data based on type
            $chartData = $this->extractChartData($dashboardData, $chartType, $period);
            
            return response()->json([
                'success' => true,
                'data' => $chartData
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error getting chart data', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'data' => []
            ], 500);
        }
    }

    /**
     * Get performance metrics
     */
    public function getPerformanceMetrics(): JsonResponse
    {
        try {
            $user = auth()->user();
            $dashboardData = $this->dashboardService->getDashboardData($user);
            
            // Extract performance metrics from dashboard data
            $metrics = $dashboardData['performance'] ?? [];
            
            return response()->json([
                'success' => true,
                'metrics' => $metrics
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error getting performance metrics', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'metrics' => []
            ], 500);
        }
    }

    /**
     * Get upcoming deadlines
     */
    public function getUpcomingDeadlines(): JsonResponse
    {
        try {
            $user = auth()->user();
            $dashboardData = $this->dashboardService->getDashboardData($user);
            
            // Extract upcoming deadlines from dashboard data
            $deadlines = $dashboardData['upcoming_deadlines'] ?? [];
            
            return response()->json([
                'success' => true,
                'deadlines' => $deadlines
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error getting upcoming deadlines', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'deadlines' => []
            ], 500);
        }
    }

    /**
     * Get recent activities
     */
    public function getRecentActivities(): JsonResponse
    {
        try {
            $user = auth()->user();
            $dashboardData = $this->dashboardService->getDashboardData($user);
            
            // Extract recent activities from dashboard data
            $activities = $dashboardData['recent_activities'] ?? [];
            
            return response()->json([
                'success' => true,
                'activities' => $activities
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error getting recent activities', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'activities' => []
            ], 500);
        }
    }

    /**
     * Clear dashboard cache
     */
    public function clearCache(): JsonResponse
    {
        try {
            $user = auth()->user();
            $this->dashboardService->clearCache($user);
            
            return response()->json([
                'success' => true,
                'message' => 'Dashboard cache cleared successfully'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error clearing dashboard cache', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear cache'
            ], 500);
        }
    }

    // ================================================================
    // HELPER METHODS
    // ================================================================

    /**
     * Extract chart data from dashboard data based on type
     */
    private function extractChartData(array $dashboardData, string $chartType, string $period): array
    {
        $statistics = $dashboardData['statistics'] ?? [];
        
        switch ($chartType) {
            case 'projects':
                return [
                    'labels' => ['Active', 'Completed', 'On Hold'],
                    'data' => [
                        $statistics['projects']['active'] ?? 0,
                        $statistics['projects']['completed'] ?? 0,
                        $statistics['projects']['on_hold'] ?? 0,
                    ]
                ];
                
            case 'quotations':
                return [
                    'labels' => ['Pending', 'Approved', 'Rejected'],
                    'data' => [
                        $statistics['quotations']['pending'] ?? 0,
                        $statistics['quotations']['approved'] ?? 0,
                        $statistics['quotations']['rejected'] ?? 0,
                    ]
                ];
                
            case 'messages':
                return [
                    'labels' => ['Read', 'Unread'],
                    'data' => [
                        $statistics['messages']['read'] ?? 0,
                        $statistics['messages']['unread'] ?? 0,
                    ]
                ];
                
            default:
                return [
                    'labels' => [],
                    'data' => []
                ];
        }
    }

    /**
     * Get basic counts for dashboard widgets
     */
    private function getBasicCounts($user): array
    {
        try {
            return [
                'unread_messages' => $user->messages()->where('read_at', null)->count(),
                'pending_approvals' => $user->quotations()->where('status', 'pending')->count(),
                'overdue_projects' => $user->projects()
                    ->where('status', 'in_progress')
                    ->where('deadline', '<', now())
                    ->count(),
            ];
        } catch (\Exception $e) {
            Log::error('Error getting basic counts', [
                'user_id' => $user->id ?? null,
                'error' => $e->getMessage()
            ]);
            
            return [
                'unread_messages' => 0,
                'pending_approvals' => 0,
                'overdue_projects' => 0,
            ];
        }
    }

    /**
     * Get client quick actions for dashboard
     */
    private function getClientQuickActions(): array
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
            [
                'title' => 'Notifications',
                'description' => 'View all notifications',
                'icon' => 'bell',
                'color' => 'indigo',
                'url' => route('client.notifications.index'),
            ],
            [
                'title' => 'Settings',
                'description' => 'Update your preferences',
                'icon' => 'cog',
                'color' => 'gray',
                'url' => route('client.notifications.preferences'),
            ],
        ];
    }
}