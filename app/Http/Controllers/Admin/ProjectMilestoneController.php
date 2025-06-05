<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectMilestone;
use App\Http\Requests\StoreProjectMilestoneRequest;
use App\Http\Requests\UpdateProjectMilestoneRequest;
use App\Services\ProjectService;
use App\Facades\Notifications;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProjectMilestoneController extends Controller
{
    protected $projectService;

    public function __construct(ProjectService $projectService)
    {
        $this->projectService = $projectService;
    }

    /**
     * Display a listing of project milestones.
     */
    public function index(Project $project)
    {
        $this->authorize('view', $project);
        
        $milestones = $project->milestones()
            ->orderBy('due_date')
            ->orderBy('sort_order')
            ->get();
            
        return view('admin.projects.milestones.index', compact('project', 'milestones'));
    }

    /**
     * Show the form for creating a new milestone.
     */
    public function create(Project $project)
    {
        $this->authorize('update', $project);
        
        return view('admin.projects.milestones.create', compact('project'));
    }

    /**
     * Store a newly created milestone.
     */
    public function store(StoreProjectMilestoneRequest $request, Project $project)
    {
        $this->authorize('update', $project);
        
        $validated = $request->validated();
        $validated['project_id'] = $project->id;
        
        // Auto-set completion date if status is completed
        if ($validated['status'] === 'completed' && empty($validated['completion_date'])) {
            $validated['completion_date'] = now();
        }
        
        // Set sort order if not provided
        if (!isset($validated['sort_order'])) {
            $validated['sort_order'] = $project->milestones()->max('sort_order') + 1;
        }
        
        $milestone = ProjectMilestone::create($validated);
        
        // Update project progress if milestone is completed
        if ($milestone->status === 'completed') {
            $this->updateProjectProgress($project);
        }
        
        // Send notifications
        Notifications::send('project.milestone_created', $milestone);
        
        $redirectRoute = $request->input('action') === 'save_and_add_another' 
            ? 'admin.projects.milestones.create'
            : 'admin.projects.show';
            
        return redirect()->route($redirectRoute, $project)
            ->with('success', 'Milestone created successfully!');
    }

    /**
     * Display the specified milestone.
     */
    public function show(Project $project, ProjectMilestone $milestone)
    {
        $this->authorize('view', $project);
        
        return view('admin.projects.milestones.show', compact('project', 'milestone'));
    }

    /**
     * Show the form for editing the specified milestone.
     */
    public function edit(Project $project, ProjectMilestone $milestone)
    {
        $this->authorize('update', $project);
        
        return view('admin.projects.milestones.edit', compact('project', 'milestone'));
    }

    /**
     * Update the specified milestone.
     */
    public function update(UpdateProjectMilestoneRequest $request, Project $project, ProjectMilestone $milestone)
    {
        $this->authorize('update', $project);
        
        $validated = $request->validated();
        $oldStatus = $milestone->status;
        
        // Auto-set completion date if status changed to completed
        if ($validated['status'] === 'completed' && $oldStatus !== 'completed' && empty($validated['completion_date'])) {
            $validated['completion_date'] = now();
        }
        
        // Clear completion date if status changed from completed
        if ($validated['status'] !== 'completed' && $oldStatus === 'completed' && empty($validated['completion_date'])) {
            $validated['completion_date'] = null;
        }
        
        $milestone->update($validated);
        
        // Update project progress if milestone status changed
        if ($oldStatus !== $milestone->status) {
            $this->updateProjectProgress($project);
            
            // Send status change notification
            Notifications::send('project.milestone_status_changed', $milestone);
        }
        
        $redirectRoute = $request->input('action') === 'save_and_continue' 
            ? 'admin.projects.milestones.edit'
            : 'admin.projects.show';
            
        return redirect()->route($redirectRoute, [$project, $milestone])
            ->with('success', 'Milestone updated successfully!');
    }

    /**
     * Remove the specified milestone.
     */
    public function destroy(Project $project, ProjectMilestone $milestone)
    {
        $this->authorize('update', $project);
        
        $milestone->delete();
        
        // Update project progress after deletion
        $this->updateProjectProgress($project);
        
        // Send notification
        Notifications::send('project.milestone_deleted', [
            'project' => $project,
            'milestone_title' => $milestone->title
        ]);
        
        return redirect()->route('admin.projects.show', $project)
            ->with('success', 'Milestone deleted successfully!');
    }

    /**
     * Mark milestone as completed.
     */
    public function complete(Project $project, ProjectMilestone $milestone)
    {
        $this->authorize('update', $project);
        
        $milestone->update([
            'status' => 'completed',
            'completion_date' => now(),
            'progress_percent' => 100
        ]);
        
        // Update project progress
        $this->updateProjectProgress($project);
        
        // Send completion notification
        Notifications::send('project.milestone_completed', $milestone);
        
        return redirect()->back()
            ->with('success', 'Milestone marked as completed!');
    }

    /**
     * Reopen a completed milestone.
     */
    public function reopen(Project $project, ProjectMilestone $milestone)
    {
        $this->authorize('update', $project);
        
        $milestone->update([
            'status' => 'in_progress',
            'completion_date' => null
        ]);
        
        // Update project progress
        $this->updateProjectProgress($project);
        
        // Send notification
        Notifications::send('project.milestone_reopened', $milestone);
        
        return redirect()->back()
            ->with('success', 'Milestone reopened successfully!');
    }

    /**
     * Update milestone order.
     */
    public function updateOrder(Request $request, Project $project)
    {
        $this->authorize('update', $project);
        
        $request->validate([
            'milestones' => 'required|array',
            'milestones.*.id' => 'required|exists:project_milestones,id',
            'milestones.*.sort_order' => 'required|integer|min:0',
        ]);
        
        DB::transaction(function () use ($request, $project) {
            foreach ($request->milestones as $milestoneData) {
                ProjectMilestone::where('id', $milestoneData['id'])
                    ->where('project_id', $project->id)
                    ->update(['sort_order' => $milestoneData['sort_order']]);
            }
        });
        
        return response()->json(['success' => true]);
    }

    /**
     * Bulk update milestones.
     */
    public function bulkUpdate(Request $request, Project $project)
    {
        $this->authorize('update', $project);
        
        $request->validate([
            'milestone_ids' => 'required|array',
            'milestone_ids.*' => 'exists:project_milestones,id',
            'action' => 'required|in:complete,reopen,delete,update_status',
            'status' => 'required_if:action,update_status|in:pending,in_progress,completed,delayed'
        ]);
        
        $milestones = ProjectMilestone::whereIn('id', $request->milestone_ids)
            ->where('project_id', $project->id)
            ->get();
            
        $updated = 0;
        
        DB::transaction(function () use ($request, $milestones, &$updated, $project) {
            foreach ($milestones as $milestone) {
                switch ($request->action) {
                    case 'complete':
                        if ($milestone->status !== 'completed') {
                            $milestone->update([
                                'status' => 'completed',
                                'completion_date' => now(),
                                'progress_percent' => 100
                            ]);
                            $updated++;
                        }
                        break;
                        
                    case 'reopen':
                        if ($milestone->status === 'completed') {
                            $milestone->update([
                                'status' => 'in_progress',
                                'completion_date' => null
                            ]);
                            $updated++;
                        }
                        break;
                        
                    case 'delete':
                        $milestone->delete();
                        $updated++;
                        break;
                        
                    case 'update_status':
                        $updateData = ['status' => $request->status];
                        
                        if ($request->status === 'completed') {
                            $updateData['completion_date'] = now();
                            $updateData['progress_percent'] = 100;
                        } elseif ($milestone->status === 'completed') {
                            $updateData['completion_date'] = null;
                        }
                        
                        $milestone->update($updateData);
                        $updated++;
                        break;
                }
            }
            
            // Update project progress after bulk changes
            $this->updateProjectProgress($project);
        });
        
        // Send bulk notification
        if ($updated > 0) {
            Notifications::send('project.milestones_bulk_updated', [
                'project' => $project,
                'action' => $request->action,
                'count' => $updated
            ]);
        }
        
        return redirect()->back()
            ->with('success', "Successfully {$request->action} {$updated} milestone(s)!");
    }

    /**
     * Get milestones for calendar/timeline view.
     */
    public function calendar(Project $project)
    {
        $this->authorize('view', $project);
        
        $milestones = $project->milestones()
            ->select(['id', 'title', 'due_date', 'completion_date', 'status'])
            ->get()
            ->map(function ($milestone) use ($project) {
                return [
                    'id' => $milestone->id,
                    'title' => $milestone->title,
                    'start' => $milestone->due_date?->format('Y-m-d'),
                    'end' => $milestone->completion_date?->format('Y-m-d'),
                    'status' => $milestone->status,
                    'color' => $this->getMilestoneColor($milestone->status),
                    'url' => route('admin.projects.milestones.edit', [$project, $milestone])
                ];
            });
            
        return response()->json($milestones);
    }

    /**
     * Get milestone statistics.
     */
    public function statistics(Project $project)
    {
        $this->authorize('view', $project);
        
        $stats = [
            'total' => $project->milestones()->count(),
            'completed' => $project->milestones()->where('status', 'completed')->count(),
            'in_progress' => $project->milestones()->where('status', 'in_progress')->count(),
            'pending' => $project->milestones()->where('status', 'pending')->count(),
            'delayed' => $project->milestones()->where('status', 'delayed')->count(),
            'overdue' => $project->milestones()
                ->where('status', '!=', 'completed')
                ->where('due_date', '<', now())
                ->count(),
            'due_soon' => $project->milestones()
                ->where('status', '!=', 'completed')
                ->whereBetween('due_date', [now(), now()->addDays(7)])
                ->count(),
        ];
        
        $stats['completion_rate'] = $stats['total'] > 0 
            ? round(($stats['completed'] / $stats['total']) * 100, 1)
            : 0;
            
        return response()->json($stats);
    }

    /**
     * Update project progress based on milestone completion.
     */
    protected function updateProjectProgress(Project $project)
    {
        $totalMilestones = $project->milestones()->count();
        
        if ($totalMilestones === 0) {
            return;
        }
        
        $completedMilestones = $project->milestones()
            ->where('status', 'completed')
            ->count();
            
        $progressPercentage = round(($completedMilestones / $totalMilestones) * 100);
        
        // Update project progress
        $project->update([
            'progress_percentage' => $progressPercentage
        ]);
        
        // Auto-update project status based on progress
        if ($progressPercentage === 100 && $project->status !== 'completed') {
            $project->update([
                'status' => 'completed',
                'actual_completion_date' => now()
            ]);
            
            // Send project completion notification
            Notifications::send('project.completed', $project);
        }
    }

    /**
     * Get color for milestone status.
     */
    protected function getMilestoneColor(string $status): string
    {
        return match ($status) {
            'completed' => '#10b981',
            'in_progress' => '#f59e0b',
            'delayed' => '#ef4444',
            'pending' => '#6b7280',
            default => '#6b7280'
        };
    }
}