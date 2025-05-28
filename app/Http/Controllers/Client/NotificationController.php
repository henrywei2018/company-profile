<?php
// File: app/Http/Controllers/Client/NotificationController.php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Services\NotificationAlertService;
use App\Services\DashboardService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Notifications\DatabaseNotification;

class NotificationController extends Controller
{
    protected NotificationAlertService $notificationService;
    protected DashboardService $dashboardService;

    public function __construct(
        NotificationAlertService $notificationService,
        DashboardService $dashboardService
    ) {
        $this->notificationService = $notificationService;
        $this->dashboardService = $dashboardService;
    }

    /**
     * Display a listing of the client's notifications.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Validate filters
        $filters = $request->validate([
            'read' => 'nullable|string|in:read,unread',
            'type' => 'nullable|string',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
        ]);

        // Get notifications
        $query = $user->notifications();

        // Apply filters
        if (!empty($filters['read'])) {
            if ($filters['read'] === 'unread') {
                $query->whereNull('read_at');
            } elseif ($filters['read'] === 'read') {
                $query->whereNotNull('read_at');
            }
        }

        if (!empty($filters['type'])) {
            $query->where('type', 'like', "%{$filters['type']}%");
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        $notifications = $query->orderBy('created_at', 'desc')->paginate(20);

        // Get statistics
        $statistics = [
            'total' => $user->notifications()->count(),
            'unread' => $user->unreadNotifications()->count(),
            'today' => $user->notifications()->whereDate('created_at', today())->count(),
            'this_week' => $user->notifications()->whereBetween('created_at', [
                now()->startOfWeek(), now()->endOfWeek()
            ])->count(),
        ];

        return view('client.notifications.index', compact('notifications', 'statistics', 'filters'));
    }

    /**
     * Display the specified notification.
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

        return view('client.notifications.show', compact('notification'));
    }

    /**
     * Mark a notification as read.
     */
    public function markAsRead(DatabaseNotification $notification): JsonResponse
    {
        // Ensure notification belongs to authenticated user
        if ($notification->notifiable_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $notification->markAsRead();

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read'
        ]);
    }

    /**
     * Mark a notification as unread.
     */
    public function markAsUnread(DatabaseNotification $notification): JsonResponse
    {
        // Ensure notification belongs to authenticated user
        if ($notification->notifiable_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $notification->update(['read_at' => null]);

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as unread'
        ]);
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead(): JsonResponse
    {
        $user = auth()->user();
        $count = $user->unreadNotifications()->update(['read_at' => now()]);

        return response()->json([
            'success' => true,
            'message' => "{$count} notifications marked as read",
            'count' => $count
        ]);
    }

    /**
     * Delete a notification.
     */
    public function destroy(DatabaseNotification $notification): JsonResponse
    {
        // Ensure notification belongs to authenticated user
        if ($notification->notifiable_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $notification->delete();

        return response()->json([
            'success' => true,
            'message' => 'Notification deleted successfully'
        ]);
    }

    /**
     * Clear all read notifications.
     */
    public function clearRead(): JsonResponse
    {
        $user = auth()->user();
        $count = $user->readNotifications()->delete();

        return response()->json([
            'success' => true,
            'message' => "{$count} read notifications cleared",
            'count' => $count
        ]);
    }

    /**
     * Get unread notifications count.
     */
    public function getUnreadCount(): JsonResponse
    {
        $count = auth()->user()->unreadNotifications()->count();

        return response()->json([
            'success' => true,
            'count' => $count
        ]);
    }

    /**
     * Get recent notifications for header dropdown.
     */
    public function getRecent(Request $request): JsonResponse
    {
        $limit = $request->get('limit', 5);
        
        $notifications = auth()->user()
            ->notifications()
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'type' => $notification->type,
                    'data' => $notification->data,
                    'read_at' => $notification->read_at,
                    'created_at' => $notification->created_at,
                    'formatted_time' => $notification->created_at->diffForHumans(),
                    'is_read' => !is_null($notification->read_at),
                ];
            });

        return response()->json([
            'success' => true,
            'notifications' => $notifications,
            'unread_count' => auth()->user()->unreadNotifications()->count()
        ]);
    }

    /**
     * Get notification preferences.
     */
    public function preferences()
    {
        $user = auth()->user();
        
        // Get current preferences (you might want to store this in user_preferences table)
        $preferences = [
            'email_notifications' => $user->email_notifications ?? true,
            'project_updates' => $user->project_update_notifications ?? true,
            'quotation_updates' => $user->quotation_update_notifications ?? true,
            'message_replies' => $user->message_reply_notifications ?? true,
            'deadline_alerts' => $user->deadline_alert_notifications ?? true,
            'marketing_emails' => $user->marketing_notifications ?? false,
        ];

        return view('client.notifications.preferences', compact('preferences'));
    }

    /**
     * Update notification preferences.
     */
    public function updatePreferences(Request $request)
    {
        $validated = $request->validate([
            'email_notifications' => 'boolean',
            'project_updates' => 'boolean',
            'quotation_updates' => 'boolean',
            'message_replies' => 'boolean',
            'deadline_alerts' => 'boolean',
            'marketing_emails' => 'boolean',
        ]);

        $user = auth()->user();
        
        // Update user preferences
        $user->update([
            'email_notifications' => $validated['email_notifications'] ?? false,
            'project_update_notifications' => $validated['project_updates'] ?? false,
            'quotation_update_notifications' => $validated['quotation_updates'] ?? false,
            'message_reply_notifications' => $validated['message_replies'] ?? false,
            'deadline_alert_notifications' => $validated['deadline_alerts'] ?? false,
            'marketing_notifications' => $validated['marketing_emails'] ?? false,
        ]);

        return redirect()->route('client.notifications.preferences')
            ->with('success', 'Notification preferences updated successfully!');
    }


    /**
     * Get notification summary for dashboard widget.
     */
    public function getSummary(): JsonResponse
    {
        $user = auth()->user();
        
        $summary = $this->notificationService->getNotificationSummary($user);
        
        return response()->json([
            'success' => true,
            'data' => $summary
        ]);
    }
}