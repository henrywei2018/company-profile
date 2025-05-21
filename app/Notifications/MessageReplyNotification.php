<?php

namespace App\Notifications;

use App\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

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
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $mail = (new MailMessage)
            ->subject($this->message->subject)
            ->greeting('Hello ' . ($notifiable->name ?? ''))
            ->line('You have received a reply to your message:')
            ->line(new HtmlString('<strong>' . $this->message->subject . '</strong>'))
            ->line(new HtmlString('<div style="padding: 10px; background-color: #f8f9fa; border-left: 4px solid #3490dc; margin: 15px 0;">' . nl2br(e($this->message->message)) . '</div>'));

        // Add attachments if any
        if ($this->message->attachments && $this->message->attachments->count() > 0) {
            $mail->line('Attachments are included in this message. You can view them by logging into your account.');
        }

        // If the notifiable is a User, add a link to view the message in the client portal
        if (method_exists($notifiable, 'hasRole') && $notifiable->hasRole('client')) {
            $mail->action('View Message', route('client.messages.show', $this->message));
        } else {
            $mail->line('If you wish to reply to this message, please do so by replying to this email or visit our website.');
        }

        $mail->line('Thank you for contacting us!');

        return $mail;
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
            'sender_email' => $this->message->email,
            'preview' => substr(strip_tags($this->message->message), 0, 100),
            'created_at' => $this->message->created_at->toIso8601String(),
        ];
    }
}