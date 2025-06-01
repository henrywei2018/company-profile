<?php
// File: app/Notifications/TestNotification.php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TestNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Test Notification from Your Dashboard')
            ->greeting('Hello ' . $this->user->name . '!')
            ->line('This is a test notification from your client dashboard.')
            ->line('If you received this email, your notification system is working correctly.')
            ->action('Visit Dashboard', route('client.dashboard'))
            ->line('Thank you for using our platform!')
            ->salutation('Best regards,<br>' . config('app.name') . ' Team');
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'test_notification',
            'title' => 'Test Notification',
            'message' => 'This is a test notification to verify your notification system is working.',
            'user_id' => $this->user->id,
            'user_name' => $this->user->name,
            'action_url' => route('client.dashboard'),
            'action_text' => 'View Dashboard',
            'priority' => 'normal',
        ];
    }
}