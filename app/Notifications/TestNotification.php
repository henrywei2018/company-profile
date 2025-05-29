<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TestNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected string $userType;

    public function __construct(string $userType = 'general')
    {
        $this->userType = $userType;
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Test Notification')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('This is a test notification to verify that the notification system is working correctly.')
            ->line('**User Type:** ' . ucfirst($this->userType))
            ->line('**Sent At:** ' . now()->format('M d, Y H:i:s'))
            ->action('Visit Dashboard', $this->userType === 'admin' ? route('admin.dashboard') : route('client.dashboard'))
            ->line('If you received this notification, the system is working properly!');
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'test_notification',
            'user_type' => $this->userType,
            'title' => 'Test Notification',
            'message' => 'Notification system test - all systems working',
        ];
    }
}