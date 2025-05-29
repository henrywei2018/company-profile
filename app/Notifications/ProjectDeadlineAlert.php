<?php
// File: app/Notifications/ProjectDeadlineAlert.php

namespace App\Notifications;

use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProjectDeadlineAlert extends Notification implements ShouldQueue
{
    use Queueable;

    protected Project $project;
    protected int $daysUntilDeadline;
    protected bool $isOverdue;

    public function __construct(Project $project, int $daysUntilDeadline, bool $isOverdue = false)
    {
        $this->project = $project;
        $this->daysUntilDeadline = $daysUntilDeadline;
        $this->isOverdue = $isOverdue;
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        if ($this->isOverdue) {
            return (new MailMessage)
                ->subject('Project Overdue: ' . $this->project->title)
                ->error()
                ->greeting('Project Overdue Alert!')
                ->line('The project "' . $this->project->title . '" is overdue by ' . abs($this->daysUntilDeadline) . ' day(s).')
                ->line('**Client:** ' . ($this->project->client->name ?? 'Not assigned'))
                ->line('**Original Deadline:** ' . $this->project->end_date->format('M d, Y'))
                ->action('View Project', route('admin.projects.show', $this->project));
        }

        return (new MailMessage)
            ->subject('Project Deadline Alert: ' . $this->project->title)
            ->warning()
            ->greeting('Project Deadline Reminder!')
            ->line('The project "' . $this->project->title . '" has a deadline approaching in ' . $this->daysUntilDeadline . ' day(s).')
            ->line('**Client:** ' . ($this->project->client->name ?? 'Not assigned'))
            ->line('**Deadline:** ' . $this->project->end_date->format('M d, Y'))
            ->action('View Project', route('admin.projects.show', $this->project));
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => $this->isOverdue ? 'project_overdue' : 'project_deadline',
            'project_id' => $this->project->id,
            'project_title' => $this->project->title,
            'client_name' => $this->project->client->name ?? 'Not assigned',
            'days' => $this->daysUntilDeadline,
            'deadline' => $this->project->end_date->toDateString(),
            'is_overdue' => $this->isOverdue,
            'title' => $this->isOverdue ? 'Project Overdue' : 'Deadline Approaching',
            'message' => $this->isOverdue 
                ? "Project \"{$this->project->title}\" is overdue by " . abs($this->daysUntilDeadline) . " day(s)"
                : "Project \"{$this->project->title}\" deadline in {$this->daysUntilDeadline} day(s)",
        ];
    }
}