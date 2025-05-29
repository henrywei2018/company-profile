<?php

namespace App\Observers;

use App\Models\Testimonial;
use App\Services\NotificationService;

class TestimonialObserver
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Handle the Testimonial "created" event.
     */
    public function created(Testimonial $testimonial): void
    {
        // Notify admins about new testimonial for review
        $this->notificationService->send('testimonial.created', $testimonial);
    }

    /**
     * Handle the Testimonial "updated" event.
     */
    public function updated(Testimonial $testimonial): void
    {
        // Check if testimonial was approved
        if ($testimonial->isDirty('is_approved')) {
            $this->handleApprovalChange($testimonial);
        }

        // Check if testimonial was featured
        if ($testimonial->isDirty('is_featured')) {
            $this->handleFeaturedChange($testimonial);
        }

        // Check if status changed
        if ($testimonial->isDirty('status')) {
            $this->handleStatusChange($testimonial);
        }
    }

    /**
     * Handle testimonial approval changes
     */
    protected function handleApprovalChange(Testimonial $testimonial): void
    {
        if ($testimonial->is_approved) {
            // Testimonial was approved
            $this->notificationService->send('testimonial.approved', $testimonial);
            
            // Thank the client
            if ($testimonial->project && $testimonial->project->client) {
                $this->notificationService->send('testimonial.thank_you', $testimonial);
            }
        } else {
            // Testimonial was rejected (if applicable)
            $this->notificationService->send('testimonial.rejected', $testimonial);
        }
    }

    /**
     * Handle featured status changes
     */
    protected function handleFeaturedChange(Testimonial $testimonial): void
    {
        if ($testimonial->is_featured && $testimonial->is_approved) {
            // Testimonial was featured
            $this->notificationService->send('testimonial.featured', $testimonial);
            
            // Notify client about being featured
            if ($testimonial->project && $testimonial->project->client) {
                $this->notificationService->send('testimonial.client_featured', $testimonial);
            }
        }
    }

    /**
     * Handle status changes
     */
    protected function handleStatusChange(Testimonial $testimonial): void
    {
        $oldStatus = $testimonial->getOriginal('status');
        $newStatus = $testimonial->status;

        // Send status change notification based on new status
        switch ($newStatus) {
            case 'approved':
                $this->notificationService->send('testimonial.approved', $testimonial);
                break;
            case 'rejected':
                $this->notificationService->send('testimonial.rejected', $testimonial);
                break;
            case 'pending':
                if ($oldStatus !== 'pending') {
                    $this->notificationService->send('testimonial.pending_review', $testimonial);
                }
                break;
        }
    }
}