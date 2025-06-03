<?php
// File: app/Http/Controllers/Client/NotificationController.php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Services\NotificationService;
use App\Services\ClientNotificationService;
use App\Services\DashboardService;
use App\Services\UserService;
use App\Facades\Notifications;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class NotificationController extends Controller
{
    protected NotificationService $notificationService;
    protected ClientNotificationService $clientNotificationService;
    protected DashboardService $dashboardService;
    protected UserService $userService;

    public function __construct(
        NotificationService $notificationService,
        ClientNotificationService $clientNotificationService,
        DashboardService $dashboardService,
        UserService $userService
    ) {
        $this->notificationService = $notificationService;
        $this->clientNotificationService = $clientNotificationService;
        $this->dashboardService = $dashboardService;
        $this->userService = $userService;
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
            'per_page' => 'nullable|integer|min:10|max:100',
        ]);

        $perPage = $filters['per_page'] ?? 20;

        // Get notifications with filters
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

        $notifications = $query->orderBy('created_at', 'desc')->paginate($perPage);

        // Get statistics using existing service
        $statistics = $this->getNotificationStatistics($user);

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

        // Clear notification cache for the user
        $this->clientNotificationService->clearNotificationCache(auth()->user());

        return view('client.notifications.show', compact('notification'));
    }

    /**
     * Mark a notification as read via AJAX.
     */
    public function markAsRead(Request $request, $notificationId): JsonResponse
    {
        try {
            $user = auth()->user();
            $notification = $user->notifications()->find($notificationId);

            if (!$notification) {
                return response()->json([
                    'success' => false,
                    'message' => 'Notification not found'
                ], 404);
            }

            if (!$notification->read_at) {
                $notification->markAsRead();
                $this->clientNotificationService->clearNotificationCache($user);
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
     * Mark all notifications as read via AJAX.
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
     * Delete a notification.
     */
    public function destroy(DatabaseNotification $notification): JsonResponse
    {
        try {
            // Ensure notification belongs to authenticated user
            if ($notification->notifiable_id !== auth()->id()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $notification->delete();
            
            // Clear notification cache
            $this->clientNotificationService->clearNotificationCache(auth()->user());

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
     * Clear all read notifications.
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
                'message' => "{$count} read notifications cleared",
                'count' => $count
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
     * Get recent notifications for header dropdown using existing DashboardService.
     */
    public function getRecent(Request $request): JsonResponse
    {
        try {
            $limit = $request->get('limit', 10);
            $user = auth()->user();
            
            // Use DashboardController's logic for consistency
            $dashboardController = app(\App\Http\Controllers\Client\DashboardController::class);
            $formattedNotifications = $dashboardController->getFormattedRecentNotificationsPublic($user, $limit);
            
            return response()->json([
                'success' => true,
                'notifications' => $formattedNotifications,
                'unread_count' => $user->unreadNotifications()->count()
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get recent notifications: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'notifications' => [],
                'unread_count' => 0
            ], 500);
        }
    }
    protected function getNotificationIcon($type): string
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
    protected function getNotificationColor($type): string
    {
        return match(true) {
            str_contains($type, 'completed') => 'green',
            str_contains($type, 'overdue') || str_contains($type, 'urgent') => 'red',
            str_contains($type, 'deadline') => 'yellow',
            str_contains($type, 'approved') => 'green',
            str_contains($type, 'rejected') => 'red',
            str_contains($type, 'created') || str_contains($type, 'pending') => 'blue',
            str_contains($type, 'chat') => 'indigo',
            str_contains($type, 'system') => 'orange',
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
            str_contains($type, 'chat') => route('client.dashboard'),
            default => route('client.dashboard'),
        };
    }

    /**
     * Get notification preferences using existing service.
     */
    public function preferences()
    {
        $user = auth()->user();
        
        // Use existing ClientNotificationService method
        $preferences = $this->clientNotificationService->getClientNotificationPreferences($user);

        return view('client.notifications.preferences', compact('preferences'));
    }

    /**
     * Update notification preferences using existing service.
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
            'notification_frequency' => 'nullable|string|in:immediate,daily,weekly',
            'quiet_hours' => 'nullable|string',
        ]);

        $user = auth()->user();
        
        // Use existing ClientNotificationService method
        $success = $this->clientNotificationService->updateClientNotificationPreferences($user, $validated);

        if ($success) {
            return redirect()->route('client.notifications.preferences')
                ->with('success', 'Notification preferences updated successfully!');
        }

        return redirect()->route('client.notifications.preferences')
            ->with('error', 'Failed to update notification preferences. Please try again.');
    }

    /**
     * Get notification summary for dashboard widget using existing service.
     */
    public function getSummary(): JsonResponse
    {
        try {
            $user = auth()->user();
            
            // Use existing ClientNotificationService method
            $summary = $this->clientNotificationService->getClientNotificationStats($user);
            
            return response()->json([
                'success' => true,
                'data' => $summary
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
     * Get unread notifications count for AJAX using existing service.
     */
    public function getUnreadCount(): JsonResponse
    {
        try {
            $user = auth()->user();
            
            // Use existing DashboardService method
            $counts = $this->dashboardService->getClientNotificationCounts($user);
            
            return response()->json([
                'success' => true,
                'count' => $counts['unread_database_notifications'],
                'total_badge_count' => $counts['total_notifications'],
                'counts' => $counts
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get unread count: ' . $e->getMessage());
            
            return response()->json([
                'success' => true,
                'count' => 0,
                'total_badge_count' => 0,
                'counts' => []
            ]);
        }
    }

    /**
     * Send test notification to verify user settings.
     */
    public function sendTest(): JsonResponse
    {
        try {
            $user = auth()->user();
            $success = $this->clientNotificationService->sendTestNotification($user);

            if ($success) {
                return response()->json([
                    'success' => true,
                    'message' => 'Test notification sent successfully! Check your email and notifications.'
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
     * Get notification statistics for the index page.
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
     * Get notifications grouped by type.
     */
    protected function getNotificationsByType($user): array
    {
        try {
            return $user->notifications()
                ->selectRaw('type, COUNT(*) as count')
                ->groupBy('type')
                ->pluck('count', 'type')
                ->toArray();
        } catch (\Exception $e) {
            Log::error('Failed to get notifications by type: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Bulk delete notifications.
     */
    public function bulkDelete(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'notification_ids' => 'required|array',
                'notification_ids.*' => 'string|exists:notifications,id',
            ]);

            $user = auth()->user();
            $count = $user->notifications()
                ->whereIn('id', $validated['notification_ids'])
                ->delete();

            // Clear notification cache
            $this->clientNotificationService->clearNotificationCache($user);

            return response()->json([
                'success' => true,
                'message' => "{$count} notifications deleted successfully",
                'count' => $count
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to bulk delete notifications: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete notifications'
            ], 500);
        }
    }

    /**
     * Get most common notification type for user.
     */
    protected function getMostCommonNotificationType($user): ?string
    {
        try {
            $notification = $user->notifications()
                ->selectRaw('type, COUNT(*) as count')
                ->groupBy('type')
                ->orderBy('count', 'desc')
                ->first();

            return $notification?->type;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Export notifications to CSV.
     */
    public function export(Request $request)
    {
        try {
            $user = auth()->user();
            $filters = $request->validate([
                'date_from' => 'nullable|date',
                'date_to' => 'nullable|date|after_or_equal:date_from',
                'type' => 'nullable|string',
                'read' => 'nullable|string|in:read,unread',
            ]);

            $query = $user->notifications();

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

            if (!empty($filters['read'])) {
                if ($filters['read'] === 'unread') {
                    $query->whereNull('read_at');
                } else {
                    $query->whereNotNull('read_at');
                }
            }

            $notifications = $query->orderBy('created_at', 'desc')->get();

            $filename = 'notifications_' . now()->format('Y-m-d_H-i-s') . '.csv';
            
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            ];

            $callback = function() use ($notifications) {
                $file = fopen('php://output', 'w');
                
                // CSV headers
                fputcsv($file, [
                    'Type',
                    'Title',
                    'Message',
                    'Read Status',
                    'Created At',
                    'Read At'
                ]);

                // CSV data
                foreach ($notifications as $notification) {
                    $data = $notification->data;
                    fputcsv($file, [
                        $notification->type,
                        $data['title'] ?? 'N/A',
                        $data['message'] ?? 'N/A',
                        $notification->read_at ? 'Read' : 'Unread',
                        $notification->created_at->format('Y-m-d H:i:s'),
                        $notification->read_at ? $notification->read_at->format('Y-m-d H:i:s') : 'N/A'
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);

        } catch (\Exception $e) {
            Log::error('Failed to export notifications: ' . $e->getMessage());
            
            return redirect()->back()->with('error', 'Failed to export notifications.');
        }
    }
}