<?php

namespace App\Notifications;

use App\Models\Project;

class ProjectStatusChangedNotification extends BaseNotification
{
    protected function configure(): void
    {
        $project = $this->data;
        
        if ($project instanceof Project) {
            $this->subject = "Project Status Updated: {$project->title}";
            $this->greeting = "Hello!";
            
            $this->addLine("The status of your project '{$project->title}' has been updated.");
            $this->addLine("New Status: " . ucfirst(str_replace('_', ' ', $project->status)));
            
            if ($project->status === 'completed') {
                $this->addLine("🎉 Congratulations! Your project has been completed successfully.");
            } elseif ($project->status === 'in_progress') {
                $this->addLine("Your project is now in progress. Our team is working on it.");
            }
            
            $this->setAction('View Project', route('client.projects.show', $project));
            $this->salutation = 'Best regards,<br>' . config('app.name') . ' Team';
        }
    }
}