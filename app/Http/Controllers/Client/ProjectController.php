<?php
// File: app/Http/Controllers/Client/ProjectController.php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectFile;
use App\Models\Testimonial;
use App\Services\ClientAccessService;
use App\Services\DashboardService;
use App\Services\TestimonialService;
use App\Facades\Notifications;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class ProjectController extends Controller
{
    protected ClientAccessService $clientAccessService;
    protected DashboardService $dashboardService;
    protected TestimonialService $testimonialService;

    public function __construct(
        ClientAccessService $clientAccessService,
        DashboardService $dashboardService,
        TestimonialService $testimonialService
    ) {
        $this->clientAccessService = $clientAccessService;
        $this->dashboardService = $dashboardService;
        $this->testimonialService = $testimonialService;
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
        
        // Get statistics using existing DashboardService
        $dashboardData = $this->dashboardService->getDashboardData($user);
        $statistics = $dashboardData['statistics']['projects'] ?? [];
        
        // Get recent activities using existing DashboardService
        $recentActivities = collect($dashboardData['recent_activities']['recent_projects'] ?? [])
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
            ->where('category_id', $project->category_id)
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

        // Get project notifications/alerts using existing notification system
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
     * Get project alerts and notifications using existing notification system.
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
                    'notification_type' => 'project.deadline_approaching',
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
                    'notification_type' => 'project.overdue',
                ];
            }
        }
        
        // Check for low progress alerts
        if ($project->status === 'in_progress' && 
            ($project->progress_percentage ?? 0) < 25 && 
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
                'notification_type' => 'project.low_progress',
            ];
        }
        
        // Check for completion eligibility
        if ($project->status === 'in_progress' && 
            ($project->progress_percentage ?? 0) >= 95) {
            
            $alerts[] = [
                'type' => 'success',
                'title' => 'Near Completion',
                'message' => 'Your project is almost complete! Awaiting final review.',
                'action' => null,
                'notification_type' => 'project.near_completion',
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
        
        // Log file download for analytics
        Log::info('Project file downloaded', [
            'user_id' => auth()->id(),
            'project_id' => $project->id,
            'file_id' => $file->id,
            'file_name' => $file->file_name,
        ]);
        
        return Storage::disk('public')->download($file->file_path, $file->file_name);
    }

    /**
     * Download a document from project.
     */
    public function downloadDocument(Project $project, ProjectFile $document)
    {
        return $this->downloadFile($project, $document);
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
     * Store a testimonial for a completed project using TestimonialService.
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
        
        // Prepare testimonial data
        $testimonialData = [
            'project_id' => $project->id,
            'client_name' => $user->name,
            'client_company' => $user->company,
            'client_position' => $validated['client_position'] ?? 'Client',
            'content' => $validated['content'],
            'rating' => $validated['rating'],
            'status' => 'pending', // Using new status field from migration
            'is_active' => false, // Needs admin approval
            'featured' => false,
        ];
        
        try {
            // Create testimonial using service (which handles notifications)
            $testimonial = $this->testimonialService->createTestimonial(
                $testimonialData, 
                $request->file('image')
            );
            
            // Send confirmation notification to client
            try {
                Notifications::send('testimonial.submitted', $testimonial, $user);
            } catch (\Exception $e) {
                Log::warning('Failed to send testimonial submission notification', [
                    'testimonial_id' => $testimonial->id,
                    'user_id' => $user->id,
                    'error' => $e->getMessage()
                ]);
            }
            
            return redirect()->route('client.projects.show', $project)
                ->with('success', 'Thank you for your testimonial! It will be reviewed by our team before being published.');
                
        } catch (\Exception $e) {
            Log::error('Failed to create testimonial', [
                'project_id' => $project->id,
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            
            return redirect()->route('client.projects.show', $project)
                ->with('error', 'Sorry, there was an issue submitting your testimonial. Please try again.');
        }
    }

    /**
     * Get project statistics for API using existing DashboardService.
     */
    public function getStatistics(): JsonResponse
    {
        try {
            $user = auth()->user();
            $dashboardData = $this->dashboardService->getDashboardData($user);
            $statistics = $dashboardData['statistics']['projects'] ?? [];
            
            return response()->json([
                'success' => true,
                'data' => $statistics,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to get project statistics', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to load statistics',
                'data' => [],
            ], 500);
        }
    }

    /**
     * Get project timeline data for API.
     */
    public function getTimeline(Project $project): JsonResponse
    {
        try {
            // Ensure the project belongs to the authenticated client
            if (!$this->clientAccessService->canAccessProject(auth()->user(), $project)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access to this project.'
                ], 403);
            }
            
            $timeline = [
                'milestones' => $project->milestones()
                    ->orderBy('due_date')
                    ->get()
                    ->map(function ($milestone) {
                        return [
                            'id' => $milestone->id,
                            'title' => $milestone->title,
                            'description' => $milestone->description,
                            'due_date' => $milestone->due_date,
                            'completed_at' => $milestone->completed_at,
                            'status' => $milestone->status,
                            'progress_percentage' => $milestone->progress_percentage ?? 0,
                        ];
                    }),
                'progress' => $this->calculateProjectProgress($project),
                'key_dates' => [
                    'start_date' => $project->start_date,
                    'end_date' => $project->end_date,
                    'actual_completion_date' => $project->actual_completion_date,
                    'created_at' => $project->created_at,
                    'updated_at' => $project->updated_at,
                ],
                'status_history' => $this->getProjectStatusHistory($project),
            ];
            
            return response()->json([
                'success' => true,
                'data' => $timeline,
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to get project timeline', [
                'project_id' => $project->id,
                'user_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to load project timeline',
                'data' => [],
            ], 500);
        }
    }

    /**
     * Get available project years for filtering.
     */
    protected function getProjectYears($user): array
    {
        try {
            return $this->clientAccessService->getClientProjects($user)
                ->selectRaw('DISTINCT YEAR(created_at) as year')
                ->whereNotNull('created_at')
                ->orderBy('year', 'desc')
                ->pluck('year')
                ->toArray();
        } catch (\Exception $e) {
            Log::error('Failed to get project years', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }

    /**
     * Calculate project progress based on milestones and status.
     */
    protected function calculateProjectProgress(Project $project): array
    {
        // Use existing progress_percentage field if available
        if (!is_null($project->progress_percentage)) {
            return [
                'percentage' => $project->progress_percentage,
                'method' => 'manual',
                'total_milestones' => $project->milestones->count(),
                'completed_milestones' => $project->milestones->where('status', 'completed')->count(),
            ];
        }
        
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
     * Get project status history for timeline.
     */
    protected function getProjectStatusHistory(Project $project): array
    {
        // This would ideally come from an audit log or status history table
        // For now, return basic status information
        $history = [];
        
        // Add creation event
        $history[] = [
            'status' => 'created',
            'date' => $project->created_at,
            'description' => 'Project created',
        ];
        
        // Add start date if available
        if ($project->start_date) {
            $history[] = [
                'status' => 'started',
                'date' => $project->start_date,
                'description' => 'Project started',
            ];
        }
        
        // Add completion date if completed
        if ($project->status === 'completed' && $project->actual_completion_date) {
            $history[] = [
                'status' => 'completed',
                'date' => $project->actual_completion_date,
                'description' => 'Project completed',
            ];
        }
        
        return $history;
    }

    /**
     * Get upcoming deadlines for this client.
     */
    public function getUpcomingDeadlines(): JsonResponse
    {
        try {
            $user = auth()->user();
            $deadlines = $this->clientAccessService->getUpcomingDeadlines($user, 30);
            
            return response()->json([
                'success' => true,
                'data' => $deadlines,
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to get upcoming deadlines', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to load deadlines',
                'data' => [],
            ], 500);
        }
    }

    /**
     * Request project update from admin.
     */
    public function requestUpdate(Project $project, Request $request): JsonResponse
    {
        try {
            // Ensure the project belongs to the authenticated client
            if (!$this->clientAccessService->canAccessProject(auth()->user(), $project)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access to this project.'
                ], 403);
            }
            
            $validated = $request->validate([
                'message' => 'required|string|max:500',
                'priority' => 'nullable|string|in:low,normal,high,urgent',
            ]);
            
            // Create message request for project update
            $message = \App\Models\Message::create([
                'user_id' => auth()->id(),
                'project_id' => $project->id,
                'subject' => "Project Update Request: {$project->title}",
                'message' => $validated['message'],
                'type' => 'project_update_request',
                'priority' => $validated['priority'] ?? 'normal',
                'requires_response' => true,
            ]);
            
            // Send notification to admins using existing notification system
            try {
                Notifications::send('message.created', $message);
            } catch (\Exception $e) {
                Log::warning('Failed to send project update request notification', [
                    'message_id' => $message->id,
                    'project_id' => $project->id,
                    'error' => $e->getMessage()
                ]);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Project update request sent successfully',
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to request project update', [
                'project_id' => $project->id,
                'user_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to send update request',
            ], 500);
        }
    }
}