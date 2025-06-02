<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProfileIncompleteNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'user.profile_incomplete',
            'title' => 'Complete Your Profile',
            'message' => 'Please complete your profile to access all features.',
            'action_url' => route('client.profile.edit'),
            'action_text' => 'Complete Profile',
            'priority' => 'normal',
        ];
    }
}