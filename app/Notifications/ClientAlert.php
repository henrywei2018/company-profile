<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ClientAlert extends Notification implements ShouldQueue
{
    use Queueable;

    protected array $alert;

    public function __construct(array $alert)
    {
        $this->alert = $alert;
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        $severity = $this->alert['severity'] ?? 'info';
        $mailMethod = match($severity) {
            'high', 'critical' => 'error',
            'medium' => 'warning',
            default => 'info',
        };

        $mail = (new MailMessage)
            ->subject($this->getAlertSubject())
            ->{$mailMethod}()
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line($this->alert['message']);

        if (isset($this->alert['data']['action_url'])) {
            $mail->action('Take Action', $this->alert['data']['action_url']);
        }

        return $mail->line('Thank you for using our services!');
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'client_alert',
            'alert_type' => $this->alert['type'],
            'severity' => $this->alert['severity'],
            'title' => $this->getAlertTitle(),
            'message' => $this->alert['message'],
            'data' => $this->alert['data'] ?? [],
        ];
    }

    private function getAlertSubject(): string
    {
        return match($this->alert['type']) {
            'overdue_payment' => 'Payment Reminder',
            'incomplete_profile' => 'Complete Your Profile',
            'pending_approvals' => 'Action Required',
            'upcoming_deadline' => 'Project Deadline Reminder',
            default => 'Important Notice',
        };
    }

    private function getAlertTitle(): string
    {
        return match($this->alert['type']) {
            'overdue_payment' => 'Payment Overdue',
            'incomplete_profile' => 'Profile Incomplete',
            'pending_approvals' => 'Pending Approvals',
            'upcoming_deadline' => 'Deadline Approaching',
            default => 'Notification',
        };
    }
}