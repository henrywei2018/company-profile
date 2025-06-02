<?php
// File: app/Traits/SendsNotifications.php

namespace App\Traits;

use App\Services\NotificationService;
use App\Services\TempNotifiable;
use App\Facades\Notifications;
use Illuminate\Support\Facades\Log;

trait SendsNotifications
{
    /**
     * Send notification with error handling and logging
     */
    protected function sendNotification(string $type, $data, $recipients = null): bool
    {
        try {
            $success = Notifications::send($type, $data, $recipients);
            
            $this->logNotificationAttempt($type, $data, $success, $recipients);
            
            return $success;
        } catch (\Exception $e) {
            Log::error("Failed to send notification", [
                'type' => $type,
                'data_type' => is_object($data) ? get_class($data) : gettype($data),
                'data_id' => is_object($data) && method_exists($data, 'getKey') ? $data->getKey() : null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Send notification to admins
     */
    protected function notifyAdmins(string $type, $data): bool
    {
        return $this->sendNotification($type, $data);
    }

    /**
     * Send notification to clients
     */
    protected function notifyClient($data, string $type, $clientEmail = null, $clientName = null): bool
    {
        // Try to find registered user first
        if (method_exists($data, 'user') && $data->user) {
            $recipient = $data->user;
        } elseif (method_exists($data, 'client') && $data->client) {
            $recipient = $data->client;
        } else {
            // Use provided email or extract from data
            $email = $clientEmail ?? ($data->email ?? null);
            $name = $clientName ?? ($data->name ?? 'Client');
            
            if (!$email) {
                Log::warning("Cannot send client notification - no email found", [
                    'type' => $type,
                    'data_type' => get_class($data),
                    'data_id' => is_object($data) && method_exists($data, 'getKey') ? $data->getKey() : null
                ]);
                return false;
            }
            
            $recipient = TempNotifiable::forMessage($email, $name);
        }

        return $this->sendNotification($type, $data, $recipient);
    }

    /**
     * Check if notifications are enabled for this type
     */
    protected function isNotificationEnabled(string $type): bool
    {
        $typeMapping = [
            'message.created' => 'message_notifications_enabled',
            'message.reply' => 'message_reply_notifications_enabled', 
            'message.urgent' => 'urgent_notifications_enabled',
            'message.auto_reply' => 'message_auto_reply_enabled',
            'project.created' => 'project_notifications_enabled',
            'quotation.created' => 'quotation_notifications_enabled',
            'user.welcome' => 'welcome_notifications_enabled',
            'chat.session_started' => 'chat_notifications_enabled',
            'testimonial.created' => 'testimonial_notifications_enabled',
            'system.certificate_expiring' => 'certificate_notifications_enabled',
        ];

        $setting = $typeMapping[$type] ?? null;
        
        if ($setting) {
            return settings($setting, true);
        }

        // Check category-level settings
        $category = explode('.', $type)[0];
        return settings("{$category}_notifications_enabled", true);
    }

    /**
     * Send notification only if enabled
     */
    protected function sendIfEnabled(string $type, $data, $recipients = null): bool
    {
        if (!$this->isNotificationEnabled($type)) {
            Log::info("Notification disabled by settings", [
                'type' => $type,
                'data_type' => is_object($data) ? get_class($data) : gettype($data)
            ]);
            return true; // Return true since it's not an error
        }

        return $this->sendNotification($type, $data, $recipients);
    }

    /**
     * Log notification attempt
     */
    protected function logNotificationAttempt(string $type, $data, bool $success, $recipients = null): void
    {
        Log::info("Notification attempt", [
            'type' => $type,
            'success' => $success,
            'data_type' => is_object($data) ? get_class($data) : gettype($data),
            'data_id' => is_object($data) && method_exists($data, 'getKey') ? $data->getKey() : null,
            'recipient_count' => is_countable($recipients) ? count($recipients) : (is_null($recipients) ? 'auto-resolved' : 1),
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Send bulk notifications
     */
    protected function sendBulkNotifications(string $type, array $dataRecipientPairs): array
    {
        $results = [];
        
        foreach ($dataRecipientPairs as $item) {
            $data = $item['data'] ?? null;
            $recipients = $item['recipients'] ?? null;
            
            $results[] = $this->sendNotification($type, $data, $recipients);
        }

        Log::info("Bulk notification completed", [
            'type' => $type,
            'total_sent' => count($dataRecipientPairs),
            'successful' => count(array_filter($results))
        ]);

        return $results;
    }
}