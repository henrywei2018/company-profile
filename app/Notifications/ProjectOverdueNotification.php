<?php

namespace App\Notifications;

use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProjectOverdueNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected Project $project;
    protected int $daysOverdue;

    public function __construct(Project $project, int $daysOverdue = null)
    {
        $this->project = $project;
        $this->daysOverdue = $daysOverdue ?? ($this->project->end_date ? now()->diffInDays($this->project->end_date) : 0);
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Project Overdue: ' . $this->project->title)
            ->error()
            ->greeting('Urgent Notice!')
            ->line('The project "' . $this->project->title . '" is overdue by ' . $this->daysOverdue . ' day(s).')
            ->line('**Client:** ' . ($this->project->client->name ?? 'Not assigned'))
            ->line('**Original Deadline:** ' . ($this->project->end_date ? $this->project->end_date->format('M d, Y') : 'Not set'))
            ->line('**Current Status:** ' . ucfirst(str_replace('_', ' ', $this->project->status)))
            ->line('Please take immediate action to address this overdue project.')
            ->action('View Project', route('admin.projects.show', $this->project))
            ->line('Contact the project team immediately to resolve this issue.')
            ->salutation('Urgent Alert,<br>' . config('app.name') . ' System');
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'project_overdue',
            'project_id' => $this->project->id,
            'project_title' => $this->project->title,
            'client_name' => $this->project->client->name ?? 'Not assigned',
            'days_overdue' => $this->daysOverdue,
            'deadline' => $this->project->end_date?->toDateString(),
            'status' => $this->project->status,
            'title' => 'Project Overdue',
            'message' => "Project \"{$this->project->title}\" is overdue by {$this->daysOverdue} day(s)",
            'priority' => 'high',
        ];
    }
}