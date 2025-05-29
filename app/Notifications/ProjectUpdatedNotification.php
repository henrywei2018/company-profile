<?php
// File: app/Notifications/ProjectUpdatedNotification.php

namespace App\Notifications;

use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProjectUpdatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected Project $project;
    protected array $changes;

    public function __construct(Project $project, array $changes = [])
    {
        $this->project = $project;
        $this->changes = $changes;
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject('Project Updated: ' . $this->project->title)
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('Your project "' . $this->project->title . '" has been updated.');

        if (!empty($this->changes)) {
            $mail->line('**Changes made:**');
            foreach ($this->changes as $field => $change) {
                if (is_array($change) && isset($change['old'], $change['new'])) {
                    $mail->line("• " . ucfirst(str_replace('_', ' ', $field)) . ": {$change['old']} → {$change['new']}");
                }
            }
        }

        return $mail
            ->line('**Current Status:** ' . ucfirst(str_replace('_', ' ', $this->project->status)))
            ->action('View Project', route('client.projects.show', $this->project))
            ->line('Thank you for using our services!')
            ->salutation('Best regards,<br>' . config('app.name') . ' Team');
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'project_updated',
            'project_id' => $this->project->id,
            'project_title' => $this->project->title,
            'client_name' => $this->project->client->name ?? 'Not assigned',
            'status' => $this->project->status,
            'changes' => $this->changes,
            'title' => 'Project Updated',
            'message' => "Project \"{$this->project->title}\" has been updated",
        ];
    }
}