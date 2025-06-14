<?php


namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PasswordChangedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected User $user;

    public function __construct(User $user)
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
            ->subject('Password Changed Successfully')
            ->line('Your password has been changed successfully.')
            ->line('If you did not make this change, please contact us immediately.')
            ->action('Secure Account', route('profile.show'));
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'user.password_changed',
            'title' => 'Password Changed',
            'message' => 'Your password has been changed successfully.',
            'priority' => 'high',
        ];
    }
}