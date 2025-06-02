<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\NotificationService;
use App\Services\DashboardService;
use App\Services\UserService;
use App\Facades\Notifications;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Notifications\DatabaseNotification;

class NotificationController extends Controller
{
    protected NotificationService $notificationService;
    protected DashboardService $dashboardService;
    protected UserService $userService;

    public function __construct(
        NotificationService $notificationService,
        DashboardService $dashboardService,
        UserService $userService
    ) {
        $this->notificationService = $notificationService;
        $this->dashboardService = $dashboardService;
        $this->userService = $userService;
        
        // Ensure only admin access
        $this->middleware(function ($request, $next) {
            if (!Auth::user()->hasAdminAccess()) {
                abort(403, 'Admin access required');
            }
            return $next($request);
        });
    }

    /**
     * Display notifications for admin users
     */
    public function index(Request $request)
    {
        $filters = $request->validate([
            'read_status' => 'nullable|string|in:read,unread',
            'type' => 'nullable|string',
            'search' => 'nullable|string|max:255',
            'sort' => 'nullable|string|in:asc,desc',
            'per_page' => 'nullable|integer|min:10|max:100',
        ]);

        $perPage = $filters['per_page'] ?? 20;
        $query = Auth::user()->notifications();

        // Apply filters
        if (!empty($filters['read_status'])) {
            if ($filters['read_status'] === 'unread') {
                $query->whereNull('read_at');
            } elseif ($filters['read_status'] === 'read') {
                $query->whereNotNull('read_at');
            }
        }

        if (!empty($filters['type'])) {
            $query->where('type', 'like', '%' . $filters['type'] . '%');
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('data->title', 'like', "%{$search}%")
                  ->orWhere('data->message', 'like', "%{$search}%");
            });
        }

        $query->orderBy('created_at', $filters['sort'] ?? 'desc');
        $notifications = $query->paginate($perPage);

        // Format notifications for display
        $formattedNotifications = $notifications->getCollection()->map(function ($notification) {
            return $this->formatNotificationForDisplay($notification);
        });

        $statistics = $this->getNotificationStatistics(Auth::user());

        return view('admin.notifications.index', [
            'notifications' => $notifications->setCollection($formattedNotifications),
            'filters' => $filters,
            'stats' => $statistics
        ]);
    }

    /**
     * Show a specific notification
     */
    public function show($notificationId)
    {
        $notification = Auth::user()->notifications()
            ->where('id', $notificationId)
            ->firstOrFail();

        // Mark as read if not already read
        if (is_null($notification->read_at)) {
            $notification->markAsRead();
        }

        $formattedNotification = $this->formatNotificationForDisplay($notification, true);

        return view('admin.notifications.show', [
            'notification' => $formattedNotification
        ]);
    }

    /**
     * Mark a notification as read
     */
    public function markAsRead(Request $request, $notificationId): JsonResponse
    {
        try {
            $notification = Auth::user()->notifications()
                ->where('id', $notificationId)
                ->firstOrFail();

            if (!$notification->read_at) {
                $notification->markAsRead();
            }

            return response()->json([
                'success' => true,
                'message' => 'Notification marked as read'
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to mark notification as read: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark notification as read'
            ], 500);
        }
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead(): JsonResponse
    {
        try {
            $count = Auth::user()->unreadNotifications()->update(['read_at' => now()]);

            return response()->json([
                'success' => true,
                'message' => "{$count} notifications marked as read",
                'count' => $count
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to mark all notifications as read: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark notifications as read'
            ], 500);
        }
    }

    /**
     * Delete a notification
     */
    public function destroy($notificationId): JsonResponse
    {
        try {
            $notification = Auth::user()->notifications()
                ->where('id', $notificationId)
                ->firstOrFail();

            $notification->delete();

            return response()->json([
                'success' => true,
                'message' => 'Notification deleted successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to delete notification: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete notification'
            ], 500);
        }
    }

    /**
     * Bulk mark notifications as read
     */
    public function bulkMarkAsRead(Request $request): JsonResponse
    {
        $request->validate([
            'notification_ids' => 'required|array',
            'notification_ids.*' => 'string|exists:notifications,id'
        ]);

        $count = Auth::user()->notifications()
            ->whereIn('id', $request->notification_ids)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json([
            'success' => true,
            'message' => "{$count} notification(s) marked as read",
            'count' => $count
        ]);
    }

    /**
     * Bulk delete notifications
     */
    public function bulkDelete(Request $request): JsonResponse
    {
        $request->validate([
            'notification_ids' => 'required|array',
            'notification_ids.*' => 'string|exists:notifications,id'
        ]);

        $count = Auth::user()->notifications()
            ->whereIn('id', $request->notification_ids)
            ->delete();

        return response()->json([
            'success' => true,
            'message' => "{$count} notification(s) deleted",
            'count' => $count
        ]);
    }

    /**
     * Show notification settings for admin
     */
    public function settings()
    {
        $user = Auth::user();
        
        $preferences = $this->getUserNotificationPreferences($user);
        $notificationTypes = $this->getNotificationTypes();

        return view('admin.notifications.settings', compact('preferences', 'notificationTypes'));
    }

    /**
     * Update notification settings
     */
    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'email_notifications' => 'boolean',
            'database_notifications' => 'boolean',
            'project_notifications' => 'boolean',
            'quotation_notifications' => 'boolean',
            'message_notifications' => 'boolean',
            'chat_notifications' => 'boolean',
            'system_notifications' => 'boolean',
            'urgent_only' => 'boolean',
            'notification_frequency' => 'in:immediate,hourly,daily,weekly',
            'quiet_hours_enabled' => 'boolean',
            'quiet_hours_start' => 'nullable|date_format:H:i',
            'quiet_hours_end' => 'nullable|date_format:H:i',
        ]);

        $success = $this->userService->updateNotificationPreferences(Auth::user(), $validated);

        if ($success) {
            return redirect()->route('admin.notifications.settings')
                ->with('success', 'Notification settings updated successfully');
        }

        return redirect()->route('admin.notifications.settings')
            ->with('error', 'Failed to update notification settings');
    }

    /**
     * Get recent notifications for header dropdown
     */
    public function getRecent(Request $request): JsonResponse
    {
        try {
            $limit = $request->get('limit', 10);
            $user = Auth::user();
            
            $notifications = $this->dashboardService->getRecentNotifications($user, $limit);
            
            return response()->json([
                'success' => true,
                'notifications' => $notifications,
                'unread_count' => $user->unreadNotifications()->count()
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get recent notifications: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'notifications' => [],
                'unread_count' => 0
            ]);
        }
    }

    /**
     * Get notification statistics
     */
    public function getStats(): JsonResponse
    {
        try {
            $stats = [
                'total_sent' => \DB::table('notifications')->count(),
                'unread_count' => \DB::table('notifications')->whereNull('read_at')->count(),
                'today_sent' => \DB::table('notifications')->whereDate('created_at', today())->count(),
                'this_week' => \DB::table('notifications')->whereBetween('created_at', [
                    now()->startOfWeek(),
                    now()->endOfWeek()
                ])->count(),
                'types_breakdown' => $this->getNotificationTypeBreakdown(),
                'daily_trends' => $this->getDailyNotificationTrends(),
            ];

            return response()->json([
                'success' => true,
                'stats' => $stats
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get notification stats: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'stats' => []
            ], 500);
        }
    }

    /**
     * Send test notification
     */
    public function sendTestNotification(Request $request): JsonResponse
    {
        if (!app()->environment(['local', 'staging'])) {
            return response()->json([
                'success' => false,
                'message' => 'Test notifications only available in development environments'
            ], 403);
        }

        $validated = $request->validate([
            'type' => 'required|string',
            'recipient_email' => 'nullable|email',
            'test_data' => 'nullable|array'
        ]);

        try {
            $user = Auth::user();
            $recipient = $validated['recipient_email'] 
                ? User::where('email', $validated['recipient_email'])->first() ?? $user
                : $user;

            $testData = $validated['test_data'] ?? [
                'title' => 'Test Notification',
                'message' => 'This is a test notification sent at ' . now()->format('Y-m-d H:i:s'),
                'action_url' => route('admin.dashboard'),
                'action_text' => 'View Dashboard'
            ];

            $success = $this->notificationService->send($validated['type'], $testData, $recipient);
            
            return response()->json([
                'success' => $success,
                'message' => $success ? 'Test notification sent successfully' : 'Failed to send test notification',
                'recipient' => $recipient->email,
                'type' => $validated['type']
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to send test notification: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to send test notification: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export notifications
     */
    public function export(Request $request)
    {
        $filters = $request->validate([
            'format' => 'nullable|string|in:csv,json',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'type' => 'nullable|string',
            'read_status' => 'nullable|string|in:read,unread',
        ]);

        $format = $filters['format'] ?? 'csv';
        $query = Auth::user()->notifications();

        // Apply filters
        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        if (!empty($filters['type'])) {
            $query->where('type', 'like', "%{$filters['type']}%");
        }

        if (!empty($filters['read_status'])) {
            if ($filters['read_status'] === 'unread') {
                $query->whereNull('read_at');
            } else {
                $query->whereNotNull('read_at');
            }
        }

        $notifications = $query->orderBy('created_at', 'desc')->get();
        $filename = 'admin_notifications_export_' . now()->format('Y-m-d_H-i-s');

        if ($format === 'json') {
            return response()->json($notifications)
                ->header('Content-Disposition', "attachment; filename=\"{$filename}.json\"");
        }

        return $this->exportToCsv($notifications, $filename);
    }

    // Protected helper methods

    /**
     * Format notification for display
     */
    protected function formatNotificationForDisplay($notification, bool $detailed = false): array
    {
        $data = [
            'id' => $notification->id,
            'type' => $this->getNotificationType($notification->type),
            'title' => $notification->data['title'] ?? 'Notification',
            'message' => $notification->data['message'] ?? '',
            'action_url' => $notification->data['action_url'] ?? null,
            'action_text' => $notification->data['action_text'] ?? null,
            'created_at' => $notification->created_at,
            'read_at' => $notification->read_at,
            'is_read' => !is_null($notification->read_at),
            'time_ago' => $notification->created_at->diffForHumans(),
            'icon' => $this->getNotificationIcon($notification->type),
            'color' => $this->getNotificationColor($notification->type),
        ];

        if ($detailed) {
            $data['full_data'] = $notification->data;
            $data['notification'] = $notification;
        }

        return $data;
    }

    /**
     * Get notification statistics
     */
    protected function getNotificationStatistics($user): array
    {
        try {
            return [
                'total' => $user->notifications()->count(),
                'unread' => $user->unreadNotifications()->count(),
                'today' => $user->notifications()->whereDate('created_at', today())->count(),
                'this_week' => $user->notifications()->whereBetween('created_at', [
                    now()->startOfWeek(), 
                    now()->endOfWeek()
                ])->count(),
                'this_month' => $user->notifications()->whereMonth('created_at', now()->month)->count(),
                'by_type' => $this->getNotificationsByType($user),
            ];
        } catch (\Exception $e) {
            Log::error('Failed to get notification statistics: ' . $e->getMessage());
            
            return [
                'total' => 0,
                'unread' => 0,
                'today' => 0,
                'this_week' => 0,
                'this_month' => 0,
                'by_type' => [],
            ];
        }
    }

    /**
     * Get notifications grouped by type
     */
    protected function getNotificationsByType($user): array
    {
        try {
            return $user->notifications()
                ->selectRaw('type, COUNT(*) as count')
                ->groupBy('type')
                ->pluck('count', 'type')
                ->mapWithKeys(function ($count, $type) {
                    return [$this->getNotificationType($type) => $count];
                })
                ->toArray();
        } catch (\Exception $e) {
            Log::error('Failed to get notifications by type: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get user notification preferences
     */
    protected function getUserNotificationPreferences($user): array
    {
        return [
            'email_notifications' => $user->email_notifications ?? true,
            'database_notifications' => $user->database_notifications ?? true,
            'project_notifications' => $user->project_notifications ?? true,
            'quotation_notifications' => $user->quotation_notifications ?? true,
            'message_notifications' => $user->message_notifications ?? true,
            'chat_notifications' => $user->chat_notifications ?? true,
            'system_notifications' => $user->system_notifications ?? true,
            'urgent_only' => $user->urgent_notifications_only ?? false,
            'notification_frequency' => $user->notification_frequency ?? 'immediate',
            'quiet_hours_enabled' => $user->quiet_hours_enabled ?? false,
            'quiet_hours_start' => $user->quiet_hours_start ?? '22:00',
            'quiet_hours_end' => $user->quiet_hours_end ?? '08:00',
        ];
    }

    /**
     * Get available notification types
     */
    protected function getNotificationTypes(): array
    {
        return [
            'project' => 'Project Updates',
            'quotation' => 'Quotation Updates',
            'message' => 'Messages',
            'chat' => 'Chat Sessions',
            'user' => 'User Account',
            'system' => 'System Alerts',
            'testimonial' => 'Testimonials',
        ];
    }

    /**
     * Extract readable notification type from class name
     */
    protected function getNotificationType(string $class): string
    {
        $shortName = class_basename($class);
        return str_replace('Notification', '', $shortName);
    }

    /**
     * Get notification icon based on type
     */
    protected function getNotificationIcon(string $type): string
    {
        return match(true) {
            str_contains($type, 'project') => 'folder',
            str_contains($type, 'quotation') => 'document-text',
            str_contains($type, 'message') => 'mail',
            str_contains($type, 'chat') => 'chat',
            str_contains($type, 'user') => 'user',
            str_contains($type, 'system') => 'cog',
            str_contains($type, 'testimonial') => 'star',
            default => 'bell',
        };
    }

    /**
     * Get notification color based on type
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

    /**
     * Get notification type breakdown for stats
     */
    protected function getNotificationTypeBreakdown(): array
    {
        return \DB::table('notifications')
            ->selectRaw('type, COUNT(*) as count')
            ->groupBy('type')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get()
            ->mapWithKeys(function ($item) {
                return [$this->getNotificationType($item->type) => $item->count];
            })
            ->toArray();
    }

    /**
     * Get daily notification trends
     */
    protected function getDailyNotificationTrends(): array
    {
        $trends = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $count = \DB::table('notifications')
                ->whereDate('created_at', $date->toDateString())
                ->count();
                
            $trends[] = [
                'date' => $date->format('M j'),
                'count' => $count
            ];
        }
        
        return $trends;
    }

    /**
     * Export notifications to CSV
     */
    protected function exportToCsv($notifications, string $filename)
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}.csv\"",
        ];

        return response()->stream(function () use ($notifications) {
            $handle = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($handle, [
                'ID',
                'Type',
                'Title',
                'Message',
                'Created At',
                'Read At',
                'Status'
            ]);
            
            // CSV data
            foreach ($notifications as $notification) {
                fputcsv($handle, [
                    $notification->id,
                    $this->getNotificationType($notification->type),
                    $notification->data['title'] ?? '',
                    $notification->data['message'] ?? '',
                    $notification->created_at->format('Y-m-d H:i:s'),
                    $notification->read_at ? $notification->read_at->format('Y-m-d H:i:s') : '',
                    $notification->read_at ? 'Read' : 'Unread'
                ]);
            }
            
            fclose($handle);
        }, 200, $headers);
    }
}