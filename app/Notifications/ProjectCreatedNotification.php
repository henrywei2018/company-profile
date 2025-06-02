<?php

namespace App\Notifications;

use App\Models\Project;

class ProjectCreatedNotification extends BaseNotification
{
    protected function configure(): void
    {
        $project = $this->data;
        
        if ($project instanceof Project) {
            $this->subject = "New Project Created: {$project->title}";
            $this->greeting = "Hello!";
            
            $this->addLine("A new project has been created: {$project->title}");
            $this->addLine("Client: " . ($project->client->name ?? 'N/A'));
            $this->addLine("Status: " . ucfirst($project->status));
            
            if ($project->start_date) {
                $this->addLine("Start Date: " . $project->start_date->format('M d, Y'));
            }
            
            $this->setAction('View Project', route('admin.projects.show', $project));
            $this->salutation = 'Best regards,<br>' . config('app.name') . ' Team';
        }
    }
}