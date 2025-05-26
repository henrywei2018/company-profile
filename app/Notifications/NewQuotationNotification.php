<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Quotation;

class NewQuotationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $quotation;

    /**
     * Create a new notification instance.
     */
    public function __construct(Quotation $quotation)
    {
        $this->quotation = $quotation;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        $channels = [];
        
        if (settings('quotation_email_enabled', true)) {
            $channels[] = 'mail';
        }
        
        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject('New Quotation Request - ' . ($this->quotation->service ? $this->quotation->service->name : 'General'))
            ->greeting('New Quotation Request!')
            ->line('You have received a new quotation request through your website.')
            ->line('**Client:** ' . $this->quotation->name)
            ->line('**Email:** ' . $this->quotation->email)
            ->line('**Phone:** ' . ($this->quotation->phone ?: 'Not provided'))
            ->line('**Company:** ' . ($this->quotation->company ?: 'Not provided'))
            ->line('**Service:** ' . ($this->quotation->service ? $this->quotation->service->name : 'General'))
            ->line('**Budget:** ' . ($this->quotation->budget_range ?: 'Not specified'))
            ->line('**Project Type:** ' . $this->quotation->project_type)
            ->line('**Requirements:**')
            ->line($this->quotation->requirements)
            ->action('Review Quotation', route('admin.quotations.show', $this->quotation))
            ->line('Please review and respond to this quotation request promptly.')
            ->replyTo($this->quotation->email, $this->quotation->name);
        
        // Add CC if configured
        if ($ccEmail = settings('quotation_cc_email')) {
            $mail->cc($ccEmail);
        }
        
        return $mail;
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        return [
            'quotation_id' => $this->quotation->id,
            'client_name' => $this->quotation->name,
            'client_email' => $this->quotation->email,
            'service' => $this->quotation->service ? $this->quotation->service->name : 'General',
            'budget' => $this->quotation->budget_range,
        ];
    }
}