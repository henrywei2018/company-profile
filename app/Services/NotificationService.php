<?php
// File: app/Services/NotificationService.php

namespace App\Services;

use App\Models\User;
use App\Models\Project;
use App\Models\Quotation;
use App\Models\Message;
use App\Models\ChatSession;
use App\Models\Certification;
use App\Models\Testimonial;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class NotificationService
{
    protected array $notificationClasses = [];
    protected array $enabledChannels = ['mail', 'database'];

    public function __construct()
    {
        $this->registerNotificationClasses();
    }

    /**
     * Register all notification classes
     */
    protected function registerNotificationClasses(): void
    {
        $this->notificationClasses = [
            // Project notifications
            'project.created' => \App\Notifications\ProjectCreatedNotification::class,
            'project.updated' => \App\Notifications\ProjectUpdatedNotification::class,
            'project.status_changed' => \App\Notifications\ProjectStatusChangedNotification::class,
            'project.deadline_approaching' => \App\Notifications\ProjectDeadlineNotification::class,
            'project.overdue' => \App\Notifications\ProjectOverdueNotification::class,
            'project.completed' => \App\Notifications\ProjectCompletedNotification::class,
            
            // Quotation notifications
            'quotation.created' => \App\Notifications\QuotationCreatedNotification::class,
            'quotation.status_updated' => \App\Notifications\QuotationStatusUpdatedNotification::class,
            'quotation.approved' => \App\Notifications\QuotationApprovedNotification::class,
            'quotation.client_response_needed' => \App\Notifications\QuotationClientResponseNotification::class,
            'quotation.expired' => \App\Notifications\QuotationExpiredNotification::class,
            'quotation.converted' => \App\Notifications\QuotationConvertedNotification::class,
            
            // Message notifications
            'message.created' => \App\Notifications\MessageCreatedNotification::class,
            'message.reply' => \App\Notifications\MessageReplyNotification::class,
            'message.urgent' => \App\Notifications\UrgentMessageNotification::class,
            'message.auto_reply' => \App\Notifications\MessageAutoReplyNotification::class,
            
            // Chat notifications
            'chat.session_started' => \App\Notifications\ChatSessionStartedNotification::class,
            'chat.message_received' => \App\Notifications\ChatMessageReceivedNotification::class,
            'chat.session_waiting' => \App\Notifications\ChatSessionWaitingNotification::class,
            'chat.session_inactive' => \App\Notifications\ChatSessionInactiveNotification::class,
            'chat.session_closed' => \App\Notifications\ChatSessionClosedNotification::class,
            
            // User notifications
            'user.welcome' => \App\Notifications\WelcomeNotification::class,
            'user.profile_incomplete' => \App\Notifications\ProfileIncompleteNotification::class,
            'user.email_verified' => \App\Notifications\EmailVerifiedNotification::class,
            'user.password_changed' => \App\Notifications\PasswordChangedNotification::class,
            
            // System notifications
            'system.maintenance' => \App\Notifications\SystemMaintenanceNotification::class,
            'system.backup_completed' => \App\Notifications\BackupCompletedNotification::class,
            'system.security_alert' => \App\Notifications\SecurityAlertNotification::class,
            'system.certificate_expiring' => \App\Notifications\CertificateExpiringNotification::class,
            
            // Testimonial notifications
            'testimonial.created' => \App\Notifications\TestimonialCreatedNotification::class,
            'testimonial.approved' => \App\Notifications\TestimonialApprovedNotification::class,
            'testimonial.featured' => \App\Notifications\TestimonialFeaturedNotification::class,
        ];
    }

    /**
     * Send notification with automatic recipient resolution
     */
    public function send(string $type, $data = null, $recipients = null, array $channels = null): bool
    {
        try {
            if (!isset($this->notificationClasses[$type])) {
                Log::warning("Notification type '{$type}' not registered");
                return false;
            }

            $notificationClass = $this->notificationClasses[$type];
            $notification = new $notificationClass($data);

            // Resolve recipients if not provided
            if ($recipients === null) {
                $recipients = $this->resolveRecipients($type, $data);
            }

            // Convert single recipient to collection
            if (!is_iterable($recipients)) {
                $recipients = collect([$recipients]);
            }

            // Filter recipients based on preferences
            $filteredRecipients = $this->filterRecipientsByPreferences($recipients, $type);

            if (empty($filteredRecipients)) {
                return true; // No recipients to notify
            }

            // Send notification
            Notification::send($filteredRecipients, $notification);

            // Log successful notification
            $this->logNotification($type, $data, count($filteredRecipients));

            return true;

        } catch (\Exception $e) {
            Log::error("Failed to send notification '{$type}': " . $e->getMessage(), [
                'type' => $type,
                'data' => $data,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Resolve notification recipients based on type and data
     */
    protected function resolveRecipients(string $type, $data)
    {
        switch ($type) {
            // Project notifications
            case 'project.created':
            case 'project.updated':
            case 'project.status_changed':
            case 'project.completed':
                return $this->getProjectNotificationRecipients($data);

            case 'project.deadline_approaching':
            case 'project.overdue':
                return $this->getProjectDeadlineRecipients($data);

            // Quotation notifications
            case 'quotation.created':
                return $this->getQuotationCreatedRecipients($data);

            case 'quotation.status_updated':
            case 'quotation.approved':
            case 'quotation.client_response_needed':
            case 'quotation.expired':
                return $this->getQuotationUpdateRecipients($data);

            case 'quotation.converted':
                return $this->getQuotationConvertedRecipients($data);

            // Message notifications
            case 'message.created':
                return $this->getMessageCreatedRecipients($data);

            case 'message.reply':
                return $this->getMessageReplyRecipients($data);

            case 'message.urgent':
                return $this->getUrgentMessageRecipients($data);

            // Chat notifications
            case 'chat.session_started':
            case 'chat.message_received':
            case 'chat.session_waiting':
            case 'chat.session_inactive':
                return $this->getChatNotificationRecipients($data);

            // User notifications
            case 'user.welcome':
            case 'user.profile_incomplete':
            case 'user.email_verified':
            case 'user.password_changed':
                return $data instanceof User ? $data : null;

            // System notifications
            case 'system.maintenance':
            case 'system.backup_completed':
            case 'system.security_alert':
            case 'system.certificate_expiring':
                return $this->getSystemNotificationRecipients();

            // Testimonial notifications
            case 'testimonial.created':
                return $this->getTestimonialCreatedRecipients($data);

            case 'testimonial.approved':
            case 'testimonial.featured':
                return $this->getTestimonialUpdateRecipients($data);

            default:
                return collect();
        }
    }

    /**
     * Get project notification recipients
     */
    protected function getProjectNotificationRecipients($project)
    {
        $recipients = collect();

        if ($project instanceof Project) {
            // Add client
            if ($project->client) {
                $recipients->push($project->client);
            }

            // Add project managers and admins
            $recipients = $recipients->merge(
                User::role(['super-admin', 'admin', 'manager'])->get()
            );
        }

        return $recipients;
    }

    /**
     * Get project deadline notification recipients
     */
    protected function getProjectDeadlineRecipients($project)
    {
        $recipients = collect();

        if ($project instanceof Project) {
            // Always notify client about their project deadlines
            if ($project->client) {
                $recipients->push($project->client);
            }

            // Notify admins about overdue projects
            if ($project->end_date && $project->end_date->isPast()) {
                $recipients = $recipients->merge(
                    User::role(['super-admin', 'admin', 'manager'])->get()
                );
            }
        }

        return $recipients;
    }

    /**
     * Get quotation created notification recipients
     */
    protected function getQuotationCreatedRecipients($quotation)
    {
        $recipients = collect();

        if ($quotation instanceof Quotation) {
            // Notify admins about new quotations
            $recipients = $recipients->merge(
                User::role(['super-admin', 'admin', 'sales'])->get()
            );
        }

        return $recipients;
    }

    /**
     * Get quotation update notification recipients
     */
    protected function getQuotationUpdateRecipients($quotation)
    {
        $recipients = collect();

        if ($quotation instanceof Quotation) {
            // Notify client about quotation updates
            if ($quotation->client) {
                $recipients->push($quotation->client);
            }

            // Also notify admins for status tracking
            $recipients = $recipients->merge(
                User::role(['super-admin', 'admin', 'sales'])->get()
            );
        }

        return $recipients;
    }

    /**
     * Get quotation converted notification recipients
     */
    protected function getQuotationConvertedRecipients($quotation)
    {
        $recipients = collect();

        if ($quotation instanceof Quotation) {
            // Notify client about project creation from quotation
            if ($quotation->client) {
                $recipients->push($quotation->client);
            }
        }

        return $recipients;
    }

    /**
     * Get message created notification recipients
     */
    protected function getMessageCreatedRecipients($message)
    {
        $recipients = collect();

        if ($message instanceof Message) {
            if ($message->type === 'client_to_admin') {
                // Client sent message to admin - notify admins
                $recipients = $recipients->merge(
                    User::role(['super-admin', 'admin', 'support'])->get()
                );
            } elseif ($message->type === 'admin_to_client') {
                // Admin sent message to client - notify client
                if ($message->user) {
                    $recipients->push($message->user);
                }
            }
        }

        return $recipients;
    }

    /**
     * Get message reply notification recipients
     */
    protected function getMessageReplyRecipients($message)
    {
        $recipients = collect();

        if ($message instanceof Message) {
            // If it's a reply, notify the original sender
            if ($message->parent && $message->parent->user) {
                $recipients->push($message->parent->user);
            }
        }

        return $recipients;
    }

    /**
     * Get urgent message notification recipients
     */
    protected function getUrgentMessageRecipients($message)
    {
        // Urgent messages go to all available admins
        return User::role(['super-admin', 'admin', 'support'])
            ->where('is_active', true)
            ->get();
    }

    /**
     * Get chat notification recipients
     */
    protected function getChatNotificationRecipients($chatSession)
    {
        if ($chatSession instanceof ChatSession) {
            // Notify available chat operators
            return User::role(['super-admin', 'admin', 'support'])
                ->where('is_active', true)
                ->get();
        }

        return collect();
    }

    /**
     * Get system notification recipients
     */
    protected function getSystemNotificationRecipients()
    {
        // System notifications go to super admins
        return User::role('super-admin')->get();
    }

    /**
     * Get testimonial created notification recipients
     */
    protected function getTestimonialCreatedRecipients($testimonial)
    {
        // Notify admins about new testimonials for approval
        return User::role(['super-admin', 'admin', 'marketing'])->get();
    }

    /**
     * Get testimonial update notification recipients
     */
    protected function getTestimonialUpdateRecipients($testimonial)
    {
        $recipients = collect();

        if ($testimonial instanceof Testimonial) {
            // Notify the client who wrote the testimonial
            if ($testimonial->project && $testimonial->project->client) {
                $recipients->push($testimonial->project->client);
            }
        }

        return $recipients;
    }

    /**
     * Filter recipients based on their notification preferences
     */
    protected function filterRecipientsByPreferences($recipients, string $type): array
    {
        $filtered = [];

        foreach ($recipients as $recipient) {
            if ($this->shouldNotifyRecipient($recipient, $type)) {
                $filtered[] = $recipient;
            }
        }

        return $filtered;
    }

    /**
     * Check if recipient should receive notification based on preferences
     */
    protected function shouldNotifyRecipient($recipient, string $type): bool
    {
        if (!$recipient instanceof User) {
            return false;
        }

        // Check if user is active
        if (!$recipient->is_active) {
            return false;
        }

        // Check global notification preference
        if (isset($recipient->email_notifications) && !$recipient->email_notifications) {
            return false;
        }

        // Check specific notification type preferences
        $preferenceKey = $this->getPreferenceKey($type);
        if ($preferenceKey && isset($recipient->{$preferenceKey}) && !$recipient->{$preferenceKey}) {
            return false;
        }

        return true;
    }

    /**
     * Get preference key for notification type
     */
    protected function getPreferenceKey(string $type): ?string
    {
        $preferences = [
            'project.' => 'project_update_notifications',
            'quotation.' => 'quotation_update_notifications',
            'message.' => 'message_reply_notifications',
            'chat.' => 'chat_notifications',
            'system.' => 'system_notifications',
            'testimonial.' => 'testimonial_notifications',
        ];

        foreach ($preferences as $prefix => $key) {
            if (str_starts_with($type, $prefix)) {
                return $key;
            }
        }

        return null;
    }

    /**
     * Log notification sending
     */
    protected function logNotification(string $type, $data, int $recipientCount): void
    {
        Log::info("Notification sent", [
            'type' => $type,
            'recipient_count' => $recipientCount,
            'data_type' => is_object($data) ? get_class($data) : gettype($data),
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Send bulk notification to multiple recipients
     */
    public function sendBulk(string $type, array $dataRecipientPairs): array
    {
        $results = [];

        foreach ($dataRecipientPairs as $item) {
            $data = $item['data'] ?? null;
            $recipients = $item['recipients'] ?? null;
            $channels = $item['channels'] ?? null;

            $results[] = $this->send($type, $data, $recipients, $channels);
        }

        return $results;
    }

    /**
     * Schedule notification for later sending
     */
    public function schedule(string $type, $data, $recipients, Carbon $sendAt, array $channels = null): bool
    {
        // This would integrate with Laravel's job queue system
        // For now, we'll just log the scheduling
        Log::info("Notification scheduled", [
            'type' => $type,
            'send_at' => $sendAt->toISOString(),
            'recipient_count' => is_countable($recipients) ? count($recipients) : 1
        ]);

        return true;
    }

    /**
     * Get notification statistics
     */
    public function getStatistics(): array
    {
        return [
            'total_types' => count($this->notificationClasses),
            'enabled_channels' => $this->enabledChannels,
            'recent_sent' => $this->getRecentNotificationCount(),
            'failed_today' => $this->getFailedNotificationCount(),
        ];
    }

    /**
     * Get recent notification count
     */
    protected function getRecentNotificationCount(): int
    {
        // This would query notification logs or database
        return Cache::get('notifications_sent_today', 0);
    }

    /**
     * Get failed notification count
     */
    protected function getFailedNotificationCount(): int
    {
        // This would query failed jobs or error logs
        return Cache::get('notifications_failed_today', 0);
    }

    /**
     * Test notification system
     */
    public function test(User $user): array
    {
        $results = [];

        try {
            // Test basic notification
            $result = $this->send('user.welcome', $user);
            $results['welcome_notification'] = $result ? 'success' : 'failed';

        } catch (\Exception $e) {
            $results['welcome_notification'] = 'error: ' . $e->getMessage();
        }

        return $results;
    }

    /**
     * Clear notification cache
     */
    public function clearCache(): void
    {
        $keys = [
            'notifications_sent_today',
            'notifications_failed_today',
            'notification_preferences_*',
            'user_notification_count_*'
        ];

        foreach ($keys as $key) {
            if (str_contains($key, '*')) {
                // Clear pattern-based cache keys
                Cache::forget($key);
            } else {
                Cache::forget($key);
            }
        }
    }

    /**
     * Get available notification types
     */
    public function getAvailableTypes(): array
    {
        return array_keys($this->notificationClasses);
    }

    /**
     * Check if notification type exists
     */
    public function hasType(string $type): bool
    {
        return isset($this->notificationClasses[$type]);
    }

    /**
     * Register new notification type
     */
    public function registerType(string $type, string $notificationClass): void
    {
        $this->notificationClasses[$type] = $notificationClass;
    }

    /**
     * Unregister notification type
     */
    public function unregisterType(string $type): void
    {
        unset($this->notificationClasses[$type]);
    }
}