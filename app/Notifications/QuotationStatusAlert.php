<?php

namespace App\Notifications;

use App\Models\Quotation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class QuotationStatusAlert extends Notification implements ShouldQueue
{
    use Queueable;

    protected Quotation $quotation;
    protected string $alertType;

    public function __construct(Quotation $quotation, string $alertType)
    {
        $this->quotation = $quotation;
        $this->alertType = $alertType;
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return match($this->alertType) {
            'pending_too_long' => (new MailMessage)
                ->subject('Quotation Pending: ' . $this->quotation->project_type)
                ->warning()
                ->greeting('Quotation Alert!')
                ->line('Quotation from "' . $this->quotation->name . '" has been pending for ' . now()->diffInDays($this->quotation->created_at) . ' days.')
                ->line('**Service:** ' . ($this->quotation->service->title ?? 'General'))
                ->action('Review Quotation', route('admin.quotations.show', $this->quotation)),

            'client_approval_needed' => (new MailMessage)
                ->subject('Client Approval Needed')
                ->info()
                ->greeting('Client Response Required!')
                ->line('Quotation for "' . $this->quotation->name . '" has been approved but still awaiting client response.')
                ->line('**Approved:** ' . $this->quotation->approved_at->diffForHumans())
                ->action('View Quotation', route('admin.quotations.show', $this->quotation)),

            'urgent_priority' => (new MailMessage)
                ->subject('Urgent Quotation: ' . $this->quotation->project_type)
                ->error()
                ->greeting('Urgent Quotation!')
                ->line('High priority quotation from "' . $this->quotation->name . '" requires immediate attention.')
                ->action('Handle Urgent Quotation', route('admin.quotations.show', $this->quotation)),

            default => (new MailMessage)
                ->subject('Quotation Alert')
                ->line('Quotation update for: ' . $this->quotation->project_type)
                ->action('View Quotation', route('admin.quotations.show', $this->quotation)),
        };
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'quotation_alert',
            'alert_type' => $this->alertType,
            'quotation_id' => $this->quotation->id,
            'client_name' => $this->quotation->name,
            'project_type' => $this->quotation->project_type,
            'service' => $this->quotation->service->title ?? 'General',
            'title' => match($this->alertType) {
                'pending_too_long' => 'Quotation Pending Too Long',
                'client_approval_needed' => 'Client Approval Needed',
                'urgent_priority' => 'Urgent Quotation',
                default => 'Quotation Alert',
            },
            'message' => match($this->alertType) {
                'pending_too_long' => "Quotation from {$this->quotation->name} pending for " . now()->diffInDays($this->quotation->created_at) . " days",
                'client_approval_needed' => "Quotation approved but awaiting client response",
                'urgent_priority' => "High priority quotation requires immediate attention",
                default => "Quotation update for {$this->quotation->project_type}",
            },
        ];
    }
}