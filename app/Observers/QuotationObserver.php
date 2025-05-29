<?php

namespace App\Observers;

use App\Models\Quotation;
use App\Services\NotificationService;

class QuotationObserver
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Handle the Quotation "created" event.
     */
    public function created(Quotation $quotation): void
    {
        // Notify admins about new quotation request
        $this->notificationService->send('quotation.created', $quotation);
        
        // Send confirmation to client
        $this->notificationService->send('quotation.confirmation', $quotation);
    }

    /**
     * Handle the Quotation "updated" event.
     */
    public function updated(Quotation $quotation): void
    {
        // Check if status changed
        if ($quotation->isDirty('status')) {
            $this->handleStatusChange($quotation);
        }

        // Check if client approved/rejected
        if ($quotation->isDirty('client_approved')) {
            $this->handleClientResponse($quotation);
        }

        // Check if priority changed
        if ($quotation->isDirty('priority')) {
            $this->handlePriorityChange($quotation);
        }
    }

    /**
     * Handle quotation status changes
     */
    protected function handleStatusChange(Quotation $quotation): void
    {
        $oldStatus = $quotation->getOriginal('status');
        $newStatus = $quotation->status;

        // Send status update notification to client
        $this->notificationService->send('quotation.status_updated', $quotation);

        // Handle specific status changes
        switch ($newStatus) {
            case 'approved':
                $this->notificationService->send('quotation.approved', $quotation);
                break;
            case 'rejected':
                $this->notificationService->send('quotation.rejected', $quotation);
                break;
            case 'reviewed':
                $this->notificationService->send('quotation.reviewed', $quotation);
                break;
        }

        // Check if quotation needs client response
        if ($newStatus === 'approved' && !$quotation->client_approved) {
            $this->notificationService->send('quotation.client_response_needed', $quotation);
        }
    }

    /**
     * Handle client response to quotation
     */
    protected function handleClientResponse(Quotation $quotation): void
    {
        if ($quotation->client_approved === true) {
            // Client accepted quotation
            $this->notificationService->send('quotation.client_accepted', $quotation);
        } elseif ($quotation->client_approved === false) {
            // Client rejected quotation
            $this->notificationService->send('quotation.client_rejected', $quotation);
        }
    }

    /**
     * Handle priority changes
     */
    protected function handlePriorityChange(Quotation $quotation): void
    {
        $oldPriority = $quotation->getOriginal('priority');
        $newPriority = $quotation->priority;

        // If changed to urgent, notify immediately
        if ($newPriority === 'urgent' && $oldPriority !== 'urgent') {
            $this->notificationService->send('quotation.urgent', $quotation);
        }
    }
}