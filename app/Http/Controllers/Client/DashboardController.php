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
     * FIXED: Display the client dashboard
     */
    public function index()
    {
        try {
            $user = auth()->user();

            // Get dashboard data
            $dashboardData = $this->dashboardService->getDashboardData($user);
            $notificationCounts = $this->getClientNotificationCounts($user);
            $recentNotifications = $this->getFormattedRecentNotifications($user, 10);

            return view('client.dashboard', [
                'user' => $user,
                'statistics' => $dashboardData['statistics'] ?? [],
                'recentActivities' => $dashboardData['recent_activities'] ?? [],
                'upcomingDeadlines' => $dashboardData['upcoming_deadlines'] ?? [],
                'notifications' => $notificationCounts,
                
                // FIXED: Required by header component
                'recentNotifications' => $recentNotifications,
                'unreadNotificationsCount' => $notificationCounts['unread_notifications'] ?? 0,
                'unreadMessagesCount' => $notificationCounts['unread_messages'] ?? 0,
                'pendingApprovalsCount' => $notificationCounts['pending_approvals'] ?? 0,
                'overdueProjectsCount' => $notificationCounts['overdue_projects'] ?? 0,
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
                'notifications' => [],
                'error' => 'Unable to load dashboard data.',
                
                // Safe fallbacks
                'recentNotifications' => collect(),
                'unreadNotificationsCount' => 0,
                'unreadMessagesCount' => 0,
                'pendingApprovalsCount' => 0,
                'overdueProjectsCount' => 0,
            ]);
        }
    }

    /**
     * FIXED: Get formatted recent notifications for header dropdown
     */
    protected function getFormattedRecentNotifications($user, int $limit = 10)
    {
        return $this->getFormattedRecentNotificationsPublic($user, $limit);
    }

    public function getFormattedRecentNotificationsPublic($user, int $limit = 10)
    {
        try {
            $notifications = $user->notifications()
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();

            return $notifications->map(function ($notification) {
                $data = $notification->data;
                $type = $notification->type; // Use the notification type directly
                
                // Extract the actual notification class name
                $notificationType = $this->extractNotificationType($type);
                
                return [
                    'id' => $notification->id,
                    'type' => $notificationType,
                    'title' => $this->getNotificationTitle($notificationType, $data),
                    'message' => $this->getNotificationMessage($notificationType, $data),
                    'url' => $this->getNotificationUrl($notificationType, $data),
                    'created_at' => $notification->created_at,
                    'read_at' => $notification->read_at,
                    'is_read' => !is_null($notification->read_at),
                    'formatted_time' => $notification->created_at->diffForHumans(),
                    'icon' => $this->getNotificationIcon($notificationType),
                    'color' => $this->getNotificationColor($notificationType),
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
    protected function extractNotificationType($fullType): string
    {
        // Convert from class name to type
        // e.g., "App\Notifications\ChatOperatorReplyNotification" -> "chat.operator_reply"
        if (str_contains($fullType, 'ChatOperatorReply')) {
            return 'chat.operator_reply';
        }
        if (str_contains($fullType, 'ChatSessionStarted')) {
            return 'chat.session_started';
        }
        if (str_contains($fullType, 'ChatMessage')) {
            return 'chat.message_received';
        }
        if (str_contains($fullType, 'ProjectCreated')) {
            return 'project.created';
        }
        if (str_contains($fullType, 'ProjectUpdated')) {
            return 'project.updated';
        }
        if (str_contains($fullType, 'ProjectCompleted')) {
            return 'project.completed';
        }
        if (str_contains($fullType, 'QuotationCreated')) {
            return 'quotation.created';
        }
        if (str_contains($fullType, 'QuotationApproved')) {
            return 'quotation.approved';
        }
        if (str_contains($fullType, 'MessageCreated')) {
            return 'message.created';
        }
        if (str_contains($fullType, 'MessageReply')) {
            return 'message.reply';
        }
        if (str_contains($fullType, 'Welcome')) {
            return 'user.welcome';
        }

        // Fallback to data type if available
        return $data['type'] ?? 'notification';
    }
    protected function getNotificationTitle($type, $data): string
    {
        return match($type) {
            'chat.operator_reply' => 'Chat Reply from Support',
            'chat.session_started' => 'Chat Session Started',
            'chat.message_received' => 'New Chat Message',
            'project.created' => 'New Project Created',
            'project.updated' => 'Project Updated',
            'project.completed' => '🎉 Project Completed',
            'project.deadline_approaching' => '⏰ Project Deadline Approaching',
            'project.overdue' => '⚠️ Project Overdue',
            'quotation.created' => 'Quotation Request Received',
            'quotation.approved' => '✅ Quotation Approved',
            'quotation.rejected' => '❌ Quotation Rejected',
            'quotation.status_updated' => 'Quotation Status Updated',
            'message.created' => 'New Message Received',
            'message.reply' => 'Message Reply',
            'user.welcome' => 'Welcome to ' . config('app.name'),
            'user.email_verified' => '✅ Email Verified',
            default => $data['title'] ?? 'Notification'
        };
    }
    protected function getNotificationMessage($type, $data): string
    {
        return match($type) {
            'chat.operator_reply' => 'You have received a reply from our support team in your chat session.',
            'chat.session_started' => 'Your chat session has been started. A support agent will be with you shortly.',
            'chat.message_received' => 'You have received a new message in your chat session.',
            'project.created' => 'A new project has been created for you: ' . ($data['project_title'] ?? 'Project'),
            'project.updated' => 'Your project "' . ($data['project_title'] ?? 'Project') . '" has been updated.',
            'project.completed' => 'Congratulations! Your project "' . ($data['project_title'] ?? 'Project') . '" has been completed.',
            'project.deadline_approaching' => 'The deadline for your project is approaching. Please review the progress.',
            'project.overdue' => 'Your project deadline has passed. Please contact us for updates.',
            'quotation.approved' => 'Great news! Your quotation request has been approved.',
            'quotation.rejected' => 'Your quotation request has been rejected. Please contact us for more information.',
            'quotation.status_updated' => 'The status of your quotation has been updated.',
            'message.created' => 'You have received a new message from our team.',
            'message.reply' => 'You have received a reply to your message.',
            'user.welcome' => 'Welcome to our platform! We\'re excited to have you on board.',
            'user.email_verified' => 'Your email address has been successfully verified.',
            default => $data['message'] ?? 'You have a new notification.'
        };
    }

    /**
     * FIXED: Get notification counts for header
     */
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
    protected function getNotificationUrl($type, $data): string
    {
        return match($type) {
            'chat.operator_reply', 'chat.session_started', 'chat.message_received' => 
                route('client.dashboard') . '#chat', // or specific chat route if exists
            'project.created', 'project.updated', 'project.completed', 'project.deadline_approaching', 'project.overdue' => 
                isset($data['project_id']) ? route('client.projects.show', $data['project_id']) : route('client.projects.index'),
            'quotation.created', 'quotation.approved', 'quotation.rejected', 'quotation.status_updated' => 
                isset($data['quotation_id']) ? route('client.quotations.show', $data['quotation_id']) : route('client.quotations.index'),
            'message.created', 'message.reply' => 
                isset($data['message_id']) ? route('client.messages.show', $data['message_id']) : route('client.messages.index'),
            'user.welcome', 'user.email_verified' => 
                route('client.dashboard'),
            default => $data['action_url'] ?? route('client.dashboard')
        };
    }

    /**
     * FIXED: Get recent notifications API endpoint
     */
    public function getNotifications(): JsonResponse
    {
        try {
            $user = auth()->user();
            $limit = request()->get('limit', 10);
            
            $recentNotifications = $this->getFormattedRecentNotifications($user, $limit);

            return response()->json([
                'success' => true,
                'notifications' => $recentNotifications,
                'unread_count' => $user->unreadNotifications()->count(),
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting notifications API', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to load notifications',
                'notifications' => [],
                'unread_count' => 0,
            ], 500);
        }
    }

    /**
     * FIXED: Get real-time stats
     */
    public function getRealtimeStats(): JsonResponse
    {
        try {
            $user = auth()->user();
            $notificationCounts = $this->getClientNotificationCounts($user);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'notifications' => [
                        'unread' => $notificationCounts['unread_notifications'],
                        'total' => $user->notifications()->count(),
                    ],
                    'messages' => [
                        'unread' => $notificationCounts['unread_messages'],
                    ],
                    'quotations' => [
                        'awaiting_approval' => $notificationCounts['pending_approvals'],
                    ],
                    'projects' => [
                        'overdue' => $notificationCounts['overdue_projects'],
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

    /**
     * FIXED: Mark notification as read
     */
    public function markNotificationRead(Request $request): JsonResponse
    {
        try {
            $notificationId = $request->input('notification_id');
            $user = auth()->user();
            
            if ($notificationId === 'all') {
                // Mark all as read
                $count = $user->unreadNotifications()->update(['read_at' => now()]);
                
                return response()->json([
                    'success' => true,
                    'message' => "{$count} notifications marked as read",
                    'count' => $count
                ]);
            } else {
                // Mark single notification as read
                $notification = $user->notifications()->find($notificationId);
                
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
     * FIXED: Helper methods for notification display
     */
    protected function getNotificationIcon($type): string
    {
        return match($type) {
            'chat.operator_reply', 'chat.session_started', 'chat.message_received' => 'chat',
            'project.created', 'project.updated', 'project.deadline_approaching', 'project.overdue' => 'folder',
            'project.completed' => 'star', // Use star for completed projects
            'quotation.created', 'quotation.approved', 'quotation.rejected', 'quotation.status_updated' => 'document-text',
            'message.created', 'message.reply' => 'mail',
            'user.welcome', 'user.email_verified' => 'user',
            'system.maintenance', 'system.alert' => 'cog',
            default => 'bell',
        };
    }

    /**
     * UPDATED: Get notification color with better mapping
     */
    protected function getNotificationColor($type): string
    {
        return match($type) {
            'project.completed', 'quotation.approved', 'user.email_verified' => 'green',
            'project.overdue', 'quotation.rejected', 'system.alert' => 'red',
            'project.deadline_approaching', 'quotation.status_updated' => 'yellow',
            'chat.operator_reply', 'chat.session_started', 'chat.message_received' => 'indigo',
            'project.created', 'quotation.created', 'message.created', 'message.reply' => 'blue',
            'user.welcome' => 'purple',
            'system.maintenance' => 'orange',
            default => 'gray',
        };
    }

    protected function getGenericTitle($type): string
    {
        return match($type) {
            'user.welcome' => 'Welcome!',
            'project.created' => 'New Project',
            'project.updated' => 'Project Updated',
            'quotation.created' => 'New Quotation',
            'message.created' => 'New Message',
            'chat.operator_reply' => 'Chat Reply',
            default => 'Notification'
        };
    }

    protected function getDefaultUrl($type): string
    {
        return match(true) {
            str_contains($type, 'project') => route('client.projects.index'),
            str_contains($type, 'quotation') => route('client.quotations.index'),
            str_contains($type, 'message') => route('client.messages.index'),
            str_contains($type, 'chat') => route('client.dashboard'), // atau route chat jika ada
            default => route('client.dashboard'),
        };
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