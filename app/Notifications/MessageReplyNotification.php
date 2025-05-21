<?php

namespace App\Notifications;

use App\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class MessageReplyNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The message instance.
     *
     * @var \App\Models\Message
     */
    protected $message;

    /**
     * Create a new notification instance.
     *
     * @param  \App\Models\Message  $message
     * @return void
     */
    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $mailMessage = (new MailMessage)
            ->subject('New message: ' . $this->message->subject)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('You have received a new message from ' . config('app.name') . ':')
            ->line('Subject: ' . $this->message->subject)
            ->line('Message:')
            ->line(Str::limit(strip_tags($this->message->message), 300))
            ->action('View Message', route('client.messages.show', $this->message))
            ->line('Thank you for using our application!');

        // Add attachments if any
        if ($this->message->attachments->count() > 0) {
            $mailMessage->line('This message has ' . $this->message->attachments->count() . ' attachment(s). Please log in to view them.');
        }

        return $mailMessage;
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'message_id' => $this->message->id,
            'subject' => $this->message->subject,
            'sender' => $this->message->name,
        ];
    }
}