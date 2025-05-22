<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Quotation;

class QuotationResponse extends Mailable
{
    use Queueable, SerializesModels;

    public $quotation;
    public $emailSubject;
    public $emailMessage;
    public $includeQuotation;

    /**
     * Create a new message instance.
     */
    public function __construct(Quotation $quotation, string $emailSubject, string $emailMessage, bool $includeQuotation = false)
    {
        $this->quotation = $quotation;
        $this->emailSubject = $emailSubject;
        $this->emailMessage = $emailMessage;
        $this->includeQuotation = $includeQuotation;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->emailSubject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.quotation-response',
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