<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Quotation;

class QuotationConfirmationNotification extends Notification implements ShouldQueue
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
        
        if (settings('quotation_client_confirmation_enabled', true)) {
            $channels[] = 'mail';
        }
        
        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        $template = settings('quotation_confirmation_template');
        
        if ($template) {
            // Replace placeholders in custom template
            $content = str_replace(
                ['{name}', '{email}', '{service}', '{company}'],
                [
                    $this->quotation->name,
                    $this->quotation->email,
                    $this->quotation->service ? $this->quotation->service->name : 'General Service',
                    $this->quotation->company ?: 'Your Company'
                ],
                $template
            );
            
            return (new MailMessage)
                ->subject('Quotation Request Received - ' . ($this->quotation->service ? $this->quotation->service->name : 'General'))
                ->view('emails.custom-template', ['content' => $content])
                ->replyTo(settings('quotation_reply_to', settings('admin_email')));
        }
        
        // Default confirmation
        return (new MailMessage)
            ->subject('Quotation Request Received - ' . ($this->quotation->service ? $this->quotation->service->name : 'General'))
            ->greeting('Hello ' . $this->quotation->name . '!')
            ->line('Thank you for your interest in our services. We have successfully received your quotation request for ' . ($this->quotation->service ? $this->quotation->service->name : 'our services') . '.')
            ->line('**What happens next?**')
            ->line('• Our team will review your requirements within 24 hours')
            ->line('• We may contact you for additional details if needed')
            ->line('• You will receive a detailed quotation within 2-3 business days')
            ->line('• Our quotation will be valid for 30 days from the date of issue')
            ->line('If you have any urgent questions, please don\'t hesitate to contact us directly.')
            ->line('Best regards,')
            ->line(settings('company_name', 'CV Usaha Prima Lestari') . ' Sales Team')
            ->replyTo(settings('quotation_reply_to', settings('admin_email')));
    }
}