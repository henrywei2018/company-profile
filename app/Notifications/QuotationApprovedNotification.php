<?php
// File: app/Notifications/QuotationApprovedNotification.php

namespace App\Notifications;

use App\Models\Quotation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class QuotationApprovedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected Quotation $quotation;
    protected ?string $adminNotes;

    public function __construct(Quotation $quotation, ?string $adminNotes = null)
    {
        $this->quotation = $quotation;
        $this->adminNotes = $adminNotes;
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject('Quotation Approved: ' . $this->quotation->project_type)
            ->success()
            ->greeting('Great News, ' . $this->quotation->name . '!')
            ->line('🎉 Your quotation request has been approved!')
            ->line('**Project Details:**')
            ->line('• **Service:** ' . ($this->quotation->service->title ?? 'General Service'))
            ->line('• **Project Type:** ' . $this->quotation->project_type)
            ->line('• **Approved On:** ' . now()->format('M d, Y'));

        if ($this->adminNotes) {
            $mail->line('**Admin Notes:**')
                 ->line($this->adminNotes);
        }

        return $mail
            ->line('**Next Steps:**')
            ->line('• We will contact you within 24 hours to discuss project details')
            ->line('• Our team will prepare a detailed project proposal')
            ->line('• You will receive timeline and cost breakdown')
            ->action('View Quotation', route('client.quotations.show', $this->quotation))
            ->line('Thank you for choosing our services!')
            ->salutation('Best regards,<br>' . config('app.name') . ' Sales Team');
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'quotation_approved',
            'quotation_id' => $this->quotation->id,
            'client_name' => $this->quotation->name,
            'project_type' => $this->quotation->project_type,
            'service' => $this->quotation->service->title ?? 'General',
            'approved_at' => now()->toISOString(),
            'admin_notes' => $this->adminNotes,
            'title' => 'Quotation Approved',
            'message' => "Your quotation for \"{$this->quotation->project_type}\" has been approved!",
            'priority' => 'high',
        ];
    }
}