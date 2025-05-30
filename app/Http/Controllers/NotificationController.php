<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Facades\Notifications;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class NotificationController extends Controller
{
    /**
     * Display notifications for admin users
     */
    public function index(Request $request)
    {
        if (!Auth::user()->hasAdminAccess()) {
            abort(403, 'Admin access required');
        }

        $query = Auth::user()->notifications();

        // Filter by read status
        if ($request->filled('read_status')) {
            if ($request->read_status === 'unread') {
                $query->whereNull('read_at');
            } elseif ($request->read_status === 'read') {
                $query->whereNotNull('read_at');
            }
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', 'like', '%' . $request->type . '%');
        }

        // Search in notification data
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('data->title', 'like', "%{$search}%")
                  ->orWhere('data->message', 'like', "%{$search}%");
            });
        }

        // Sort by date
        $query->orderBy('created_at', $request->get('sort', 'desc'));

        $notifications = $query->paginate(20);

        // Format notifications for display
        $formattedNotifications = $notifications->getCollection()->map(function ($notification) {
            return [
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
                'notification' => $notification, // For additional processing if needed
            ];
        });

        return view('admin.notifications.index', [
            'notifications' => $notifications->setCollection($formattedNotifications),
            'filters' => [
                'read_status' => $request->read_status,
                'type' => $request->type,
                'search' => $request->search,
                'sort' => $request->sort,
            ],
            'stats' => [
                'total' => Auth::user()->notifications()->count(),
                'unread' => Auth::user()->unreadNotifications()->count(),
                'today' => Auth::user()->notifications()->whereDate('created_at', today())->count(),
            ]
        ]);
    }

    /**
     * Show a specific notification
     */
    public function show(Request $request, $notificationId)
    {
        $notification = Auth::user()->notifications()
            ->where('id', $notificationId)
            ->firstOrFail();

        // Mark as read if not already read
        if (is_null($notification->read_at)) {
            $notification->markAsRead();
        }

        $formattedNotification = [
            'id' => $notification->id,
            'type' => $this->getNotificationType($notification->type),
            'title' => $notification->data['title'] ?? 'Notification',
            'message' => $notification->data['message'] ?? '',
            'action_url' => $notification->data['action_url'] ?? null,
            'action_text' => $notification->data['action_text'] ?? null,
            'created_at' => $notification->created_at,
            'read_at' => $notification->read_at,
            'full_data' => $notification->data,
        ];

        return view('admin.notifications.show', [
            'notification' => $formattedNotification
        ]);
    }

    /**
     * Delete a notification
     */
    public function destroy(Request $request, $notificationId)
    {
        $notification = Auth::user()->notifications()
            ->where('id', $notificationId)
            ->firstOrFail();

        $notification->delete();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Notification deleted successfully'
            ]);
        }

        return redirect()->route('admin.notifications.index')
            ->with('success', 'Notification deleted successfully');
    }

    /**
     * Bulk mark notifications as read
     */
    public function bulkMarkAsRead(Request $request)
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
            'message' => "{$count} notification(s) marked as read"
        ]);
    }

    /**
     * Bulk delete notifications
     */
    public function bulkDelete(Request $request)
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
            'message' => "{$count} notification(s) deleted"
        ]);
    }

    /**
     * Show notification settings for admin
     */
    public function settings(Request $request)
    {
        if (!Auth::user()->hasAdminAccess()) {
            abort(403, 'Admin access required');
        }

        $user = Auth::user();
        
        // Get current notification preferences
        $preferences = [
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

        return view('admin.notifications.settings', [
            'preferences' => $preferences,
            'notification_types' => $this->getNotificationTypes(),
        ]);
    }

    /**
     * Update notification settings
     */
    public function updateSettings(Request $request)
    {
        if (!Auth::user()->hasAdminAccess()) {
            abort(403, 'Admin access required');
        }

        $request->validate([
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

        $user = Auth::user();
        
        $user->update([
            'email_notifications' => $request->boolean('email_notifications'),
            'database_notifications' => $request->boolean('database_notifications'),
            'project_notifications' => $request->boolean('project_notifications'),
            'quotation_notifications' => $request->boolean('quotation_notifications'),
            'message_notifications' => $request->boolean('message_notifications'),
            'chat_notifications' => $request->boolean('chat_notifications'),
            'system_notifications' => $request->boolean('system_notifications'),
            'urgent_notifications_only' => $request->boolean('urgent_only'),
            'notification_frequency' => $request->notification_frequency,
            'quiet_hours_enabled' => $request->boolean('quiet_hours_enabled'),
            'quiet_hours_start' => $request->quiet_hours_start,
            'quiet_hours_end' => $request->quiet_hours_end,
        ]);

        return redirect()->route('admin.notifications.settings')
            ->with('success', 'Notification settings updated successfully');
    }

    /**
     * Show notification preferences for clients
     */
    public function preferences(Request $request)
    {
        $user = Auth::user();
        
        $preferences = [
            'email_notifications' => $user->email_notifications ?? true,
            'project_update_notifications' => $user->project_update_notifications ?? true,
            'quotation_update_notifications' => $user->quotation_update_notifications ?? true,
            'message_reply_notifications' => $user->message_reply_notifications ?? true,
            'deadline_reminder_notifications' => $user->deadline_reminder_notifications ?? true,
            'newsletter_subscription' => $user->newsletter_subscription ?? false,
            'marketing_emails' => $user->marketing_emails ?? false,
            'notification_frequency' => $user->notification_frequency ?? 'immediate',
        ];

        return view('client.notifications.preferences', [
            'preferences' => $preferences
        ]);
    }

    /**
     * Update client notification preferences
     */
    public function updatePreferences(Request $request)
    {
        $request->validate([
            'email_notifications' => 'boolean',
            'project_update_notifications' => 'boolean',
            'quotation_update_notifications' => 'boolean',
            'message_reply_notifications' => 'boolean',
            'deadline_reminder_notifications' => 'boolean',
            'newsletter_subscription' => 'boolean',
            'marketing_emails' => 'boolean',
            'notification_frequency' => 'in:immediate,daily,weekly',
        ]);

        $user = Auth::user();
        
        $user->update([
            'email_notifications' => $request->boolean('email_notifications'),
            'project_update_notifications' => $request->boolean('project_update_notifications'),
            'quotation_update_notifications' => $request->boolean('quotation_update_notifications'),
            'message_reply_notifications' => $request->boolean('message_reply_notifications'),
            'deadline_reminder_notifications' => $request->boolean('deadline_reminder_notifications'),
            'newsletter_subscription' => $request->boolean('newsletter_subscription'),
            'marketing_emails' => $request->boolean('marketing_emails'),
            'notification_frequency' => $request->notification_frequency,
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Notification preferences updated successfully'
            ]);
        }

        return redirect()->route('client.notifications.preferences')
            ->with('success', 'Notification preferences updated successfully');
    }

    /**
     * Handle email delivery webhooks
     */
    public function handleEmailDelivery(Request $request)
    {
        // Verify webhook signature if needed
        $this->verifyWebhookSignature($request);

        $data = $request->all();
        
        Log::info('Email delivery webhook received', ['data' => $data]);

        // Process email delivery confirmation
        // Update notification status, track delivery metrics, etc.
        
        return response()->json(['status' => 'success']);
    }

    /**
     * Handle email bounce webhooks
     */
    public function handleEmailBounce(Request $request)
    {
        $this->verifyWebhookSignature($request);

        $data = $request->all();
        
        Log::warning('Email bounce webhook received', ['data' => $data]);

        // Handle bounced emails
        if (isset($data['email'])) {
            $user = User::where('email', $data['email'])->first();
            if ($user) {
                // Mark email as bounced, potentially disable email notifications
                $user->update([
                    'email_bounced' => true,
                    'email_notifications' => false,
                    'bounce_reason' => $data['reason'] ?? 'Unknown',
                    'bounced_at' => now()
                ]);
            }
        }
        
        return response()->json(['status' => 'success']);
    }

    /**
     * Handle email complaint webhooks
     */
    public function handleEmailComplaint(Request $request)
    {
        $this->verifyWebhookSignature($request);

        $data = $request->all();
        
        Log::warning('Email complaint webhook received', ['data' => $data]);

        // Handle spam complaints
        if (isset($data['email'])) {
            $user = User::where('email', $data['email'])->first();
            if ($user) {
                // Disable all email notifications for this user
                $user->update([
                    'email_notifications' => false,
                    'marketing_emails' => false,
                    'newsletter_subscription' => false,
                    'complaint_filed' => true,
                    'complaint_reason' => $data['reason'] ?? 'Spam complaint',
                    'complained_at' => now()
                ]);
            }
        }
        
        return response()->json(['status' => 'success']);
    }

    /**
     * Handle unsubscribe requests
     */
    public function unsubscribe(Request $request, $token)
    {
        $user = $this->findUserByUnsubscribeToken($token);
        
        if (!$user) {
            abort(404, 'Invalid unsubscribe link');
        }

        return view('notifications.unsubscribe', [
            'user' => $user,
            'token' => $token
        ]);
    }

    /**
     * Process unsubscribe request
     */
    public function processUnsubscribe(Request $request, $token)
    {
        $user = $this->findUserByUnsubscribeToken($token);
        
        if (!$user) {
            abort(404, 'Invalid unsubscribe link');
        }

        $request->validate([
            'unsubscribe_type' => 'required|in:all,marketing,notifications',
            'reason' => 'nullable|string|max:500'
        ]);

        switch ($request->unsubscribe_type) {
            case 'all':
                $user->update([
                    'email_notifications' => false,
                    'marketing_emails' => false,
                    'newsletter_subscription' => false,
                ]);
                $message = 'You have been unsubscribed from all emails.';
                break;
                
            case 'marketing':
                $user->update([
                    'marketing_emails' => false,
                    'newsletter_subscription' => false,
                ]);
                $message = 'You have been unsubscribed from marketing emails.';
                break;
                
            case 'notifications':
                $user->update([
                    'email_notifications' => false,
                ]);
                $message = 'You have been unsubscribed from notification emails.';
                break;
        }

        // Log unsubscribe
        Log::info('User unsubscribed', [
            'user_id' => $user->id,
            'email' => $user->email,
            'type' => $request->unsubscribe_type,
            'reason' => $request->reason
        ]);

        return view('notifications.unsubscribe-success', [
            'message' => $message,
            'user' => $user
        ]);
    }

    /**
     * Send test notification (for debugging in local environment)
     */
    public function sendTestNotification(Request $request)
    {
        if (!app()->environment('local')) {
            abort(403, 'Test notifications only available in local environment');
        }

        $request->validate([
            'type' => 'required|string',
            'recipient_email' => 'nullable|email',
            'test_data' => 'nullable|array'
        ]);

        $user = Auth::user();
        $recipient = $request->recipient_email 
            ? User::where('email', $request->recipient_email)->first() ?? $user
            : $user;

        $testData = $request->test_data ?? [
            'title' => 'Test Notification',
            'message' => 'This is a test notification sent at ' . now()->format('Y-m-d H:i:s'),
            'action_url' => route('admin.dashboard'),
            'action_text' => 'View Dashboard'
        ];

        try {
            Notifications::send($request->type, $testData, $recipient);
            
            return response()->json([
                'success' => true,
                'message' => 'Test notification sent successfully',
                'recipient' => $recipient->email,
                'type' => $request->type
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send test notification: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get notification statistics for admin dashboard
     */
    public function getStats(Request $request)
    {
        if (!Auth::user()->hasAdminAccess()) {
            abort(403, 'Admin access required');
        }

        $stats = [
            'total_sent' => \DB::table('notifications')->count(),
            'unread_count' => \DB::table('notifications')->whereNull('read_at')->count(),
            'today_sent' => \DB::table('notifications')->whereDate('created_at', today())->count(),
            'this_week' => \DB::table('notifications')->whereBetween('created_at', [
                now()->startOfWeek(),
                now()->endOfWeek()
            ])->count(),
            'types_breakdown' => \DB::table('notifications')
                ->select('type', \DB::raw('count(*) as count'))
                ->groupBy('type')
                ->orderBy('count', 'desc')
                ->limit(10)
                ->get()
                ->mapWithKeys(function ($item) {
                    return [$this->getNotificationType($item->type) => $item->count];
                }),
            'daily_trends' => $this->getDailyNotificationTrends(),
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }

    /**
     * Helper methods
     */
    private function verifyWebhookSignature(Request $request): void
    {
        // Implement webhook signature verification based on your email service provider
        // Example for AWS SES, SendGrid, Mailgun, etc.
        
        $signature = $request->header('X-Webhook-Signature');
        $secret = config('services.email.webhook_secret');
        
        if (!$signature || !$secret) {
            return; // Skip verification if not configured
        }

        $expectedSignature = hash_hmac('sha256', $request->getContent(), $secret);
        
        if (!hash_equals($signature, $expectedSignature)) {
            abort(401, 'Invalid webhook signature');
        }
    }

    private function findUserByUnsubscribeToken(string $token): ?User
    {
        // Decode the unsubscribe token to find user
        // This is a simple implementation - you might want to use JWT or encrypted tokens
        try {
            $decoded = base64_decode($token);
            $parts = explode(':', $decoded);
            
            if (count($parts) !== 2) {
                return null;
            }
            
            [$userId, $hash] = $parts;
            $user = User::find($userId);
            
            if (!$user || !Hash::check($user->email . $user->created_at, $hash)) {
                return null;
            }
            
            return $user;
            
        } catch (\Exception $e) {
            return null;
        }
    }

    private function getNotificationType(string $class): string
    {
        // Extract readable notification type from class name
        $shortName = class_basename($class);
        return str_replace('Notification', '', $shortName);
    }

    private function getNotificationTypes(): array
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

    private function getDailyNotificationTrends(): array
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
     * Export notifications data
     */
    public function export(Request $request)
    {
        if (!Auth::user()->hasAdminAccess()) {
            abort(403, 'Admin access required');
        }

        $format = $request->get('format', 'csv');
        $notifications = Auth::user()->notifications()
            ->orderBy('created_at', 'desc')
            ->get();

        $filename = 'notifications_export_' . now()->format('Y-m-d_H-i-s');

        switch ($format) {
            case 'json':
                return response()->json($notifications)
                    ->header('Content-Disposition', "attachment; filename=\"{$filename}.json\"");
                    
            case 'csv':
            default:
                return $this->exportToCsv($notifications, $filename);
        }
    }

    private function exportToCsv($notifications, string $filename)
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