<?php
// File: app/Notifications/BaseNotification.php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

abstract class BaseNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $data;
    protected $subject;
    protected $greeting;
    protected $lines = [];
    protected $action;
    protected $actionUrl;
    protected $salutation;

    public function __construct($data = null)
    {
        $this->data = $data;
        $this->configure();
    }

    /**
     * Configure notification content - to be overridden by child classes
     */
    abstract protected function configure(): void;

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        $channels = ['database'];

        // Add mail channel if user has email notifications enabled
        if ($this->shouldSendEmail($notifiable)) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject($this->subject)
            ->greeting($this->greeting);

        // Add content lines
        foreach ($this->lines as $line) {
            $mail->line($line);
        }

        // Add action if provided
        if ($this->action && $this->actionUrl) {
            $mail->action($this->action, $this->actionUrl);
        }

        // Add salutation
        if ($this->salutation) {
            $mail->salutation($this->salutation);
        }

        return $mail;
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        return [
            'type' => $this->getType(),
            'title' => $this->subject,
            'message' => $this->getSummary(),
            'data' => $this->getNotificationData(),
            'action_url' => $this->actionUrl,
            'action_text' => $this->action,
        ];
    }

    /**
     * Get notification type
     */
    protected function getType(): string
    {
        return strtolower(class_basename(static::class));
    }

    /**
     * Get notification summary for database storage
     */
    protected function getSummary(): string
    {
        return implode(' ', array_slice($this->lines, 0, 2));
    }

    /**
     * Get notification data for database storage
     */
    protected function getNotificationData(): array
    {
        return [
            'model_type' => is_object($this->data) ? get_class($this->data) : null,
            'model_id' => is_object($this->data) && method_exists($this->data, 'getKey') ? $this->data->getKey() : null,
        ];
    }

    /**
     * Check if email should be sent to user
     */
    protected function shouldSendEmail($notifiable): bool
    {
        if (!$notifiable instanceof \App\Models\User) {
            return false;
        }

        // Check global email preference
        if (isset($notifiable->email_notifications) && !$notifiable->email_notifications) {
            return false;
        }

        return true;
    }

    /**
     * Add line to notification content
     */
    protected function addLine(string $line): static
    {
        $this->lines[] = $line;
        return $this;
    }

    /**
     * Set notification action
     */
    protected function setAction(string $text, string $url): static
    {
        $this->action = $text;
        $this->actionUrl = $url;
        return $this;
    }
}

// File: app/Notifications/ProjectCreatedNotification.php

namespace App\Notifications;

use App\Models\Project;

class ProjectCreatedNotification extends BaseNotification
{
    protected function configure(): void
    {
        $project = $this->data;
        
        if ($project instanceof Project) {
            $this->subject = "New Project Created: {$project->title}";
            $this->greeting = "Hello!";
            
            $this->addLine("A new project has been created: {$project->title}");
            $this->addLine("Client: " . ($project->client->name ?? 'N/A'));
            $this->addLine("Status: " . ucfirst($project->status));
            
            if ($project->start_date) {
                $this->addLine("Start Date: " . $project->start_date->format('M d, Y'));
            }
            
            $this->setAction('View Project', route('admin.projects.show', $project));
            $this->salutation = 'Best regards,<br>' . config('app.name') . ' Team';
        }
    }
}

// File: app/Notifications/ProjectStatusChangedNotification.php

namespace App\Notifications;

use App\Models\Project;

class ProjectStatusChangedNotification extends BaseNotification
{
    protected function configure(): void
    {
        $project = $this->data;
        
        if ($project instanceof Project) {
            $this->subject = "Project Status Updated: {$project->title}";
            $this->greeting = "Hello!";
            
            $this->addLine("The status of your project '{$project->title}' has been updated.");
            $this->addLine("New Status: " . ucfirst(str_replace('_', ' ', $project->status)));
            
            if ($project->status === 'completed') {
                $this->addLine("🎉 Congratulations! Your project has been completed successfully.");
            } elseif ($project->status === 'in_progress') {
                $this->addLine("Your project is now in progress. Our team is working on it.");
            }
            
            $this->setAction('View Project', route('client.projects.show', $project));
            $this->salutation = 'Best regards,<br>' . config('app.name') . ' Team';
        }
    }
}

// File: app/Notifications/ProjectDeadlineNotification.php

namespace App\Notifications;

use App\Models\Project;

class ProjectDeadlineNotification extends BaseNotification
{
    protected function configure(): void
    {
        $project = $this->data;
        
        if ($project instanceof Project) {
            $daysUntilDeadline = $project->end_date ? now()->diffInDays($project->end_date, false) : 0;
            $isOverdue = $daysUntilDeadline < 0;
            
            if ($isOverdue) {
                $this->subject = "Project Overdue: {$project->title}";
                $this->greeting = "Urgent Notice!";
                $this->addLine("Your project '{$project->title}' is " . abs($daysUntilDeadline) . " day(s) overdue.");
                $this->addLine("Please contact us immediately to discuss the project status.");
            } else {
                $this->subject = "Project Deadline Approaching: {$project->title}";
                $this->greeting = "Reminder!";
                $this->addLine("Your project '{$project->title}' deadline is approaching.");
                $this->addLine("Days until deadline: {$daysUntilDeadline}");
                $this->addLine("Deadline: " . $project->end_date->format('M d, Y'));
            }
            
            $this->setAction('View Project', route('client.projects.show', $project));
            $this->salutation = 'Best regards,<br>' . config('app.name') . ' Team';
        }
    }
}

// File: app/Notifications/QuotationCreatedNotification.php

namespace App\Notifications;

use App\Models\Quotation;

class QuotationCreatedNotification extends BaseNotification
{
    protected function configure(): void
    {
        $quotation = $this->data;
        
        if ($quotation instanceof Quotation) {
            $this->subject = "New Quotation Request: {$quotation->project_type}";
            $this->greeting = "New Request!";
            
            $this->addLine("A new quotation request has been submitted:");
            $this->addLine("Client: {$quotation->name}");
            $this->addLine("Project: {$quotation->project_type}");
            $this->addLine("Service: " . ($quotation->service->title ?? 'General'));
            
            if ($quotation->budget_range) {
                $this->addLine("Budget: {$quotation->budget_range}");
            }
            
            $this->setAction('Review Quotation', route('admin.quotations.show', $quotation));
            $this->salutation = 'Best regards,<br>' . config('app.name') . ' Team';
        }
    }
}

// File: app/Notifications/QuotationStatusUpdatedNotification.php

namespace App\Notifications;

use App\Models\Quotation;

class QuotationStatusUpdatedNotification extends BaseNotification
{
    protected function configure(): void
    {
        $quotation = $this->data;
        
        if ($quotation instanceof Quotation) {
            $this->subject = "Quotation Status Updated: {$quotation->project_type}";
            $this->greeting = "Hello {$quotation->name}!";
            
            $this->addLine("Your quotation request status has been updated:");
            $this->addLine("Project: {$quotation->project_type}");
            $this->addLine("Status: " . ucfirst($quotation->status));
            
            $statusMessages = [
                'pending' => 'Your quotation is being reviewed by our team.',
                'reviewed' => 'Your quotation has been reviewed. We will send you a detailed quote soon.',
                'approved' => 'Great news! Your quotation has been approved.',
                'rejected' => 'Thank you for your interest. We cannot proceed with this quotation at this time.',
            ];
            
            if (isset($statusMessages[$quotation->status])) {
                $this->addLine($statusMessages[$quotation->status]);
            }
            
            $this->setAction('View Quotation', route('client.quotations.show', $quotation));
            $this->salutation = 'Best regards,<br>' . config('app.name') . ' Team';
        }
    }
}

// File: app/Notifications/MessageCreatedNotification.php

namespace App\Notifications;

use App\Models\Message;

class MessageCreatedNotification extends BaseNotification
{
    protected function configure(): void
    {
        $message = $this->data;
        
        if ($message instanceof Message) {
            $this->subject = "New Message: {$message->subject}";
            $this->greeting = "New Message!";
            
            $this->addLine("You have received a new message:");
            $this->addLine("From: {$message->name}");
            $this->addLine("Subject: {$message->subject}");
            
            if ($message->priority === 'urgent') {
                $this->addLine("⚠️ This message is marked as urgent.");
            }
            
            $this->setAction('View Message', route('admin.messages.show', $message));
            $this->salutation = 'Best regards,<br>' . config('app.name') . ' Team';
        }
    }
}

// File: app/Notifications/MessageReplyNotification.php

namespace App\Notifications;

use App\Models\Message;

class MessageReplyNotification extends BaseNotification
{
    protected function configure(): void
    {
        $message = $this->data;
        
        if ($message instanceof Message) {
            $this->subject = "New Reply: {$message->subject}";
            $this->greeting = "Hello!";
            
            $this->addLine("You have received a reply to your message:");
            $this->addLine("Subject: {$message->subject}");
            $this->addLine("From: " . config('app.name') . " Support Team");
            
            if ($message->attachments && $message->attachments->count() > 0) {
                $this->addLine("This message includes {$message->attachments->count()} attachment(s).");
            }
            
            $this->setAction('View Message', route('client.messages.show', $message));
            $this->salutation = 'Best regards,<br>' . config('app.name') . ' Team';
        }
    }
}

// File: app/Notifications/UrgentMessageNotification.php

namespace App\Notifications;

use App\Models\Message;

class UrgentMessageNotification extends BaseNotification
{
    protected function configure(): void
    {
        $message = $this->data;
        
        if ($message instanceof Message) {
            $this->subject = "🚨 URGENT: {$message->subject}";
            $this->greeting = "Urgent Alert!";
            
            $this->addLine("An urgent message requires immediate attention:");
            $this->addLine("From: {$message->name} ({$message->email})");
            $this->addLine("Subject: {$message->subject}");
            $this->addLine("Received: " . $message->created_at->format('M d, Y H:i'));
            
            $this->addLine("Please respond as soon as possible.");
            
            $this->setAction('View Message Now', route('admin.messages.show', $message));
            $this->salutation = 'Best regards,<br>' . config('app.name') . ' System';
        }
    }
}

// File: app/Notifications/ChatSessionStartedNotification.php

namespace App\Notifications;

use App\Models\ChatSession;

class ChatSessionStartedNotification extends BaseNotification
{
    protected function configure(): void
    {
        $chatSession = $this->data;
        
        if ($chatSession instanceof ChatSession) {
            $this->subject = "New Chat Session Started";
            $this->greeting = "New Chat!";
            
            $this->addLine("A new chat session has been started:");
            $this->addLine("Visitor: " . $chatSession->getVisitorName());
            
            if ($chatSession->getVisitorEmail()) {
                $this->addLine("Email: " . $chatSession->getVisitorEmail());
            }
            
            $this->addLine("Session ID: {$chatSession->session_id}");
            
            $this->setAction('Join Chat', route('admin.chat.show', $chatSession));
            $this->salutation = 'Best regards,<br>' . config('app.name') . ' System';
        }
    }
}

// File: app/Notifications/ChatSessionWaitingNotification.php

namespace App\Notifications;

use App\Models\ChatSession;

class ChatSessionWaitingNotification extends BaseNotification
{
    protected function configure(): void
    {
        $chatSession = $this->data;
        
        if ($chatSession instanceof ChatSession) {
            $waitingMinutes = now()->diffInMinutes($chatSession->created_at);
            
            $this->subject = "Chat Session Waiting - Action Required";
            $this->greeting = "Chat Alert!";
            
            $this->addLine("A chat session has been waiting for {$waitingMinutes} minutes:");
            $this->addLine("Visitor: " . $chatSession->getVisitorName());
            $this->addLine("Started: " . $chatSession->created_at->format('H:i'));
            
            $this->addLine("Please assign an operator to this chat session.");
            
            $this->setAction('Handle Chat', route('admin.chat.show', $chatSession));
            $this->salutation = 'Best regards,<br>' . config('app.name') . ' System';
        }
    }
}

// File: app/Notifications/WelcomeNotification.php

namespace App\Notifications;

use App\Models\User;

class WelcomeNotification extends BaseNotification
{
    protected function configure(): void
    {
        $user = $this->data;
        
        if ($user instanceof User) {
            $this->subject = "Welcome to " . config('app.name') . "!";
            $this->greeting = "Welcome {$user->name}!";
            
            $this->addLine("Thank you for joining " . config('app.name') . ". We're excited to have you on board!");
            
            if ($user->hasRole('client')) {
                $this->addLine("As a client, you can:");
                $this->addLine("• View and track your projects");
                $this->addLine("• Request quotations for new projects");
                $this->addLine("• Communicate with our team through messages");
                $this->addLine("• Leave testimonials for completed projects");
                
                $this->setAction('Explore Client Portal', route('client.dashboard'));
            } else {
                $this->addLine("We're here to help you with all your construction and supply needs.");
                $this->setAction('Visit Our Website', route('home'));
            }
            
            $this->salutation = 'Welcome aboard!<br>' . config('app.name') . ' Team';
        }
    }
}

// File: app/Notifications/ProfileIncompleteNotification.php

namespace App\Notifications;

use App\Models\User;

class ProfileIncompleteNotification extends BaseNotification
{
    protected function configure(): void
    {
        $user = $this->data;
        
        if ($user instanceof User) {
            $this->subject = "Complete Your Profile - " . config('app.name');
            $this->greeting = "Hello {$user->name}!";
            
            $this->addLine("We noticed your profile is incomplete.");
            $this->addLine("Completing your profile helps us serve you better and ensures smooth communication.");
            
            $missingFields = [];
            if (empty($user->phone)) $missingFields[] = 'Phone number';
            if (empty($user->company)) $missingFields[] = 'Company name';
            if (empty($user->address)) $missingFields[] = 'Address';
            
            if (!empty($missingFields)) {
                $this->addLine("Missing information: " . implode(', ', $missingFields));
            }
            
            $this->setAction('Complete Profile', route('profile.edit'));
            $this->salutation = 'Best regards,<br>' . config('app.name') . ' Team';
        }
    }
}

// File: app/Notifications/SystemMaintenanceNotification.php

namespace App\Notifications;

class SystemMaintenanceNotification extends BaseNotification
{
    protected function configure(): void
    {
        $maintenanceData = $this->data;
        
        $this->subject = "Scheduled System Maintenance - " . config('app.name');
        $this->greeting = "Important Notice!";
        
        $this->addLine("We will be performing scheduled system maintenance:");
        
        if (is_array($maintenanceData)) {
            if (isset($maintenanceData['start_time'])) {
                $this->addLine("Start Time: " . $maintenanceData['start_time']);
            }
            if (isset($maintenanceData['end_time'])) {
                $this->addLine("End Time: " . $maintenanceData['end_time']);
            }
            if (isset($maintenanceData['description'])) {
                $this->addLine("Description: " . $maintenanceData['description']);
            }
        }
        
        $this->addLine("During this time, some features may be temporarily unavailable.");
        $this->addLine("We apologize for any inconvenience and appreciate your patience.");
        
        $this->salutation = 'Best regards,<br>' . config('app.name') . ' Technical Team';
    }
}

// File: app/Notifications/CertificateExpiringNotification.php

namespace App\Notifications;

use App\Models\Certification;

class CertificateExpiringNotification extends BaseNotification
{
    protected function configure(): void
    {
        $certification = $this->data;
        
        if ($certification instanceof Certification) {
            $daysUntilExpiry = $certification->expiry_date ? now()->diffInDays($certification->expiry_date, false) : 0;
            $isExpired = $daysUntilExpiry < 0;
            
            if ($isExpired) {
                $this->subject = "Certificate Expired: {$certification->name}";
                $this->greeting = "Critical Alert!";
                $this->addLine("Certificate '{$certification->name}' has expired " . abs($daysUntilExpiry) . " day(s) ago.");
            } else {
                $this->subject = "Certificate Expiring Soon: {$certification->name}";
                $this->greeting = "Attention Required!";
                $this->addLine("Certificate '{$certification->name}' will expire in {$daysUntilExpiry} day(s).");
            }
            
            $this->addLine("Issuer: {$certification->issuer}");
            $this->addLine("Expiry Date: " . $certification->expiry_date->format('M d, Y'));
            $this->addLine("Please take immediate action to renew this certificate.");
            
            $this->setAction('View Certificate', route('admin.certifications.show', $certification));
            $this->salutation = 'Best regards,<br>' . config('app.name') . ' System';
        }
    }
}

// File: app/Notifications/TestimonialCreatedNotification.php

namespace App\Notifications;

use App\Models\Testimonial;

class TestimonialCreatedNotification extends BaseNotification
{
    protected function configure(): void
    {
        $testimonial = $this->data;
        
        if ($testimonial instanceof Testimonial) {
            $this->subject = "New Testimonial Submitted";
            $this->greeting = "New Testimonial!";
            
            $this->addLine("A new testimonial has been submitted for review:");
            $this->addLine("Client: {$testimonial->client_name}");
            $this->addLine("Project: " . ($testimonial->project->title ?? 'N/A'));
            $this->addLine("Rating: {$testimonial->rating}/5 stars");
            
            $this->addLine("Please review and approve this testimonial.");
            
            $this->setAction('Review Testimonial', route('admin.testimonials.show', $testimonial));
            $this->salutation = 'Best regards,<br>' . config('app.name') . ' System';
        }
    }
}

// File: app/Notifications/TestimonialApprovedNotification.php

namespace App\Notifications;

use App\Models\Testimonial;

class TestimonialApprovedNotification extends BaseNotification
{
    protected function configure(): void
    {
        $testimonial = $this->data;
        
        if ($testimonial instanceof Testimonial) {
            $this->subject = "Your Testimonial Has Been Approved!";
            $this->greeting = "Thank you {$testimonial->client_name}!";
            
            $this->addLine("Your testimonial for the project '{$testimonial->project->title}' has been approved and published.");
            $this->addLine("We truly appreciate you taking the time to share your experience.");
            $this->addLine("Your feedback helps us improve our services and helps other clients make informed decisions.");
            
            if ($testimonial->featured) {
                $this->addLine("🌟 Your testimonial has also been featured on our website!");
            }
            
            $this->setAction('View Your Testimonial', route('testimonials.show', $testimonial));
            $this->salutation = 'With gratitude,<br>' . config('app.name') . ' Team';
        }
    }
}