<?php
// File: app/Notifications/MessageReplyNotification.php

namespace App\Notifications;

use App\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MessageReplyNotification extends Notification implements ShouldQueue
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
        $companyName = settings('company_name', config('app.name'));
        
        return (new MailMessage)
            ->subject('New Reply: ' . $this->message->subject)
            ->greeting('Hello!')
            ->line('You have received a reply to your message.')
            ->line('**Subject:** ' . $this->message->subject)
            ->line('**From:** ' . config('app.name') . ' Support Team')
            ->when($this->message->attachments && $this->message->attachments->count() > 0, function ($mail) {
                return $mail->line('This message includes ' . $this->message->attachments->count() . ' attachment(s).');
            })
            ->action('View Message', $this->getMessageUrl())
            ->line('Thank you for using our services!')
            ->salutation("Best regards,<br>{$companyName} Team");
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'message.reply',
            'title' => 'New Message Reply',
            'message' => 'You have received a reply to your message.',
            'subject' => $this->message->subject,
            'priority' => $this->message->priority ?? 'normal',
            'action_url' => $this->getMessageUrl(),
            'message_id' => $this->message->id,
            'action_text' => 'View Messages',
            'sender_name' => $this->message->name ?? 'Support Team',
            'created_at' => $this->message->created_at->toISOString(),
        ];
    }

    protected function getMessageUrl(): string
    {
        // Try to determine if this is for admin or client
        try {
            if ($this->message->user) {
                // Client message
                return route('client.messages.show', $this->message);
            } else {
                // Admin message  
                return route('admin.messages.show', $this->message);
            }
        } catch (\Exception $e) {
            // Fallback
            return route('client.messages.index');
        }
    }
}