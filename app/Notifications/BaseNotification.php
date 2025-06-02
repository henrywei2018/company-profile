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



// File: app/Notifications/TestimonialApprovedNotification.php

