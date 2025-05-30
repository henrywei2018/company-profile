<?php

namespace App\Services;

use App\Models\User;
use App\Services\NotificationService;
use App\Services\TempNotifiable;
use Illuminate\Support\Facades\Log;

/**
 * Client Notification Service
 * 
 * Handles notifications specifically for clients, including
 * both registered and non-registered users.
 */
class ClientNotificationService
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService = null)
    {
        $this->notificationService = $notificationService ?? app(NotificationService::class);
    }

    /**
     * Send notification to client (registered or temp).
     */
    public function sendToClient(string $type, $data, $client): bool
    {
        try {
            // If client is already a User instance, use it directly
            if ($client instanceof User) {
                return $this->notificationService->send($type, $data, $client);
            }

            // If client is an array with email/name, create TempNotifiable
            if (is_array($client) && isset($client['email'])) {
                $tempNotifiable = TempNotifiable::fromArray($client);
                return $this->notificationService->send($type, $data, $tempNotifiable);
            }

            // If client is a string (email), create basic TempNotifiable
            if (is_string($client) && filter_var($client, FILTER_VALIDATE_EMAIL)) {
                $tempNotifiable = new TempNotifiable($client);
                return $this->notificationService->send($type, $data, $tempNotifiable);
            }

            Log::warning('Invalid client data for notification', [
                'type' => $type,
                'client' => $client
            ]);

            return false;

        } catch (\Exception $e) {
            Log::error('Failed to send client notification', [
                'type' => $type,
                'error' => $e->getMessage(),
                'client_type' => gettype($client)
            ]);

            return false;
        }
    }

    /**
     * Send project notification to client.
     */
    public function sendProjectNotification(string $type, $project, ?User $client = null): bool
    {
        $client = $client ?? $project->client;
        
        if (!$client) {
            Log::warning('No client found for project notification', [
                'project_id' => $project->id ?? 'unknown',
                'type' => $type
            ]);
            return false;
        }

        return $this->sendToClient($type, $project, $client);
    }

    /**
     * Send quotation notification to client.
     */
    public function sendQuotationNotification(string $type, $quotation): bool
    {
        // Try registered client first
        if ($quotation->client) {
            return $this->sendToClient($type, $quotation, $quotation->client);
        }

        // Fall back to email if no registered client
        if ($quotation->email) {
            $tempNotifiable = TempNotifiable::forQuotation(
                $quotation->email,
                $quotation->name
            );
            return $this->notificationService->send($type, $quotation, $tempNotifiable);
        }

        Log::warning('No client contact info for quotation notification', [
            'quotation_id' => $quotation->id ?? 'unknown',
            'type' => $type
        ]);

        return false;
    }

    /**
     * Send message notification to client.
     */
    public function sendMessageNotification(string $type, $message): bool
    {
        // Try registered client first
        if ($message->user) {
            return $this->sendToClient($type, $message, $message->user);
        }

        // Fall back to email if no registered client
        if ($message->email) {
            $tempNotifiable = TempNotifiable::forMessage(
                $message->email,
                $message->name
            );
            return $this->notificationService->send($type, $message, $tempNotifiable);
        }

        Log::warning('No client contact info for message notification', [
            'message_id' => $message->id ?? 'unknown',
            'type' => $type
        ]);

        return false;
    }

    /**
     * Send welcome notification to new client.
     */
    public function sendWelcomeNotification(User $client): bool
    {
        return $this->sendToClient('user.welcome', $client, $client);
    }

    /**
     * Send email verification notification.
     */
    public function sendEmailVerificationNotification(User $client): bool
    {
        return $this->sendToClient('user.verify_email', $client, $client);
    }

    /**
     * Send password reset notification.
     */
    public function sendPasswordResetNotification(User $client, string $token): bool
    {
        $data = [
            'user' => $client,
            'token' => $token,
            'url' => route('password.reset', ['token' => $token, 'email' => $client->email])
        ];

        return $this->sendToClient('user.password_reset', $data, $client);
    }

    /**
     * Send deadline reminder to client.
     */
    public function sendDeadlineReminder($project): bool
    {
        if (!$project->client) {
            return false;
        }

        return $this->sendProjectNotification('project.deadline_approaching', $project);
    }

    /**
     * Send project completion notification.
     */
    public function sendProjectCompletionNotification($project): bool
    {
        if (!$project->client) {
            return false;
        }

        return $this->sendProjectNotification('project.completed', $project);
    }

    /**
     * Send bulk notifications to multiple clients.
     */
    public function sendBulkToClients(string $type, $data, array $clients): array
    {
        $results = [];

        foreach ($clients as $client) {
            $results[] = $this->sendToClient($type, $data, $client);
        }

        return $results;
    }

    /**
     * Check if client has notification preferences allowing this type.
     */
    public function clientAllowsNotificationType(User $client, string $type): bool
    {
        // Check global email notifications preference
        if (!$client->email_notifications) {
            return false;
        }

        // Check specific notification type preferences
        $typeMapping = [
            'project.' => 'project_update_notifications',
            'quotation.' => 'quotation_update_notifications',
            'message.' => 'message_reply_notifications',
            'user.' => 'system_notifications',
        ];

        foreach ($typeMapping as $prefix => $preference) {
            if (str_starts_with($type, $prefix)) {
                return $client->{$preference} ?? true;
            }
        }

        return true; // Default to allow if no specific preference found
    }

    /**
     * Get client notification preferences.
     */
    public function getClientNotificationPreferences(User $client): array
    {
        return [
            'email_notifications' => $client->email_notifications ?? true,
            'project_updates' => $client->project_update_notifications ?? true,
            'quotation_updates' => $client->quotation_update_notifications ?? true,
            'message_replies' => $client->message_reply_notifications ?? true,
            'deadline_alerts' => $client->deadline_alert_notifications ?? true,
            'system_notifications' => $client->system_notifications ?? false,
            'marketing_emails' => $client->marketing_emails ?? false,
            'notification_frequency' => $client->notification_frequency ?? 'immediate',
            'quiet_hours' => $client->quiet_hours ?? null,
        ];
    }

    /**
     * Update client notification preferences.
     */
    public function updateClientNotificationPreferences(User $client, array $preferences): bool
    {
        try {
            $client->update([
                'email_notifications' => $preferences['email_notifications'] ?? true,
                'project_update_notifications' => $preferences['project_updates'] ?? true,
                'quotation_update_notifications' => $preferences['quotation_updates'] ?? true,
                'message_reply_notifications' => $preferences['message_replies'] ?? true,
                'deadline_alert_notifications' => $preferences['deadline_alerts'] ?? true,
                'system_notifications' => $preferences['system_notifications'] ?? false,
                'marketing_emails' => $preferences['marketing_emails'] ?? false,
                'notification_frequency' => $preferences['notification_frequency'] ?? 'immediate',
                'quiet_hours' => $preferences['quiet_hours'] ?? null,
            ]);

            // Send confirmation notification
            $this->sendToClient('user.preferences_updated', $preferences, $client);

            Log::info('Client notification preferences updated', [
                'client_id' => $client->id,
                'preferences' => array_keys($preferences)
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Failed to update client notification preferences', [
                'client_id' => $client->id,
                'error' => $e->getMessage()
            ]);

            return false;
        }
    }

    /**
     * Get notification statistics for client.
     */
    public function getClientNotificationStats(User $client): array
    {
        try {
            return [
                'total_notifications' => $client->notifications()->count(),
                'unread_notifications' => $client->unreadNotifications()->count(),
                'notifications_this_week' => $client->notifications()
                    ->where('created_at', '>=', now()->startOfWeek())
                    ->count(),
                'notifications_this_month' => $client->notifications()
                    ->where('created_at', '>=', now()->startOfMonth())
                    ->count(),
                'last_notification' => $client->notifications()
                    ->latest()
                    ->first()?->created_at,
                'most_common_type' => $this->getMostCommonNotificationType($client),
            ];
        } catch (\Exception $e) {
            Log::error('Failed to get client notification stats', [
                'client_id' => $client->id,
                'error' => $e->getMessage()
            ]);

            return [];
        }
    }

    /**
     * Get most common notification type for client.
     */
    protected function getMostCommonNotificationType(User $client): ?string
    {
        try {
            $notification = $client->notifications()
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
     * Send test notification to client.
     */
    public function sendTestNotification(User $client): bool
    {
        $testData = [
            'title' => 'Test Notification',
            'message' => 'This is a test notification to verify your settings are working correctly.',
            'timestamp' => now()->toISOString(),
        ];

        return $this->sendToClient('system.test', $testData, $client);
    }

    /**
     * Schedule delayed notification for client.
     */
    public function scheduleNotification(string $type, $data, $client, \Carbon\Carbon $sendAt): bool
    {
        try {
            // This would integrate with Laravel's job scheduling system
            // For now, log the scheduled notification
            Log::info('Client notification scheduled', [
                'type' => $type,
                'client_id' => $client instanceof User ? $client->id : 'temp',
                'send_at' => $sendAt->toISOString()
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to schedule client notification', [
                'type' => $type,
                'error' => $e->getMessage()
            ]);

            return false;
        }
    }

    /**
     * Clear notification cache for client.
     */
    public function clearNotificationCache(User $client): void
    {
        try {
            \Illuminate\Support\Facades\Cache::forget("client_notifications_{$client->id}");
            \Illuminate\Support\Facades\Cache::forget("client_notification_count_{$client->id}");
            
            Log::info('Client notification cache cleared', [
                'client_id' => $client->id
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to clear client notification cache', [
                'client_id' => $client->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}