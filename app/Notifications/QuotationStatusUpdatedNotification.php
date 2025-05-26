<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Quotation;

class QuotationStatusUpdatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $quotation;
    protected $oldStatus;
    protected $newStatus;

    /**
     * Create a new notification instance.
     */
    public function __construct(Quotation $quotation, $oldStatus, $newStatus)
    {
        $this->quotation = $quotation;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        $channels = [];
        
        if (settings('quotation_status_updates_enabled', true)) {
            $channels[] = 'mail';
        }
        
        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        $statusMessages = [
            'pending' => 'Your quotation request is being reviewed by our team.',
            'reviewed' => 'Your quotation request has been reviewed and we will send you a detailed quote soon.',
            'approved' => 'Great news! Your quotation request has been approved. We will send you the detailed quotation shortly.',
            'rejected' => 'Thank you for your interest. Unfortunately, we cannot proceed with your quotation request at this time.',
            'sent' => 'Your quotation has been prepared and sent. Please check your email for the detailed quote.',
            'accepted' => 'Thank you for accepting our quotation! We will contact you soon to discuss the next steps.',
            'completed' => 'Your project has been successfully completed. Thank you for choosing our services!',
        ];

        $message = $statusMessages[$this->newStatus] ?? 'Your quotation status has been updated.';

        return (new MailMessage)
            ->subject('Quotation Status Update - ' . ucfirst($this->newStatus))
            ->greeting('Hello ' . $this->quotation->name . '!')
            ->line('Your quotation request status has been updated.')
            ->line('**Service:** ' . ($this->quotation->service ? $this->quotation->service->name : 'General'))
            ->line('**Status:** ' . ucfirst($this->newStatus))
            ->line($message)
            ->when($this->newStatus === 'approved', function ($mail) {
                return $mail->line('We will contact you within 24 hours to discuss the details and next steps.');
            })
            ->when($this->newStatus === 'rejected', function ($mail) {
                return $mail->line('If you have any questions about this decision, please feel free to contact us.');
            })
            ->line('If you have any questions, please don\'t hesitate to reach out to us.')
            ->line('Best regards,')
            ->line(settings('company_name', 'CV Usaha Prima Lestari') . ' Team')
            ->replyTo(settings('quotation_reply_to', settings('admin_email')));
    }
}