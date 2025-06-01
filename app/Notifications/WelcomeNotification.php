<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WelcomeNotification extends Notification implements ShouldQueue
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
        $companyName = settings('company_name', config('app.name'));
        
        return (new MailMessage)
            ->subject("Welcome to {$companyName}!")
            ->greeting("Welcome {$this->user->name}!")
            ->line("Thank you for joining {$companyName}. We're excited to have you on board!")
            ->when($this->user->hasRole('client'), function ($mail) {
                return $mail
                    ->line("As a client, you can:")
                    ->line("• View and track your projects")
                    ->line("• Request quotations for new projects")
                    ->line("• Communicate with our team through messages")
                    ->line("• Leave testimonials for completed projects");
            })
            ->when(!$this->user->hasRole('client'), function ($mail) {
                return $mail->line("We're here to help you with all your construction and supply needs.");
            })
            ->action(
                $this->user->hasRole('client') ? 'Explore Client Portal' : 'Visit Our Website',
                $this->user->hasRole('client') ? route('client.dashboard') : route('home')
            )
            ->line('If you have any questions, feel free to contact us.')
            ->salutation("Welcome aboard!<br>" . $companyName . " Team");
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'user.welcome',
            'title' => 'Welcome to ' . settings('company_name', config('app.name')),
            'message' => "Welcome {$this->user->name}! Thank you for joining us.",
            'action_url' => $this->user->hasRole('client') ? route('client.dashboard') : route('home'),
            'action_text' => $this->user->hasRole('client') ? 'Go to Dashboard' : 'Visit Website',
            'user_id' => $this->user->id,
            'user_name' => $this->user->name,
            'user_email' => $this->user->email,
            'priority' => 'normal',
        ];
    }
}