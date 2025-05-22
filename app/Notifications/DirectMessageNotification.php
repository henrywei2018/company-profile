<?php

namespace App\Notifications;

use App\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DirectMessageNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $message;

    /**
     * Create a new notification instance.
     */
    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        $companyName = config('app.name', 'CV. Usaha Prima Lestari');
        
        return (new MailMessage)
            ->subject($this->message->subject)
            ->greeting('Hello!')
            ->line("You have received a new message from {$companyName}.")
            ->line('Subject: ' . $this->message->subject)
            ->line('')
            ->line($this->message->message)
            ->when($this->message->attachments->count() > 0, function ($mail) {
                return $mail->line('This message includes ' . $this->message->attachments->count() . ' attachment(s).');
            })
            ->action('View Message', $this->getViewUrl())
            ->line('If you have any questions, please don\'t hesitate to contact us.')
            ->salutation('Best regards,<br>' . $companyName . ' Team');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        return [
            'type' => 'direct_message',
            'message_id' => $this->message->id,
            'subject' => $this->message->subject,
            'sender_name' => $this->message->name,
            'sender_email' => $this->message->email,
            'has_attachments' => $this->message->attachments->count() > 0,
            'attachment_count' => $this->message->attachments->count(),
            'preview' => \Illuminate\Support\Str::limit(strip_tags($this->message->message), 100),
        ];
    }

    /**
     * Get the appropriate view URL based on recipient type.
     */
    protected function getViewUrl(): string
    {
        // If the recipient is a registered user, send them to client portal
        if ($this->message->user_id) {
            return route('client.messages.show', $this->message->id);
        }
        
        // For non-registered users, we could create a public view or direct them to contact
        return route('contact.index');
    }

    /**
     * Get the notification's database type.
     */
    public function databaseType($notifiable): string
    {
        return 'direct_message';
    }
}