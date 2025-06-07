<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\Service;
use App\Models\User;
use App\Models\Quotation;
use App\Services\FileUploadService;
use App\Services\ProjectService;
use App\Facades\Notifications;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

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
     * Display a listing of projects.
     */
    public function index(Request $request)
    {
        $query = Project::with(['client', 'category', 'service'])
            ->when($request->filled('status'), fn($q) => $q->where('status', $request->status))
            ->when($request->filled('category'), fn($q) => $q->where('category_id', $request->category))
            ->when($request->filled('client'), fn($q) => $q->where('client_id', $request->client))
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = $request->search;
                return $q->where(function ($query) use ($search) {
                    $query->where('title', 'like', "%{$search}%")
                          ->orWhere('description', 'like', "%{$search}%")
                          ->orWhere('location', 'like', "%{$search}%");
                });
            });

        $projects = $query->orderBy('created_at', 'desc')->paginate(15);

        $categories = ProjectCategory::all();
        $clients = User::role('client')->get();
        $years = Project::selectRaw('YEAR(created_at) as year')
                    ->distinct()
                    ->orderByDesc('year')
                    ->pluck('year', 'year')
                    ->toArray();

        return view('admin.projects.index', compact('projects', 'categories', 'clients', 'years'));
    }

    /**
     * Show the form for creating a new project.
     */
    public function create(Request $request)
    {
        $categories = ProjectCategory::active()->get();
        $services = Service::active()->get();
        $clients = User::role('client')->get();
        
        // Check if creating from quotation
        $quotation = null;
        if ($request->has('from_quotation')) {
            $quotation = Quotation::findOrFail($request->from_quotation);
            
            // Verify quotation is approved
            if ($quotation->status !== 'approved') {
                return redirect()->route('admin.quotations.index')
                    ->with('error', 'Only approved quotations can be converted to projects.');
            }
            
            // Check if project already exists for this quotation
            if ($quotation->hasProject()) {
                return redirect()->route('admin.projects.show', $quotation->project)
                    ->with('info', 'A project already exists for this quotation.');
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
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:projects,slug',
            'description' => 'nullable|string',
            'short_description' => 'nullable|string|max:500',
            'client_id' => 'nullable|exists:users,id',
            'quotation_id' => 'nullable|exists:quotations,id',
            'category_id' => 'nullable|exists:project_categories,id',
            'service_id' => 'nullable|exists:services,id',
            'status' => 'required|in:planning,in_progress,on_hold,completed,cancelled',
            'priority' => 'required|in:low,normal,high,urgent',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'estimated_completion_date' => 'nullable|date',
            'budget' => 'nullable|numeric|min:0',
            'progress_percentage' => 'nullable|integer|min:0|max:100',
            'featured' => 'boolean',
            'is_active' => 'boolean',
            'location' => 'nullable|string|max:255',
            'challenge' => 'nullable|string',
            'solution' => 'nullable|string',
            'results' => 'nullable|string',
            'technologies_used' => 'nullable|array',
            'team_members' => 'nullable|array',
            'client_feedback' => 'nullable|string',
            'lessons_learned' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string',
            'images.*' => 'nullable|image|max:2048',
            'services_used' => 'nullable|array',
        ]);

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

        // Set defaults and ensure proper data types
        $validated['featured'] = $request->boolean('featured');
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['progress_percentage'] = $validated['progress_percentage'] ?? 0;
        
        // Handle array fields properly
        $validated['technologies_used'] = $validated['technologies_used'] ?? [];
        $validated['team_members'] = $validated['team_members'] ?? [];
        $validated['services_used'] = $validated['services_used'] ?? [];

        $project = Project::create($validated);

        // Handle image uploads
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $path = $this->fileUploadService->uploadImage(
                    $image, 
                    'projects/' . $project->id,
                    null,
                    1200, // max width
                    800   // max height
                );
                
                $project->images()->create([
                    'image_path' => $path,
                    'alt_text' => $request->input("alt_texts.{$index}", $project->title),
                    'is_featured' => $index === 0,
                    'sort_order' => $index + 1,
                ]);
            }
        }

        // If created from quotation, update quotation status
        if ($project->quotation_id) {
            $quotation = Quotation::find($project->quotation_id);
            if ($quotation) {
                $quotation->update([
                    'status' => 'approved', // Ensure it stays approved
                    'admin_notes' => ($quotation->admin_notes ? $quotation->admin_notes . "\n\n" : '') 
                        . "Project created: " . $project->title . " on " . now()->format('Y-m-d H:i:s')
                ]);
            }
        }

        // Send notification
        Notifications::send('project.created', $project);

        return redirect()->route('admin.projects.show', $project)
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
            'quotation',
            'images', 
            'files', 
            'milestones' => function($query) {
                $query->orderBy('due_date')->orderBy('sort_order');
            },
            'testimonials',
            'messages'
        ]);

        // Calculate milestone statistics
        $milestoneStats = [
            'total' => $project->milestones->count(),
            'completed' => $project->milestones->where('status', 'completed')->count(),
            'in_progress' => $project->milestones->where('status', 'in_progress')->count(),
            'pending' => $project->milestones->where('status', 'pending')->count(),
            'delayed' => $project->milestones->where('status', 'delayed')->count(),
            'overdue' => $project->milestones->filter(function($milestone) {
                return $milestone->isOverdue();
            })->count(),
            'due_soon' => $project->milestones->filter(function($milestone) {
                return $milestone->isDueSoon();
            })->count(),
        ];
        
        return view('admin.projects.show', compact('project', 'milestoneStats'));
    }

    /**
     * Show the form for editing the specified project.
     */
    public function edit(Project $project)
    {
        $categories = ProjectCategory::active()->get();
        $services = Service::active()->get();
        $clients = User::role('client')->get();
        
        $project->load(['images', 'files']);
        
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
    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:projects,slug,' . $project->id,
            'description' => 'nullable|string',
            'short_description' => 'nullable|string|max:500',
            'client_id' => 'nullable|exists:users,id',
            'category_id' => 'nullable|exists:project_categories,id',
            'service_id' => 'nullable|exists:services,id',
            'status' => 'required|in:planning,in_progress,on_hold,completed,cancelled',
            'priority' => 'required|in:low,normal,high,urgent',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'estimated_completion_date' => 'nullable|date',
            'actual_completion_date' => 'nullable|date',
            'budget' => 'nullable|numeric|min:0',
            'actual_cost' => 'nullable|numeric|min:0',
            'progress_percentage' => 'nullable|integer|min:0|max:100',
            'featured' => 'boolean',
            'is_active' => 'boolean',
            'location' => 'nullable|string|max:255',
            'challenge' => 'nullable|string',
            'solution' => 'nullable|string',
            'results' => 'nullable|string',
            'technologies_used' => 'nullable|array',
            'team_members' => 'nullable|array',
            'client_feedback' => 'nullable|string',
            'lessons_learned' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string',
            'images.*' => 'nullable|image|max:2048',
            'services_used' => 'nullable|array',
        ]);

        // Generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        // Set boolean values
        $validated['featured'] = $request->boolean('featured');
        $validated['is_active'] = $request->boolean('is_active');

        // Handle array fields properly
        $validated['technologies_used'] = $validated['technologies_used'] ?? [];
        $validated['team_members'] = $validated['team_members'] ?? [];
        $validated['services_used'] = $validated['services_used'] ?? [];

        // Auto-set completion date if status changed to completed
        if ($validated['status'] === 'completed' && $project->status !== 'completed') {
            $validated['actual_completion_date'] = now();
            $validated['progress_percentage'] = 100;
        }

        $project->update($validated);

        // Handle new image uploads
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $path = $this->fileUploadService->uploadImage(
                    $image, 
                    'projects/' . $project->id,
                    null,
                    1200,
                    800
                );
                
                $project->images()->create([
                    'image_path' => $path,
                    'alt_text' => $request->input("alt_texts.{$index}", $project->title),
                    'is_featured' => $project->images()->count() === 0,
                    'sort_order' => $project->images()->max('sort_order') + 1,
                ]);
            }
        }

        // Send notification
        Notifications::send('project.updated', $project);

        $redirectRoute = match($request->input('action')) {
            'save_and_continue' => 'admin.projects.edit',
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
        Notifications::send('project.deleted', $project);

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

        foreach ($request->items as $item) {
            Project::where('id', $item['id'])
                ->update(['display_order' => $item['order']]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Quick update project settings (AJAX).
     */
    public function quickUpdate(Request $request, Project $project)
    {
        $validated = $request->validate([
            'status' => 'nullable|in:planning,in_progress,on_hold,completed,cancelled',
            'priority' => 'nullable|in:low,normal,high,urgent',
            'progress_percentage' => 'nullable|integer|min:0|max:100',
            'featured' => 'boolean',
            'is_active' => 'boolean',
        ]);

        // Remove empty values
        $validated = array_filter($validated, function($value) {
            return $value !== null && $value !== '';
        });

        // Handle boolean fields
        if ($request->has('featured')) {
            $validated['featured'] = $request->boolean('featured');
        }
        if ($request->has('is_active')) {
            $validated['is_active'] = $request->boolean('is_active');
        }

        // Auto-set completion date if status changed to completed
        if (isset($validated['status']) && $validated['status'] === 'completed' && $project->status !== 'completed') {
            $validated['actual_completion_date'] = now();
            if (!isset($validated['progress_percentage'])) {
                $validated['progress_percentage'] = 100;
            }
        }

        $project->update($validated);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Project updated successfully!',
                'project' => [
                    'status' => $project->status,
                    'formatted_status' => $project->formatted_status,
                    'status_color' => $project->status_color,
                    'progress_percentage' => $project->progress_percentage,
                    'priority' => $project->priority,
                    'featured' => $project->featured,
                    'is_active' => $project->is_active,
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
        if (!$project->quotation_id) {
            return redirect()->back()
                ->with('error', 'This project was not created from a quotation.');
        }

        DB::transaction(function () use ($project) {
            // Update the original quotation
            $quotation = $project->quotation;
            if ($quotation) {
                $quotation->update([
                    'status' => 'pending',
                    'admin_notes' => ($quotation->admin_notes ? $quotation->admin_notes . "\n\n" : '') 
                        . "Project converted back to quotation on " . now()->format('Y-m-d H:i:s')
                ]);
            }

            // Optionally keep project data or delete it
            // For now, we'll just update its status
            $project->update([
                'status' => 'cancelled',
                'is_active' => false
            ]);
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

        // Check if project already exists
        if ($quotation->hasProject()) {
            return redirect()->route('admin.projects.show', $quotation->project)
                ->with('info', 'A project already exists for this quotation.');
        }

        return redirect()->route('admin.projects.create', ['from_quotation' => $quotation->id]);
    }
}