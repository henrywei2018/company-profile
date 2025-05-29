<?php
// File: app/Observers/ProjectObserver.php

namespace App\Observers;

use App\Models\Project;
use App\Services\NotificationService;

class ProjectObserver
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Handle the Project "created" event.
     */
    public function created(Project $project): void
    {
        // Notify admins and client about new project
        $this->notificationService->send('project.created', $project);
    }

    /**
     * Handle the Project "updated" event.
     */
    public function updated(Project $project): void
    {
        // Check if status changed
        if ($project->isDirty('status')) {
            $this->handleStatusChange($project);
        }

        // Check if end date changed
        if ($project->isDirty('end_date')) {
            $this->checkDeadlineAlert($project);
        }

        // Send general update notification if important fields changed
        $importantFields = ['title', 'description', 'status', 'start_date', 'end_date', 'budget'];
        if ($project->isDirty($importantFields)) {
            $changes = $this->getChangedFields($project, $importantFields);
            $this->notificationService->send('project.updated', $project);
        }
    }

    /**
     * Handle project status changes
     */
    protected function handleStatusChange(Project $project): void
    {
        $oldStatus = $project->getOriginal('status');
        $newStatus = $project->status;

        // Send status change notification
        $this->notificationService->send('project.status_changed', $project);

        // Send completion notification
        if ($newStatus === 'completed') {
            $this->notificationService->send('project.completed', $project);
        }

        // Check for overdue status
        if ($newStatus === 'in_progress' && $this->isOverdue($project)) {
            $this->notificationService->send('project.overdue', $project);
        }
    }

    /**
     * Check if project needs deadline alert
     */
    protected function checkDeadlineAlert(Project $project): void
    {
        if (!$project->end_date || $project->status !== 'in_progress') {
            return;
        }

        $daysUntilDeadline = now()->diffInDays($project->end_date, false);
        
        // Alert if deadline is within 7 days
        if ($daysUntilDeadline <= 7 && $daysUntilDeadline > 0) {
            $this->notificationService->send('project.deadline_approaching', $project);
        }
        
        // Alert if overdue
        if ($daysUntilDeadline < 0) {
            $this->notificationService->send('project.overdue', $project);
        }
    }

    /**
     * Check if project is overdue
     */
    protected function isOverdue(Project $project): bool
    {
        return $project->end_date && $project->end_date->isPast();
    }

    /**
     * Get changed fields for notification
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
}