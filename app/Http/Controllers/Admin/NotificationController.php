<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\NotificationService;
use App\Services\DashboardService;
use App\Services\UserService;
use App\Services\NotificationTypeHelper;
use App\Facades\Notifications;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
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
        $user = Auth::user();
        
        // Enhanced validation with more filter options
        $filters = $request->validate([
            'read_status' => 'nullable|string|in:read,unread,all',
            'type' => 'nullable|string',
            'category' => 'nullable|string|in:chat,project,quotation,message,user,system,testimonial',
            'priority' => 'nullable|string|in:low,normal,high,urgent',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'search' => 'nullable|string|max:255',
            'per_page' => 'nullable|integer|min:10|max:100',
            'sort_by' => 'nullable|string|in:created_at,read_at,type',
            'sort_order' => 'nullable|string|in:asc,desc',
        ]);

        $perPage = $filters['per_page'] ?? 20;
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortOrder = $filters['sort_order'] ?? 'desc';

        // Build query with enhanced filtering
        $query = $user->notifications();

        // Apply read status filter
        if (!empty($filters['read_status'])) {
            switch ($filters['read_status']) {
                case 'unread':
                    $query->whereNull('read_at');
                    break;
                case 'read':
                    $query->whereNotNull('read_at');
                    break;
                // 'all' shows everything (no filter)
            }
        }

        // Apply type filter (exact match or partial)
        if (!empty($filters['type'])) {
            $query->where('type', 'like', "%{$filters['type']}%");
        }

        // Apply category filter
        if (!empty($filters['category'])) {
            $query->where(function($q) use ($filters) {
                $categoryTypes = $this->getTypesByCategory($filters['category']);
                foreach ($categoryTypes as $type) {
                    $q->orWhere('type', 'like', "%{$type}%");
                }
            });
        }

        // Apply date filters
        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        // Apply search filter
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(data, '$.title')) LIKE ?", ["%{$search}%"])
                  ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(data, '$.message')) LIKE ?", ["%{$search}%"]);
            });
        }

        // Apply sorting
        $query->orderBy($sortBy, $sortOrder);

        // Get paginated results
        $notifications = $query->paginate($perPage);

        // Process notifications to proper format
        $processedNotifications = $notifications->through(function ($notification) {
            return $this->formatNotificationForView($notification);
        });

        // Get enhanced statistics
        $statistics = $this->getEnhancedNotificationStatistics($user, $filters);

        // Get filter options for dropdowns
        $filterOptions = $this->getFilterOptions($user);

        return view('admin.notifications.index', compact(
            'processedNotifications', 
            'statistics', 
            'filters',
            'filterOptions'
        ));
    }

    /**
     * Display the specified notification
     */
    public function show(DatabaseNotification $notification)
    {
        // Ensure notification belongs to authenticated user
        if ($notification->notifiable_id !== auth()->id()) {
            abort(403, 'Unauthorized access to this notification.');
        }

        // Mark as read if unread
        if (!$notification->read_at) {
            $notification->markAsRead();
        }

        // Format notification for view
        $formattedNotification = $this->formatNotificationForView($notification);

        // Get related notifications (same category)
        $relatedNotifications = $this->getRelatedNotifications($notification, 5);

        return view('admin.notifications.show', compact('formattedNotification', 'relatedNotifications'));
    }

    /**
     * Get unread notifications count - FIXED VERSION
     */
    public function getUnreadCount(): JsonResponse
    {
        try {
            $user = Auth::user();
            
            // Use DashboardService to get consistent counts
            $counts = $this->dashboardService->getAdminNotificationCounts();
            
            return response()->json([
                'success' => true,
                'count' => $counts['unread_database_notifications'],
                'total_badge_count' => $counts['total_notifications'],
                'counts' => $counts
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get admin unread count: ' . $e->getMessage());
            
            return response()->json([
                'success' => true,
                'count' => 0,
                'total_badge_count' => 0,
                'counts' => []
            ]);
        }
    }

    /**
     * Mark a notification as read - FIXED VERSION
     */
    public function markAsRead(Request $request, $notificationId): JsonResponse
    {
        try {
            $user = Auth::user();
            
            if ($notificationId === 'all') {
                return $this->markAllAsRead();
            }

            $notification = $user->notifications()->find($notificationId);

            if (!$notification) {
                return response()->json([
                    'success' => false,
                    'message' => 'Notification not found'
                ], 404);
            }

            $wasUnread = is_null($notification->read_at);
            
            if ($wasUnread) {
                $notification->markAsRead();
            }

            // Get updated counts
            $unreadCount = $user->unreadNotifications()->count();

            return response()->json([
                'success' => true,
                'message' => $wasUnread ? 'Notification marked as read' : 'Notification was already read',
                'was_unread' => $wasUnread,
                'unread_count' => $unreadCount,
                'notification' => $this->formatNotificationForApi($notification)
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to mark admin notification as read: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark notification as read'
            ], 500);
        }
    }

    /**
     * Mark all notifications as read - FIXED VERSION
     */
    public function markAllAsRead(): JsonResponse
    {
        try {
            $user = Auth::user();
            $count = $user->unreadNotifications()->update(['read_at' => now()]);

            return response()->json([
                'success' => true,
                'message' => $count > 0 ? "{$count} notifications marked as read" : "No unread notifications found",
                'count' => $count,
                'unread_count' => 0
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to mark all admin notifications as read: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark notifications as read'
            ], 500);
        }
    }

    /**
     * Get recent notifications for header dropdown - FIXED VERSION
     */
    public function getRecent(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'limit' => 'nullable|integer|min:1|max:50',
                'category' => 'nullable|string',
                'unread_only' => 'nullable|boolean',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $limit = $request->get('limit', 10);
            $category = $request->get('category');
            $unreadOnly = $request->get('unread_only', false); // Admin sees all by default
            
            $user = Auth::user();
            
            // Use DashboardService method like client does
            $notifications = $this->dashboardService->getRecentNotifications($user, $limit);
            
            // Apply additional filtering if requested
            if ($category) {
                $notifications = array_filter($notifications, function($notification) use ($category) {
                    return ($notification['category'] ?? '') === $category;
                });
                $notifications = array_values($notifications); // Re-index array
            }

            if ($unreadOnly) {
                $notifications = array_filter($notifications, function($notification) {
                    return !$notification['is_read'];
                });
                $notifications = array_values($notifications); // Re-index array
            }

            return response()->json([
                'success' => true,
                'notifications' => $notifications,
                'unread_count' => $user->unreadNotifications()->count(),
                'total_count' => $user->notifications()->count(),
                'filters_applied' => [
                    'category' => $category,
                    'unread_only' => $unreadOnly,
                    'limit' => $limit
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get recent admin notifications: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'notifications' => [],
                'unread_count' => 0,
                'total_count' => 0
            ]);
        }
    }

    /**
     * Delete a notification
     */
    public function destroy(DatabaseNotification $notification): JsonResponse
    {
        try {
            // Ensure notification belongs to authenticated user
            if ($notification->notifiable_id !== auth()->id()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $wasUnread = is_null($notification->read_at);
            $notification->delete();

            $unreadCount = auth()->user()->unreadNotifications()->count();

            return response()->json([
                'success' => true,
                'message' => 'Notification deleted successfully',
                'was_unread' => $wasUnread,
                'unread_count' => $unreadCount
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to delete admin notification: ' . $e->getMessage());
            
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
     * Send test notification - FIXED VERSION
     */
    public function sendTestNotification(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            
            // Send test notification using our centralized system
            $success = Notifications::send('user.welcome', $user, $user);

            if ($success) {
                Log::info('Test notification sent to admin', ['user_id' => $user->id]);
                
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
            Log::error('Failed to send test notification to admin: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to send test notification: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show notification preferences/settings
     */
    public function preferences()
    {
        $user = Auth::user();
        
        $preferences = $this->getUserNotificationPreferences($user);
        $notificationTypes = $this->getNotificationTypes();
        $stats = $this->getNotificationStatistics($user);

        return view('admin.notifications.preferences', compact('preferences', 'notificationTypes', 'stats'));
    }

    /**
     * Update notification preferences
     */
    public function updatePreferences(Request $request)
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
            return redirect()->route('admin.notifications.preferences')
                ->with('success', 'Notification preferences updated successfully');
        }

        return redirect()->route('admin.notifications.preferences')
            ->with('error', 'Failed to update notification preferences');
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

    // === HELPER METHODS ===

    /**
     * Format notification for view display - SAME AS CLIENT
     */
    protected function formatNotificationForView(DatabaseNotification $notification): array
    {
        $data = $notification->data;
        $actualType = NotificationTypeHelper::classToType($notification->type);
        $category = NotificationTypeHelper::getCategory($actualType);

        return [
            'id' => $notification->id,
            'type' => $actualType,
            'category' => $category,
            'title' => $data['title'] ?? NotificationTypeHelper::getDisplayTitle($actualType),
            'message' => $data['message'] ?? '',
            'action_url' => $data['action_url'] ?? '#',
            'action_text' => $data['action_text'] ?? 'View',
            'created_at' => $notification->created_at,
            'read_at' => $notification->read_at,
            'is_read' => !is_null($notification->read_at),
            'formatted_time' => $notification->created_at->diffForHumans(),
            'formatted_date' => $notification->created_at->format('M d, Y H:i'),
            'icon' => $this->dashboardService->getNotificationIcon($actualType),
            'color' => $this->dashboardService->getNotificationColor($actualType),
            'priority' => $data['priority'] ?? 'normal',
            'raw_data' => $data,
        ];
    }

    /**
     * Format notification for API response
     */
    protected function formatNotificationForApi(DatabaseNotification $notification): array
    {
        $formatted = $this->formatNotificationForView($notification);
        
        // Remove raw_data for API response
        unset($formatted['raw_data']);
        
        return $formatted;
    }

    /**
     * Get enhanced notification statistics
     */
    protected function getEnhancedNotificationStatistics($user, array $filters = []): array
    {
        try {
            $baseStats = [
                'total' => $user->notifications()->count(),
                'unread' => $user->unreadNotifications()->count(),
                'today' => $user->notifications()->whereDate('created_at', today())->count(),
                'this_week' => $user->notifications()->whereBetween('created_at', [
                    now()->startOfWeek(), 
                    now()->endOfWeek()
                ])->count(),
                'this_month' => $user->notifications()->whereMonth('created_at', now()->month)->count(),
            ];

            // Add category breakdown
            $baseStats['by_category'] = $this->getNotificationsByCategory($user);
            
            // Add filtered results count if filters applied
            if (!empty($filters)) {
                $filteredQuery = $this->applyFiltersToQuery($user->notifications(), $filters);
                $baseStats['filtered_count'] = $filteredQuery->count();
            }

            return $baseStats;
        } catch (\Exception $e) {
            Log::error('Failed to get enhanced admin notification statistics: ' . $e->getMessage());
            
            return [
                'total' => 0,
                'unread' => 0,
                'today' => 0,
                'this_week' => 0,
                'this_month' => 0,
                'by_category' => [],
            ];
        }
    }

    /**
     * Get notifications grouped by category
     */
    protected function getNotificationsByCategory($user): array
    {
        try {
            $notifications = $user->notifications()->get();
            $byCategory = [];

            foreach ($notifications as $notification) {
                $type = NotificationTypeHelper::classToType($notification->type);
                $category = NotificationTypeHelper::getCategory($type);
                
                if (!isset($byCategory[$category])) {
                    $byCategory[$category] = [
                        'total' => 0,
                        'unread' => 0,
                        'types' => []
                    ];
                }
                
                $byCategory[$category]['total']++;
                
                if (!$notification->read_at) {
                    $byCategory[$category]['unread']++;
                }
                
                if (!isset($byCategory[$category]['types'][$type])) {
                    $byCategory[$category]['types'][$type] = 0;
                }
                $byCategory[$category]['types'][$type]++;
            }

            return $byCategory;
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Get notification types by category for filtering
     */
    protected function getTypesByCategory(string $category): array
    {
        $allTypes = NotificationTypeHelper::getTypesByCategory();
        return $allTypes[$category] ?? [];
    }

    /**
     * Get filter options for dropdowns
     */
    protected function getFilterOptions($user): array
    {
        $categories = array_keys($this->getNotificationsByCategory($user));
        
        return [
            'categories' => $categories,
            'read_options' => [
                'all' => 'All Notifications',
                'unread' => 'Unread Only',
                'read' => 'Read Only'
            ],
            'sort_options' => [
                'created_at' => 'Date Created',
                'read_at' => 'Date Read',
                'type' => 'Type'
            ],
            'per_page_options' => [10, 20, 50, 100]
        ];
    }

    /**
     * Get related notifications (same category)
     */
    protected function getRelatedNotifications(DatabaseNotification $notification, int $limit = 5): array
    {
        $type = NotificationTypeHelper::classToType($notification->type);
        $category = NotificationTypeHelper::getCategory($type);
        
        $related = $notification->notifiable
            ->notifications()
            ->where('id', '!=', $notification->id)
            ->where('type', 'like', "%{$category}%")
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();

        return $related->map(function($n) {
            return $this->formatNotificationForView($n);
        })->toArray();
    }

    /**
     * Apply filters to notification query
     */
    protected function applyFiltersToQuery($query, array $filters)
    {
        // Same logic as in index method
        if (!empty($filters['read_status'])) {
            switch ($filters['read_status']) {
                case 'unread':
                    $query->whereNull('read_at');
                    break;
                case 'read':
                    $query->whereNotNull('read_at');
                    break;
            }
        }

        if (!empty($filters['type'])) {
            $query->where('type', 'like', "%{$filters['type']}%");
        }

        if (!empty($filters['category'])) {
            $categoryTypes = $this->getTypesByCategory($filters['category']);
            $query->where(function($q) use ($categoryTypes) {
                foreach ($categoryTypes as $type) {
                    $q->orWhere('type', 'like', "%{$type}%");
                }
            });
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(data, '$.title')) LIKE ?", ["%{$search}%"])
                  ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(data, '$.message')) LIKE ?", ["%{$search}%"]);
            });
        }

        return $query;
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
            Log::error('Failed to get admin notification statistics: ' . $e->getMessage());
            
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
                    return [NotificationTypeHelper::getDisplayTitle($type) => $count];
                })
                ->toArray();
        } catch (\Exception $e) {
            Log::error('Failed to get notifications by type: ' . $e->getMessage());
            return [];
        }
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
                $actualType = NotificationTypeHelper::classToType($notification->type);
                fputcsv($handle, [
                    $notification->id,
                    NotificationTypeHelper::getDisplayTitle($actualType),
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