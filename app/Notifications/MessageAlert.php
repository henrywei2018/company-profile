<?php

namespace App\Notifications;

use App\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MessageAlert extends Notification implements ShouldQueue
{
    use Queueable;

    protected Message $message;
    protected string $alertType;

    public function __construct(Message $message, string $alertType = 'new')
    {
        $this->message = $message;
        $this->alertType = $alertType;
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return match($this->alertType) {
            'urgent' => (new MailMessage)
                ->subject('Urgent Message: ' . $this->message->subject)
                ->error()
                ->greeting('Urgent Message Alert!')
                ->line('You have received an urgent message that requires immediate attention.')
                ->line('**From:** ' . $this->message->name . ' (' . $this->message->email . ')')
                ->line('**Subject:** ' . $this->message->subject)
                ->action('View Message', route('admin.messages.show', $this->message)),

            'unreplied' => (new MailMessage)
                ->subject('Unreplied Message: ' . $this->message->subject)
                ->warning()
                ->greeting('Message Needs Reply!')
                ->line('Message from "' . $this->message->name . '" has been unreplied for ' . now()->diffInHours($this->message->created_at) . ' hours.')
                ->line('**Subject:** ' . $this->message->subject)
                ->action('Reply to Message', route('admin.messages.show', $this->message)),

            default => (new MailMessage)
                ->subject('New Message: ' . $this->message->subject)
                ->greeting('New Message Received!')
                ->line('You have received a new message from ' . $this->message->name)
                ->line('**Subject:** ' . $this->message->subject)
                ->action('View Message', route('admin.messages.show', $this->message)),
        };
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'message_alert',
            'alert_type' => $this->alertType,
            'message_id' => $this->message->id,
            'sender_name' => $this->message->name,
            'sender_email' => $this->message->email,
            'subject' => $this->message->subject,
            'priority' => $this->message->priority ?? 'normal',
            'title' => match($this->alertType) {
                'urgent' => 'Urgent Message',
                'unreplied' => 'Unreplied Message',
                default => 'New Message',
            },
            'message' => match($this->alertType) {
                'urgent' => "Urgent message from {$this->message->name}: {$this->message->subject}",
                'unreplied' => "Message from {$this->message->name} unreplied for " . now()->diffInHours($this->message->created_at) . " hours",
                default => "New message from {$this->message->name}: {$this->message->subject}",
            },
        ];
    }
}