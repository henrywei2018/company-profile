<?php

namespace App\Notifications;

use App\Models\Quotation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class QuotationClientResponseNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected Quotation $quotation;
    protected string $responseType; // 'needed', 'reminder', 'overdue'

    public function __construct(Quotation $quotation, string $responseType = 'needed')
    {
        $this->quotation = $quotation;
        $this->responseType = $responseType;
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        $mail = new MailMessage;

        switch ($this->responseType) {
            case 'reminder':
                $mail->subject('Reminder: Response Needed - ' . $this->quotation->project_type)
                     ->error()
                     ->greeting('Friendly Reminder, ' . $this->quotation->name)
                     ->line('We sent you an approved quotation that requires your response.')
                     ->line('**Quotation Details:**')
                     ->line('• **Project:** ' . $this->quotation->project_type)
                     ->line('• **Approved:** ' . $this->quotation->approved_at?->format('M d, Y'))
                     ->line('• **Valid Until:** ' . $this->quotation->approved_at?->addDays(30)->format('M d, Y'))
                     ->line('Please review and let us know your decision at your earliest convenience.');
                break;

            case 'overdue':
                $mail->subject('Urgent: Response Overdue - ' . $this->quotation->project_type)
                     ->error()
                     ->greeting('Urgent Notice, ' . $this->quotation->name)
                     ->line('⚠️ Your response to our approved quotation is overdue.')
                     ->line('The quotation may expire soon if we don\'t hear from you.')
                     ->line('**Quotation expires in:** ' . $this->getExpiryDays() . ' days');
                break;

            default: // 'needed'
                $mail->subject('Response Needed: ' . $this->quotation->project_type)
                     ->greeting('Hello ' . $this->quotation->name)
                     ->line('Your quotation has been approved and requires your response.')
                     ->line('Please review the details and let us know if you would like to proceed.');
        }

        return $mail
            ->action('Review Quotation', route('client.quotations.show', $this->quotation))
            ->line('If you have any questions, please don\'t hesitate to contact us.')
            ->salutation('Best regards,<br>' . config('app.name') . ' Team');
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'quotation_client_response',
            'response_type' => $this->responseType,
            'quotation_id' => $this->quotation->id,
            'client_name' => $this->quotation->name,
            'project_type' => $this->quotation->project_type,
            'approved_at' => $this->quotation->approved_at?->toISOString(),
            'expires_in_days' => $this->getExpiryDays(),
            'title' => $this->getNotificationTitle(),
            'message' => $this->getNotificationMessage(),
            'priority' => $this->responseType === 'overdue' ? 'high' : 'medium',
        ];
    }

    protected function getExpiryDays(): int
    {
        if (!$this->quotation->approved_at) {
            return 0;
        }
        
        $expiryDate = $this->quotation->approved_at->addDays(30);
        return max(0, now()->diffInDays($expiryDate, false));
    }

    protected function getNotificationTitle(): string
    {
        return match($this->responseType) {
            'reminder' => 'Quotation Response Reminder',
            'overdue' => 'Quotation Response Overdue',
            default => 'Quotation Response Needed',
        };
    }

    protected function getNotificationMessage(): string
    {
        return match($this->responseType) {
            'reminder' => "Reminder: Your response is needed for \"{$this->quotation->project_type}\"",
            'overdue' => "Urgent: Response overdue for \"{$this->quotation->project_type}\"",
            default => "Your response is needed for \"{$this->quotation->project_type}\"",
        };
    }
}