<?php

namespace App\Notifications;

use App\Models\Testimonial;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TestimonialFeaturedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected Testimonial $testimonial;

    public function __construct(Testimonial $testimonial)
    {
        $this->testimonial = $testimonial;
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your Testimonial Has Been Featured!')
            ->success()
            ->greeting('Congratulations, ' . $this->testimonial->client_name . '!')
            ->line('🌟 Great news! Your testimonial has been selected as a featured testimonial on our website.')
            ->line('**Testimonial Details:**')
            ->line('• **Project:** ' . ($this->testimonial->project->title ?? 'N/A'))
            ->line('• **Rating:** ' . $this->testimonial->rating . '/5 stars')
            ->line('• **Featured Date:** ' . now()->format('M d, Y'))
            ->line('**Why Your Testimonial Was Featured:**')
            ->line('Your testimonial provides valuable insights for potential clients and showcases the quality of our work.')
            ->line('**Benefits of Being Featured:**')
            ->line('• Increased visibility on our website')
            ->line('• Helping other clients make informed decisions')
            ->line('• Recognition as a valued client')
            ->line('Thank you for taking the time to share your experience with us!')
            ->action('View Featured Testimonials', route('home') . '#testimonials')
            ->line('We truly appreciate your feedback and continued trust in our services.')
            ->salutation('With gratitude,<br>' . config('app.name') . ' Team');
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'testimonial_featured',
            'testimonial_id' => $this->testimonial->id,
            'client_name' => $this->testimonial->client_name,
            'project_title' => $this->testimonial->project->title ?? 'N/A',
            'rating' => $this->testimonial->rating,
            'featured_at' => now()->toISOString(),
            'testimonial_content' => \Illuminate\Support\Str::limit($this->testimonial->content, 100),
            'title' => 'Testimonial Featured',
            'message' => 'Your testimonial has been featured on our website! 🌟',
            'priority' => 'normal',
        ];
    }
}