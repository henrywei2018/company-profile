<?php
// File: app/Services/NotificationDataService.php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class NotificationDataService
{
    /**
     * Format notifications for header display
     */
    public function formatNotificationsForHeader(Collection $notifications): Collection
    {
        return $notifications->map(function ($notification) {
            return $this->formatSingleNotification($notification);
        });
    }

    /**
     * Format a single notification for display
     */
    public function formatSingleNotification($notification): array
    {
        // Handle both database notification objects and arrays
        if (is_object($notification)) {
            $data = $notification->data ?? [];
            $notificationArray = [
                'id' => $notification->id,
                'type' => $notification->type ?? 'notification',
                'read_at' => $notification->read_at,
                'created_at' => $notification->created_at,
                'data' => $data
            ];
        } else {
            $notificationArray = $notification;
            $data = $notification['data'] ?? $notification;
        }

        // Extract the core notification data
        $coreData = $this->extractCoreData($data);
        
        return [
            'id' => $notificationArray['id'] ?? uniqid(),
            'type' => $this->getDisplayType($notificationArray['type'] ?? 'notification'),
            'title' => $coreData['title'] ?? $this->generateTitle($notificationArray['type'] ?? 'notification'),
            'message' => $coreData['message'] ?? '',
            'action_url' => $coreData['action_url'] ?? '#',
            'action_text' => $coreData['action_text'] ?? 'View',
            'icon' => $this->getNotificationIcon($notificationArray['type'] ?? 'notification'),
            'color' => $this->getNotificationColor($notificationArray['type'] ?? 'notification'),
            'priority' => $coreData['priority'] ?? 'normal',
            'created_at' => $notificationArray['created_at'] ?? now(),
            'read_at' => $notificationArray['read_at'] ?? null,
            'is_read' => !empty($notificationArray['read_at']),
            'time_ago' => $this->getTimeAgo($notificationArray['created_at'] ?? now())
        ];
    }

    /**
     * Extract core data from nested notification data structure
     */
    protected function extractCoreData($data): array
    {
        // If data is nested (like your JSON structure), extract the inner data
        if (isset($data['data']) && is_array($data['data'])) {
            $innerData = $data['data'];
            return [
                'title' => $data['title'] ?? $innerData['title'] ?? null,
                'message' => $data['message'] ?? $innerData['message'] ?? null,
                'action_url' => $data['action_url'] ?? $innerData['action_url'] ?? null,
                'action_text' => $data['action_text'] ?? $innerData['action_text'] ?? null,
                'priority' => $data['priority'] ?? $innerData['priority'] ?? 'normal',
            ];
        }

        // Direct data structure
        return [
            'title' => $data['title'] ?? null,
            'message' => $data['message'] ?? null,
            'action_url' => $data['action_url'] ?? null,
            'action_text' => $data['action_text'] ?? null,
            'priority' => $data['priority'] ?? 'normal',
        ];
    }

    /**
     * Generate title based on notification type
     */
    protected function generateTitle(string $type): string
    {
        return match(true) {
            str_contains($type, 'chat.message_received') => 'New Chat Message',
            str_contains($type, 'chat.session_started') => 'New Chat Session',
            str_contains($type, 'project.created') => 'New Project Created',
            str_contains($type, 'project.completed') => 'Project Completed',
            str_contains($type, 'quotation.created') => 'New Quotation Request',
            str_contains($type, 'quotation.approved') => 'Quotation Approved',
            str_contains($type, 'message.created') => 'New Message',
            str_contains($type, 'user.welcome') => 'Welcome!',
            default => 'Notification'
        };
    }

    /**
     * Get display type
     */
    protected function getDisplayType(string $type): string
    {
        $parts = explode('.', $type);
        return ucfirst($parts[0] ?? 'notification');
    }

    /**
     * Get notification icon
     */
    protected function getNotificationIcon(string $type): string
    {
        return match(true) {
            str_contains($type, 'chat') => 'message-square',
            str_contains($type, 'project') => 'folder',
            str_contains($type, 'quotation') => 'document-text',
            str_contains($type, 'message') => 'mail',
            str_contains($type, 'user') => 'user',
            str_contains($type, 'system') => 'cog',
            default => 'bell'
        };
    }

    /**
     * Get notification color
     */
    protected function getNotificationColor(string $type): string
    {
        return match(true) {
            str_contains($type, 'completed') || str_contains($type, 'approved') => 'green',
            str_contains($type, 'urgent') || str_contains($type, 'overdue') => 'red',
            str_contains($type, 'deadline') || str_contains($type, 'pending') => 'yellow',
            str_contains($type, 'created') || str_contains($type, 'new') => 'blue',
            default => 'gray'
        };
    }

    /**
     * Get time ago string
     */
    protected function getTimeAgo($timestamp): string
    {
        try {
            if (is_string($timestamp)) {
                $timestamp = \Carbon\Carbon::parse($timestamp);
            }
            return $timestamp->diffForHumans();
        } catch (\Exception $e) {
            return 'Unknown time';
        }
    }

    /**
     * Get recent notifications for a user
     */
    public function getRecentNotifications(User $user, int $limit = 5): Collection
    {
        try {
            $notifications = $user->notifications()
                ->latest()
                ->limit($limit)
                ->get();

            return $this->formatNotificationsForHeader($notifications);
        } catch (\Exception $e) {
            Log::error('Failed to get recent notifications: ' . $e->getMessage());
            return collect();
        }
    }

    /**
     * Get unread notification count
     */
    public function getUnreadCount(User $user): int
    {
        try {
            return $user->unreadNotifications()->count();
        } catch (\Exception $e) {
            Log::error('Failed to get unread notification count: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Test notification data formatting
     */
    public function testNotificationFormatting(): array
    {
        // Sample notification data like yours
        $sampleNotification = [
            'id' => 'test-123',
            'type' => 'chat.message_received',
            'data' => [
                "priority" => "normal",
                "timestamp" => "2025-06-01T08:33:20.016003Z",
                "session_id" => "65adaf1a-d6ce-416a-89fd-52bd39ccee6d",
                "visitor_name" => "PT Maju Bersama",
                "session_status" => "active",
                "notification_type" => "chat.message_received",
                "icon" => "message-square",
                "type" => "chat.message_received",
                "title" => "New Chat Message",
                "message" => "New chat message received (Session: 65adaf1a...).",
                "action_url" => "http://localhost:8000/admin/chat/3",
                "created_at" => "2025-06-01T08:33:20.017265Z",
                "action_text" => "View Chat"
            ],
            'created_at' => now(),
            'read_at' => null
        ];

        return $this->formatSingleNotification($sampleNotification);
    }
}