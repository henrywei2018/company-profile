<?php

namespace App\Notifications;

use App\Models\Quotation;

class QuotationStatusUpdatedNotification extends BaseNotification
{
    protected function configure(): void
    {
        $quotation = $this->data;
        
        if ($quotation instanceof Quotation) {
            $this->subject = "Quotation Status Updated: {$quotation->project_type}";
            $this->greeting = "Hello {$quotation->name}!";
            
            $this->addLine("Your quotation request status has been updated:");
            $this->addLine("Project: {$quotation->project_type}");
            $this->addLine("Status: " . ucfirst($quotation->status));
            
            $statusMessages = [
                'pending' => 'Your quotation is being reviewed by our team.',
                'reviewed' => 'Your quotation has been reviewed. We will send you a detailed quote soon.',
                'approved' => 'Great news! Your quotation has been approved.',
                'rejected' => 'Thank you for your interest. We cannot proceed with this quotation at this time.',
            ];
            
            if (isset($statusMessages[$quotation->status])) {
                $this->addLine($statusMessages[$quotation->status]);
            }
            
            $this->setAction('View Quotation', route('client.quotations.show', $quotation));
            $this->salutation = 'Best regards,<br>' . config('app.name') . ' Team';
        }
    }
}