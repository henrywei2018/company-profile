<?php

namespace App\Notifications;

use App\Models\Project;

class ProjectDeadlineNotification extends BaseNotification
{
    protected function configure(): void
    {
        $project = $this->data;
        
        if ($project instanceof Project) {
            $daysUntilDeadline = $project->end_date ? now()->diffInDays($project->end_date, false) : 0;
            $isOverdue = $daysUntilDeadline < 0;
            
            if ($isOverdue) {
                $this->subject = "Project Overdue: {$project->title}";
                $this->greeting = "Urgent Notice!";
                $this->addLine("Your project '{$project->title}' is " . abs($daysUntilDeadline) . " day(s) overdue.");
                $this->addLine("Please contact us immediately to discuss the project status.");
            } else {
                $this->subject = "Project Deadline Approaching: {$project->title}";
                $this->greeting = "Reminder!";
                $this->addLine("Your project '{$project->title}' deadline is approaching.");
                $this->addLine("Days until deadline: {$daysUntilDeadline}");
                $this->addLine("Deadline: " . $project->end_date->format('M d, Y'));
            }
            
            $this->setAction('View Project', route('client.projects.show', $project));
            $this->salutation = 'Best regards,<br>' . config('app.name') . ' Team';
        }
    }
}