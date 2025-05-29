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
    protected string $ipAddress;
    protected string $userAgent;

    public function __construct(User $user, string $ipAddress = null, string $userAgent = null)
    {
        $this->user = $user;
        $this->ipAddress = $ipAddress ?? request()->ip();
        $this->userAgent = $userAgent ?? request()->userAgent();
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Password Changed Successfully')
            ->greeting('Hello ' . $this->user->name)
            ->line('🔐 Your account password has been changed successfully.')
            ->line('**Security Details:**')
            ->line('• **Changed:** ' . now()->format('M d, Y H:i'))
            ->line('• **IP Address:** ' . $this->ipAddress)
            ->line('• **Browser:** ' . $this->getBrowserName())
            ->line('**Important Security Notice:**')
            ->line('If you did not make this change, please contact us immediately.')
            ->line('**Security Recommendations:**')
            ->line('• Use a strong, unique password')
            ->line('• Enable two-factor authentication if available')
            ->line('• Keep your account information updated')
            ->action('Login to Account', route('login'))
            ->line('If you have any concerns about your account security, please contact our support team.')
            ->salutation('Stay secure,<br>' . config('app.name') . ' Security Team');
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'password_changed',
            'user_id' => $this->user->id,
            'user_name' => $this->user->name,
            'user_email' => $this->user->email,
            'changed_at' => now()->toISOString(),
            'ip_address' => $this->ipAddress,
            'user_agent' => $this->userAgent,
            'browser' => $this->getBrowserName(),
            'title' => 'Password Changed',
            'message' => 'Your account password has been changed successfully',
            'priority' => 'medium',
        ];
    }

    protected function getBrowserName(): string
    {
        $userAgent = $this->userAgent;
        
        if (strpos($userAgent, 'Chrome') !== false) {
            return 'Google Chrome';
        } elseif (strpos($userAgent, 'Firefox') !== false) {
            return 'Mozilla Firefox';
        } elseif (strpos($userAgent, 'Safari') !== false) {
            return 'Safari';
        } elseif (strpos($userAgent, 'Edge') !== false) {
            return 'Microsoft Edge';
        } else {
            return 'Unknown Browser';
        }
    }
}