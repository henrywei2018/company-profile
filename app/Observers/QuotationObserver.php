<?php
// File: app/Observers/QuotationObserver.php

namespace App\Observers;

use App\Models\Quotation;
use App\Traits\SendsNotifications;
use App\Facades\Notifications;
use Illuminate\Support\Facades\Log;

class QuotationObserver
{
    use SendsNotifications;

    /**
     * Handle the Quotation "created" event.
     */
    public function created(Quotation $quotation): void
    {
        try {
            Log::info('Quotation created, sending notifications', [
                'quotation_id' => $quotation->id,
                'project_type' => $quotation->project_type,
                'client_email' => $quotation->email
            ]);

            // Notify admins about new quotation request
            $this->sendIfEnabled('quotation.created', $quotation);
            
            // Send confirmation to client (registered or temp)
            $this->notifyClient($quotation, 'quotation.confirmation', $quotation->email, $quotation->name);

        } catch (\Exception $e) {
            Log::error('Failed to send quotation created notifications', [
                'quotation_id' => $quotation->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle the Quotation "updated" event.
     */
    public function updated(Quotation $quotation): void
    {
        try {
            // Check if status changed
            if ($quotation->isDirty('status')) {
                $this->handleStatusChange($quotation);
            }

            // Check if client response changed
            if ($quotation->isDirty('client_approved')) {
                $this->handleClientResponse($quotation);
            }

            // Check if priority changed
            if ($quotation->isDirty('priority')) {
                $this->handlePriorityChange($quotation);
            }

            // Check if converted to project
            if ($quotation->isDirty('project_id') && $quotation->project_id) {
                $this->handleQuotationConversion($quotation);
            }

        } catch (\Exception $e) {
            Log::error('Failed to send quotation update notifications', [
                'quotation_id' => $quotation->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle quotation status changes
     */
    protected function handleStatusChange(Quotation $quotation): void
    {
        $oldStatus = $quotation->getOriginal('status');
        $newStatus = $quotation->status;

        Log::info('Quotation status changed', [
            'quotation_id' => $quotation->id,
            'old_status' => $oldStatus,
            'new_status' => $newStatus
        ]);

        // Send status update notification to client
        $this->notifyClient($quotation, 'quotation.status_updated', $quotation->email, $quotation->name);

        // Handle specific status changes
        switch ($newStatus) {
            case 'approved':
                $this->handleQuotationApproval($quotation);
                break;
                
            case 'rejected':
                $this->handleQuotationRejection($quotation);
                break;
                
            case 'reviewed':
                $this->sendIfEnabled('quotation.reviewed', $quotation);
                break;
                
            case 'sent':
                $this->handleQuotationSent($quotation);
                break;
                
            case 'expired':
                $this->handleQuotationExpiry($quotation);
                break;
        }

        // Notify admins about status change
        $this->notifyAdmins('quotation.status_updated', $quotation);
    }

    /**
     * Handle quotation approval
     */
    protected function handleQuotationApproval(Quotation $quotation): void
    {
        // Update approval timestamp
        if (!$quotation->approved_at) {
            $quotation->update(['approved_at' => now()]);
        }

        // Send approval notification to client
        $this->notifyClient($quotation, 'quotation.approved', $quotation->email, $quotation->name);

        // Send notification to admins
        $this->notifyAdmins('quotation.approved', $quotation);

        // Check if client response is needed
        if (is_null($quotation->client_approved)) {
            $this->scheduleClientResponseReminder($quotation);
        }

        Log::info('Quotation approval notifications sent', [
            'quotation_id' => $quotation->id
        ]);
    }

    /**
     * Handle quotation rejection
     */
    protected function handleQuotationRejection(Quotation $quotation): void
    {
        // Send rejection notification to client
        $this->notifyClient($quotation, 'quotation.rejected', $quotation->email, $quotation->name);

        // Notify admins
        $this->notifyAdmins('quotation.rejected', $quotation);

        Log::info('Quotation rejection notifications sent', [
            'quotation_id' => $quotation->id
        ]);
    }

    /**
     * Handle quotation sent to client
     */
    protected function handleQuotationSent(Quotation $quotation): void
    {
        // Update sent timestamp
        if (!$quotation->sent_at) {
            $quotation->update(['sent_at' => now()]);
        }

        // Send notification to client
        $this->notifyClient($quotation, 'quotation.sent', $quotation->email, $quotation->name);

        Log::info('Quotation sent notification triggered', [
            'quotation_id' => $quotation->id
        ]);
    }

    /**
     * Handle quotation expiry
     */
    protected function handleQuotationExpiry(Quotation $quotation): void
    {
        // Send expiry notification to client
        $this->notifyClient($quotation, 'quotation.expired', $quotation->email, $quotation->name);

        // Notify admins
        $this->notifyAdmins('quotation.expired', $quotation);

        Log::info('Quotation expiry notifications sent', [
            'quotation_id' => $quotation->id
        ]);
    }

    /**
     * Handle quotation conversion to project
     */
    protected function handleQuotationConversion(Quotation $quotation): void
    {
        if ($quotation->project) {
            // Send conversion notification to client
            $this->notifyClient($quotation, 'quotation.converted', $quotation->email, $quotation->name);

            // Notify admins
            $this->notifyAdmins('quotation.converted', $quotation);

            Log::info('Quotation conversion notifications sent', [
                'quotation_id' => $quotation->id,
                'project_id' => $quotation->project_id
            ]);
        }
    }

    /**
     * Handle client response to quotation
     */
    protected function handleClientResponse(Quotation $quotation): void
    {
        if ($quotation->client_approved === true) {
            // Client accepted quotation
            $this->notifyAdmins('quotation.client_accepted', $quotation);
            
            Log::info('Client accepted quotation', [
                'quotation_id' => $quotation->id
            ]);
            
        } elseif ($quotation->client_approved === false) {
            // Client rejected quotation
            $this->notifyAdmins('quotation.client_rejected', $quotation);
            
            Log::info('Client rejected quotation', [
                'quotation_id' => $quotation->id
            ]);
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
            $this->sendNotification('quotation.urgent', $quotation);
            
            Log::info('Quotation marked as urgent', [
                'quotation_id' => $quotation->id
            ]);
        }
    }

    /**
     * Schedule client response reminder
     */
    protected function scheduleClientResponseReminder(Quotation $quotation): void
    {
        // Schedule reminders for client response
        $reminderDays = [7, 14, 21]; // Days after approval to send reminders
        
        foreach ($reminderDays as $days) {
            $reminderDate = now()->addDays($days);
            
            // Here you would schedule the notification job
            Log::info('Client response reminder scheduled', [
                'quotation_id' => $quotation->id,
                'reminder_date' => $reminderDate->toDateString(),
                'days_after_approval' => $days
            ]);
        }
    }

    /**
     * Check for expired quotations (called by scheduled job)
     */
    public static function checkExpiredQuotations(): void
    {
        $expiredQuotations = Quotation::where('status', 'approved')
            ->where('approved_at', '<', now()->subDays(30))
            ->whereNull('client_approved')
            ->get();

        foreach ($expiredQuotations as $quotation) {
            $quotation->update(['status' => 'expired']);
            // Observer will handle the notification
        }

        Log::info('Expired quotations check completed', [
            'expired_count' => $expiredQuotations->count()
        ]);
    }

    /**
     * Check for quotations needing client response
     */
    public static function checkPendingClientResponses(): void
    {
        $pendingQuotations = Quotation::where('status', 'approved')
            ->whereNull('client_approved')
            ->where('approved_at', '<', now()->subDays(7))
            ->get();

        foreach ($pendingQuotations as $quotation) {
            $daysWaiting = $quotation->approved_at->diffInDays(now());
            
            if ($daysWaiting >= 21) {
                // Overdue response
                Notifications::send('quotation.client_response_overdue', $quotation);
            } elseif ($daysWaiting >= 14) {
                // Reminder
                Notifications::send('quotation.client_response_reminder', $quotation);
            } elseif ($daysWaiting >= 7) {
                // Initial reminder
                Notifications::send('quotation.client_response_needed', $quotation);
            }
        }

        Log::info('Pending client responses check completed', [
            'pending_count' => $pendingQuotations->count()
        ]);
    }
}