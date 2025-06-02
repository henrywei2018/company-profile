<?php

namespace App\Notifications;

use App\Models\Testimonial;

class TestimonialApprovedNotification extends BaseNotification
{
    protected function configure(): void
    {
        $testimonial = $this->data;
        
        if ($testimonial instanceof Testimonial) {
            $this->subject = "Your Testimonial Has Been Approved!";
            $this->greeting = "Thank you {$testimonial->client_name}!";
            
            $this->addLine("Your testimonial for the project '{$testimonial->project->title}' has been approved and published.");
            $this->addLine("We truly appreciate you taking the time to share your experience.");
            $this->addLine("Your feedback helps us improve our services and helps other clients make informed decisions.");
            
            if ($testimonial->featured) {
                $this->addLine("🌟 Your testimonial has also been featured on our website!");
            }
            
            $this->setAction('View Your Testimonial', route('testimonials.show', $testimonial));
            $this->salutation = 'With gratitude,<br>' . config('app.name') . ' Team';
        }
    }
}