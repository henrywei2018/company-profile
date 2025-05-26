<?php
// File: app/Notifications/MessageAutoReplyNotification.php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Message;

class MessageAutoReplyNotification extends Notification implements ShouldQueue
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
        $channels = [];
        
        if (settings('message_auto_reply_enabled', true)) {
            $channels[] = 'mail';
        }
        
        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        $template = settings('message_auto_reply_template');
        
        if ($template) {
            // Replace placeholders in custom template
            $content = str_replace(
                ['{name}', '{email}', '{subject}', '{company}'],
                [
                    $this->message->name,
                    $this->message->email,
                    $this->message->subject,
                    settings('company_name', 'CV Usaha Prima Lestari')
                ],
                $template
            );
            
            return (new MailMessage)
                ->subject('Thank you for contacting us - ' . $this->message->subject)
                ->view('emails.custom-template', ['content' => $content])
                ->replyTo(settings('message_reply_to', settings('admin_email')));
        }
        
        // Default auto-reply
        return (new MailMessage)
            ->subject('Thank you for contacting us - ' . $this->message->subject)
            ->greeting('Hello ' . $this->message->name . '!')
            ->line('Thank you for reaching out to us. We have received your message regarding "' . $this->message->subject . '" and will respond within 24 hours.')
            ->line('Our team is committed to providing you with the best service possible.')
            ->line('If you have any urgent questions, please don\'t hesitate to call us directly.')
            ->line('Best regards,')
            ->line(settings('company_name', 'CV Usaha Prima Lestari') . ' Team')
            ->replyTo(settings('message_reply_to', settings('admin_email')));
    }
}