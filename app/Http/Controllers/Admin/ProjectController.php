<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\ProjectImage;
use App\Models\Service;
use App\Models\User;
use App\Models\Quotation;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Services\FileUploadService;
use App\Services\ProjectService;
use App\Facades\Notifications;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ProjectController extends Controller
{
    protected $fileUploadService;
    protected $projectService;

    public function __construct(FileUploadService $fileUploadService, ProjectService $projectService)
    {
        $this->fileUploadService = $fileUploadService;
        $this->projectService = $projectService;
    }

    /**
     * Get actual database columns for projects table
     */
    private function getProjectColumns(): array
    {
        static $columns = null;

        if ($columns === null) {
            $columns = Schema::getColumnListing('projects');
        }

        return $columns;
    }

    /**
     * Check if a column exists in projects table
     */
    private function hasColumn(string $column): bool
    {
        return in_array($column, $this->getProjectColumns());
    }

    /**
     * Filter data to only include existing columns
     */
    private function filterToExistingColumns(array $data): array
    {
        $existingColumns = $this->getProjectColumns();
        return array_intersect_key($data, array_flip($existingColumns));
    }

    /**
     * Display a listing of projects.
     */
    public function index(Request $request)
    {
        $query = Project::with(['client', 'category', 'service'])
            ->when($request->filled('status'), fn($q) => $q->where('status', $request->status))
            ->when($request->filled('category'), fn($q) => $q->where('category_id', $request->category))
            ->when($request->filled('service'), fn($q) => $q->where('service_id', $request->service))
            ->when($request->filled('client'), fn($q) => $q->where('client_id', $request->client))
            ->when($request->filled('year'), fn($q) => $q->whereYear('created_at', $request->year))
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = $request->search;
                return $q->where(function ($query) use ($search) {
                    $query->where('title', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhere('location', 'like', "%{$search}%");

                    // Only search additional fields if they exist
                    if ($this->hasColumn('short_description')) {
                        $query->orWhere('short_description', 'like', "%{$search}%");
                    }
                    if ($this->hasColumn('client_name')) {
                        $query->orWhere('client_name', 'like', "%{$search}%");
                    }
                });
            });

        // Apply additional filters only if columns exist
        if ($request->filled('priority') && $this->hasColumn('priority')) {
            $query->where('priority', $request->priority);
        }
        if ($request->filled('active_only') && $this->hasColumn('is_active')) {
            $query->where('is_active', true);
        }
        if ($request->filled('featured_only')) {
            $query->where('featured', true);
        }

        // Add sorting
        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');

        $allowedSortFields = ['title', 'status', 'start_date', 'end_date', 'created_at', 'updated_at', 'year'];

        // Add additional sort fields if columns exist
        if ($this->hasColumn('priority')) {
            $allowedSortFields[] = 'priority';
        }
        if ($this->hasColumn('progress_percentage')) {
            $allowedSortFields[] = 'progress_percentage';
        }
        if ($this->hasColumn('display_order')) {
            $allowedSortFields[] = 'display_order';
        }
        if ($this->hasColumn('budget')) {
            $allowedSortFields[] = 'budget';
        }

        if (in_array($sortField, $allowedSortFields)) {
            $query->orderBy($sortField, $sortDirection);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $projects = $query->paginate(15)->withQueryString();

        // Get filter options
        $categories = ProjectCategory::where('is_active', true)->get();
        $services = Service::where('is_active', true)->get();
        $clients = User::role('client')->get();
        $years = Project::selectRaw('YEAR(created_at) as year')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year', 'year')
            ->toArray();

        // Get summary statistics
        $stats = [
            'total' => Project::count(),
            'completed' => Project::where('status', 'completed')->count(),
            'in_progress' => Project::where('status', 'in_progress')->count(),
            'planning' => Project::where('status', 'planning')->count(),
            'featured' => Project::where('featured', true)->count(),
        ];

        // Add additional stats if columns exist
        if ($this->hasColumn('is_active')) {
            $stats['active'] = Project::where('is_active', true)->count();
        }
        if ($this->hasColumn('priority')) {
            $stats['high_priority'] = Project::whereIn('priority', ['high', 'urgent'])->count();
        }

        return view('admin.projects.index', compact(
            'projects',
            'categories',
            'services',
            'clients',
            'years',
            'stats'
        ));
    }

    /**
     * Show the form for creating a new project.
     */
    public function create(Request $request)
    {
        $categories = ProjectCategory::where('is_active', true)->orderBy('sort_order')->get();
        $services = Service::where('is_active', true)->orderBy('sort_order')->get();
        $clients = User::role('client')->where('is_active', true)->orderBy('name')->get();

        // Check if creating from quotation
        $quotation = null;
        if ($request->has('from_quotation')) {
            $quotation = Quotation::findOrFail($request->from_quotation);

            // Verify quotation is approved
            if ($quotation->status !== 'approved') {
                return redirect()->route('admin.quotations.index')
                    ->with('error', 'Only approved quotations can be converted to projects.');
            }

            // Check if project already exists for this quotation (only if column exists)
            if ($this->hasColumn('quotation_id') && $quotation->project_created ?? false) {
                $existingProject = Project::where('quotation_id', $quotation->id)->first();
                if ($existingProject) {
                    return redirect()->route('admin.projects.show', $existingProject)
                        ->with('info', 'A project already exists for this quotation.');
                }
            }
        }

        return view('admin.projects.create', compact(
            'categories',
            'services',
            'clients',
            'quotation'
        ));
    }

    /**
     * Store a newly created project.
     */
    public function store(StoreProjectRequest $request)
{
    $validated = $request->validated();

    // Generate slug if not provided
    if (empty($validated['slug'])) {
        $validated['slug'] = Str::slug($validated['title']);
        
        // Ensure slug is unique
        $originalSlug = $validated['slug'];
        $counter = 1;
        while (Project::where('slug', $validated['slug'])->exists()) {
            $validated['slug'] = $originalSlug . '-' . $counter;
            $counter++;
        }
    }

    // Set basic defaults for core columns
    $validated['featured'] = $request->boolean('featured', false);
    $validated['year'] = $validated['year'] ?? date('Y');
    
    // Handle optional columns only if they exist
    if ($this->hasColumn('is_active')) {
        $validated['is_active'] = $request->boolean('is_active', true);
    }
    if ($this->hasColumn('progress_percentage')) {
        $validated['progress_percentage'] = $validated['progress_percentage'] ?? 0;
    }
    if ($this->hasColumn('display_order')) {
        $validated['display_order'] = $validated['display_order'] ?? (Project::max('display_order') + 1);
    }
    if ($this->hasColumn('priority')) {
        $validated['priority'] = $validated['priority'] ?? 'normal';
    }

    // Auto-set completion date if status is completed and column exists
    if ($validated['status'] === 'completed') {
        if ($this->hasColumn('actual_completion_date') && empty($validated['actual_completion_date'])) {
            $validated['actual_completion_date'] = now();
        }
        if ($this->hasColumn('progress_percentage')) {
            $validated['progress_percentage'] = 100;
        }
    }

    // Filter to only existing columns
    $validated = $this->filterToExistingColumns($validated);

    $project = Project::create($validated);

    // Process temporary images from universal-uploader
    $this->processTempImages($project);

    // Handle traditional image uploads (fallback)
    if ($request->hasFile('images')) {
        foreach ($request->file('images') as $index => $image) {
            try {
                $path = $this->fileUploadService->uploadImage(
                    $image, 
                    'projects/' . $project->id,
                    null,
                    1200, // max width
                    800   // max height
                );
                
                $project->images()->create([
                    'image_path' => $path,
                    'alt_text' => $request->input("image_alt_texts.{$index}") 
                        ?? $project->title . ' - Image ' . ($index + 1),
                    'is_featured' => $index === 0,
                    'sort_order' => $index + 1,
                ]);
            } catch (\Exception $e) {
                \Log::error('Image upload failed: ' . $e->getMessage());
            }
        }
    }

    // If created from quotation, update quotation status (only if columns exist)
    if ($this->hasColumn('quotation_id') && $project->quotation_id) {
        $quotation = Quotation::find($project->quotation_id);
        if ($quotation) {
            $updateData = [];
            if (Schema::hasColumn('quotations', 'project_created')) {
                $updateData['project_created'] = true;
            }
            if (Schema::hasColumn('quotations', 'project_created_at')) {
                $updateData['project_created_at'] = now();
            }
            if (Schema::hasColumn('quotations', 'admin_notes')) {
                $updateData['admin_notes'] = ($quotation->admin_notes ? $quotation->admin_notes . "\n\n" : '') 
                    . "Project created: " . $project->title . " on " . now()->format('Y-m-d H:i:s');
            }
            
            if (!empty($updateData)) {
                $quotation->update($updateData);
            }
        }
    }

    // Send notification
    if (class_exists('App\Facades\Notifications')) {
        Notifications::send('project.created', $project);
    }

    $redirectRoute = match($request->input('action')) {
        'save_and_add_another' => 'admin.projects.create',
        'save_and_add_milestone' => 'admin.projects.milestones.create',
        default => 'admin.projects.show'
    };

    return redirect()->route($redirectRoute, $project)
        ->with('success', 'Project created successfully!');
}

    /**
     * Display the specified project.
     */
    public function show(Project $project)
    {
        $project->load([
            'client',
            'category',
            'service',
            'images' => fn($q) => $q->orderBy('sort_order'),
            'files' => fn($q) => $q->orderBy('created_at', 'desc')->limit(10),
            'milestones' => fn($q) => $q->orderBy('due_date')->orderBy('sort_order'),
            'testimonials' => fn($q) => $q->where('is_active', true),
            'messages' => fn($q) => $q->orderBy('created_at', 'desc')->limit(5)
        ]);

        // Load quotation only if column exists
        if ($this->hasColumn('quotation_id')) {
            $project->load('quotation');
        }

        // SAFE milestone handling - prevent trim() errors
        $project->milestones->each(function ($milestone) {
            // Ensure dependencies is always an array
            if (!is_array($milestone->dependencies)) {
                $milestone->dependencies = [];
            }
        });

        // Calculate statistics safely
        $milestoneStats = [
            'total' => $project->milestones->count(),
            'completed' => $project->milestones->where('status', 'completed')->count(),
            'in_progress' => $project->milestones->where('status', 'in_progress')->count(),
            'pending' => $project->milestones->where('status', 'pending')->count(),
            'delayed' => $project->milestones->where('status', 'delayed')->count(),
        ];

        $milestoneStats['overdue'] = $project->milestones->filter(function ($milestone) {
            return $milestone->due_date &&
                $milestone->due_date < now() &&
                $milestone->status !== 'completed';
        })->count();

        $milestoneStats['due_soon'] = $project->milestones->filter(function ($milestone) {
            return $milestone->due_date &&
                $milestone->due_date >= now() &&
                $milestone->due_date <= now()->addDays(7) &&
                $milestone->status !== 'completed';
        })->count();

        $milestoneStats['completion_rate'] = $milestoneStats['total'] > 0
            ? round(($milestoneStats['completed'] / $milestoneStats['total']) * 100, 1)
            : 0;

        // Safe file stats
        $fileStats = [
            'total_files' => $project->files->count(),
            'total_size' => $project->files->sum('file_size'),
            'public_files' => $project->files->where('is_public', true)->count(),
            'recent_files' => $project->files->take(5),
        ];

        // Safe timeline data
        $timelineData = [
            'days_since_start' => $project->start_date ? now()->diffInDays($project->start_date) : 0,
            'days_until_deadline' => $project->end_date ? $project->end_date->diffInDays(now()) : null,
            'is_overdue' => $project->end_date && $project->end_date < now() && $project->status !== 'completed',
        ];

        if ($this->hasColumn('estimated_completion_date') && $project->estimated_completion_date) {
            $timelineData['estimated_days_remaining'] = $project->estimated_completion_date->diffInDays(now());
        }

        return view('admin.projects.show', compact(
            'project',
            'milestoneStats',
            'fileStats',
            'timelineData'
        ));
    }

    /**
     * Show the form for editing the specified project.
     */
    public function edit(Project $project)
    {
        $categories = ProjectCategory::where('is_active', true)->orderBy('sort_order')->get();
        $services = Service::where('is_active', true)->orderBy('sort_order')->get();
        $clients = User::role('client')->where('is_active', true)->orderBy('name')->get();

        $project->load([
            'images' => function ($query) {
                $query->orderBy('sort_order')->orderBy('created_at');
            },
            'files'
        ]);

        return view('admin.projects.edit', compact(
            'project',
            'categories',
            'services',
            'clients'
        ));
    }

    /**
     * Update the specified project.
     */
    public function update(UpdateProjectRequest $request, Project $project)
{
    $validated = $request->validated();
    $oldStatus = $project->status;

    // Generate slug if not provided
    if (empty($validated['slug'])) {
        $validated['slug'] = Str::slug($validated['title']);
    }

    // Set boolean values
    $validated['featured'] = $request->boolean('featured', false);
    if ($this->hasColumn('is_active')) {
        $validated['is_active'] = $request->boolean('is_active', true);
    }

    // Handle completion status
    if ($validated['status'] === 'completed' && $oldStatus !== 'completed') {
        if ($this->hasColumn('actual_completion_date') && empty($validated['actual_completion_date'])) {
            $validated['actual_completion_date'] = now();
        }
        if ($this->hasColumn('progress_percentage')) {
            $validated['progress_percentage'] = 100;
        }
        if ($this->hasColumn('completion_notification_sent_at')) {
            $validated['completion_notification_sent_at'] = now();
        }
    }

    if ($validated['status'] !== 'completed' && $oldStatus === 'completed') {
        if ($this->hasColumn('actual_completion_date')) {
            $validated['actual_completion_date'] = null;
        }
    }

    // Filter to only existing columns
    $validated = $this->filterToExistingColumns($validated);

    $project->update($validated);

    // Process temporary images from universal-uploader
    $this->processTempImages($project);

    // Handle traditional image uploads (fallback)
    if ($request->hasFile('images')) {
        foreach ($request->file('images') as $index => $image) {
            try {
                $path = $this->fileUploadService->uploadImage(
                    $image, 
                    'projects/' . $project->id,
                    null, 1200, 800
                );
                
                $project->images()->create([
                    'image_path' => $path,
                    'alt_text' => $request->input("image_alt_texts.{$index}") 
                        ?? $project->title . ' - Image ' . ($project->images()->count() + 1),
                    'is_featured' => $project->images()->count() === 0,
                    'sort_order' => $project->images()->max('sort_order') + 1,
                ]);
            } catch (\Exception $e) {
                \Log::error('Image upload failed: ' . $e->getMessage());
            }
        }
    }

    // Handle existing image alt text updates
    if ($request->has('existing_image_alt')) {
        foreach ($request->input('existing_image_alt', []) as $imageId => $altText) {
            $project->images()->where('id', $imageId)->update(['alt_text' => $altText]);
        }
    }

    // Send notifications
    if (class_exists('App\Facades\Notifications')) {
        if ($oldStatus !== $validated['status']) {
            Notifications::send('project.status_changed', [
                'project' => $project,
                'old_status' => $oldStatus,
                'new_status' => $validated['status']
            ]);
        } else {
            Notifications::send('project.updated', $project);
        }
    }

    $redirectRoute = match($request->input('action')) {
        'save_and_continue' => 'admin.projects.edit',
        'save_and_add_milestone' => 'admin.projects.milestones.create',
        default => 'admin.projects.show'
    };

    return redirect()->route($redirectRoute, $project)
        ->with('success', 'Project updated successfully!');
}

    /**
     * Remove the specified project.
     */
    public function destroy(Project $project)
    {
        // Check if project has related data
        $hasRelatedData = $project->milestones()->count() > 0 ||
            $project->files()->count() > 0 ||
            $project->testimonials()->count() > 0;

        if ($hasRelatedData) {
            return redirect()->back()
                ->with('error', 'Cannot delete project with related milestones, files, or testimonials. Please remove them first.');
        }

        // Delete associated images
        foreach ($project->images as $image) {
            if (Storage::disk('public')->exists($image->image_path)) {
                Storage::disk('public')->delete($image->image_path);
            }
            $image->delete();
        }

        // Delete associated files
        foreach ($project->files as $file) {
            if (Storage::disk('public')->exists($file->file_path)) {
                Storage::disk('public')->delete($file->file_path);
            }
            $file->delete();
        }

        // Send notification before deletion
        if (class_exists('App\Facades\Notifications')) {
            Notifications::send('project.deleted', $project);
        }

        // If project was created from quotation, update quotation (only if columns exist)
        if ($this->hasColumn('quotation_id') && $project->quotation_id) {
            $quotation = Quotation::find($project->quotation_id);
            if ($quotation) {
                $updateData = [];
                if (Schema::hasColumn('quotations', 'project_created')) {
                    $updateData['project_created'] = false;
                }
                if (Schema::hasColumn('quotations', 'project_created_at')) {
                    $updateData['project_created_at'] = null;
                }

                if (!empty($updateData)) {
                    $quotation->update($updateData);
                }
            }
        }

        $project->delete();

        return redirect()->route('admin.projects.index')
            ->with('success', 'Project deleted successfully!');
    }

    /**
     * Toggle project featured status.
     */
    public function toggleFeatured(Project $project)
    {
        $project->update(['featured' => !$project->featured]);

        return redirect()->back()
            ->with('success', 'Project featured status updated!');
    }

    /**
     * Update project display order.
     */
    public function updateOrder(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:projects,id',
            'items.*.order' => 'required|integer|min:0',
        ]);

        if ($this->hasColumn('display_order')) {
            DB::transaction(function () use ($request) {
                foreach ($request->items as $item) {
                    Project::where('id', $item['id'])
                        ->update(['display_order' => $item['order']]);
                }
            });

            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => 'Display order not supported']);
    }

    /**
     * Quick update project settings (AJAX).
     */
    public function quickUpdate(Request $request, Project $project)
    {
        // Basic validation for fields that might be updated
        $rules = [];
        if ($this->hasColumn('status')) {
            $rules['status'] = 'nullable|in:planning,in_progress,on_hold,completed,cancelled';
        }
        if ($this->hasColumn('priority')) {
            $rules['priority'] = 'nullable|in:low,normal,high,urgent';
        }
        if ($this->hasColumn('progress_percentage')) {
            $rules['progress_percentage'] = 'nullable|integer|min:0|max:100';
        }
        if ($this->hasColumn('is_active')) {
            $rules['is_active'] = 'boolean';
        }
        $rules['featured'] = 'boolean';

        $validated = $request->validate($rules);

        // Store old values for comparison
        $oldStatus = $project->status;

        // Remove empty values
        $validated = array_filter($validated, function ($value) {
            return $value !== null && $value !== '';
        });

        // Handle boolean fields
        if ($request->has('featured')) {
            $validated['featured'] = $request->boolean('featured');
        }
        if ($request->has('is_active') && $this->hasColumn('is_active')) {
            $validated['is_active'] = $request->boolean('is_active');
        }

        // Auto-set completion date if status changed to completed (only if columns exist)
        if (isset($validated['status']) && $validated['status'] === 'completed' && $oldStatus !== 'completed') {
            if ($this->hasColumn('actual_completion_date')) {
                $validated['actual_completion_date'] = now();
            }
            if ($this->hasColumn('progress_percentage') && !isset($validated['progress_percentage'])) {
                $validated['progress_percentage'] = 100;
            }
            if ($this->hasColumn('completion_notification_sent_at')) {
                $validated['completion_notification_sent_at'] = now();
            }
        }

        // Filter to only existing columns
        $validated = $this->filterToExistingColumns($validated);

        $project->update($validated);

        // Send notification if status changed
        if (class_exists('App\Facades\Notifications')) {
            if (isset($validated['status']) && $oldStatus !== $validated['status']) {
                Notifications::send('project.status_changed', [
                    'project' => $project,
                    'old_status' => $oldStatus,
                    'new_status' => $validated['status']
                ]);
            }
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Project updated successfully!',
                'project' => [
                    'id' => $project->id,
                    'status' => $project->status,
                    'formatted_status' => ucfirst(str_replace('_', ' ', $project->status)),
                    'status_color' => $this->getStatusColor($project->status),
                    'progress_percentage' => $project->progress_percentage ?? 0,
                    'priority' => $project->priority ?? 'normal',
                    'priority_color' => $this->getPriorityColor($project->priority ?? 'normal'),
                    'featured' => $project->featured,
                    'is_active' => $project->is_active ?? true,
                ]
            ]);
        }

        return redirect()->back()->with('success', 'Project updated successfully!');
    }

    /**
     * Convert project back to quotation.
     */
    public function convertToQuotation(Project $project)
    {
        if (!$this->hasColumn('quotation_id') || !$project->quotation_id) {
            return redirect()->back()
                ->with('error', 'This project was not created from a quotation.');
        }

        DB::transaction(function () use ($project) {
            // Update the original quotation
            $quotation = $project->quotation;
            if ($quotation) {
                $updateData = ['status' => 'pending'];

                if (Schema::hasColumn('quotations', 'project_created')) {
                    $updateData['project_created'] = false;
                }
                if (Schema::hasColumn('quotations', 'project_created_at')) {
                    $updateData['project_created_at'] = null;
                }
                if (Schema::hasColumn('quotations', 'admin_notes')) {
                    $updateData['admin_notes'] = ($quotation->admin_notes ? $quotation->admin_notes . "\n\n" : '')
                        . "Project converted back to quotation on " . now()->format('Y-m-d H:i:s');
                }

                $quotation->update($updateData);
            }

            // Update project status
            $projectUpdateData = ['status' => 'cancelled'];
            if ($this->hasColumn('is_active')) {
                $projectUpdateData['is_active'] = false;
            }

            $project->update($projectUpdateData);
        });

        return redirect()->route('admin.quotations.show', $project->quotation)
            ->with('success', 'Project has been converted back to quotation.');
    }

    /**
     * Create project from quotation (specific method).
     */
    public function createFromQuotation(Quotation $quotation)
    {
        // Verify quotation is approved
        if ($quotation->status !== 'approved') {
            return redirect()->route('admin.quotations.show', $quotation)
                ->with('error', 'Only approved quotations can be converted to projects.');
        }

        // Check if project already exists (only if quotation_id column exists)
        if ($this->hasColumn('quotation_id') && Schema::hasColumn('quotations', 'project_created') && $quotation->project_created) {
            $existingProject = Project::where('quotation_id', $quotation->id)->first();
            if ($existingProject) {
                return redirect()->route('admin.projects.show', $existingProject)
                    ->with('info', 'A project already exists for this quotation.');
            }
        }

        return redirect()->route('admin.projects.create', ['from_quotation' => $quotation->id]);
    }

    /**
     * Get projects statistics for dashboard.
     */
    public function getStatistics()
    {
        $stats = [
            'total_projects' => Project::count(),
            'completed_projects' => Project::where('status', 'completed')->count(),
            'in_progress_projects' => Project::where('status', 'in_progress')->count(),
            'planning_projects' => Project::where('status', 'planning')->count(),
            'featured_projects' => Project::where('featured', true)->count(),
            'projects_by_status' => Project::select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray(),
        ];

        // Add optional stats if columns exist
        if ($this->hasColumn('is_active')) {
            $stats['active_projects'] = Project::where('is_active', true)->count();
        }
        if ($this->hasColumn('priority')) {
            $stats['high_priority_projects'] = Project::whereIn('priority', ['high', 'urgent'])->count();
            $stats['projects_by_priority'] = Project::select('priority', DB::raw('count(*) as count'))
                ->groupBy('priority')
                ->pluck('count', 'priority')
                ->toArray();
        }
        if ($this->hasColumn('budget')) {
            $stats['total_budget'] = Project::whereNotNull('budget')->sum('budget');
        }
        if ($this->hasColumn('actual_cost')) {
            $stats['total_actual_cost'] = Project::whereNotNull('actual_cost')->sum('actual_cost');
        }
        if ($this->hasColumn('progress_percentage')) {
            $stats['average_progress'] = Project::where('is_active', true)->avg('progress_percentage');
        }

        // Overdue projects calculation
        $stats['overdue_projects'] = Project::where('end_date', '<', now())
            ->where('status', '!=', 'completed')
            ->count();

        $stats['due_soon_projects'] = Project::whereBetween('end_date', [now(), now()->addDays(7)])
            ->where('status', '!=', 'completed')
            ->count();

        // Category breakdown
        $stats['projects_by_category'] = Project::with('category')
            ->select('category_id', DB::raw('count(*) as count'))
            ->groupBy('category_id')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->category->name ?? 'Uncategorized' => $item->count];
            })
            ->toArray();

        // Recent projects
        $stats['recent_projects'] = Project::with(['client', 'category'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($project) {
                return [
                    'id' => $project->id,
                    'title' => $project->title,
                    'status' => $project->status,
                    'client_name' => $project->client?->name ?? ($this->hasColumn('client_name') ? $project->client_name : 'Unknown'),
                    'category' => $project->category?->name,
                    'progress' => $this->hasColumn('progress_percentage') ? ($project->progress_percentage ?? 0) : 0,
                    'created_at' => $project->created_at->format('M j, Y'),
                ];
            });

        return response()->json($stats);
    }


    /**
     * Search projects (AJAX).
     */
    public function search(Request $request)
    {
        $request->validate([
            'query' => 'required|string|min:2|max:255',
            'limit' => 'nullable|integer|min:1|max:50',
        ]);

        $limit = $request->get('limit', 10);
        $searchQuery = $request->get('query');

        $projects = Project::with(['client', 'category'])
            ->where(function ($q) use ($searchQuery) {
                $q->where('title', 'like', "%{$searchQuery}%")
                    ->orWhere('description', 'like', "%{$searchQuery}%")
                    ->orWhere('location', 'like', "%{$searchQuery}%");

                // Add additional search fields if they exist
                if ($this->hasColumn('short_description')) {
                    $q->orWhere('short_description', 'like', "%{$searchQuery}%");
                }
                if ($this->hasColumn('client_name')) {
                    $q->orWhere('client_name', 'like', "%{$searchQuery}%");
                }
            });

        // Only search active projects if column exists
        if ($this->hasColumn('is_active')) {
            $projects->where('is_active', true);
        }

        $projects = $projects->orderBy('title')
            ->limit($limit)
            ->get();

        return response()->json([
            'success' => true,
            'projects' => $projects->map(function ($project) {
                return [
                    'id' => $project->id,
                    'title' => $project->title,
                    'client_name' => $project->client?->name ?? ($this->hasColumn('client_name') ? $project->client_name : 'Unknown'),
                    'category' => $project->category?->name,
                    'status' => $project->status,
                    'progress' => $this->hasColumn('progress_percentage') ? ($project->progress_percentage ?? 0) : 0,
                    'url' => route('admin.projects.show', $project),
                ];
            }),
            'total' => $projects->count(),
        ]);
    }

    /**
     * Get project timeline data for charts.
     */
    public function getTimelineData(Project $project)
    {
        $milestones = $project->milestones()
            ->orderBy('due_date')
            ->orderBy('sort_order')
            ->get();

        $timelineData = [
            'project' => [
                'title' => $project->title,
                'start_date' => $project->start_date?->format('Y-m-d'),
                'end_date' => $project->end_date?->format('Y-m-d'),
                'progress' => $this->hasColumn('progress_percentage') ? ($project->progress_percentage ?? 0) : 0,
                'status' => $project->status,
            ],
            'milestones' => $milestones->map(function ($milestone) {
                return [
                    'id' => $milestone->id,
                    'title' => $milestone->title,
                    'due_date' => $milestone->due_date?->format('Y-m-d'),
                    'completion_date' => $milestone->completion_date?->format('Y-m-d'),
                    'status' => $milestone->status,
                    'progress' => $milestone->progress_percent ?? 0,
                    'color' => $this->getMilestoneColor($milestone->status),
                ];
            }),
        ];

        // Add estimated completion if column exists
        if ($this->hasColumn('estimated_completion_date') && $project->estimated_completion_date) {
            $timelineData['project']['estimated_completion'] = $project->estimated_completion_date->format('Y-m-d');
        }
        if ($this->hasColumn('actual_completion_date') && $project->actual_completion_date) {
            $timelineData['project']['actual_completion'] = $project->actual_completion_date->format('Y-m-d');
        }

        return response()->json($timelineData);
    }

    /**
     * Set project image as featured (AJAX)
     */
    public function setFeaturedImage(Project $project, $imageId)
    {
        // Reset all images to not featured
        $project->images()->update(['is_featured' => false]);

        // Set the specified image as featured
        $image = $project->images()->find($imageId);
        if ($image) {
            $image->update(['is_featured' => true]);

            return response()->json([
                'success' => true,
                'message' => 'Featured image updated successfully!'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Image not found'
        ], 404);
    }

    /**
     * Soft delete project (if soft deletes are enabled).
     */
    public function softDelete(Project $project)
    {
        if ($this->hasColumn('is_active')) {
            $project->update(['is_active' => false]);
        }

        // If using soft deletes trait
        if (method_exists($project, 'delete')) {
            $project->delete();
        }

        if (class_exists('App\Facades\Notifications')) {
            Notifications::send('project.archived', $project);
        }

        return redirect()->back()
            ->with('success', 'Project archived successfully!');
    }

    /**
     * Restore soft deleted project.
     */
    public function restore($id)
    {
        $project = Project::withTrashed()->findOrFail($id);
        $project->restore();

        if ($this->hasColumn('is_active')) {
            $project->update(['is_active' => true]);
        }

        if (class_exists('App\Facades\Notifications')) {
            Notifications::send('project.restored', $project);
        }

        return redirect()->route('admin.projects.show', $project)
            ->with('success', 'Project restored successfully!');
    }

    /**
     * Get status color for UI.
     */
    private function getStatusColor(string $status): string
    {
        return match ($status) {
            'planning' => '#6b7280',
            'in_progress' => '#3b82f6',
            'on_hold' => '#f59e0b',
            'completed' => '#10b981',
            'cancelled' => '#ef4444',
            default => '#6b7280'
        };
    }

    /**
     * Get priority color for UI.
     */
    private function getPriorityColor(string $priority): string
    {
        return match ($priority) {
            'low' => '#10b981',
            'normal' => '#6b7280',
            'high' => '#f59e0b',
            'urgent' => '#ef4444',
            default => '#6b7280'
        };
    }

    /**
     * Get milestone color for UI.
     */
    private function getMilestoneColor(string $status): string
    {
        return match ($status) {
            'pending' => '#6b7280',
            'in_progress' => '#3b82f6',
            'completed' => '#10b981',
            'delayed' => '#ef4444',
            default => '#6b7280'
        };
    }
    public function uploadTempImages(Request $request)
{
    try {
        // Log the incoming request for debugging
        \Log::info('Project images upload request received', [
            'files' => array_keys($request->allFiles()),
            'data' => $request->except(['files']),
            'content_type' => $request->header('Content-Type')
        ]);
        
        // Support multiple possible field names from Universal File Uploader
        $fieldName = null;
        $files = null;
        
        // Check different possible field names that Universal File Uploader might use
        $possibleFields = ['temp_images', 'project_images', 'images', 'files'];
        
        foreach ($possibleFields as $field) {
            if ($request->hasFile($field)) {
                $fieldName = $field;
                $uploadedFiles = $request->file($field);
                
                // Handle both single file and array of files
                if (is_array($uploadedFiles)) {
                    $files = $uploadedFiles;
                } else {
                    $files = [$uploadedFiles]; // Convert single file to array
                }
                
                \Log::info('Found files in field: ' . $field, [
                    'count' => count($files),
                    'is_array' => is_array($uploadedFiles)
                ]);
                break;
            }
        }
        
        if (!$files || empty($files)) {
            \Log::error('No files found in request', [
                'expected_fields' => $possibleFields,
                'actual_files' => array_keys($request->allFiles()),
                'all_input' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'No files uploaded. Expected one of: ' . implode(', ', $possibleFields)
            ], 422);
        }

        // Get category from request (for organization)
        $category = $request->input('category', 'gallery');
        
        // Create temp directory if it doesn't exist
        $tempDir = 'temp/projects';
        if (!Storage::disk('public')->exists($tempDir)) {
            Storage::disk('public')->makeDirectory($tempDir);
        }

        // Store temp file info in session
        $sessionKey = 'project_temp_files_' . session()->getId();
        $sessionData = session()->get($sessionKey, []);
        
        $uploadedFiles = [];
        $errors = [];

        // Process each file
        foreach ($files as $index => $file) {
            try {
                // Validate each file
                if (!$file || !$file->isValid()) {
                    $errors[] = "File " . ($index + 1) . " is invalid";
                    continue;
                }

                // Check MIME type
                $allowedMimes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp', 'image/gif'];
                if (!in_array($file->getMimeType(), $allowedMimes)) {
                    $errors[] = "File '{$file->getClientOriginalName()}' must be an image (JPEG, PNG, WebP, or GIF)";
                    continue;
                }

                // Check file size (5MB = 5120KB)
                if ($file->getSize() > 5120 * 1024) {
                    $errors[] = "File '{$file->getClientOriginalName()}' size cannot exceed 5MB";
                    continue;
                }

                // Generate unique filename
                $extension = $file->getClientOriginalExtension();
                $filename = 'project_temp_' . uniqid() . '_' . time() . '_' . $index . '.' . $extension;
                
                // Store in temporary directory
                $tempPath = $tempDir . '/' . $filename;
                $file->storeAs($tempDir, $filename, 'public');

                // Create session data for this file
                $tempId = uniqid() . '_' . time() . '_' . $index;
                $sessionData[$tempId] = [
                    'temp_id' => $tempId,
                    'temp_path' => $tempPath,
                    'original_name' => $file->getClientOriginalName(),
                    'file_size' => $file->getSize(),
                    'uploaded_at' => now(),
                    'category' => $category,
                    'image_type' => $category
                ];

                $uploadedFile = [
                    'temp_id' => $tempId,
                    'id' => $tempId,
                    'name' => ucfirst($category) . ' Image',
                    'file_name' => $file->getClientOriginalName(),
                    'category' => $category,
                    'type' => $category,
                    'url' => Storage::disk('public')->url($tempPath),
                    'size' => $this->formatFileSize($file->getSize()),
                    'temp_path' => $tempPath,
                    'is_temp' => true,
                    'created_at' => now()->format('M j, Y H:i')
                ];

                $uploadedFiles[] = $uploadedFile;

                \Log::info('Temp project image processed', [
                    'file' => $uploadedFile['file_name'],
                    'temp_id' => $tempId,
                    'size' => $uploadedFile['size']
                ]);

            } catch (\Exception $e) {
                $fileName = isset($file) ? $file->getClientOriginalName() : "File " . ($index + 1);
                $errors[] = "Failed to process '{$fileName}': " . $e->getMessage();
                \Log::error('Failed to process individual file', [
                    'file_name' => $fileName,
                    'error' => $e->getMessage()
                ]);
            }
        }
        
        // Update session with all uploaded files
        session()->put($sessionKey, $sessionData);

        // Determine response based on results
        if (empty($uploadedFiles) && !empty($errors)) {
            // All files failed
            return response()->json([
                'success' => false,
                'message' => 'All files failed to upload: ' . implode(', ', $errors)
            ], 422);
        } elseif (!empty($uploadedFiles) && !empty($errors)) {
            // Some files succeeded, some failed
            $successCount = count($uploadedFiles);
            $errorCount = count($errors);
            
            return response()->json([
                'success' => true,
                'message' => "{$successCount} file(s) uploaded successfully, {$errorCount} file(s) failed",
                'files' => $uploadedFiles,
                'errors' => $errors
            ]);
        } else {
            // All files succeeded
            $successCount = count($uploadedFiles);
            
            \Log::info('All temp project images uploaded successfully', [
                'count' => $successCount,
                'session_key' => $sessionKey
            ]);

            return response()->json([
                'success' => true,
                'message' => "{$successCount} image(s) uploaded successfully!",
                'files' => $uploadedFiles
            ]);
        }

    } catch (\Exception $e) {
        \Log::error('Temporary project image upload failed: ' . $e->getMessage(), [
            'trace' => $e->getTraceAsString()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Upload failed: ' . $e->getMessage()
        ], 500);
    }
}
public function deleteTempImage(Request $request)
{
    try {
        // Log the incoming request for debugging
        \Log::info('Delete temp project image request received', [
            'method' => $request->method(),
            'content_type' => $request->header('Content-Type'),
            'raw_content' => $request->getContent(),
            'all_input' => $request->all(),
            'json_input' => $request->json()->all() ?? null
        ]);

        // Handle both JSON and form data
        $input = [];
        
        if ($request->isJson()) {
            $input = $request->json()->all();
        } else {
            $input = $request->all();
        }
        
        // Try to get temp_id from various sources
        $tempId = $input['temp_id'] ?? 
                  $input['id'] ?? 
                  $request->input('temp_id') ?? 
                  $request->input('id');
        
        // If content is JSON string, try to decode it
        if (empty($tempId) && $request->getContent()) {
            $rawContent = $request->getContent();
            if (is_string($rawContent)) {
                $decoded = json_decode($rawContent, true);
                if (is_array($decoded)) {
                    $tempId = $decoded['temp_id'] ?? $decoded['id'] ?? null;
                }
            }
        }

        \Log::info('Processing delete request', [
            'temp_id' => $tempId,
            'input_keys' => array_keys($input)
        ]);

        if (empty($tempId)) {
            return response()->json([
                'success' => false,
                'message' => 'No temporary file ID provided',
                'debug' => [
                    'received_input' => $input,
                    'raw_content' => $request->getContent(),
                ]
            ], 422);
        }

        // Get session data
        $sessionKey = 'project_temp_files_' . session()->getId();
        $sessionData = session()->get($sessionKey, []);

        // Find the temp file
        $tempFileData = null;
        $foundKey = null;
        
        foreach ($sessionData as $key => $data) {
            if ($key === $tempId || ($data['temp_id'] ?? null) === $tempId) {
                $tempFileData = $data;
                $foundKey = $key;
                break;
            }
        }

        if (!$tempFileData) {
            \Log::warning('Temporary project file not found', [
                'temp_id' => $tempId,
                'available_files' => array_map(function($data) {
                    return [
                        'temp_id' => $data['temp_id'] ?? 'no_temp_id',
                        'file_name' => $data['original_name'] ?? 'unknown'
                    ];
                }, $sessionData)
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Temporary file not found',
                'debug' => [
                    'temp_id' => $tempId,
                    'available_files' => array_keys($sessionData)
                ]
            ], 404);
        }

        // Delete physical file
        if (Storage::disk('public')->exists($tempFileData['temp_path'])) {
            Storage::disk('public')->delete($tempFileData['temp_path']);
            \Log::info('Physical temp file deleted', ['path' => $tempFileData['temp_path']]);
        } else {
            \Log::warning('Physical temp file not found', ['path' => $tempFileData['temp_path']]);
        }

        // Remove from session
        unset($sessionData[$foundKey]);
        session()->put($sessionKey, $sessionData);

        \Log::info('Temporary project image deleted successfully', [
            'temp_id' => $tempId,
            'file_name' => $tempFileData['original_name'] ?? 'unknown',
            'session_id' => session()->getId(),
            'remaining_files' => count($sessionData)
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Image deleted successfully!'
        ]);

    } catch (\Exception $e) {
        \Log::error('Temporary project image deletion failed: ' . $e->getMessage(), [
            'request_data' => $request->all(),
            'raw_content' => $request->getContent(),
            'trace' => $e->getTraceAsString()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Failed to delete temporary image: ' . $e->getMessage()
        ], 500);
    }
}

    /**
     * Get current temp files
     */
    public function getTempFiles(Request $request)
    {
        try {
            $sessionKey = 'project_temp_files_' . session()->getId();
            $sessionData = session()->get($sessionKey, []);

            $files = [];
            foreach ($sessionData as $tempId => $data) {
                // Verify file still exists
                if (Storage::disk('public')->exists($data['temp_path'])) {
                    $files[] = [
                        'id' => $data['temp_id'],
                        'name' => 'Project Image',
                        'file_name' => $data['original_name'],
                        'category' => $data['category'] ?? 'gallery',
                        'type' => $data['image_type'] ?? 'gallery',
                        'url' => Storage::disk('public')->url($data['temp_path']),
                        'size' => $this->formatFileSize($data['file_size']),
                        'temp_id' => $data['temp_id'],
                        'is_temp' => true,
                        'created_at' => \Carbon\Carbon::parse($data['uploaded_at'])->format('M j, Y H:i')
                    ];
                } else {
                    // Clean up broken reference
                    unset($sessionData[$tempId]);
                }
            }

            // Update session with cleaned data
            session()->put($sessionKey, $sessionData);

            return response()->json([
                'success' => true,
                'files' => $files
            ]);

        } catch (\Exception $e) {
            \Log::error('Failed to get temp project files: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to get temporary files'
            ], 500);
        }
    }

    /**
     * Cleanup old temporary files (run via scheduler)
     */
    public function cleanupTempFiles()
    {
        try {
            $tempDir = 'temp/projects';
            $cutoffTime = now()->subHours(2);
            $deletedCount = 0;

            if (Storage::disk('public')->exists($tempDir)) {
                $files = Storage::disk('public')->files($tempDir);

                foreach ($files as $file) {
                    $lastModified = Storage::disk('public')->lastModified($file);

                    if ($lastModified < $cutoffTime->timestamp) {
                        Storage::disk('public')->delete($file);
                        $deletedCount++;
                    }
                }
            }

            \Log::info("Cleaned up {$deletedCount} temporary project files");

            return response()->json([
                'success' => true,
                'message' => "Cleaned up {$deletedCount} temporary files",
                'deleted_count' => $deletedCount
            ]);

        } catch (\Exception $e) {
            \Log::error('Temporary project files cleanup failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Cleanup failed: ' . $e->getMessage()
            ], 500);
        }
    }
    /**
     * Format file size helper method
     */
    protected function formatFileSize(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }

    /**
     * Cleanup session temp files
     */
    protected function cleanupSessionTempFiles(string $sessionId)
    {
        try {
            $tempDir = 'temp/projects';

            if (!Storage::disk('public')->exists($tempDir)) {
                return;
            }

            $files = Storage::disk('public')->files($tempDir);
            $deletedCount = 0;

            foreach ($files as $file) {
                // Check if file belongs to this session (contains session ID or is old)
                $filename = basename($file);
                if (
                    str_contains($filename, $sessionId) ||
                    Storage::disk('public')->lastModified($file) < now()->subHours(1)->timestamp
                ) {

                    Storage::disk('public')->delete($file);
                    $deletedCount++;
                }
            }

            if ($deletedCount > 0) {
                \Log::info("Cleaned up {$deletedCount} session temporary project files for session: {$sessionId}");
            }

        } catch (\Exception $e) {
            \Log::warning('Failed to cleanup session temp project files: ' . $e->getMessage());
        }
    }
    public function deleteImage(Project $project, ProjectImage $image)
{
    try {
        // Verify the image belongs to this project
        if ($image->project_id !== $project->id) {
            return response()->json([
                'success' => false,
                'message' => 'Image does not belong to this project'
            ], 404);
        }

        // Delete physical file
        if (Storage::disk('public')->exists($image->image_path)) {
            Storage::disk('public')->delete($image->image_path);
        }

        // If this was the featured image, make the next image featured
        if ($image->is_featured && $project->images()->count() > 1) {
            $nextImage = $project->images()
                ->where('id', '!=', $image->id)
                ->orderBy('sort_order')
                ->first();
            
            if ($nextImage) {
                $nextImage->update(['is_featured' => true]);
            }
        }

        // Delete the image record
        $image->delete();

        // Reorder remaining images
        $this->reorderProjectImages($project);

        return response()->json([
            'success' => true,
            'message' => 'Image deleted successfully!'
        ]);

    } catch (\Exception $e) {
        \Log::error('Project image deletion failed: ' . $e->getMessage(), [
            'project_id' => $project->id,
            'image_id' => $image->id
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Failed to delete image: ' . $e->getMessage()
        ], 500);
    }
}

/**
 * Reorder project images
 */
public function reorderImages(Request $request, Project $project)
{
    try {
        $request->validate([
            'image_ids' => 'required|array',
            'image_ids.*' => 'integer|exists:project_images,id',
        ]);

        $imageIds = $request->input('image_ids');
        
        // Verify all images belong to this project
        $projectImageIds = $project->images()->pluck('id')->toArray();
        $invalidIds = array_diff($imageIds, $projectImageIds);
        
        if (!empty($invalidIds)) {
            return response()->json([
                'success' => false,
                'message' => 'Some images do not belong to this project'
            ], 422);
        }

        // Update sort order
        foreach ($imageIds as $index => $imageId) {
            $project->images()->where('id', $imageId)->update([
                'sort_order' => $index + 1,
                'is_featured' => $index === 0 // First image is featured
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Images reordered successfully!'
        ]);

    } catch (\Exception $e) {
        \Log::error('Project images reorder failed: ' . $e->getMessage(), [
            'project_id' => $project->id
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Failed to reorder images: ' . $e->getMessage()
        ], 500);
    }
}

/**
 * Helper method to reorder project images after deletion
 */
private function reorderProjectImages(Project $project)
{
    $images = $project->images()->orderBy('sort_order')->get();
    
    foreach ($images as $index => $image) {
        $image->update([
            'sort_order' => $index + 1,
            'is_featured' => $index === 0
        ]);
    }
}

/**
 * Update project request validation rules to include temp_images handling
 */
protected function getValidationRules(): array
{
    return [
        'temp_images' => 'nullable|array',
        'temp_images.*' => 'nullable|string', // These will be temp IDs
        'images' => 'nullable|array|max:10',
        'images.*' => 'image|mimes:jpeg,jpg,png,webp|max:5120', // 5MB max
        'image_alt_texts' => 'nullable|array',
        'image_alt_texts.*' => 'nullable|string|max:255',
        'existing_image_alt' => 'nullable|array',
        'existing_image_alt.*' => 'nullable|string|max:255',
    ];
}

protected function processTempImages(Project $project): array
{
    $sessionKey = 'project_temp_files_' . session()->getId();
    $sessionData = session()->get($sessionKey, []);
    $processedImages = [];

    if (empty($sessionData)) {
        return $processedImages;
    }

    // Create project directory if it doesn't exist
    $projectDir = 'projects/' . $project->id;
    if (!Storage::disk('public')->exists($projectDir)) {
        Storage::disk('public')->makeDirectory($projectDir);
    }

    foreach ($sessionData as $tempId => $data) {
        try {
            if (!Storage::disk('public')->exists($data['temp_path'])) {
                \Log::warning('Temp file not found during processing', ['temp_path' => $data['temp_path']]);
                continue;
            }

            // Get file info
            $tempFilePath = $data['temp_path'];
            $originalName = $data['original_name'];
            $category = $data['category'] ?? 'gallery';
            
            // Generate permanent filename
            $extension = pathinfo($originalName, PATHINFO_EXTENSION);
            $permanentFileName = 'project_' . $project->id . '_' . uniqid() . '.' . $extension;
            $permanentPath = $projectDir . '/' . $permanentFileName;

            // Move file from temp to permanent location
            Storage::disk('public')->move($tempFilePath, $permanentPath);

            // Determine sort order and featured status
            $currentImageCount = $project->images()->count();
            $sortOrder = $currentImageCount + 1;
            $isFeatured = $currentImageCount === 0; // First image is featured

            // Create ProjectImage record with enhanced metadata
            $imageRecord = $project->images()->create([
                'image_path' => $permanentPath,
                'alt_text' => $this->generateAltText($project, $originalName, $category),
                'is_featured' => $isFeatured,
                'sort_order' => $sortOrder,
            ]);

            $processedImages[] = $imageRecord;

            \Log::info('Temp image processed successfully', [
                'temp_id' => $tempId,
                'permanent_path' => $permanentPath,
                'image_id' => $imageRecord->id,
                'category' => $category,
                'sort_order' => $sortOrder,
                'is_featured' => $isFeatured
            ]);

        } catch (\Exception $e) {
            \Log::error('Failed to process temp image: ' . $e->getMessage(), [
                'temp_id' => $tempId,
                'temp_path' => $data['temp_path'] ?? 'unknown',
                'project_id' => $project->id,
                'trace' => $e->getTraceAsString()
            ]);
            
            // Continue processing other images even if one fails
            continue;
        }
    }

    // Clear session data after processing
    session()->forget($sessionKey);

    // Send notification if images were processed
    if (count($processedImages) > 0) {
        \Log::info('Project images processed successfully', [
            'project_id' => $project->id,
            'processed_count' => count($processedImages),
            'total_images' => $project->images()->count()
        ]);
    }

    return $processedImages;
}

/**
 * Generate meaningful alt text for images
 */
protected function generateAltText(Project $project, string $originalName, string $category): string
{
    $baseName = pathinfo($originalName, PATHINFO_FILENAME);
    
    $categoryLabels = [
        'gallery' => 'Gallery Image',
        'before' => 'Before Photo',
        'during' => 'During Construction',
        'after' => 'Final Result',
        'detail' => 'Detail Shot'
    ];
    
    $categoryLabel = $categoryLabels[$category] ?? 'Project Image';
    
    return $project->title . ' - ' . $categoryLabel . ' (' . $baseName . ')';
}

/**
 * Get project statistics for dashboard
 */
public function getProjectStats(): array
{
    return [
        'total_projects' => Project::count(),
        'active_projects' => Project::where('status', 'in_progress')->count(),
        'completed_projects' => Project::where('status', 'completed')->count(),
        'featured_projects' => Project::where('featured', true)->count(),
        'total_images' => \DB::table('project_images')->count(),
        'recent_projects' => Project::latest()->take(5)->get(['id', 'title', 'status', 'created_at']),
    ];
}

/**
 * Bulk operations for projects
 */
public function bulkAction(Request $request)
{
    $request->validate([
        'action' => 'required|string|in:activate,deactivate,feature,unfeature,delete',
        'project_ids' => 'required|array|min:1',
        'project_ids.*' => 'integer|exists:projects,id',
    ]);

    $action = $request->input('action');
    $projectIds = $request->input('project_ids');
    
    try {
        switch ($action) {
            case 'activate':
                if ($this->hasColumn('is_active')) {
                    Project::whereIn('id', $projectIds)->update(['is_active' => true]);
                    $message = 'Projects activated successfully!';
                } else {
                    return response()->json(['success' => false, 'message' => 'Active status not supported'], 422);
                }
                break;
                
            case 'deactivate':
                if ($this->hasColumn('is_active')) {
                    Project::whereIn('id', $projectIds)->update(['is_active' => false]);
                    $message = 'Projects deactivated successfully!';
                } else {
                    return response()->json(['success' => false, 'message' => 'Active status not supported'], 422);
                }
                break;
                
            case 'feature':
                Project::whereIn('id', $projectIds)->update(['featured' => true]);
                $message = 'Projects marked as featured successfully!';
                break;
                
            case 'unfeature':
                Project::whereIn('id', $projectIds)->update(['featured' => false]);
                $message = 'Projects unmarked as featured successfully!';
                break;
                
            case 'delete':
                // Check for related data before deletion
                $projectsWithRelations = Project::whereIn('id', $projectIds)
                    ->whereHas('milestones')
                    ->orWhereHas('files')
                    ->orWhereHas('testimonials')
                    ->count();
                
                if ($projectsWithRelations > 0) {
                    return response()->json([
                        'success' => false, 
                        'message' => 'Some projects have related data and cannot be deleted. Please remove milestones, files, and testimonials first.'
                    ], 422);
                }
                
                // Delete images first
                $projects = Project::whereIn('id', $projectIds)->with('images')->get();
                foreach ($projects as $project) {
                    foreach ($project->images as $image) {
                        if (Storage::disk('public')->exists($image->image_path)) {
                            Storage::disk('public')->delete($image->image_path);
                        }
                    }
                }
                
                Project::whereIn('id', $projectIds)->delete();
                $message = 'Projects deleted successfully!';
                break;
                
            default:
                return response()->json(['success' => false, 'message' => 'Invalid action'], 422);
        }

        return response()->json(['success' => true, 'message' => $message]);

    } catch (\Exception $e) {
        \Log::error('Bulk action failed: ' . $e->getMessage(), [
            'action' => $action,
            'project_ids' => $projectIds
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Bulk action failed: ' . $e->getMessage()
        ], 500);
    }
}

/**
 * Export projects data
 */
public function export(Request $request)
{
    try {
        $format = $request->input('format', 'csv');
        $projects = Project::with(['category', 'client', 'service'])
            ->orderBy('created_at', 'desc')
            ->get();

        switch ($format) {
            case 'csv':
                return $this->exportCsv($projects);
            case 'json':
                return $this->exportJson($projects);
            default:
                return response()->json(['error' => 'Invalid format'], 422);
        }

    } catch (\Exception $e) {
        \Log::error('Project export failed: ' . $e->getMessage());
        return response()->json(['error' => 'Export failed'], 500);
    }
}

/**
 * Export projects as CSV
 */
private function exportCsv($projects)
{
    $filename = 'projects_export_' . date('Y-m-d_H-i-s') . '.csv';
    
    $headers = [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => "attachment; filename=\"{$filename}\"",
    ];

    $callback = function() use ($projects) {
        $file = fopen('php://output', 'w');
        
        // CSV headers
        fputcsv($file, [
            'ID', 'Title', 'Slug', 'Status', 'Category', 'Client', 'Service',
            'Location', 'Year', 'Featured', 'Start Date', 'End Date',
            'Images Count', 'Created At', 'Updated At'
        ]);

        // CSV data
        foreach ($projects as $project) {
            fputcsv($file, [
                $project->id,
                $project->title,
                $project->slug,
                $project->status,
                $project->category->name ?? '',
                $project->client->name ?? $project->client_name ?? '',
                $project->service->title ?? '',
                $project->location,
                $project->year,
                $project->featured ? 'Yes' : 'No',
                $project->start_date?->format('Y-m-d'),
                $project->end_date?->format('Y-m-d'),
                $project->images()->count(),
                $project->created_at->format('Y-m-d H:i:s'),
                $project->updated_at->format('Y-m-d H:i:s'),
            ]);
        }

        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}

/**
 * Export projects as JSON
 */
private function exportJson($projects)
{
    $filename = 'projects_export_' . date('Y-m-d_H-i-s') . '.json';
    
    $data = $projects->map(function($project) {
        return [
            'id' => $project->id,
            'title' => $project->title,
            'slug' => $project->slug,
            'description' => $project->description,
            'status' => $project->status,
            'category' => $project->category?->name,
            'client' => $project->client?->name ?? $project->client_name,
            'service' => $project->service?->title,
            'location' => $project->location,
            'year' => $project->year,
            'featured' => $project->featured,
            'start_date' => $project->start_date?->format('Y-m-d'),
            'end_date' => $project->end_date?->format('Y-m-d'),
            'challenge' => $project->challenge,
            'solution' => $project->solution,
            'result' => $project->result,
            'images_count' => $project->images()->count(),
            'created_at' => $project->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $project->updated_at->format('Y-m-d H:i:s'),
        ];
    });

    return response()->json($data)
        ->header('Content-Type', 'application/json')
        ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
}
}