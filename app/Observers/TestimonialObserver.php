<?php
// File: app/Observers/TestimonialObserver.php

namespace App\Observers;

use App\Models\Testimonial;
use App\Traits\SendsNotifications;
use App\Facades\Notifications;
use Illuminate\Support\Facades\Log;

class TestimonialObserver
{
    use SendsNotifications;

    /**
     * Handle the Testimonial "created" event.
     */
    public function created(Testimonial $testimonial): void
    {
        try {
            Log::info('Testimonial created, sending notifications', [
                'testimonial_id' => $testimonial->id,
                'client_name' => $testimonial->client_name,
                'project_id' => $testimonial->project_id
            ]);

            // Notify admins about new testimonial for review
            $this->sendIfEnabled('testimonial.created', $testimonial);

            // Send thank you notification to client
            if ($testimonial->project && $testimonial->project->client) {
                $this->notifyClient($testimonial, 'testimonial.thank_you');
            }

        } catch (\Exception $e) {
            Log::error('Failed to send testimonial created notifications', [
                'testimonial_id' => $testimonial->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle the Testimonial "updated" event.
     */
    public function updated(Testimonial $testimonial): void
    {
        try {
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

            // Check if rating changed significantly
            if ($testimonial->isDirty('rating')) {
                $this->handleRatingChange($testimonial);
            }

        } catch (\Exception $e) {
            Log::error('Failed to send testimonial update notifications', [
                'testimonial_id' => $testimonial->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle testimonial approval changes
     */
    protected function handleApprovalChange(Testimonial $testimonial): void
    {
        if ($testimonial->is_approved) {
            // Testimonial was approved
            $this->handleTestimonialApproval($testimonial);
        } else {
            // Testimonial was rejected
            $this->handleTestimonialRejection($testimonial);
        }
    }

    /**
     * Handle testimonial approval
     */
    protected function handleTestimonialApproval(Testimonial $testimonial): void
    {
        // Update approval timestamp
        if (!$testimonial->approved_at) {
            $testimonial->update(['approved_at' => now()]);
        }

        // Notify client about approval
        if ($testimonial->project && $testimonial->project->client) {
            $this->notifyClient($testimonial, 'testimonial.approved');
        }

        // Notify admins
        $this->notifyAdmins('testimonial.approved', $testimonial);

        // Check if this is a high-rating testimonial for potential featuring
        if ($testimonial->rating >= 5) {
            $this->considerForFeaturing($testimonial);
        }

        Log::info('Testimonial approval notifications sent', [
            'testimonial_id' => $testimonial->id,
            'rating' => $testimonial->rating
        ]);
    }

    /**
     * Handle testimonial rejection
     */
    protected function handleTestimonialRejection(Testimonial $testimonial): void
    {
        // Notify client about rejection (if appropriate)
        if ($testimonial->project && $testimonial->project->client) {
            $this->notifyClient($testimonial, 'testimonial.rejected');
        }

        // Notify admins
        $this->notifyAdmins('testimonial.rejected', $testimonial);

        Log::info('Testimonial rejection notifications sent', [
            'testimonial_id' => $testimonial->id
        ]);
    }

    /**
     * Handle featured status changes
     */
    protected function handleFeaturedChange(Testimonial $testimonial): void
    {
        if ($testimonial->is_featured && $testimonial->is_approved) {
            // Testimonial was featured
            $this->handleTestimonialFeatured($testimonial);
        } elseif (!$testimonial->is_featured && $testimonial->getOriginal('is_featured')) {
            // Testimonial was unfeatured
            $this->handleTestimonialUnfeatured($testimonial);
        }
    }

    /**
     * Handle testimonial being featured
     */
    protected function handleTestimonialFeatured(Testimonial $testimonial): void
    {
        // Update featured timestamp
        if (!$testimonial->featured_at) {
            $testimonial->update(['featured_at' => now()]);
        }

        // Send featured notification to client
        if ($testimonial->project && $testimonial->project->client) {
            $this->notifyClient($testimonial, 'testimonial.featured');
        }

        // Notify admins
        $this->notifyAdmins('testimonial.featured', $testimonial);

        // Notify marketing team
        $this->notifyMarketingTeam($testimonial);

        Log::info('Testimonial featured notifications sent', [
            'testimonial_id' => $testimonial->id
        ]);
    }

    /**
     * Handle testimonial being unfeatured
     */
    protected function handleTestimonialUnfeatured(Testimonial $testimonial): void
    {
        // Notify admins about unfeaturing
        $this->notifyAdmins('testimonial.unfeatured', $testimonial);

        Log::info('Testimonial unfeatured notification sent', [
            'testimonial_id' => $testimonial->id
        ]);
    }

    /**
     * Handle status changes
     */
    protected function handleStatusChange(Testimonial $testimonial): void
    {
        $oldStatus = $testimonial->getOriginal('status');
        $newStatus = $testimonial->status;

        Log::info('Testimonial status changed', [
            'testimonial_id' => $testimonial->id,
            'old_status' => $oldStatus,
            'new_status' => $newStatus
        ]);

        // Send status change notification based on new status
        switch ($newStatus) {
            case 'approved':
                $this->handleTestimonialApproval($testimonial);
                break;
                
            case 'rejected':
                $this->handleTestimonialRejection($testimonial);
                break;
                
            case 'pending':
                if ($oldStatus !== 'pending') {
                    $this->sendIfEnabled('testimonial.pending_review', $testimonial);
                }
                break;
                
            case 'published':
                $this->handleTestimonialPublished($testimonial);
                break;
        }
    }

    /**
     * Handle testimonial being published
     */
    protected function handleTestimonialPublished(Testimonial $testimonial): void
    {
        // Notify client about publication
        if ($testimonial->project && $testimonial->project->client) {
            $this->notifyClient($testimonial, 'testimonial.published');
        }

        // Notify admins
        $this->notifyAdmins('testimonial.published', $testimonial);

        Log::info('Testimonial published notifications sent', [
            'testimonial_id' => $testimonial->id
        ]);
    }

    /**
     * Handle rating changes
     */
    protected function handleRatingChange(Testimonial $testimonial): void
    {
        $oldRating = $testimonial->getOriginal('rating');
        $newRating = $testimonial->rating;

        // If rating improved significantly, consider for featuring
        if ($newRating >= 5 && $oldRating < 5) {
            $this->considerForFeaturing($testimonial);
        }

        // If rating dropped significantly, notify admins
        if ($newRating <= 3 && $oldRating > 3) {
            $this->notifyAdmins('testimonial.low_rating', $testimonial);
        }

        Log::info('Testimonial rating changed', [
            'testimonial_id' => $testimonial->id,
            'old_rating' => $oldRating,
            'new_rating' => $newRating
        ]);
    }

    /**
     * Consider testimonial for featuring
     */
    protected function considerForFeaturing(Testimonial $testimonial): void
    {
        if ($testimonial->rating >= 5 && $testimonial->is_approved && !$testimonial->is_featured) {
            // Notify admins to consider featuring
            $this->notifyAdmins('testimonial.consider_featuring', $testimonial);
            
            Log::info('Testimonial considered for featuring', [
                'testimonial_id' => $testimonial->id,
                'rating' => $testimonial->rating
            ]);
        }
    }

    /**
     * Notify marketing team about featured testimonials
     */
    protected function notifyMarketingTeam(Testimonial $testimonial): void
    {
        try {
            // Send to marketing team for social media, website updates, etc.
            $this->sendNotification('testimonial.marketing_opportunity', $testimonial);
            
            Log::info('Marketing team notified about featured testimonial', [
                'testimonial_id' => $testimonial->id
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to notify marketing team', [
                'testimonial_id' => $testimonial->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Override notifyClient to handle testimonial-specific client notifications
     */
    protected function notifyClient($testimonial, string $type): bool
    {
        if ($testimonial->project && $testimonial->project->client) {
            // Use project client
            return $this->sendNotification($type, $testimonial, $testimonial->project->client);
        } elseif ($testimonial->client_email) {
            // Use email address
            return $this->sendNotification($type, $testimonial, \App\Services\TempNotifiable::forMessage(
                    $testimonial->client_email,
                    $testimonial->client_name ?? 'Client'
                ));    
        }

        Log::warning('Cannot notify client - no contact info found', [
            'testimonial_id' => $testimonial->id
        ]);

        return false;
    }

    /**
     * Check for testimonials needing follow-up (called by scheduled job)
     */
    public static function checkTestimonialFollowups(): void
    {
        // Find projects completed but no testimonial yet
        $projectsNeedingTestimonials = \App\Models\Project::where('status', 'completed')
            ->where('actual_completion_date', '>', now()->subDays(30))
            ->whereDoesntHave('testimonial')
            ->with('client')
            ->get();

        foreach ($projectsNeedingTestimonials as $project) {
            if ($project->client) {
                Notifications::send('project.request_testimonial', $project, $project->client);
            }
        }

        Log::info('Testimonial follow-up check completed', [
            'projects_count' => $projectsNeedingTestimonials->count()
        ]);
    }

    /**
     * Check for low-rated testimonials needing attention
     */
    public static function checkLowRatedTestimonials(): void
    {
        $lowRatedTestimonials = Testimonial::where('rating', '<=', 3)
            ->where('created_at', '>', now()->subDays(7))
            ->where('admin_notified', false)
            ->get();

        foreach ($lowRatedTestimonials as $testimonial) {
            Notifications::send('testimonial.low_rating_alert', $testimonial);
            $testimonial->update(['admin_notified' => true]);
        }

        Log::info('Low-rated testimonials check completed', [
            'low_rated_count' => $lowRatedTestimonials->count()
        ]);
    }

    /**
     * Send monthly testimonial summary
     */
    public static function sendMonthlyTestimonialSummary(): void
    {
        $summary = [
            'total_this_month' => Testimonial::whereMonth('created_at', now()->month)->count(),
            'approved_this_month' => Testimonial::whereMonth('created_at', now()->month)
                ->where('is_approved', true)->count(),
            'featured_this_month' => Testimonial::whereMonth('created_at', now()->month)
                ->where('is_featured', true)->count(),
            'average_rating' => Testimonial::whereMonth('created_at', now()->month)
                ->avg('rating'),
            'pending_approval' => Testimonial::where('status', 'pending')->count()
        ];

        Notifications::send('testimonial.monthly_summary', $summary);

        Log::info('Monthly testimonial summary sent', $summary);
    }
}