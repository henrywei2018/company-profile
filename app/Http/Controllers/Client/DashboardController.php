<?php
// File: app/Http/Controllers/Client/DashboardController.php - SIMPLE API APPROACH

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
     * SIMPLE APPROACH: Load dashboard and let JavaScript fetch notification data
     */
    public function index()
    {
        try {
            $user = auth()->user();

            // Get basic dashboard data (without notifications)
            $dashboardData = $this->dashboardService->getDashboardData($user);

            // Get basic counts for server-side rendering
            $basicCounts = $this->getBasicCounts($user);

            return view('client.dashboard', [
                'user' => $user,
                'statistics' => $dashboardData['statistics'] ?? [],
                'recentActivities' => $dashboardData['recent_activities'] ?? [],
                'upcomingDeadlines' => $dashboardData['upcoming_deadlines'] ?? [],
                'quickActions' => $this->getClientQuickActions(),
                'notifications' => $basicCounts,
                
                // SIMPLE: Let JavaScript handle notification data loading
                'recentNotifications' => collect(), // Empty initially
                'unreadNotificationsCount' => $basicCounts['unread_database_notifications'] ?? 0,
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
                'notifications' => [],
                'recentNotifications' => collect(),
                'unreadNotificationsCount' => 0,
                'unreadMessagesCount' => 0,
                'pendingApprovalsCount' => 0,
                'overdueProjectsCount' => 0,
                'error' => 'Unable to load dashboard data. Please try again.'
            ]);
        }
    }

    /**
     * Get basic counts for initial page load
     */
    protected function getBasicCounts($user): array
    {
        try {
            return [
                'unread_database_notifications' => $user->unreadNotifications()->count(),
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
            ];
        } catch (\Exception $e) {
            Log::error('Error getting basic counts', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            return [
                'unread_database_notifications' => 0,
                'unread_messages' => 0,
                'pending_approvals' => 0,
                'overdue_projects' => 0,
            ];
        }
    }

    // Keep existing methods for AJAX calls
    public function getRealtimeStats(): JsonResponse
    {
        try {
            $user = Auth::user();
            $basicCounts = $this->getBasicCounts($user);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'notifications' => [
                        'unread' => $basicCounts['unread_database_notifications'],
                        'total' => $user->notifications()->count(),
                    ],
                    'messages' => [
                        'unread' => $basicCounts['unread_messages'],
                    ],
                    'quotations' => [
                        'awaiting_approval' => $basicCounts['pending_approvals'],
                    ],
                    'projects' => [
                        'overdue' => $basicCounts['overdue_projects'],
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

    // Proxy methods to NotificationController
    public function markNotificationRead(Request $request): JsonResponse
    {
        // Redirect to NotificationController
        $notificationController = app(\App\Http\Controllers\Client\NotificationController::class);
        
        if ($request->input('notification_id') === 'all') {
            return $notificationController->markAllAsRead();
        } else {
            return $notificationController->markAsRead($request, $request->input('notification_id'));
        }
    }

    public function testNotification(): JsonResponse
    {
        // Redirect to NotificationController
        $notificationController = app(\App\Http\Controllers\Client\NotificationController::class);
        return $notificationController->sendTest();
    }

    // Keep existing helper methods
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
}