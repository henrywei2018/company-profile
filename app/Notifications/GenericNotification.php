<?php
// File: app/Notifications/GenericNotification.php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class GenericNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $data;
    protected $notificationType;
    protected $customTitle;
    protected $customMessage;

    public function __construct($data, string $type = 'generic', string $title = null, string $message = null)
    {
        $this->data = $data;
        $this->notificationType = $type;
        $this->customTitle = $title;
        $this->customMessage = $message;
    }

    public function toMail($notifiable): MailMessage
    {
        $companyName = settings('company_name', config('app.name', 'CV Usaha Prima Lestari'));
        
        return (new MailMessage)
            ->subject($this->getTitle())
            ->greeting("Hello!")
            ->line($this->getMessage())
            ->when($this->getActionUrl(), function ($mail) {
                return $mail->action($this->getActionText(), $this->getActionUrl());
            })
            ->line('Thank you for using our services!')
            ->salutation("Best regards,<br>{$companyName} Team");
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => $this->notificationType,
            'title' => $this->getTitle(),
            'message' => $this->getMessage(),
            'data' => $this->getNotificationData(),
            'action_url' => $this->getActionUrl(),
            'action_text' => $this->getActionText(),
            'priority' => $this->getPriority(),
            'icon' => $this->getIcon(),
            'created_at' => now()->toISOString(),
        ];
    }

    protected function getTitle(): string
    {
        if ($this->customTitle) {
            return $this->customTitle;
        }

        return match($this->notificationType) {
            // Project notifications
            'project.created' => 'New Project Created',
            'project.updated' => 'Project Updated',
            'project.status_changed' => 'Project Status Changed',
            'project.completed' => 'Project Completed',
            'project.deadline_approaching' => 'Project Deadline Approaching',
            'project.overdue' => 'Project Overdue',
            
            // Quotation notifications
            'quotation.created' => 'New Quotation Request',
            'quotation.status_updated' => 'Quotation Status Updated',
            'quotation.approved' => 'Quotation Approved',
            'quotation.client_response_needed' => 'Response Required',
            'quotation.expired' => 'Quotation Expired',
            'quotation.converted' => 'Quotation Converted to Project',
            
            // Message notifications
            'message.created' => 'New Message Received',
            'message.reply' => 'Message Reply',
            'message.urgent' => 'Urgent Message',
            'message.auto_reply' => 'Auto Reply Sent',
            
            // Chat notifications
            'chat.session_started' => 'New Chat Session',
            'chat.message_received' => 'New Chat Message',
            'chat.session_waiting' => 'Chat Session Waiting',
            'chat.session_inactive' => 'Chat Session Inactive',
            'chat.session_closed' => 'Chat Session Closed',
            
            // User notifications
            'user.welcome' => 'Welcome!',
            'user.profile_incomplete' => 'Complete Your Profile',
            'user.email_verified' => 'Email Verified',
            'user.password_changed' => 'Password Changed',
            
            // System notifications
            'system.maintenance' => 'System Maintenance',
            'system.backup_completed' => 'Backup Completed',
            'system.security_alert' => 'Security Alert',
            'system.certificate_expiring' => 'Certificate Expiring',
            
            // Testimonial notifications
            'testimonial.created' => 'New Testimonial Submitted',
            'testimonial.approved' => 'Testimonial Approved',
            'testimonial.featured' => 'Testimonial Featured',
            
            default => 'Notification'
        };
    }

    protected function getMessage(): string
    {
        if ($this->customMessage) {
            return $this->customMessage;
        }

        $context = $this->getContextualInfo();

        return match($this->notificationType) {
            // Project notifications
            'project.created' => "A new project has been created{$context}.",
            'project.updated' => "A project has been updated{$context}.",
            'project.status_changed' => "Project status has been changed{$context}.",
            'project.completed' => "🎉 A project has been completed successfully{$context}.",
            'project.deadline_approaching' => "⏰ Project deadline is approaching{$context}.",
            'project.overdue' => "⚠️ A project is overdue{$context}.",
            
            // Quotation notifications
            'quotation.created' => "A new quotation request has been received{$context}.",
            'quotation.status_updated' => "Quotation status has been updated{$context}.",
            'quotation.approved' => "✅ Your quotation has been approved{$context}.",
            'quotation.client_response_needed' => "Your response is needed for a quotation{$context}.",
            'quotation.expired' => "⏰ A quotation has expired{$context}.",
            'quotation.converted' => "🎉 Quotation has been converted to a project{$context}.",
            
            // Message notifications
            'message.created' => "You have received a new message{$context}.",
            'message.reply' => "You have received a reply to your message{$context}.",
            'message.urgent' => "🚨 You have received an urgent message{$context}.",
            'message.auto_reply' => "An auto-reply has been sent{$context}.",
            
            // Chat notifications
            'chat.session_started' => "A new chat session has started{$context}.",
            'chat.message_received' => "New chat message received{$context}.",
            'chat.session_waiting' => "Chat session is waiting for an operator{$context}.",
            'chat.session_inactive' => "Chat session has been inactive{$context}.",
            'chat.session_closed' => "Chat session has been closed{$context}.",
            
            // User notifications
            'user.welcome' => "Welcome to our platform! We're excited to have you on board.",
            'user.profile_incomplete' => "Please complete your profile to access all features.",
            'user.email_verified' => "✅ Your email address has been successfully verified.",
            'user.password_changed' => "🔒 Your password has been changed successfully.",
            
            // System notifications
            'system.maintenance' => "🔧 System maintenance is scheduled{$context}.",
            'system.backup_completed' => "✅ System backup has been completed successfully.",
            'system.security_alert' => "🔒 Security alert{$context}.",
            'system.certificate_expiring' => "⚠️ SSL certificate is expiring soon{$context}.",
            
            // Testimonial notifications
            'testimonial.created' => "A new testimonial has been submitted for review{$context}.",
            'testimonial.approved' => "✅ Your testimonial has been approved and published{$context}.",
            'testimonial.featured' => "🌟 Your testimonial has been featured{$context}.",
            
            default => 'You have a new notification.'
        };
    }

    protected function getContextualInfo(): string
    {
        if (!$this->data) {
            return '';
        }

        // Extract contextual information based on data type
        if (is_object($this->data)) {
            $className = class_basename($this->data);
            
            switch ($className) {
                case 'Project':
                    $title = $this->data->title ?? 'Unknown Project';
                    return " for project: {$title}";
                    
                case 'Quotation':
                    $type = $this->data->project_type ?? 'Unknown Type';
                    return " for quotation: {$type}";
                    
                case 'Message':
                    $subject = $this->data->subject ?? 'No Subject';
                    return " with subject: {$subject}";
                    
                case 'ChatSession':
                    $sessionId = $this->data->session_id ?? 'Unknown Session';
                    return " (Session: " . substr($sessionId, 0, 8) . "...)";
                    
                case 'User':
                    $name = $this->data->name ?? 'Unknown User';
                    return " for user: {$name}";
                    
                case 'Testimonial':
                    if (isset($this->data->project->title)) {
                        return " for project: {$this->data->project->title}";
                    }
                    return '';
                    
                default:
                    return '';
            }
        }

        return '';
    }

    protected function getNotificationData(): array
    {
        $baseData = [
            'notification_type' => $this->notificationType,
            'timestamp' => now()->toISOString(),
        ];

        if (!$this->data) {
            return $baseData;
        }

        // Add relevant data based on object type
        if (is_object($this->data)) {
            $className = class_basename($this->data);
            
            switch ($className) {
                case 'Project':
                    return array_merge($baseData, [
                        'project_id' => $this->data->id ?? null,
                        'project_title' => $this->data->title ?? null,
                        'project_status' => $this->data->status ?? null,
                        'client_name' => $this->data->client->name ?? null,
                    ]);
                    
                case 'Quotation':
                    return array_merge($baseData, [
                        'quotation_id' => $this->data->id ?? null,
                        'project_type' => $this->data->project_type ?? null,
                        'quotation_status' => $this->data->status ?? null,
                        'client_name' => $this->data->name ?? null,
                    ]);
                    
                case 'Message':
                    return array_merge($baseData, [
                        'message_id' => $this->data->id ?? null,
                        'message_subject' => $this->data->subject ?? null,
                        'sender_name' => $this->data->name ?? null,
                        'priority' => $this->data->priority ?? 'normal',
                    ]);
                    
                case 'ChatSession':
                    return array_merge($baseData, [
                        'session_id' => $this->data->session_id ?? null,
                        'visitor_name' => $this->data->getVisitorName() ?? 'Anonymous',
                        'session_status' => $this->data->status ?? null,
                        'priority' => $this->data->priority ?? 'normal',
                    ]);
                    
                case 'User':
                    return array_merge($baseData, [
                        'user_id' => $this->data->id ?? null,
                        'user_name' => $this->data->name ?? null,
                        'user_email' => $this->data->email ?? null,
                    ]);
                    
                case 'Testimonial':
                    return array_merge($baseData, [
                        'testimonial_id' => $this->data->id ?? null,
                        'client_name' => $this->data->client_name ?? null,
                        'rating' => $this->data->rating ?? null,
                        'project_title' => $this->data->project->title ?? null,
                    ]);
                    
                default:
                    return array_merge($baseData, [
                        'data_type' => $className,
                        'data_id' => $this->data->id ?? null,
                    ]);
            }
        }

        // Handle array or scalar data
        if (is_array($this->data)) {
            return array_merge($baseData, ['custom_data' => $this->data]);
        }

        return array_merge($baseData, ['data' => $this->data]);
    }

    protected function getActionUrl(): ?string
    {
        if (!$this->data || !is_object($this->data)) {
            return null;
        }

        $className = class_basename($this->data);
        
        try {
            switch ($className) {
                case 'Project':
                    return route('client.projects.show', $this->data->id ?? 0);
                    
                case 'Quotation':
                    return route('client.quotations.show', $this->data->id ?? 0);
                    
                case 'Message':
                    return route('client.messages.show', $this->data->id ?? 0);
                    
                case 'ChatSession':
                    return route('admin.chat.show', $this->data->id ?? 0);
                    
                case 'User':
                    return route('client.profile.show');
                    
                case 'Testimonial':
                    return route('client.testimonials.show', $this->data->id ?? 0);
                    
                default:
                    return route('client.dashboard');
            }
        } catch (\Exception $e) {
            // If route generation fails, return dashboard
            return route('client.dashboard');
        }
    }

    protected function getActionText(): string
    {
        if (!$this->data || !is_object($this->data)) {
            return 'View Details';
        }

        $className = class_basename($this->data);
        
        return match($className) {
            'Project' => 'View Project',
            'Quotation' => 'View Quotation',
            'Message' => 'View Message',
            'ChatSession' => 'View Chat',
            'User' => 'View Profile',
            'Testimonial' => 'View Testimonial',
            default => 'View Details'
        };
    }

    protected function getPriority(): string
    {
        // Determine priority based on notification type
        $highPriorityTypes = [
            'project.overdue',
            'message.urgent',
            'chat.session_waiting',
            'system.security_alert',
            'quotation.expired',
        ];

        $lowPriorityTypes = [
            'user.welcome',
            'user.email_verified',
            'testimonial.featured',
            'system.backup_completed',
        ];

        if (in_array($this->notificationType, $highPriorityTypes)) {
            return 'high';
        }

        if (in_array($this->notificationType, $lowPriorityTypes)) {
            return 'low';
        }

        // Check if data object has priority
        if (is_object($this->data) && isset($this->data->priority)) {
            return $this->data->priority;
        }

        return 'normal';
    }

    protected function getIcon(): string
    {
        return match($this->notificationType) {
            // Project icons
            'project.created', 'project.updated' => 'folder-plus',
            'project.completed' => 'check-circle',
            'project.deadline_approaching', 'project.overdue' => 'clock',
            'project.status_changed' => 'refresh-cw',
            
            // Quotation icons
            'quotation.created' => 'file-text',
            'quotation.approved' => 'check-circle',
            'quotation.client_response_needed' => 'alert-circle',
            'quotation.expired' => 'clock',
            'quotation.converted' => 'arrow-right-circle',
            
            // Message icons
            'message.created', 'message.reply' => 'mail',
            'message.urgent' => 'alert-triangle',
            'message.auto_reply' => 'message-circle',
            
            // Chat icons
            'chat.session_started', 'chat.message_received' => 'message-square',
            'chat.session_waiting' => 'clock',
            'chat.session_inactive' => 'pause-circle',
            'chat.session_closed' => 'x-circle',
            
            // User icons
            'user.welcome' => 'user-plus',
            'user.profile_incomplete' => 'user-x',
            'user.email_verified' => 'user-check',
            'user.password_changed' => 'lock',
            
            // System icons
            'system.maintenance' => 'tool',
            'system.backup_completed' => 'hard-drive',
            'system.security_alert' => 'shield-alert',
            'system.certificate_expiring' => 'shield',
            
            // Testimonial icons
            'testimonial.created' => 'star',
            'testimonial.approved' => 'check-star',
            'testimonial.featured' => 'award',
            
            default => 'bell'
        };
    }

    /**
     * Determine if notification should be sent via mail
     */
    public function shouldSendMail($notifiable): bool
    {
        // Only send mail for important notifications
        $mailableTypes = [
            'user.welcome',
            'user.email_verified',
            'user.password_changed',
            'project.completed',
            'project.overdue',
            'quotation.approved',
            'message.urgent',
            'system.security_alert',
        ];

        return in_array($this->notificationType, $mailableTypes);
    }

    /**
     * Get the mail representation of the notification (override via method)
     */
    public function via($notifiable): array
    {
        $channels = ['database']; // default channel

        if (method_exists($this, 'shouldSendMail') && $this->shouldSendMail($notifiable)) {
            $channels[] = 'mail';
        }

        return $channels;
    }

}