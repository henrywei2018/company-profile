<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\Service;
use App\Models\User;
use App\Models\Quotation;
use App\Services\FileUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ProjectController extends Controller
{
    protected $fileUploadService;

    public function __construct(FileUploadService $fileUploadService)
    {
        $this->fileUploadService = $fileUploadService;
    }

    /**
     * Display a listing of projects.
     */
    public function index(Request $request)
    {
        $query = Project::with(['client', 'category', 'service'])
            ->when($request->filled('status'), function ($q) use ($request) {
                return $q->where('status', $request->status);
            })
            ->when($request->filled('category'), function ($q) use ($request) {
                return $q->where('project_category_id', $request->category);
            })
            ->when($request->filled('client'), function ($q) use ($request) {
                return $q->where('client_id', $request->client);
            })
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
        
        return view('admin.projects.index', compact('projects', 'categories', 'clients'));
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
            'project_category_id' => 'nullable|exists:project_categories,id',
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

        // Set defaults
        $validated['featured'] = $request->boolean('featured');
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['progress_percentage'] = $validated['progress_percentage'] ?? 0;

        $project = Project::create($validated);

        // Handle image uploads
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $this->fileUploadService->uploadImage(
                    $image, 
                    'projects/' . $project->id,
                    null,
                    1200, // max width
                    800   // max height
                );
                
                $project->images()->create([
                    'file_path' => $path,
                    'file_name' => $image->getClientOriginalName(),
                    'file_size' => $image->getSize(),
                    'mime_type' => $image->getMimeType(),
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
            'attachments', 
            'testimonials',
            'updates'
        ]);
        
        return view('admin.projects.show', compact('project'));
    }

    /**
     * Show the form for editing the specified project.
     */
    public function edit(Project $project)
    {
        $categories = ProjectCategory::active()->get();
        $services = Service::active()->get();
        $clients = User::role('client')->get();
        
        $project->load(['images', 'attachments']);
        
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
            'project_category_id' => 'nullable|exists:project_categories,id',
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
        ]);

        // Generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        // Set boolean values
        $validated['featured'] = $request->boolean('featured');
        $validated['is_active'] = $request->boolean('is_active');

        // Auto-set completion date if status changed to completed
        if ($validated['status'] === 'completed' && $project->status !== 'completed') {
            $validated['actual_completion_date'] = now();
            $validated['progress_percentage'] = 100;
        }

        $project->update($validated);

        // Handle new image uploads
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $this->fileUploadService->uploadImage(
                    $image, 
                    'projects/' . $project->id,
                    null,
                    1200,
                    800
                );
                
                $project->images()->create([
                    'file_path' => $path,
                    'file_name' => $image->getClientOriginalName(),
                    'file_size' => $image->getSize(),
                    'mime_type' => $image->getMimeType(),
                ]);
            }
        }

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
            if (Storage::disk('public')->exists($image->file_path)) {
                Storage::disk('public')->delete($image->file_path);
            }
            $image->delete();
        }

        // Delete associated attachments
        foreach ($project->attachments as $attachment) {
            if (Storage::disk('public')->exists($attachment->file_path)) {
                Storage::disk('public')->delete($attachment->file_path);
            }
            $attachment->delete();
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

        foreach ($request->items as $item) {
            Project::where('id', $item['id'])
                ->update(['display_order' => $item['order']]);
        }

        return response()->json(['success' => true]);
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