<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EmailVerifiedNotification extends Notification implements ShouldQueue
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
            ->subject('Email Verified Successfully')
            ->greeting("Welcome, {$this->user->name}!")
            ->line("✅ Your email address has been successfully verified.")
            ->line("**Account Details:**")
            ->line("• **Name:** {$this->user->name}")
            ->line("• **Email:** {$this->user->email}")
            ->line("• **Verified:** " . now()->format('M d, Y H:i'))
            ->line("**What You Can Do Now:**")
            ->line("• Access your full account features")
            ->line("• Submit quotation requests")
            ->line("• Track your projects")
            ->line("• Communicate with our team")
            ->action(
                'Go to Dashboard',
                $this->user->hasRole('client') ? route('client.dashboard') : route('admin.dashboard')
            )
            ->salutation("Welcome aboard,<br>" . $companyName . " Team");
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'user.email_verified',
            'title' => 'Email Verified Successfully',
            'message' => "Your email address has been successfully verified. Welcome aboard!",
            'action_url' => $this->user->hasRole('client') ? route('client.dashboard') : route('admin.dashboard'),
            'action_text' => 'Go to Dashboard',
            'user_id' => $this->user->id,
            'user_name' => $this->user->name,
            'user_email' => $this->user->email,
            'verified_at' => now()->toISOString(),
            'priority' => 'normal',
        ];
    }
}