<?php
// File: app/Notifications/WelcomeNotification.php - SAFE VERSION

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WelcomeNotification extends Notification
{
    use Queueable;

    protected ?User $user;

    public function __construct(?User $user = null)
    {
        $this->user = $user;
        
        // If user is null, throw a more helpful error
        if (!$this->user) {
            throw new \InvalidArgumentException('WelcomeNotification requires a valid User object. Received null.');
        }
    }

    public function via($notifiable): array
    {
        return ['database']; // Removed mail for testing, removed ShouldQueue for immediate processing
    }

    public function toArray($notifiable): array
    {
        // Ensure we have a user
        if (!$this->user) {
            throw new \Exception('No user available for notification data');
        }

        return [
            'type' => 'user.welcome',
            'title' => 'Welcome to ' . $this->getCompanyName(),
            'message' => "Welcome {$this->user->name}! Thank you for joining us.",
            'action_url' => $this->getActionUrl(),
            'action_text' => $this->getActionText(),
            'user_id' => $this->user->id,
            'user_name' => $this->user->name,
            'user_email' => $this->user->email,
            'priority' => 'normal',
            'created_at' => now()->toISOString(),
        ];
    }

    protected function getCompanyName(): string
    {
        try {
            return settings('company_name', config('app.name', 'CV Usaha Prima Lestari'));
        } catch (\Exception $e) {
            return 'CV Usaha Prima Lestari';
        }
    }

    protected function getActionUrl(): string
    {
        try {
            if (method_exists($this->user, 'hasRole') && $this->user->hasRole('client')) {
                return route('client.dashboard');
            }
            return route('home');
        } catch (\Exception $e) {
            return url('/'); // Fallback to home URL
        }
    }

    protected function getActionText(): string
    {
        try {
            if (method_exists($this->user, 'hasRole') && $this->user->hasRole('client')) {
                return 'Go to Dashboard';
            }
            return 'Visit Website';
        } catch (\Exception $e) {
            return 'Visit Website';
        }
    }

    public function toMail($notifiable): MailMessage
    {
        $companyName = $this->getCompanyName();
        
        return (new MailMessage)
            ->subject("Welcome to {$companyName}!")
            ->greeting("Welcome {$this->user->name}!")
            ->line("Thank you for joining {$companyName}. We're excited to have you on board!")
            ->action($this->getActionText(), $this->getActionUrl())
            ->line('If you have any questions, feel free to contact us.')
            ->salutation("Welcome aboard!<br>" . $companyName . " Team");
    }
}