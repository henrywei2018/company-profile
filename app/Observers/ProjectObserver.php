<?php
// File: app/Observers/ProjectObserver.php

namespace App\Observers;

use App\Models\Project;
use App\Traits\SendsNotifications;
use App\Facades\Notifications;
use Illuminate\Support\Facades\Log;

class ProjectObserver
{
    use SendsNotifications;

    /**
     * Handle the Project "created" event.
     */
    public function created(Project $project): void
    {
        try {
            Log::info('Project created, sending notifications', [
                'project_id' => $project->id,
                'title' => $project->title,
                'client_id' => $project->client_id
            ]);

            // Notify admins about new project
            $this->sendIfEnabled('project.created', $project);

            // Notify client if exists
            if ($project->client) {
                $this->notifyClient($project, 'project.created');
            }

        } catch (\Exception $e) {
            Log::error('Failed to send project created notifications', [
                'project_id' => $project->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle the Project "updated" event.
     */
    public function updated(Project $project): void
    {
        try {
            // Check if status changed
            if ($project->isDirty('status')) {
                $this->handleStatusChange($project);
            }

            // Check if end date changed (deadline change)
            if ($project->isDirty('end_date')) {
                $this->handleDeadlineChange($project);
            }

            // Send general update notification for important fields
            $importantFields = ['title', 'description', 'start_date', 'end_date', 'budget'];
            if ($project->isDirty($importantFields)) {
                $this->handleProjectUpdate($project);
            }

        } catch (\Exception $e) {
            Log::error('Failed to send project update notifications', [
                'project_id' => $project->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle project status changes
     */
    protected function handleStatusChange(Project $project): void
    {
        $oldStatus = $project->getOriginal('status');
        $newStatus = $project->status;

        Log::info('Project status changed', [
            'project_id' => $project->id,
            'old_status' => $oldStatus,
            'new_status' => $newStatus
        ]);

        // Send status change notification to client
        if ($project->client) {
            $this->notifyClient($project, 'project.status_changed');
        }

        // Handle specific status changes
        switch ($newStatus) {
            case 'completed':
                $this->handleProjectCompletion($project);
                break;
                
            case 'in_progress':
                if ($this->isOverdue($project)) {
                    $this->sendNotification('project.overdue', $project);
                }
                break;
                
            case 'on_hold':
                $this->sendNotification('project.on_hold', $project);
                break;
                
            case 'cancelled':
                $this->sendNotification('project.cancelled', $project);
                break;
        }

        // Notify admins about status change
        $this->notifyAdmins('project.status_changed', $project);
    }

    /**
     * Handle project completion
     */
    protected function handleProjectCompletion(Project $project): void
    {
        // Update completion date if not set
        if (!$project->actual_completion_date) {
            $project->update(['actual_completion_date' => now()]);
        }

        // Send completion notifications
        if ($project->client) {
            $this->notifyClient($project, 'project.completed');
        }
        
        $this->notifyAdmins('project.completed', $project);

        Log::info('Project completion notifications sent', [
            'project_id' => $project->id
        ]);
    }

    /**
     * Handle deadline changes
     */
    protected function handleDeadlineChange(Project $project): void
    {
        if (!$project->end_date) {
            return;
        }

        $daysUntilDeadline = now()->diffInDays($project->end_date, false);
        
        // Check for approaching deadline
        if ($daysUntilDeadline <= 7 && $daysUntilDeadline > 0 && $project->status === 'in_progress') {
            $this->sendNotification('project.deadline_approaching', $project);
        }
        
        // Check if now overdue
        if ($daysUntilDeadline < 0 && $project->status === 'in_progress') {
            $this->sendNotification('project.overdue', $project);
        }

        // Notify client about deadline change
        if ($project->client) {
            $this->notifyClient($project, 'project.deadline_changed');
        }
    }

    /**
     * Handle general project updates
     */
    protected function handleProjectUpdate(Project $project): void
    {
        // Get changed fields for context
        $changes = $this->getChangedFields($project, ['title', 'description', 'start_date', 'end_date', 'budget']);
        
        if (!empty($changes)) {
            // Notify client about updates
            if ($project->client) {
                $this->notifyClient($project, 'project.updated');
            }

            // Notify admins
            $this->notifyAdmins('project.updated', $project);

            Log::info('Project update notifications sent', [
                'project_id' => $project->id,
                'changes' => array_keys($changes)
            ]);
        }
    }

    /**
     * Check if project is overdue
     */
    protected function isOverdue(Project $project): bool
    {
        return $project->end_date && $project->end_date->isPast() && $project->status === 'in_progress';
    }

    /**
     * Get changed fields for notification context
     */
    protected function getChangedFields(Project $project, array $fields): array
    {
        $changes = [];
        foreach ($fields as $field) {
            if ($project->isDirty($field)) {
                $changes[$field] = [
                    'old' => $project->getOriginal($field),
                    'new' => $project->getAttribute($field)
                ];
            }
        }
        return $changes;
    }

    /**
     * Schedule deadline reminder notifications
     */
    public function scheduleDeadlineReminders(Project $project): void
    {
        if (!$project->end_date || $project->status !== 'in_progress') {
            return;
        }

        $reminderDays = [7, 3, 1]; // Days before deadline to send reminders
        
        foreach ($reminderDays as $days) {
            $reminderDate = $project->end_date->subDays($days);
            
            if ($reminderDate->isFuture()) {
                // Here you would schedule the notification
                // This could integrate with Laravel's job scheduling
                Log::info('Deadline reminder scheduled', [
                    'project_id' => $project->id,
                    'reminder_date' => $reminderDate->toDateString(),
                    'days_before' => $days
                ]);
            }
        }
    }

    /**
     * Check for overdue projects (could be called by scheduled job)
     */
    public static function checkOverdueProjects(): void
    {
        $overdueProjects = Project::where('status', 'in_progress')
            ->where('end_date', '<', now())
            ->whereNotNull('end_date')
            ->get();

        foreach ($overdueProjects as $project) {
            Notifications::send('project.overdue', $project);
        }

        Log::info('Overdue project check completed', [
            'overdue_count' => $overdueProjects->count()
        ]);
    }
}