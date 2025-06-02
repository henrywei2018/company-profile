<?php

namespace App\Notifications;

use App\Models\Quotation;

class QuotationCreatedNotification extends BaseNotification
{
    protected function configure(): void
    {
        $quotation = $this->data;
        
        if ($quotation instanceof Quotation) {
            $this->subject = "New Quotation Request: {$quotation->project_type}";
            $this->greeting = "New Request!";
            
            $this->addLine("A new quotation request has been submitted:");
            $this->addLine("Client: {$quotation->name}");
            $this->addLine("Project: {$quotation->project_type}");
            $this->addLine("Service: " . ($quotation->service->title ?? 'General'));
            
            if ($quotation->budget_range) {
                $this->addLine("Budget: {$quotation->budget_range}");
            }
            
            $this->setAction('Review Quotation', route('admin.quotations.show', $quotation));
            $this->salutation = 'Best regards,<br>' . config('app.name') . ' Team';
        }
    }
}