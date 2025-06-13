<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\NotificationService;
use App\Services\ClientNotificationService;
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
    protected ClientNotificationService $clientNotificationService;

    public function __construct(
        NotificationService $notificationService,
        ClientNotificationService $clientNotificationService
    ) {
        $this->notificationService = $notificationService;
        $this->clientNotificationService = $clientNotificationService;
        
        // Ensure only authenticated users can access
        $this->middleware('auth');
    }

    /**
     * Display notifications for logged-in client
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Enhanced validation with client-specific filter options
        $filters = $request->validate([
            'read_status' => 'nullable|string|in:read,unread,all',
            'type' => 'nullable|string',
            'category' => 'nullable|string|in:project,quotation,message,chat,user,system',
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

        // Build query for logged-in user's notifications only
        $query = $user->notifications()
            ->orderBy($sortBy, $sortOrder);

        // Apply read status filter
        if (isset($filters['read_status'])) {
            switch ($filters['read_status']) {
                case 'read':
                    $query->whereNotNull('read_at');
                    break;
                case 'unread':
                    $query->whereNull('read_at');
                    break;
                // 'all' - no additional filter needed
            }
        }

        // Apply type filter
        if (!empty($filters['type'])) {
            $query->where('type', 'like', '%' . $filters['type'] . '%');
        }

        // Apply category filter by examining notification data
        if (!empty($filters['category'])) {
            $query->where(function ($q) use ($filters) {
                $q->whereJsonContains('data->category', $filters['category'])
                  ->orWhere('type', 'like', '%' . $filters['category'] . '%');
            });
        }

        // Apply priority filter
        if (!empty($filters['priority'])) {
            $query->whereJsonContains('data->priority', $filters['priority']);
        }

        // Apply date range filter
        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        // Apply search filter
        if (!empty($filters['search'])) {
            $searchTerm = $filters['search'];
            $query->where(function ($q) use ($searchTerm) {
                $q->whereJsonContains('data->title', $searchTerm)
                  ->orWhereJsonContains('data->message', $searchTerm)
                  ->orWhere('type', 'like', '%' . $searchTerm . '%');
            });
        }

        $notifications = $query->paginate($perPage);

        // Get notification statistics for the client
        $stats = [
            'total' => $user->notifications()->count(),
            'unread' => $user->unreadNotifications()->count(),
            'today' => $user->notifications()->whereDate('created_at', today())->count(),
            'this_week' => $user->notifications()->whereBetween('created_at', [
                now()->startOfWeek(),
                now()->endOfWeek()
            ])->count(),
        ];

        // Get notification categories for filter dropdown
        $categories = $this->getNotificationCategories($user);

        return view('client.notifications.index', compact(
            'notifications',
            'filters',
            'stats',
            'categories'
        ));
    }

    /**
     * Show a specific notification
     */
    public function show(DatabaseNotification $notification)
    {
        $user = Auth::user();
        
        // Ensure notification belongs to authenticated user
        if ($notification->notifiable_id !== $user->id) {
            abort(403, 'Unauthorized access to notification');
        }

        // Mark as read if unread
        if (is_null($notification->read_at)) {
            $notification->markAsRead();
        }

        return view('client.notifications.show', compact('notification'));
    }

    /**
     * Mark a notification as read
     */
    public function markAsRead(DatabaseNotification $notification): JsonResponse
    {
        try {
            $user = Auth::user();
            
            // Ensure notification belongs to authenticated user
            if ($notification->notifiable_id !== $user->id) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $wasUnread = is_null($notification->read_at);
            $notification->markAsRead();

            $unreadCount = $user->unreadNotifications()->count();

            return response()->json([
                'success' => true,
                'message' => 'Notification marked as read',
                'was_unread' => $wasUnread,
                'unread_count' => $unreadCount
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to mark client notification as read: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark notification as read'
            ], 500);
        }
    }

    /**
     * Mark all notifications as read for logged-in user
     */
    public function markAllAsRead(): JsonResponse
    {
        try {
            $user = Auth::user();
            $updatedCount = $user->unreadNotifications()->update(['read_at' => now()]);

            Log::info('All notifications marked as read for client', [
                'user_id' => $user->id,
                'count' => $updatedCount
            ]);

            return response()->json([
                'success' => true,
                'message' => "Marked {$updatedCount} notifications as read",
                'updated_count' => $updatedCount,
                'unread_count' => 0
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to mark all client notifications as read: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark notifications as read'
            ], 500);
        }
    }

    /**
     * Get unread notifications count for logged-in user
     */
    public function getUnreadCount(): JsonResponse
    {
        try {
            $user = Auth::user();
            $unreadCount = $user->unreadNotifications()->count();

            return response()->json([
                'success' => true,
                'unread_count' => $unreadCount
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get client unread count: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'unread_count' => 0
            ]);
        }
    }

    /**
     * Get recent notifications for logged-in user (API endpoint)
     */
    public function getRecent(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            
            $validated = $request->validate([
                'limit' => 'nullable|integer|min:1|max:50',
                'category' => 'nullable|string',
                'unread_only' => 'nullable|boolean',
            ]);

            $limit = $validated['limit'] ?? 10;
            $category = $validated['category'] ?? null;
            $unreadOnly = $validated['unread_only'] ?? false;

            // Build query for user's notifications
            $query = $user->notifications()
                ->orderBy('created_at', 'desc')
                ->limit($limit);

            if ($unreadOnly) {
                $query->whereNull('read_at');
            }

            if ($category) {
                $query->where(function ($q) use ($category) {
                    $q->whereJsonContains('data->category', $category)
                      ->orWhere('type', 'like', '%' . $category . '%');
                });
            }

            $notifications = $query->get()->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'type' => $notification->type,
                    'title' => $notification->data['title'] ?? 'Notification',
                    'message' => $notification->data['message'] ?? '',
                    'category' => $this->extractCategory($notification),
                    'priority' => $notification->data['priority'] ?? 'normal',
                    'action_url' => $notification->data['action_url'] ?? null,
                    'action_text' => $notification->data['action_text'] ?? null,
                    'is_read' => !is_null($notification->read_at),
                    'created_at' => $notification->created_at,
                    'read_at' => $notification->read_at,
                    'formatted_time' => $notification->created_at->diffForHumans(),
                    'formatted_date' => $notification->created_at->format('M d, Y H:i'),
                    'icon' => $this->getNotificationIcon($notification),
                    'color' => $this->getNotificationColor($notification),
                ];
            })->toArray();

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
            Log::error('Failed to get recent client notifications: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'notifications' => [],
                'unread_count' => 0,
                'total_count' => 0
            ]);
        }
    }

    /**
     * Delete a notification (client can only delete their own)
     */
    public function destroy(DatabaseNotification $notification): JsonResponse
    {
        try {
            $user = Auth::user();
            
            // Ensure notification belongs to authenticated user
            if ($notification->notifiable_id !== $user->id) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $wasUnread = is_null($notification->read_at);
            $notification->delete();

            $unreadCount = $user->unreadNotifications()->count();

            return response()->json([
                'success' => true,
                'message' => 'Notification deleted successfully',
                'was_unread' => $wasUnread,
                'unread_count' => $unreadCount
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to delete client notification: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete notification'
            ], 500);
        }
    }

    /**
     * Bulk delete notifications for logged-in user
     */
    public function bulkDelete(Request $request): JsonResponse
    {
        $request->validate([
            'notification_ids' => 'required|array',
            'notification_ids.*' => 'required|string|exists:notifications,id',
        ]);

        try {
            $user = Auth::user();
            $notificationIds = $request->notification_ids;

            // Only delete notifications that belong to the authenticated user
            $deletedCount = $user->notifications()
                ->whereIn('id', $notificationIds)
                ->delete();

            $unreadCount = $user->unreadNotifications()->count();

            Log::info('Bulk deleted client notifications', [
                'user_id' => $user->id,
                'count' => $deletedCount,
                'notification_ids' => $notificationIds
            ]);

            return response()->json([
                'success' => true,
                'message' => "Deleted {$deletedCount} notifications",
                'deleted_count' => $deletedCount,
                'unread_count' => $unreadCount
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to bulk delete client notifications: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete notifications'
            ], 500);
        }
    }

    /**
     * Bulk mark notifications as read for logged-in user
     */
    public function bulkMarkAsRead(Request $request): JsonResponse
    {
        $request->validate([
            'notification_ids' => 'required|array',
            'notification_ids.*' => 'required|string|exists:notifications,id',
        ]);

        try {
            $user = Auth::user();
            $notificationIds = $request->notification_ids;

            // Only mark notifications as read that belong to the authenticated user
            $updatedCount = $user->notifications()
                ->whereIn('id', $notificationIds)
                ->whereNull('read_at')
                ->update(['read_at' => now()]);

            $unreadCount = $user->unreadNotifications()->count();

            Log::info('Bulk marked client notifications as read', [
                'user_id' => $user->id,
                'count' => $updatedCount,
                'notification_ids' => $notificationIds
            ]);

            return response()->json([
                'success' => true,
                'message' => "Marked {$updatedCount} notifications as read",
                'updated_count' => $updatedCount,
                'unread_count' => $unreadCount
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to bulk mark client notifications as read: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark notifications as read'
            ], 500);
        }
    }

    /**
     * Clear all read notifications for logged-in user
     */
    public function clearRead(): JsonResponse
    {
        try {
            $user = Auth::user();
            $deletedCount = $user->notifications()
                ->whereNotNull('read_at')
                ->delete();

            Log::info('Cleared read notifications for client', [
                'user_id' => $user->id,
                'count' => $deletedCount
            ]);

            return response()->json([
                'success' => true,
                'message' => "Cleared {$deletedCount} read notifications",
                'deleted_count' => $deletedCount,
                'unread_count' => $user->unreadNotifications()->count()
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to clear read client notifications: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear notifications'
            ], 500);
        }
    }

    /**
     * Get notification preferences
     */
    public function preferences()
    {
        $user = Auth::user();
        
        // Get current preferences from ClientNotificationService
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
     * Update notification preferences
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
            'notification_frequency' => 'nullable|string|in:immediate,hourly,daily,weekly',
            'quiet_hours_start' => 'nullable|date_format:H:i',
            'quiet_hours_end' => 'nullable|date_format:H:i',
            'notification_sound' => 'boolean',
            'desktop_notifications' => 'boolean',
        ]);

        $user = Auth::user();
        
        // Use ClientNotificationService to update preferences
        $success = $this->clientNotificationService->updateClientNotificationPreferences($user, $validated);

        if ($success) {
            return redirect()->route('client.notifications.preferences')
                ->with('success', 'Notification preferences updated successfully!');
        }

        return redirect()->route('client.notifications.preferences')
            ->with('error', 'Failed to update notification preferences. Please try again.');
    }

    /**
     * Get notification summary for logged-in user
     */
    public function getSummary(): JsonResponse
    {
        try {
            $user = Auth::user();
            
            $summary = [
                'total_notifications' => $user->notifications()->count(),
                'unread_notifications' => $user->unreadNotifications()->count(),
                'today_notifications' => $user->notifications()->whereDate('created_at', today())->count(),
                'this_week_notifications' => $user->notifications()->whereBetween('created_at', [
                    now()->startOfWeek(),
                    now()->endOfWeek()
                ])->count(),
                'categories' => $this->getNotificationCategoryCounts($user),
                'recent_activity' => $user->notifications()
                    ->latest()
                    ->limit(5)
                    ->get()
                    ->map(function ($notification) {
                        return [
                            'id' => $notification->id,
                            'title' => $notification->data['title'] ?? 'Notification',
                            'created_at' => $notification->created_at,
                            'is_read' => !is_null($notification->read_at),
                        ];
                    })
            ];

            return response()->json([
                'success' => true,
                'summary' => $summary
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get client notification summary: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'summary' => []
            ]);
        }
    }

    /**
     * Extract category from notification type or data
     */
    private function extractCategory(DatabaseNotification $notification): string
    {
        // Check if category is explicitly set in data
        if (isset($notification->data['category'])) {
            return $notification->data['category'];
        }

        // Extract from notification type
        $type = $notification->type;
        if (str_contains($type, 'Project')) return 'project';
        if (str_contains($type, 'Quotation')) return 'quotation';
        if (str_contains($type, 'Message')) return 'message';
        if (str_contains($type, 'Chat')) return 'chat';
        if (str_contains($type, 'User') || str_contains($type, 'Welcome')) return 'user';

        return 'system';
    }

    /**
     * Get available notification categories for the user
     */
    private function getNotificationCategories(User $user): array
    {
        return $user->notifications()
            ->selectRaw('DISTINCT CASE 
                WHEN type LIKE "%Project%" THEN "project"
                WHEN type LIKE "%Quotation%" THEN "quotation" 
                WHEN type LIKE "%Message%" THEN "message"
                WHEN type LIKE "%Chat%" THEN "chat"
                WHEN type LIKE "%User%" OR type LIKE "%Welcome%" THEN "user"
                ELSE "system"
            END as category')
            ->pluck('category')
            ->filter()
            ->unique()
            ->sort()
            ->values()
            ->toArray();
    }

    /**
     * Get notification counts by category for the user
     */
    private function getNotificationCategoryCounts(User $user): array
    {
        $categories = ['project', 'quotation', 'message', 'chat', 'user', 'system'];
        $counts = [];

        foreach ($categories as $category) {
            $counts[$category] = $user->notifications()
                ->where(function ($query) use ($category) {
                    $query->whereJsonContains('data->category', $category)
                          ->orWhere('type', 'like', '%' . ucfirst($category) . '%');
                })
                ->count();
        }

        return array_filter($counts); // Remove categories with 0 count
    }

    /**
     * Get notification icon based on type
     */
    private function getNotificationIcon($notification): string
    {
        $type = $notification->type;
        
        if (str_contains($type, 'Project')) return 'folder';
        if (str_contains($type, 'Quotation')) return 'document-text';
        if (str_contains($type, 'Message')) return 'mail';
        if (str_contains($type, 'Chat')) return 'chat-bubble-left';
        if (str_contains($type, 'User') || str_contains($type, 'Welcome')) return 'user';
        
        return 'bell'; // default
    }

    /**
     * Get notification color based on type
     */
    private function getNotificationColor($notification): string
    {
        $type = $notification->type;
        $priority = $notification->data['priority'] ?? 'normal';
        
        // Priority-based colors
        if ($priority === 'urgent') return 'red';
        if ($priority === 'high') return 'orange';
        
        // Type-based colors
        if (str_contains($type, 'Project')) return 'blue';
        if (str_contains($type, 'Quotation')) return 'green';
        if (str_contains($type, 'Message')) return 'purple';
        if (str_contains($type, 'Chat')) return 'indigo';
        if (str_contains($type, 'User') || str_contains($type, 'Welcome')) return 'gray';
        
        return 'blue'; // default
    }
}