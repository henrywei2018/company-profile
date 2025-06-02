<?php
// File: app/Notifications/UrgentMessageNotification.php

namespace App\Notifications;

use App\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UrgentMessageNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected Message $message;

    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('🚨 URGENT: ' . $this->message->subject)
            ->error() // Makes email red/urgent styling
            ->greeting('Urgent Alert!')
            ->line('An urgent message requires immediate attention:')
            ->line('**From:** ' . $this->message->name . ' (' . $this->message->email . ')')
            ->line('**Subject:** ' . $this->message->subject)
            ->line('**Received:** ' . $this->message->created_at->format('M d, Y H:i'))
            ->line('**Message Preview:**')
            ->line('"' . \Illuminate\Support\Str::limit($this->message->message, 200) . '"')
            ->line('Please respond as soon as possible.')
            ->action('View Message Now', route('admin.messages.show', $this->message))
            ->salutation('Urgent Alert,<br>' . config('app.name') . ' System');
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'message.urgent',
            'title' => '🚨 URGENT: ' . $this->message->subject,
            'message' => 'An urgent message requires immediate attention from ' . $this->message->name,
            'subject' => $this->message->subject,
            'sender_name' => $this->message->name,
            'sender_email' => $this->message->email,
            'priority' => 'urgent',
            'action_url' => route('admin.messages.show', $this->message),
            'message_id' => $this->message->id,
            'action_text' => 'View Message Now',
            'message_preview' => \Illuminate\Support\Str::limit($this->message->message, 100),
            'created_at' => $this->message->created_at->toISOString(),
        ];
    }
}