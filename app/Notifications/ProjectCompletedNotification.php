<?php

namespace App\Notifications;

use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProjectCompletedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected Project $project;

    public function __construct(Project $project)
    {
        $this->project = $project;
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Project Completed: ' . $this->project->title)
            ->success()
            ->greeting('Congratulations!')
            ->line('🎉 Great news! Your project "' . $this->project->title . '" has been successfully completed.')
            ->line('**Project Details:**')
            ->line('• **Title:** ' . $this->project->title)
            ->line('• **Completion Date:** ' . ($this->project->actual_completion_date ? $this->project->actual_completion_date->format('M d, Y') : now()->format('M d, Y')))
            ->line('• **Duration:** ' . $this->getProjectDuration())
            ->line('We hope you are satisfied with the quality of our work.')
            ->action('View Project', route('client.projects.show', $this->project))
            ->line('**What\'s Next?**')
            ->line('• Review the completed project details')
            ->line('• Download any final documents or certificates')
            ->line('• Consider leaving a testimonial about your experience')
            ->line('Thank you for choosing our services!')
            ->salutation('With appreciation,<br>' . config('app.name') . ' Team');
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'project_completed',
            'project_id' => $this->project->id,
            'project_title' => $this->project->title,
            'client_name' => $this->project->client->name ?? 'Not assigned',
            'completion_date' => $this->project->actual_completion_date?->toDateString() ?? now()->toDateString(),
            'duration' => $this->getProjectDuration(),
            'title' => 'Project Completed',
            'message' => "🎉 Project \"{$this->project->title}\" has been completed successfully!",
            'priority' => 'normal',
        ];
    }

    protected function getProjectDuration(): string
    {
        if (!$this->project->start_date) {
            return 'Duration not available';
        }

        $endDate = $this->project->actual_completion_date ?? now();
        $days = $this->project->start_date->diffInDays($endDate);
        
        if ($days < 1) {
            return 'Completed in less than a day';
        } elseif ($days === 1) {
            return '1 day';
        } else {
            return $days . ' days';
        }
    }
}