<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Quotation;

class QuotationReceived extends Mailable
{
    use Queueable, SerializesModels;

    public $quotation;
    public $isAdminNotification;

    /**
     * Create a new message instance.
     */
    public function __construct(Quotation $quotation, bool $isAdminNotification = false)
    {
        $this->quotation = $quotation;
        $this->isAdminNotification = $isAdminNotification;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = $this->isAdminNotification 
            ? 'New Quotation Request from ' . $this->quotation->name
            : 'Thank you for your quotation request - ' . config('app.name');

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $view = $this->isAdminNotification 
            ? 'emails.quotation-received-admin'
            : 'emails.quotation-received-client';

        return new Content(
            view: $view,
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}