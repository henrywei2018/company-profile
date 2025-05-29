<?php

namespace App\Notifications;

use App\Models\Quotation;
use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class QuotationConvertedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected Quotation $quotation;
    protected Project $project;

    public function __construct(Quotation $quotation, Project $project)
    {
        $this->quotation = $quotation;
        $this->project = $project;
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Project Started: ' . $this->project->title)
            ->success()
            ->greeting('Excellent News, ' . $this->quotation->name . '!')
            ->line('🚀 Your approved quotation has been converted into an active project!')
            ->line('**Project Information:**')
            ->line('• **Project Title:** ' . $this->project->title)
            ->line('• **Project ID:** #' . $this->project->id)
            ->line('• **Start Date:** ' . ($this->project->start_date ? $this->project->start_date->format('M d, Y') : 'To be scheduled'))
            ->line('• **Expected Completion:** ' . ($this->project->end_date ? $this->project->end_date->format('M d, Y') : 'To be determined'))
            ->line('• **Status:** ' . ucfirst(str_replace('_', ' ', $this->project->status)))
            ->line('**What Happens Next:**')
            ->line('• Our project manager will contact you within 24 hours')
            ->line('• You will receive a detailed project timeline')
            ->line('• Regular progress updates will be provided')
            ->line('• You can track project status in your client portal')
            ->action('View Project', route('client.projects.show', $this->project))
            ->line('Thank you for choosing our services!')
            ->salutation('Excited to work with you,<br>' . config('app.name') . ' Project Team');
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'quotation_converted',
            'quotation_id' => $this->quotation->id,
            'project_id' => $this->project->id,
            'project_title' => $this->project->title,
            'client_name' => $this->quotation->name,
            'start_date' => $this->project->start_date?->toDateString(),
            'end_date' => $this->project->end_date?->toDateString(),
            'status' => $this->project->status,
            'title' => 'Quotation Converted to Project',
            'message' => "Your quotation has been converted to project \"{$this->project->title}\"",
            'priority' => 'high',
        ];
    }
}