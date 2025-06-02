<?php

namespace App\Notifications;

use App\Models\Testimonial;

class TestimonialCreatedNotification extends BaseNotification
{
    protected function configure(): void
    {
        $testimonial = $this->data;
        
        if ($testimonial instanceof Testimonial) {
            $this->subject = "New Testimonial Submitted";
            $this->greeting = "New Testimonial!";
            
            $this->addLine("A new testimonial has been submitted for review:");
            $this->addLine("Client: {$testimonial->client_name}");
            $this->addLine("Project: " . ($testimonial->project->title ?? 'N/A'));
            $this->addLine("Rating: {$testimonial->rating}/5 stars");
            
            $this->addLine("Please review and approve this testimonial.");
            
            $this->setAction('Review Testimonial', route('admin.testimonials.show', $testimonial));
            $this->salutation = 'Best regards,<br>' . config('app.name') . ' System';
        }
    }
}