<?php
// File: app/Mail/NotificationMail.php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Queue\SerializesModels;

class NotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    protected MailMessage $mailMessage;
    protected string $notificationType;

    /**
     * Create a new message instance.
     */
    public function __construct(MailMessage $mailMessage, string $notificationType)
    {
        $this->mailMessage = $mailMessage;
        $this->notificationType = $notificationType;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->mailMessage->subject ?: 'Notification from ' . config('app.name'),
            from: config('mail.from.address'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.notification',
            with: [
                'mailMessage' => $this->mailMessage,
                'notificationType' => $this->notificationType,
                'greeting' => $this->mailMessage->greeting,
                'introLines' => $this->mailMessage->introLines,
                'actionText' => $this->mailMessage->actionText,
                'actionUrl' => $this->mailMessage->actionUrl,
                'outroLines' => $this->mailMessage->outroLines,
                'salutation' => $this->mailMessage->salutation,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        // Handle any attachments from the MailMessage if needed
        return [];
    }
}