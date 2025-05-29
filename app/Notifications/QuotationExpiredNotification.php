<?php

namespace App\Notifications;

use App\Models\Quotation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class QuotationExpiredNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected Quotation $quotation;

    public function __construct(Quotation $quotation)
    {
        $this->quotation = $quotation;
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Quotation Expired: ' . $this->quotation->project_type)
            ->error()
            ->greeting('Hello ' . $this->quotation->name)
            ->line('Your quotation for "' . $this->quotation->project_type . '" has expired.')
            ->line('**Quotation Details:**')
            ->line('• **Project:** ' . $this->quotation->project_type)
            ->line('• **Service:** ' . ($this->quotation->service->title ?? 'General'))
            ->line('• **Approved:** ' . $this->quotation->approved_at?->format('M d, Y'))
            ->line('• **Expired:** ' . now()->format('M d, Y'))
            ->line('**What You Can Do:**')
            ->line('• Submit a new quotation request if you\'re still interested')
            ->line('• Contact us to discuss renewing this quotation')
            ->line('• Our team is ready to help with updated pricing')
            ->action('Request New Quotation', route('quotation.create'))
            ->line('We hope to work with you in the future!')
            ->salutation('Best regards,<br>' . config('app.name') . ' Team');
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'quotation_expired',
            'quotation_id' => $this->quotation->id,
            'client_name' => $this->quotation->name,
            'project_type' => $this->quotation->project_type,
            'service' => $this->quotation->service->title ?? 'General',
            'approved_at' => $this->quotation->approved_at?->toISOString(),
            'expired_at' => now()->toISOString(),
            'title' => 'Quotation Expired',
            'message' => "Quotation for \"{$this->quotation->project_type}\" has expired",
            'priority' => 'low',
        ];
    }
}