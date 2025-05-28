<?php
// File: app/Http/Controllers/Client/ProjectController.php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectFile;
use App\Models\Testimonial;
use App\Services\ClientAccessService;
use App\Services\DashboardService;
use App\Services\NotificationAlertService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Gate;

class ProjectController extends Controller
{
    protected ClientAccessService $clientAccessService;
    protected DashboardService $dashboardService;
    protected NotificationAlertService $notificationService;

    public function __construct(
        ClientAccessService $clientAccessService,
        DashboardService $dashboardService,
        NotificationAlertService $notificationService
    ) {
        $this->clientAccessService = $clientAccessService;
        $this->dashboardService = $dashboardService;
        $this->notificationService = $notificationService;
    }

    /**
     * Display a listing of the client's projects.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Validate filters
        $filters = $request->validate([
            'status' => 'nullable|string|in:planning,in_progress,on_hold,completed,cancelled',
            'category' => 'nullable|exists:project_categories,id',
            'search' => 'nullable|string|max:255',
            'year' => 'nullable|integer|min:2000|max:2050',
            'sort' => 'nullable|string|in:title,status,created_at,updated_at,end_date',
            'direction' => 'nullable|string|in:asc,desc',
        ]);
        
        // Get projects using service
        $projectsQuery = $this->clientAccessService->getClientProjects($user, $filters);
        
        // Apply sorting
        $sortField = $filters['sort'] ?? 'updated_at';
        $sortDirection = $filters['direction'] ?? 'desc';
        $projectsQuery->orderBy($sortField, $sortDirection);
        
        // Paginate results
        $projects = $projectsQuery->with([
            'category',
            'service',
            'images' => fn($q) => $q->orderBy('sort_order'),
            'testimonial'
        ])->paginate(12);

        // Get filter options
        $categories = \App\Models\ProjectCategory::active()->orderBy('name')->get();
        $years = $this->getProjectYears($user);
        
        // Get statistics
        $statistics = $this->getProjectStatistics($user);
        
        // Get recent activities
        $dashboardData = $this->dashboardService->getDashboardData($user);
        $recentActivities = collect($dashboardData['recent_activities'] ?? [])
            ->where('type', 'project')
            ->take(5)
            ->values();

        return view('client.projects.index', compact(
            'projects',
            'categories', 
            'years',
            'statistics',
            'recentActivities',
            'filters'
        ));
    }

    /**
     * Display the specified project.
     */
    public function show(Project $project)
    {
        // Ensure the project belongs to the authenticated client
        if (!$this->clientAccessService->canAccessProject(auth()->user(), $project)) {
            abort(403, 'Unauthorized access to this project.');
        }
        
        // Load all necessary relationships
        $project->load([
            'category',
            'client',
            'service',
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
            
        // Get related projects (client's other projects in same category)
        $relatedProjects = $this->clientAccessService->getClientProjects(auth()->user())
            ->where('project_category_id', $project->project_category_id)
            ->where('id', '!=', $project->id)
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

        // Get project notifications/alerts
        $projectAlerts = $this->getProjectAlerts($project);

        return view('client.projects.show', compact(
            'project',
            'milestones',
            'files',
            'relatedProjects',
            'progress',
            'messages',
            'projectAlerts'
        ));
    }

    /**
     * Get project alerts and notifications.
     */
    protected function getProjectAlerts(Project $project): array
    {
        $alerts = [];
        
        // Check for deadline alerts
        if ($project->status === 'in_progress' && $project->end_date) {
            $daysUntilDeadline = now()->diffInDays($project->end_date, false);
            
            if ($daysUntilDeadline <= 7 && $daysUntilDeadline > 0) {
                $alerts[] = [
                    'type' => 'warning',
                    'title' => 'Deadline Approaching',
                    'message' => "Project deadline is in {$daysUntilDeadline} day(s)",
                    'action' => null,
                ];
            } elseif ($daysUntilDeadline < 0) {
                $daysOverdue = abs($daysUntilDeadline);
                $alerts[] = [
                    'type' => 'error',
                    'title' => 'Project Overdue',
                    'message' => "Project is {$daysOverdue} day(s) overdue",
                    'action' => [
                        'text' => 'Contact Support',
                        'url' => route('client.messages.create', ['project_id' => $project->id]),
                    ],
                ];
            }
        }
        
        // Check for low progress alerts
        if ($project->status === 'in_progress' && 
            $project->progress_percentage < 25 && 
            $project->created_at->diffInDays(now()) > 30) {
            
            $alerts[] = [
                'type' => 'info',
                'title' => 'Low Progress',
                'message' => 'Project progress seems slower than expected',
                'action' => [
                    'text' => 'Request Update',
                    'url' => route('client.messages.create', [
                        'project_id' => $project->id, 
                        'subject' => 'Project Progress Update Request'
                    ]),
                ],
            ];
        }
        
        return $alerts;
    }

    /**
     * Download a project file.
     */
    public function downloadFile(Project $project, ProjectFile $file)
    {
        // Ensure the project belongs to the authenticated client
        if (!$this->clientAccessService->canAccessProject(auth()->user(), $project)) {
            abort(403, 'Unauthorized access to this project.');
        }
        
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
        if (!$this->clientAccessService->canAccessProject(auth()->user(), $project)) {
            abort(403, 'Unauthorized access to this project.');
        }
        
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
        if (!$this->clientAccessService->canAccessProject(auth()->user(), $project)) {
            abort(403, 'Unauthorized access to this project.');
        }
        
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
        if (!$this->clientAccessService->canAccessProject(auth()->user(), $project)) {
            abort(403, 'Unauthorized access to this project.');
        }
        
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
        
        // Create testimonial
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
        
        // Clear dashboard cache
        $this->dashboardService->clearCache($user);
        
        return redirect()->route('client.projects.show', $project)
            ->with('success', 'Thank you for your testimonial! It will be reviewed by our team before being published.');
    }

    /**
     * Get project statistics for API.
     */
    public function getStatistics(): JsonResponse
    {
        $user = auth()->user();
        $statistics = $this->getProjectStatistics($user);
        
        return response()->json([
            'success' => true,
            'data' => $statistics,
        ]);
    }

    /**
     * Get project timeline data for API.
     */
    public function getTimeline(Project $project): JsonResponse
    {
        // Ensure the project belongs to the authenticated client
        if (!$this->clientAccessService->canAccessProject(auth()->user(), $project)) {
            abort(403, 'Unauthorized access to this project.');
        }
        
        $timeline = [
            'milestones' => $project->milestones()->orderBy('due_date')->get(),
            'progress' => $this->calculateProjectProgress($project),
            'key_dates' => [
                'start_date' => $project->start_date,
                'end_date' => $project->end_date,
                'actual_completion_date' => $project->actual_completion_date,
            ],
        ];
        
        return response()->json([
            'success' => true,
            'data' => $timeline,
        ]);
    }

    /**
     * Get project statistics for the client.
     */
    protected function getProjectStatistics($user): array
    {
        $dashboardData = $this->dashboardService->getDashboardData($user);
        return $dashboardData['statistics']['projects'] ?? [];
    }

    /**
     * Get available project years for filtering.
     */
    protected function getProjectYears($user): array
    {
        return $this->clientAccessService->getClientProjects($user)
            ->selectRaw('DISTINCT YEAR(created_at) as year')
            ->whereNotNull('created_at')
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();
    }

    /**
     * Calculate project progress based on milestones and status.
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
}