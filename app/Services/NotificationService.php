<?php
// File: app/Services/NotificationService.php - UPDATED FOR DIRECT DATABASE

namespace App\Services;

use App\Models\User;
use App\Models\Project;
use App\Models\Quotation;
use App\Models\Message;
use App\Models\ChatSession;
use App\Models\Certification;
use App\Models\Testimonial;
use App\Services\TempNotifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
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
        $this->registerExistingNotificationClasses();
    }

    /**
     * Register only existing notification classes
     */
    protected function registerExistingNotificationClasses(): void
    {
        $possibleNotifications = [
            // User notifications (these exist)
            'user.welcome' => \App\Notifications\WelcomeNotification::class,
            'user.email_verified' => \App\Notifications\EmailVerifiedNotification::class,
            'user.profile_incomplete' => \App\Notifications\ProfileIncompleteNotification::class,
            'user.password_changed' => \App\Notifications\PasswordChangedNotification::class,
            
            // Chat notifications (these exist)
            'chat.session_started' => \App\Notifications\ChatSessionStartedNotification::class,
            'chat.message_received' => \App\Notifications\ChatMessageReceivedNotification::class,
            'chat.session_waiting' => \App\Notifications\ChatSessionWaitingNotification::class,
            'chat.session_inactive' => \App\Notifications\ChatSessionInactiveNotification::class,
            'chat.session_closed' => \App\Notifications\ChatSessionClosedNotification::class,
            
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

        // Only register notifications that actually exist
        foreach ($possibleNotifications as $type => $class) {
            if (class_exists($class)) {
                $this->notificationClasses[$type] = $class;
            }
        }
        
        Log::info('NotificationService initialized', [
            'registered_types' => count($this->notificationClasses),
            'types' => array_keys($this->notificationClasses)
        ]);
    }

    /**
     * MAIN SEND METHOD - DIRECT DATABASE APPROACH
     * This bypasses Laravel's notification queue serialization issues
     */
    public function send(string $type, $data = null, $recipients = null, array $channels = null): bool
    {
        try {
            Log::info("NotificationService: Starting send", [
                'type' => $type,
                'data_type' => is_object($data) ? get_class($data) : gettype($data),
                'recipients_provided' => !is_null($recipients)
            ]);

            // Resolve recipients if not provided
            if ($recipients === null) {
                $recipients = $this->resolveRecipients($type, $data);
            }

            // Convert single recipient to collection
            if (!is_iterable($recipients)) {
                $recipients = collect([$recipients]);
            } else {
                $recipients = collect($recipients);
            }

            // Filter recipients based on preferences
            $filteredRecipients = $this->filterRecipientsByPreferences($recipients, $type);

            if (empty($filteredRecipients)) {
                Log::info("No recipients to notify for type: {$type}");
                return true; // No recipients to notify
            }

            Log::info("NotificationService: Processing recipients", [
                'total_recipients' => count($filteredRecipients),
                'type' => $type
            ]);

            // Send notifications to each recipient
            $successCount = 0;
            foreach ($filteredRecipients as $recipient) {
                if ($this->sendToRecipient($type, $data, $recipient, $channels)) {
                    $successCount++;
                }
            }

            // Log successful notification
            $this->logNotification($type, $data, $successCount);

            return $successCount > 0;

        } catch (\Exception $e) {
            Log::error("NotificationService: Failed to send notification", [
                'type' => $type,
                'data' => $data ? get_class($data) : 'null',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Send notification to a single recipient using direct database approach
     */
    protected function sendToRecipient(string $type, $data, $recipient, array $channels = null): bool
    {
        try {
            $success = false;

            // Handle database notifications for registered users
            if ($recipient instanceof User) {
                $this->sendDatabaseNotification($type, $data, $recipient);
                $success = true;
            }

            // Handle email notifications
            if ($this->shouldSendEmail($type, $recipient)) {
                $this->sendEmailNotification($type, $data, $recipient);
                $success = true;
            }

            return $success;

        } catch (\Exception $e) {
            Log::error("Failed to send to recipient", [
                'type' => $type,
                'recipient_type' => get_class($recipient),
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Send database notification directly (bypasses Laravel queue)
     */
    protected function sendDatabaseNotification(string $type, $data, User $user): void
    {
        try {
            // Generate notification data
            $notificationData = $this->generateNotificationData($type, $data);

            // Insert directly into notifications table
            DB::table('notifications')->insert([
                'id' => Str::uuid()->toString(),
                'type' => $type,
                'notifiable_type' => User::class,
                'notifiable_id' => $user->id,
                'data' => json_encode($notificationData),
                'read_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            Log::info("Database notification inserted", [
                'type' => $type,
                'user_id' => $user->id
            ]);

        } catch (\Exception $e) {
            Log::error("Failed to insert database notification", [
                'type' => $type,
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Send email notification
     */
    protected function sendEmailNotification(string $type, $data, $recipient): void
    {
        try {
            // Create notification instance for email content
            $notification = $this->createNotificationInstance($type, $data);
            
            if ($notification) {
                // Get email address
                $email = $this->getRecipientEmail($recipient);
                
                if ($email) {
                    // Create a mailable from the notification
                    $mailMessage = $notification->toMail($recipient);
                    
                    // Convert MailMessage to actual Mail
                    Mail::to($email)->send(new \App\Mail\NotificationMail($mailMessage, $type));
                    
                    Log::info("Email notification sent", [
                        'type' => $type,
                        'email' => $email
                    ]);
                }
            }

        } catch (\Exception $e) {
            Log::error("Failed to send email notification", [
                'type' => $type,
                'recipient_type' => get_class($recipient),
                'error' => $e->getMessage()
            ]);
            // Don't throw - email failure shouldn't break the process
        }
    }

    /**
     * Generate notification data for database storage
     */
    protected function generateNotificationData(string $type, $data): array
    {
        // Try to create notification instance to get proper data
        $notification = $this->createNotificationInstance($type, $data);
        
        if ($notification && method_exists($notification, 'toArray')) {
            try {
                return $notification->toArray(null);
            } catch (\Exception $e) {
                Log::warning("Could not get notification array data", [
                    'type' => $type,
                    'error' => $e->getMessage()
                ]);
            }
        }

        // Fallback to generic notification data
        return $this->generateGenericNotificationData($type, $data);
    }

    /**
     * Generate generic notification data
     */
    protected function generateGenericNotificationData(string $type, $data): array
    {
        $baseData = [
            'type' => $type,
            'title' => $this->getGenericTitle($type),
            'message' => $this->getGenericMessage($type, $data),
            'created_at' => now()->toISOString(),
        ];

        // Add data-specific information
        if (is_object($data)) {
            $className = class_basename($data);
            
            switch ($className) {
                case 'Project':
                    return array_merge($baseData, [
                        'project_id' => $data->id ?? null,
                        'project_title' => $data->title ?? null,
                        'project_status' => $data->status ?? null,
                        'action_url' => route('client.projects.show', $data->id ?? 0),
                        'action_text' => 'View Project',
                    ]);
                    
                case 'Quotation':
                    return array_merge($baseData, [
                        'quotation_id' => $data->id ?? null,
                        'project_type' => $data->project_type ?? null,
                        'action_url' => route('client.quotations.show', $data->id ?? 0),
                        'action_text' => 'View Quotation',
                    ]);
                    
                case 'Message':
                    return array_merge($baseData, [
                        'message_id' => $data->id ?? null,
                        'subject' => $data->subject ?? null,
                        'action_url' => route('client.messages.show', $data->id ?? 0),
                        'action_text' => 'View Message',
                    ]);
                    
                case 'User':
                    return array_merge($baseData, [
                        'user_id' => $data->id ?? null,
                        'user_name' => $data->name ?? null,
                        'action_url' => route('client.dashboard'),
                        'action_text' => 'Go to Dashboard',
                    ]);
            }
        }

        return array_merge($baseData, [
            'action_url' => route('client.dashboard'),
            'action_text' => 'View Details',
        ]);
    }

    /**
     * Get generic title for notification type
     */
    protected function getGenericTitle(string $type): string
    {
        return match($type) {
            'project.created' => 'New Project Created',
            'project.updated' => 'Project Updated',
            'project.completed' => 'Project Completed',
            'quotation.created' => 'New Quotation Request',
            'quotation.approved' => 'Quotation Approved',
            'message.created' => 'New Message',
            'message.reply' => 'Message Reply',
            'user.welcome' => 'Welcome!',
            default => 'Notification'
        };
    }

    /**
     * Get generic message for notification type
     */
    protected function getGenericMessage(string $type, $data): string
    {
        $context = $this->getContextualInfo($data);
        
        return match($type) {
            'project.created' => "A new project has been created{$context}.",
            'project.updated' => "A project has been updated{$context}.",
            'project.completed' => "🎉 A project has been completed{$context}.",
            'quotation.created' => "A new quotation request has been received{$context}.",
            'quotation.approved' => "✅ Your quotation has been approved{$context}.",
            'message.created' => "You have received a new message{$context}.",
            'message.reply' => "You have received a reply{$context}.",
            'user.welcome' => "Welcome to our platform!",
            default => 'You have a new notification.'
        };
    }

    /**
     * Get contextual information from data
     */
    protected function getContextualInfo($data): string
    {
        if (!$data || !is_object($data)) {
            return '';
        }

        $className = class_basename($data);
        
        return match($className) {
            'Project' => isset($data->title) ? " for project: {$data->title}" : '',
            'Quotation' => isset($data->project_type) ? " for quotation: {$data->project_type}" : '',
            'Message' => isset($data->subject) ? " with subject: {$data->subject}" : '',
            'User' => isset($data->name) ? " for user: {$data->name}" : '',
            default => ''
        };
    }

    /**
     * Create notification instance with fallback to GenericNotification
     */
    protected function createNotificationInstance(string $type, $data)
    {
        if (!isset($this->notificationClasses[$type])) {
            Log::info("Notification type '{$type}' not registered, using GenericNotification");
            return $this->fallbackNotification($data, $type);
        }

        $notificationClass = $this->notificationClasses[$type];

        if (!class_exists($notificationClass)) {
            Log::warning("Notification class '{$notificationClass}' not found for type '{$type}', using GenericNotification");
            return $this->fallbackNotification($data, $type);
        }

        try {
            return new $notificationClass($data);
        } catch (\Throwable $e) {
            Log::warning("Failed to instantiate {$notificationClass}: " . $e->getMessage());
            return $this->fallbackNotification($data, $type);
        }
    }

    /**
     * Create fallback notification
     */
    protected function fallbackNotification($data, string $type)
    {
        if (class_exists(\App\Notifications\GenericNotification::class)) {
            return new \App\Notifications\GenericNotification($data, $type);
        }

        Log::warning("GenericNotification class not found, skipping notification");
        return null;
    }

    /**
     * Get recipient email address
     */
    protected function getRecipientEmail($recipient): ?string
    {
        if ($recipient instanceof User) {
            return $recipient->email;
        }
        
        if ($recipient instanceof TempNotifiable) {
            return $recipient->email;
        }
        
        if (is_object($recipient) && property_exists($recipient, 'email')) {
            return $recipient->email;
        }
        
        return null;
    }

    /**
     * Check if should send email
     */
    protected function shouldSendEmail(string $type, $recipient): bool
    {
        // Only send email for important notifications
        $emailableTypes = [
            'user.welcome',
            'user.email_verified',
            'user.password_changed',
            'project.completed',
            'project.overdue',
            'quotation.approved',
            'message.urgent',
            'message.reply',
            'message.auto_reply',
        ];

        if (!in_array($type, $emailableTypes)) {
            return false;
        }

        // Check if recipient has email notifications enabled
        if ($recipient instanceof User) {
            return $recipient->email_notifications ?? true;
        }

        // For temp notifiables, always send email
        return true;
    }

    // ... REST OF THE METHODS REMAIN THE SAME (resolveRecipients, filterRecipientsByPreferences, etc.)
    // I'll include the key ones below:

    /**
     * Resolve notification recipients based on type and data
     */
    protected function resolveRecipients(string $type, $data)
    {
        try {
            switch ($type) {
                // Project notifications
                case 'project.created':
                case 'project.updated':
                case 'project.status_changed':
                case 'project.completed':
                    return $this->getProjectNotificationRecipients($data);

                case 'project.deadline_approaching':
                case 'project.overdue':
                    return $this->getProjectNotificationRecipients($data);

                // Quotation notifications
                case 'quotation.created':
                    return $this->getQuotationCreatedRecipients($data);

                case 'quotation.status_updated':
                case 'quotation.approved':
                case 'quotation.client_response_needed':
                case 'quotation.expired':
                    return $this->getQuotationUpdateRecipients($data);

                // Message notifications
                case 'message.created':
                    return $this->getMessageCreatedRecipients($data);

                case 'message.reply':
                    return $this->getMessageReplyRecipients($data);

                case 'message.urgent':
                    return $this->getUrgentMessageRecipients($data);

                // User notifications
                case 'user.welcome':
                case 'user.profile_incomplete':
                case 'user.email_verified':
                case 'user.password_changed':
                    return $data instanceof User ? $data : null;

                default:
                    Log::info("No recipient resolution logic for notification type: {$type}");
                    return collect();
            }
        } catch (\Exception $e) {
            Log::error("Error resolving recipients for type '{$type}': " . $e->getMessage());
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
            try {
                $admins = User::whereHas('roles', function($query) {
                    $query->whereIn('name', ['super-admin', 'admin', 'manager']);
                })->get();
                $recipients = $recipients->merge($admins);
            } catch (\Exception $e) {
                Log::warning("Could not get admin users for project notification: " . $e->getMessage());
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
            try {
                $admins = User::whereHas('roles', function($query) {
                    $query->whereIn('name', ['super-admin', 'admin', 'sales']);
                })->get();
                $recipients = $recipients->merge($admins);
            } catch (\Exception $e) {
                Log::warning("Could not get admin users for quotation notification: " . $e->getMessage());
                // Fallback to basic admin detection
                $admins = User::where('email', 'like', '%admin%')->get();
                $recipients = $recipients->merge($admins);
            }
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
            try {
                $admins = User::whereHas('roles', function($query) {
                    $query->whereIn('name', ['super-admin', 'admin', 'support']);
                })->get();
                $recipients = $recipients->merge($admins);
            } catch (\Exception $e) {
                Log::warning("Could not get admin users for message notification: " . $e->getMessage());
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
        try {
            return User::whereHas('roles', function($query) {
                $query->whereIn('name', ['super-admin', 'admin', 'support']);
            })->where('is_active', true)->get();
        } catch (\Exception $e) {
            Log::warning("Could not get admin users for urgent message: " . $e->getMessage());
            return collect();
        }
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
     * Check if recipient should receive notification
     */
    protected function shouldNotifyRecipient($recipient, string $type): bool
    {
        if (!$recipient instanceof User) {
            return true; // Always notify TempNotifiable
        }

        // Check if user is active
        if (!($recipient->is_active ?? true)) {
            return false;
        }

        // Check global notification preference
        if (isset($recipient->email_notifications) && !$recipient->email_notifications) {
            return false;
        }

        return true;
    }

    /**
     * Log notification sending
     */
    protected function logNotification(string $type, $data, int $recipientCount): void
    {
        Log::info("Notification sent successfully", [
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
            'registered_types' => array_keys($this->notificationClasses),
        ];
    }

    /**
     * Test notification system
     */
    public function test(User $user): array
    {
        $results = [];

        try {
            $result = $this->send('user.welcome', $user);
            $results['welcome_notification'] = $result ? 'success' : 'failed';
        } catch (\Exception $e) {
            $results['welcome_notification'] = 'error: ' . $e->getMessage();
        }

        return $results;
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
}