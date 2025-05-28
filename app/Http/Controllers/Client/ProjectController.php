<?php
// File: app/Http/Controllers/Client/ProjectController.php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectFile;
use App\Models\ProjectMilestone;
use App\Models\Testimonial;
use App\Services\ClientAccessService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Gate;

class ProjectController extends Controller
{
    protected ClientAccessService $clientAccessService;

    public function __construct(ClientAccessService $clientAccessService)
    {
        $this->clientAccessService = $clientAccessService;
    }

    /**
     * Display a listing of the client's projects.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Use the client access service to get properly filtered projects
        $projectsQuery = $this->clientAccessService->getClientProjects($user, [
            'status' => $request->input('status'),
            'category_id' => $request->input('category'),
            'search' => $request->input('search'),
            'year' => $request->input('year'),
        ]);

        $projects = $projectsQuery->with([
            'category',
            'images' => fn($q) => $q->orderBy('sort_order'),
            'testimonial'
        ])->paginate(12);

        // Get filter options
        $categories = \App\Models\ProjectCategory::active()->orderBy('name')->get();
        $statuses = $this->getProjectStatuses();
        $years = $this->getProjectYears($user);
        
        // Get statistics for the view
        $statistics = $this->getProjectStatistics($user);

        return view('client.projects.index', compact(
            'projects',
            'categories', 
            'statuses',
            'years',
            'statistics'
        ));
    }

    /**
     * Display the specified project.
     */
    public function show(Project $project)
    {
        // Ensure the project belongs to the authenticated client
        $this->authorize('view', $project);
        
        // Load all necessary relationships based on database schema
        $project->load([
            'category',
            'client',
            'quotation.service',
            'images' => fn($q) => $q->orderBy('sort_order'),
            'files' => fn($q) => $q->where('is_public', true)->orderBy('created_at', 'desc'),
            'testimonial',
            'milestones' => fn($q) => $q->orderBy('due_date')
        ]);

        // Get project timeline/milestones
        $milestones = $project->milestones;
        
        // Get project files categorized
        $files = $project->files()
            ->where('is_public', true)
            ->orderBy('category')
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('category');
            
        // Get related projects (same category, different client projects)
        $relatedProjects = Project::where('category_id', $project->category_id)
            ->where('id', '!=', $project->id)
            ->where('client_id', $project->client_id) // Only client's other projects
            ->with(['category', 'images' => fn($q) => $q->where('is_featured', true)])
            ->limit(3)
            ->get();

        // Calculate project progress
        $progress = $this->calculateProjectProgress($project);
        
        // Get project communications/messages
        $messages = \App\Models\Message::where('project_id', $project->id)
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('client.projects.show', compact(
            'project',
            'milestones',
            'files',
            'relatedProjects',
            'progress',
            'messages'
        ));
    }

    /**
     * Download a project file.
     */
    public function downloadFile(Project $project, ProjectFile $file)
    {
        // Ensure the project belongs to the authenticated client
        $this->authorize('view', $project);
        
        // Ensure the file belongs to the project and is public
        if ($file->project_id !== $project->id || !$file->is_public) {
            abort(404, 'File not found or not accessible.');
        }
        
        // Check if file exists in storage
        if (!Storage::disk('public')->exists($file->file_path)) {
            abort(404, 'File not found in storage.');
        }
        
        return Storage::disk('public')->download($file->file_path, $file->file_name);
    }
    

    /**
     * Display project documents/files page.
     */
    public function documents(Project $project)
    {
        // Ensure the project belongs to the authenticated client
        $this->authorize('view', $project);
        
        // Get all public files for this project, organized by category
        $files = ProjectFile::where('project_id', $project->id)
            ->where('is_public', true)
            ->orderBy('category')
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('category');
            
        // Get file statistics
        $fileStats = [
            'total_files' => $files->flatten()->count(),
            'total_size' => $files->flatten()->sum('file_size'),
            'categories' => $files->keys()->count(),
            'recent_uploads' => $files->flatten()
                ->where('created_at', '>=', now()->subDays(7))
                ->count(),
        ];

        return view('client.projects.documents', compact(
            'project',
            'files',
            'fileStats'
        ));
    }

    /**
     * Show form to create a testimonial for a completed project.
     */
    public function showTestimonialForm(Project $project)
    {
        // Ensure the project belongs to the authenticated client
        $this->authorize('view', $project);
        
        // Check if project is completed
        if ($project->status !== 'completed') {
            return redirect()->route('client.projects.show', $project)
                ->with('error', 'Testimonials can only be submitted for completed projects.');
        }
        
        // Check if testimonial already exists
        if ($project->testimonial) {
            return redirect()->route('client.projects.show', $project)
                ->with('info', 'You have already submitted a testimonial for this project.');
        }

        return view('client.projects.testimonial', compact('project'));
    }

    /**
     * Store a testimonial for a completed project.
     */
    public function storeTestimonial(Request $request, Project $project)
    {
        // Ensure the project belongs to the authenticated client
        $this->authorize('view', $project);
        
        // Validate project status and existing testimonial
        if ($project->status !== 'completed') {
            return redirect()->route('client.projects.show', $project)
                ->with('error', 'Testimonials can only be submitted for completed projects.');
        }
        
        if ($project->testimonial) {
            return redirect()->route('client.projects.show', $project)
                ->with('info', 'You have already submitted a testimonial for this project.');
        }
        
        $user = auth()->user();
        
        $validated = $request->validate([
            'content' => 'required|string|min:10|max:1000',
            'rating' => 'required|integer|min:1|max:5',
            'client_position' => 'nullable|string|max:100',
            'image' => 'nullable|image|max:1024',
        ]);
        
        // Create testimonial (synced with testimonials table schema)
        $testimonial = Testimonial::create([
            'project_id' => $project->id,
            'client_name' => $user->name,
            'client_company' => $user->company,
            'client_position' => $validated['client_position'] ?? 'Client',
            'content' => $validated['content'],
            'rating' => $validated['rating'],
            'is_active' => false, // Needs admin approval
            'featured' => false,
        ]);
        
        // Handle image upload if provided
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('testimonials', 'public');
            $testimonial->update(['image' => $path]);
        }
        
        return redirect()->route('client.projects.show', $project)
            ->with('success', 'Thank you for your testimonial! It will be reviewed by our team before being published.');
    }
    

    /**
     * Get project statistics for the client.
     */
    protected function getProjectStatistics($user): array
    {
        $isAdmin = $user->hasAnyRole(['super-admin', 'admin', 'manager']);
        
        $query = Project::query();
        if (!$isAdmin) {
            $query->where('client_id', $user->id);
        }
        
        return [
            'total' => (clone $query)->count(),
            'active' => (clone $query)->whereIn('status', ['in_progress', 'on_hold'])->count(),
            'completed' => (clone $query)->where('status', 'completed')->count(),
            'planning' => (clone $query)->where('status', 'planning')->count(),
            'overdue' => (clone $query)
                ->where('status', 'in_progress')
                ->where('end_date', '<', now())
                ->whereNotNull('end_date')
                ->count(),
            'this_year' => (clone $query)->whereYear('created_at', now()->year)->count(),
        ];
    }

    /**
     * Get available project years for filtering.
     */
    protected function getProjectYears($user): array
    {
        $isAdmin = $user->hasAnyRole(['super-admin', 'admin', 'manager']);
        
        $query = Project::query();
        if (!$isAdmin) {
            $query->where('client_id', $user->id);
        }
        
        return $query->selectRaw('DISTINCT YEAR(created_at) as year')
            ->whereNotNull('created_at')
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();
    }

    /**
     * Get project statuses with metadata.
     */
    protected function getProjectStatuses(): array
    {
        return [
            'planning' => [
                'label' => 'Planning',
                'color' => 'yellow',
                'description' => 'Project is in planning phase'
            ],
            'in_progress' => [
                'label' => 'In Progress',
                'color' => 'blue',
                'description' => 'Project is actively being worked on'
            ],
            'on_hold' => [
                'label' => 'On Hold',
                'color' => 'orange',
                'description' => 'Project is temporarily paused'
            ],
            'completed' => [
                'label' => 'Completed',
                'color' => 'green',
                'description' => 'Project has been finished successfully'
            ],
            'cancelled' => [
                'label' => 'Cancelled',
                'color' => 'red',
                'description' => 'Project has been cancelled'
            ],
        ];
    }

    /**
     * Calculate project progress based on milestones.
     */
    protected function calculateProjectProgress(Project $project): array
    {
        $milestones = $project->milestones;
        
        if ($milestones->isEmpty()) {
            // If no milestones, calculate based on dates
            if ($project->start_date && $project->end_date) {
                $totalDays = $project->start_date->diffInDays($project->end_date);
                $elapsedDays = $project->start_date->diffInDays(now());
                $percentage = $totalDays > 0 ? min(100, max(0, ($elapsedDays / $totalDays) * 100)) : 0;
                
                return [
                    'percentage' => round($percentage, 1),
                    'method' => 'date_based',
                    'total_milestones' => 0,
                    'completed_milestones' => 0,
                ];
            }
            
            // Default progress based on status
            $statusProgress = [
                'planning' => 10,
                'in_progress' => 50,
                'on_hold' => 50,
                'completed' => 100,
                'cancelled' => 0,
            ];
            
            return [
                'percentage' => $statusProgress[$project->status] ?? 0,
                'method' => 'status_based',
                'total_milestones' => 0,
                'completed_milestones' => 0,
            ];
        }
        
        // Calculate based on milestones
        $totalMilestones = $milestones->count();
        $completedMilestones = $milestones->where('status', 'completed')->count();
        $percentage = $totalMilestones > 0 ? ($completedMilestones / $totalMilestones) * 100 : 0;
        
        return [
            'percentage' => round($percentage, 1),
            'method' => 'milestone_based',
            'total_milestones' => $totalMilestones,
            'completed_milestones' => $completedMilestones,
            'in_progress_milestones' => $milestones->where('status', 'in_progress')->count(),
            'delayed_milestones' => $milestones->where('status', 'delayed')->count(),
        ];
    }

    /**
     * Authorize access to project using policy.
     */
    public function authorize($ability, $arguments = [])
    {
        return Gate::authorize($ability, $arguments);
    }
}