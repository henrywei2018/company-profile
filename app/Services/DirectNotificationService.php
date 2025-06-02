<?php
// File: app/Services/DirectNotificationService.php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class DirectNotificationService
{
    /**
     * Store notification directly to database (bypassing Laravel's notification system)
     */
    public function send(string $type, $data, $recipients = null): bool
    {
        try {
            // Resolve recipients
            if ($recipients === null) {
                $recipients = $this->resolveRecipients($type, $data);
            }
            
            if (!is_iterable($recipients)) {
                $recipients = collect([$recipients]);
            } else {
                $recipients = collect($recipients);
            }
            
            $successCount = 0;
            
            foreach ($recipients as $recipient) {
                if ($this->storeNotification($recipient, $type, $data)) {
                    $successCount++;
                }
            }
            
            Log::info("Direct notifications sent", [
                'type' => $type,
                'recipients' => count($recipients),
                'successful' => $successCount
            ]);
            
            return $successCount > 0;
            
        } catch (\Exception $e) {
            Log::error("Failed to send direct notification", [
                'type' => $type,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
    
    /**
     * Store individual notification
     */
    protected function storeNotification($recipient, string $type, $data): bool
    {
        try {
            if (!$recipient instanceof User) {
                return false;
            }
            
            $notificationData = $this->buildNotificationData($type, $data, $recipient);
            
            return DB::table('notifications')->insert([
                'id' => Str::uuid(),
                'type' => $this->getNotificationClass($type),
                'notifiable_type' => 'App\\Models\\User',
                'notifiable_id' => $recipient->id,
                'data' => json_encode($notificationData),
                'read_at' => null,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
        } catch (\Exception $e) {
            Log::error("Failed to store individual notification", [
                'type' => $type,
                'recipient_id' => $recipient->id ?? 'unknown',
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
    
    /**
     * Build notification data based on type
     */
    protected function buildNotificationData(string $type, $data, User $recipient): array
    {
        $baseData = [
            'type' => $type,
            'created_at' => now()->toISOString(),
            'priority' => 'normal'
        ];
        
        return match($type) {
            'user.welcome' => array_merge($baseData, [
                'title' => 'Welcome to ' . config('app.name'),
                'message' => "Welcome {$recipient->name}! Thank you for joining us.",
                'action_url' => $this->getActionUrl('dashboard', $recipient),
                'action_text' => 'Go to Dashboard',
                'user_id' => $recipient->id,
                'user_name' => $recipient->name,
                'user_email' => $recipient->email,
            ]),
            
            'project.updated' => array_merge($baseData, [
                'title' => 'Project Updated',
                'message' => 'Project "' . ($data->title ?? 'Unknown') . '" has been updated.',
                'action_url' => $this->getActionUrl('project', $data),
                'action_text' => 'View Project',
                'project_id' => $data->id ?? null,
                'project_title' => $data->title ?? null,
                'project_status' => $data->status ?? null,
            ]),
            
            'project.created' => array_merge($baseData, [
                'title' => 'New Project Created',
                'message' => 'A new project "' . ($data->title ?? 'Unknown') . '" has been created.',
                'action_url' => $this->getActionUrl('project', $data),
                'action_text' => 'View Project',
                'project_id' => $data->id ?? null,
                'project_title' => $data->title ?? null,
                'project_status' => $data->status ?? null,
            ]),
            
            'message.created' => array_merge($baseData, [
                'title' => 'New Message Received',
                'message' => 'You have received a new message: ' . ($data->subject ?? 'No subject'),
                'action_url' => $this->getActionUrl('message', $data),
                'action_text' => 'View Message',
                'message_id' => $data->id ?? null,
                'message_subject' => $data->subject ?? null,
                'sender_name' => $data->name ?? null,
            ]),
            
            'message.reply' => array_merge($baseData, [
                'title' => 'Message Reply',
                'message' => 'You have received a reply to your message.',
                'action_url' => $this->getActionUrl('message', $data),
                'action_text' => 'View Messages',
                'message_id' => $data->id ?? null,
                'message_subject' => $data->subject ?? null,
            ]),
            
            default => array_merge($baseData, [
                'title' => 'Notification',
                'message' => 'You have a new notification.',
                'action_url' => $this->getActionUrl('dashboard'),
                'action_text' => 'View Details',
            ])
        };
    }
    
    /**
     * Get notification class name
     */
    protected function getNotificationClass(string $type): string
    {
        $classMap = [
            'user.welcome' => 'App\\Notifications\\WelcomeNotification',
            'project.created' => 'App\\Notifications\\ProjectCreatedNotification',
            'project.updated' => 'App\\Notifications\\ProjectUpdatedNotification',
            'message.created' => 'App\\Notifications\\MessageCreatedNotification',
            'message.reply' => 'App\\Notifications\\MessageReplyNotification',
        ];
        
        return $classMap[$type] ?? 'App\\Notifications\\GenericNotification';
    }
    
    /**
     * Get action URL for notification
     */
    protected function getActionUrl(string $type, $data = null, User $user = null): string
    {
        try {
            return match($type) {
                'dashboard' => route('admin.dashboard'),
                'project' => route('admin.projects.show', $data->id ?? 1),
                'message' => route('admin.messages.show', $data->id ?? 1),
                default => route('admin.dashboard')
            };
        } catch (\Exception $e) {
            return url('/');
        }
    }
    
    /**
     * Resolve notification recipients
     */
    protected function resolveRecipients(string $type, $data)
    {
        return match($type) {
            'user.welcome' => $data instanceof User ? [$data] : [],
            'project.created', 'project.updated' => $this->getProjectRecipients($data),
            'message.created' => $this->getMessageRecipients($data),
            default => auth()->check() ? [auth()->user()] : []
        };
    }
    
    protected function getProjectRecipients($project): array
    {
        $recipients = [];
        
        if (isset($project->client) && $project->client) {
            $recipients[] = $project->client;
        }
        
        // Add current user for testing
        if (auth()->check()) {
            $recipients[] = auth()->user();
        }
        
        return $recipients;
    }
    
    protected function getMessageRecipients($message): array
    {
        // For testing, send to current user
        return auth()->check() ? [auth()->user()] : [];
    }
}
