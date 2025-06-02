<?php
// File: app/Notifications/MessageCreatedNotification.php

namespace App\Notifications;

use App\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MessageCreatedNotification extends Notification implements ShouldQueue
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
            ->subject('New Message: ' . $this->message->subject)
            ->greeting('New Message!')
            ->line('You have received a new message:')
            ->line('**From:** ' . $this->message->name)
            ->line('**Email:** ' . $this->message->email)
            ->line('**Subject:** ' . $this->message->subject)
            ->when($this->message->priority === 'urgent', function ($mail) {
                return $mail->line('⚠️ This message is marked as urgent.');
            })
            ->action('View Message', route('admin.messages.show', $this->message))
            ->line('Please respond promptly to provide excellent customer service.')
            ->salutation('Best regards,<br>' . config('app.name') . ' System');
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'message.created',
            'title' => 'New Message: ' . $this->message->subject,
            'message' => 'You have received a new message from ' . $this->message->name,
            'subject' => $this->message->subject,
            'sender_name' => $this->message->name,
            'sender_email' => $this->message->email,
            'priority' => $this->message->priority ?? 'normal',
            'action_url' => route('admin.messages.show', $this->message),
            'message_id' => $this->message->id,
            'action_text' => 'View Message',
            'created_at' => $this->message->created_at->toISOString(),
        ];
    }
}