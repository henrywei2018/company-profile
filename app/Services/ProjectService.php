<?php

namespace App\Services;

use App\Models\Project;
use App\Models\ProjectImage;
use App\Repositories\Interfaces\ProjectRepositoryInterface;
use App\Services\FileUploadService;
use App\Facades\Notifications;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ProjectService
{
    protected $projectRepository;
    protected $fileUploadService;
    
    public function __construct(
        ProjectRepositoryInterface $projectRepository,
        FileUploadService $fileUploadService
    ) {
        $this->projectRepository = $projectRepository;
        $this->fileUploadService = $fileUploadService;
    }
    
    public function createProject(array $data, array $images = [], array $altTexts = []): Project
    {
        // Create the project
        $project = $this->projectRepository->create($data);
        
        // Process images
        if (!empty($images)) {
            $this->processProjectImages($project, $images, $altTexts);
        }
        
        // Process SEO data if available
        if (isset($data['seo_title']) || isset($data['seo_description']) || isset($data['seo_keywords'])) {
            $this->processSeoData($project, $data);
        }

        // Send notifications
        $this->sendProjectNotifications('project.created', $project);
        
        return $project;
    }
    
    public function updateProject(
        Project $project,
        array $data,
        array $existingImages = [],
        array $existingAltTexts = [],
        array $newImages = [],
        array $newAltTexts = [],
        ?int $featuredImageId = null
    ): Project {
        $oldStatus = $project->status;
        
        // Update project data
        $project = $this->projectRepository->update($project, $data);
        
        // Process existing images
        $this->updateExistingImages($project, $existingImages, $existingAltTexts, $featuredImageId);
        
        // Process new images
        if (!empty($newImages)) {
            $this->processProjectImages($project, $newImages, $newAltTexts);
        }
        
        // Process SEO data
        $this->processSeoData($project, $data);

        // Send notifications based on what changed
        if ($oldStatus !== $project->status) {
            $this->sendProjectNotifications('project.status_changed', $project);
        } else {
            $this->sendProjectNotifications('project.updated', $project);
        }

        // Check for deadline notifications
        $this->checkProjectDeadlines($project);
        
        return $project;
    }
    
    public function deleteProject(Project $project): bool
    {
        // Send notification before deletion
        $this->sendProjectNotifications('project.deleted', $project);

        // Delete associated images
        foreach ($project->images as $image) {
            Storage::disk('public')->delete($image->image_path);
            $image->delete();
        }
        
        // Delete project
        return $this->projectRepository->delete($project);
    }
    
    public function toggleFeatured(Project $project): Project
    {
        $wasFeatured = $project->featured;
        
        $project->update([
            'featured' => !$project->featured
        ]);

        // Send notification for featured status change
        $notificationType = $project->featured ? 'project.featured' : 'project.unfeatured';
        $this->sendProjectNotifications($notificationType, $project);
        
        return $project;
    }

    public function changeStatus(Project $project, string $newStatus, ?string $notes = null): Project
    {
        $oldStatus = $project->status;
        
        $updateData = ['status' => $newStatus];
        
        // Add completion date if project is completed
        if ($newStatus === 'completed' && $oldStatus !== 'completed') {
            $updateData['actual_completion_date'] = now();
        }

        // Add notes if provided
        if ($notes) {
            $updateData['status_notes'] = $notes;
        }

        $project->update($updateData);

        // Send specific notifications based on status
        switch ($newStatus) {
            case 'completed':
                $this->sendProjectNotifications('project.completed', $project);
                break;
            case 'cancelled':
                $this->sendProjectNotifications('project.cancelled', $project);
                break;
            case 'on_hold':
                $this->sendProjectNotifications('project.on_hold', $project);
                break;
            default:
                $this->sendProjectNotifications('project.status_changed', $project);
        }

        return $project;
    }

    public function updateProgress(Project $project, int $percentage): Project
    {
        $oldPercentage = $project->progress_percentage ?? 0;
        
        $project->update(['progress_percentage' => $percentage]);

        // Send notification for significant progress milestones
        if ($this->isSignificantProgress($oldPercentage, $percentage)) {
            $this->sendProjectNotifications('project.progress_updated', $project);
        }

        return $project;
    }

    public function addMilestone(Project $project, array $milestoneData): Project
    {
        $project->milestones()->create($milestoneData);

        // Send notification about new milestone
        $this->sendProjectNotifications('project.milestone_added', $project);

        return $project;
    }

    public function bulkUpdateStatus(array $projectIds, string $status): int
    {
        $updated = 0;
        
        foreach ($projectIds as $id) {
            $project = Project::find($id);
            if ($project instanceof Project) {
                $this->changeStatus($project, $status);
                $updated++;
            }
        }

        // Send bulk notification
        if ($updated > 0) {
            Notifications::send('project.bulk_status_updated', [
                'count' => $updated,
                'status' => $status
            ]);
        }

        return $updated;
    }

    public function getOverdueProjects(): \Illuminate\Database\Eloquent\Collection
    {
        return Project::where('status', 'in_progress')
            ->where('end_date', '<', now())
            ->whereNotNull('end_date')
            ->with(['client', 'category'])
            ->get();
    }

    public function sendOverdueNotifications(): int
    {
        $overdueProjects = $this->getOverdueProjects();
        $sent = 0;

        foreach ($overdueProjects as $project) {
            // Only send notification if not already sent today
            if (!$project->overdue_notification_sent_at || 
                $project->overdue_notification_sent_at->isToday() === false) {
                
                $this->sendProjectNotifications('project.overdue', $project);
                
                $project->update(['overdue_notification_sent_at' => now()]);
                $sent++;
            }
        }

        return $sent;
    }

    public function sendDeadlineReminders(): int
    {
        $upcomingProjects = Project::where('status', 'in_progress')
            ->whereBetween('end_date', [now(), now()->addDays(7)])
            ->whereNotNull('end_date')
            ->with(['client', 'category'])
            ->get();

        $sent = 0;

        foreach ($upcomingProjects as $project) {
            $daysUntilDeadline = now()->diffInDays($project->end_date, false);
            
            // Send reminders at 7, 3, and 1 day(s) before deadline
            if (in_array($daysUntilDeadline, [7, 3, 1])) {
                // Check if we already sent a reminder for this specific day
                $lastSent = $project->deadline_notification_sent_at;
                $shouldSend = !$lastSent || 
                             $lastSent->diffInDays(now()) >= 1;

                if ($shouldSend) {
                    $this->sendProjectNotifications('project.deadline_approaching', $project);
                    $project->update(['deadline_notification_sent_at' => now()]);
                    $sent++;
                }
            }
        }

        return $sent;
    }

    public function getProjectStatistics(): array
    {
        return [
            'total' => Project::count(),
            'active' => Project::whereIn('status', ['in_progress', 'on_hold'])->count(),
            'completed' => Project::where('status', 'completed')->count(),
            'overdue' => $this->getOverdueProjects()->count(),
            'upcoming_deadlines' => Project::where('status', 'in_progress')
                ->whereBetween('end_date', [now(), now()->addDays(7)])
                ->count(),
            'by_status' => Project::selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray(),
            'completion_rate' => $this->calculateCompletionRate(),
            'average_duration' => $this->calculateAverageDuration(),
        ];
    }

    protected function sendProjectNotifications(string $type, Project $project): void
    {
        try {
            Notifications::send($type, $project);
        } catch (\Exception $e) {
            \Log::error("Failed to send project notification: {$type}", [
                'project_id' => $project->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    protected function checkProjectDeadlines(Project $project): void
    {
        if ($project->status === 'in_progress' && $project->end_date) {
            $daysUntilDeadline = now()->diffInDays($project->end_date, false);
            
            // Send approaching deadline notification
            if ($daysUntilDeadline <= 7 && $daysUntilDeadline > 0) {
                $lastSent = $project->deadline_notification_sent_at;
                $shouldSend = !$lastSent || $lastSent->diffInDays(now()) >= 1;
                
                if ($shouldSend) {
                    $this->sendProjectNotifications('project.deadline_approaching', $project);
                    $project->update(['deadline_notification_sent_at' => now()]);
                }
            }
            
            // Send overdue notification
            if ($daysUntilDeadline < 0) {
                $lastSent = $project->overdue_notification_sent_at;
                $shouldSend = !$lastSent || !$lastSent->isToday();
                
                if ($shouldSend) {
                    $this->sendProjectNotifications('project.overdue', $project);
                    $project->update(['overdue_notification_sent_at' => now()]);
                }
            }
        }
    }

    protected function isSignificantProgress(int $oldPercentage, int $newPercentage): bool
    {
        $milestones = [25, 50, 75, 90, 100];
        
        foreach ($milestones as $milestone) {
            if ($oldPercentage < $milestone && $newPercentage >= $milestone) {
                return true;
            }
        }
        
        return false;
    }

    protected function calculateCompletionRate(): float
    {
        $total = Project::count();
        if ($total === 0) return 0;
        
        $completed = Project::where('status', 'completed')->count();
        return round(($completed / $total) * 100, 1);
    }

    protected function calculateAverageDuration(): int
    {
        $completedProjects = Project::where('status', 'completed')
            ->whereNotNull('start_date')
            ->whereNotNull('actual_completion_date')
            ->get();

        if ($completedProjects->isEmpty()) return 0;

        $totalDays = $completedProjects->sum(function($project) {
            return $project->start_date->diffInDays($project->actual_completion_date);
        });

        return round($totalDays / $completedProjects->count());
    }
    
    private function processProjectImages(Project $project, array $images, array $altTexts = []): void
    {
        foreach ($images as $index => $image) {
            $path = $this->fileUploadService->uploadImage(
                $image,
                'projects',
                null,
                1200
            );
            
            ProjectImage::create([
                'project_id' => $project->id,
                'image_path' => $path,
                'alt_text' => $altTexts[$index] ?? $project->title,
                'is_featured' => $index === 0,
                'sort_order' => $index + 1,
            ]);
        }
    }
    
    private function updateExistingImages(
        Project $project,
        array $existingImages,
        array $existingAltTexts,
        ?int $featuredImageId
    ): void {
        foreach ($project->images as $image) {
            if (!in_array($image->id, $existingImages)) {
                Storage::disk('public')->delete($image->image_path);
                $image->delete();
            } else {
                $index = array_search($image->id, $existingImages);
                $image->update([
                    'alt_text' => $existingAltTexts[$index] ?? $project->title,
                    'is_featured' => $featuredImageId && $featuredImageId == $image->id,
                    'sort_order' => $index + 1,
                ]);
            }
        }
    }
    
    private function processSeoData(Project $project, array $data): void
    {
        $project->updateSeo([
            'title' => $data['seo_title'] ?? null,
            'description' => $data['seo_description'] ?? null,
            'keywords' => $data['seo_keywords'] ?? null,
        ]);
    }
}