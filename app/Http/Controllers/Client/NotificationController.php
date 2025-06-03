<?php
// File: app/Http/Controllers/Client/NotificationController.php - PROPER VERSION

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Services\NotificationService;
use App\Services\ClientNotificationService;
use App\Services\DashboardService;
use App\Services\NotificationTypeHelper;
use App\Facades\Notifications;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class NotificationController extends Controller
{
    protected NotificationService $notificationService;
    protected ClientNotificationService $clientNotificationService;
    protected DashboardService $dashboardService;

    public function __construct(
        NotificationService $notificationService,
        ClientNotificationService $clientNotificationService,
        DashboardService $dashboardService
    ) {
        $this->notificationService = $notificationService;
        $this->clientNotificationService = $clientNotificationService;
        $this->dashboardService = $dashboardService;
    }

    /**
     * Display a listing of the client's notifications with proper filtering and pagination.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Enhanced validation with more filter options
        $filters = $request->validate([
            'read' => 'nullable|string|in:read,unread,all',
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
        if (!empty($filters['read'])) {
            switch ($filters['read']) {
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

        // Apply category filter by checking notification type
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

        // Apply search filter (in notification data)
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

        return view('client.notifications.index', compact(
            'processedNotifications', 
            'statistics', 
            'filters',
            'filterOptions'
        ));
    }

    /**
     * Display the specified notification with proper type handling.
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

        // Clear notification cache
        $this->clientNotificationService->clearNotificationCache(auth()->user());

        // Format notification for view
        $formattedNotification = $this->formatNotificationForView($notification);

        // Get related notifications (same category)
        $relatedNotifications = $this->getRelatedNotifications($notification, 5);

        return view('client.notifications.show', compact('formattedNotification', 'relatedNotifications'));
    }

    /**
     * Mark a notification as read via AJAX with enhanced response.
     */
    public function markAsRead(Request $request, $notificationId): JsonResponse
    {
        try {
            $user = auth()->user();
            
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
                $this->clientNotificationService->clearNotificationCache($user);
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
            Log::error('Failed to mark notification as read: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark notification as read'
            ], 500);
        }
    }

    /**
     * Mark all notifications as read with enhanced response.
     */
    public function markAllAsRead(): JsonResponse
    {
        try {
            $user = auth()->user();
            $count = $user->unreadNotifications()->update(['read_at' => now()]);
            
            // Clear notification cache
            $this->clientNotificationService->clearNotificationCache($user);

            return response()->json([
                'success' => true,
                'message' => $count > 0 ? "{$count} notifications marked as read" : "No unread notifications found",
                'count' => $count,
                'unread_count' => 0
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
     * Mark notification as unread (toggle functionality).
     */
    public function markAsUnread(Request $request, DatabaseNotification $notification): JsonResponse
    {
        try {
            // Ensure notification belongs to authenticated user
            if ($notification->notifiable_id !== auth()->id()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $notification->update(['read_at' => null]);
            
            // Clear notification cache
            $this->clientNotificationService->clearNotificationCache(auth()->user());

            $unreadCount = auth()->user()->unreadNotifications()->count();

            return response()->json([
                'success' => true,
                'message' => 'Notification marked as unread',
                'unread_count' => $unreadCount,
                'notification' => $this->formatNotificationForApi($notification)
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to mark notification as unread: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark notification as unread'
            ], 500);
        }
    }

    /**
     * Delete a notification with proper authorization.
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
            
            // Clear notification cache
            $this->clientNotificationService->clearNotificationCache(auth()->user());

            $unreadCount = auth()->user()->unreadNotifications()->count();

            return response()->json([
                'success' => true,
                'message' => 'Notification deleted successfully',
                'was_unread' => $wasUnread,
                'unread_count' => $unreadCount
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
     * Clear all read notifications with confirmation.
     */
    public function clearRead(): JsonResponse
    {
        try {
            $user = auth()->user();
            $count = $user->readNotifications()->delete();
            
            // Clear notification cache
            $this->clientNotificationService->clearNotificationCache($user);

            return response()->json([
                'success' => true,
                'message' => $count > 0 ? "{$count} read notifications cleared" : "No read notifications found",
                'count' => $count,
                'unread_count' => $user->unreadNotifications()->count()
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to clear read notifications: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear notifications'
            ], 500);
        }
    }

    /**
     * Get recent notifications for header dropdown using enhanced formatting.
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
            $unreadOnly = $request->get('unread_only', true);
            
            $user = auth()->user();
            
            // Use enhanced DashboardService method
            $notifications = $this->dashboardService->getRecentNotifications($user, $limit);
            Log::info(json_encode($notifications));
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
            Log::error('Failed to get recent notifications: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'notifications' => [],
                'unread_count' => 0,
                'total_count' => 0
            ]);
        }
    }

    /**
     * Get notification preferences with enhanced options.
     */
    public function preferences()
    {
        $user = auth()->user();
        
        // Get current preferences
        $preferences = $this->clientNotificationService->getClientNotificationPreferences($user);
        
        // Get available notification types by category
        $availableTypes = NotificationTypeHelper::getTypesByCategory();
        
        // Get notification statistics for preference insights
        $stats = $this->clientNotificationService->getClientNotificationStats($user);

        return view('client.notifications.preferences', compact(
            'preferences', 
            'availableTypes', 
            'stats'
        ));
    }

    /**
     * Update notification preferences with enhanced validation.
     */
    public function updatePreferences(Request $request)
    {
        $validated = $request->validate([
            'email_notifications' => 'boolean',
            'project_updates' => 'boolean',
            'quotation_updates' => 'boolean',
            'message_replies' => 'boolean',
            'deadline_alerts' => 'boolean',
            'system_notifications' => 'boolean',
            'marketing_emails' => 'boolean',
            'chat_notifications' => 'boolean',
            'testimonial_notifications' => 'boolean',
            'notification_frequency' => 'nullable|string|in:immediate,hourly,daily,weekly',
            'quiet_hours_start' => 'nullable|date_format:H:i',
            'quiet_hours_end' => 'nullable|date_format:H:i',
            'notification_sound' => 'boolean',
            'desktop_notifications' => 'boolean',
        ]);

        $user = auth()->user();
        
        // Use existing service method with enhanced data
        $success = $this->clientNotificationService->updateClientNotificationPreferences($user, $validated);

        if ($success) {
            // Log preference change
            Log::info('Notification preferences updated', [
                'user_id' => $user->id,
                'preferences' => array_keys(array_filter($validated))
            ]);

            return redirect()->route('client.notifications.preferences')
                ->with('success', 'Notification preferences updated successfully!');
        }

        return redirect()->route('client.notifications.preferences')
            ->with('error', 'Failed to update notification preferences. Please try again.');
    }

    /**
     * Get notification summary with enhanced metrics.
     */
    public function getSummary(): JsonResponse
    {
        try {
            $user = auth()->user();
            
            // Get comprehensive stats
            $summary = $this->clientNotificationService->getClientNotificationStats($user);
            
            // Add category breakdown
            $categoryStats = $this->getNotificationsByCategory($user);
            
            // Add trending data
            $trendingData = $this->getNotificationTrends($user);

            return response()->json([
                'success' => true,
                'data' => array_merge($summary, [
                    'by_category' => $categoryStats,
                    'trends' => $trendingData,
                    'preferences_summary' => $this->getPreferencesSummary($user)
                ])
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get notification summary: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'data' => []
            ]);
        }
    }

    /**
     * Get unread notifications count with category breakdown.
     */
    public function getUnreadCount(): JsonResponse
    {
        try {
            $user = auth()->user();
            
            // Get basic count
            $counts = $this->dashboardService->getClientNotificationCounts($user);
            
            // Add category breakdown for unread
            $unreadByCategory = $this->getUnreadByCategory($user);

            return response()->json([
                'success' => true,
                'count' => $counts['unread_database_notifications'],
                'total_badge_count' => $counts['total_notifications'],
                'counts' => $counts,
                'unread_by_category' => $unreadByCategory
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get unread count: ' . $e->getMessage());
            
            return response()->json([
                'success' => true,
                'count' => 0,
                'total_badge_count' => 0,
                'counts' => [],
                'unread_by_category' => []
            ]);
        }
    }

    /**
     * Send test notification with proper type.
     */
    public function sendTest(): JsonResponse
    {
        try {
            $user = auth()->user();
            
            // Send test notification using our centralized system
            $success = Notifications::send('user.welcome', $user, $user);

            if ($success) {
                Log::info('Test notification sent', ['user_id' => $user->id]);
                
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
            Log::error('Failed to send test notification: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to send test notification.'
            ], 500);
        }
    }

    /**
     * Bulk delete notifications with proper validation.
     */
    public function bulkDelete(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'notification_ids' => 'required|array|min:1',
                'notification_ids.*' => 'string|exists:notifications,id',
                'confirm' => 'required|boolean|accepted',
            ]);

            $user = auth()->user();
            $notificationIds = $validated['notification_ids'];
            
            // Ensure all notifications belong to the user
            $userNotificationIds = $user->notifications()
                ->whereIn('id', $notificationIds)
                ->pluck('id')
                ->toArray();

            if (count($userNotificationIds) !== count($notificationIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Some notifications do not belong to you'
                ], 403);
            }

            // Count unread before deletion
            $unreadBeforeDeletion = $user->notifications()
                ->whereIn('id', $userNotificationIds)
                ->whereNull('read_at')
                ->count();

            // Delete notifications
            $count = $user->notifications()
                ->whereIn('id', $userNotificationIds)
                ->delete();

            // Clear notification cache
            $this->clientNotificationService->clearNotificationCache($user);

            return response()->json([
                'success' => true,
                'message' => "{$count} notifications deleted successfully",
                'count' => $count,
                'unread_count' => $user->unreadNotifications()->count(),
                'deleted_unread_count' => $unreadBeforeDeletion
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to bulk delete notifications: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete notifications'
            ], 500);
        }
    }

    // === HELPER METHODS ===

    /**
     * Format notification for view display.
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
     * Format notification for API response.
     */
    protected function formatNotificationForApi(DatabaseNotification $notification): array
    {
        $formatted = $this->formatNotificationForView($notification);
        
        // Remove raw_data for API response
        unset($formatted['raw_data']);
        
        return $formatted;
    }

    /**
     * Get enhanced notification statistics.
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
            Log::error('Failed to get enhanced notification statistics: ' . $e->getMessage());
            
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
     * Get notifications grouped by category.
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
     * Get notification types by category for filtering.
     */
    protected function getTypesByCategory(string $category): array
    {
        $allTypes = NotificationTypeHelper::getTypesByCategory();
        return $allTypes[$category] ?? [];
    }

    /**
     * Get filter options for dropdowns.
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
     * Get related notifications (same category).
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
     * Get unread notifications by category.
     */
    protected function getUnreadByCategory($user): array
    {
        $unreadNotifications = $user->unreadNotifications()->get();
        $unreadByCategory = [];

        foreach ($unreadNotifications as $notification) {
            $type = NotificationTypeHelper::classToType($notification->type);
            $category = NotificationTypeHelper::getCategory($type);
            
            if (!isset($unreadByCategory[$category])) {
                $unreadByCategory[$category] = 0;
            }
            $unreadByCategory[$category]++;
        }

        return $unreadByCategory;
    }

    /**
     * Get notification trends (last 7 days).
     */
    protected function getNotificationTrends($user): array
    {
        $trends = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $count = $user->notifications()
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
     * Get preferences summary.
     */
    protected function getPreferencesSummary($user): array
    {
        $preferences = $this->clientNotificationService->getClientNotificationPreferences($user);
        
        $enabledCount = count(array_filter($preferences, function($value, $key) {
            return $value === true && !in_array($key, ['notification_frequency', 'quiet_hours']);
        }, ARRAY_FILTER_USE_BOTH));

        return [
            'enabled_preferences' => $enabledCount,
            'total_preferences' => count($preferences) - 2, // Exclude frequency and quiet_hours
            'notification_frequency' => $preferences['notification_frequency'] ?? 'immediate',
            'quiet_hours_enabled' => !empty($preferences['quiet_hours'])
        ];
    }

    /**
     * Apply filters to notification query.
     */
    protected function applyFiltersToQuery($query, array $filters)
    {
        // Apply the same filters as in index method
        if (!empty($filters['read'])) {
            switch ($filters['read']) {
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
}