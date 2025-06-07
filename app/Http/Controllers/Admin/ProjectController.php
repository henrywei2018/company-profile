<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectCategory;
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

        // Get summary statistics - only count fields that exist
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
    
    // Handle JSON fields properly - each field handles its own data
    if ($this->hasColumn('services_used') && array_key_exists('services_used', $validated)) {
        $validated['services_used'] = $this->processJsonField($validated['services_used']);
    }
    
    if ($this->hasColumn('technologies_used') && array_key_exists('technologies_used', $validated)) {
        $validated['technologies_used'] = $this->processJsonField($validated['technologies_used']);
    }
    
    if ($this->hasColumn('team_members') && array_key_exists('team_members', $validated)) {
        $validated['team_members'] = $this->processJsonField($validated['team_members']);
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

    // Handle image uploads
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

        // Calculate milestone statistics if milestones exist
        $milestoneStats = [
            'total' => $project->milestones->count(),
            'completed' => $project->milestones->where('status', 'completed')->count(),
            'in_progress' => $project->milestones->where('status', 'in_progress')->count(),
            'pending' => $project->milestones->where('status', 'pending')->count(),
            'delayed' => $project->milestones->where('status', 'delayed')->count(),
        ];

        // Add time-based calculations
        $milestoneStats['overdue'] = $project->milestones->filter(function ($milestone) {
            return $milestone->due_date && $milestone->due_date < now() && $milestone->status !== 'completed';
        })->count();

        $milestoneStats['due_soon'] = $project->milestones->filter(function ($milestone) {
            return $milestone->due_date &&
                $milestone->due_date >= now() &&
                $milestone->due_date <= now()->addDays(7) &&
                $milestone->status !== 'completed';
        })->count();

        // Calculate completion rate
        $milestoneStats['completion_rate'] = $milestoneStats['total'] > 0
            ? round(($milestoneStats['completed'] / $milestoneStats['total']) * 100, 1)
            : 0;

        // Get file statistics
        $fileStats = [
            'total_files' => $project->files->count(),
            'total_size' => $project->files->sum('file_size'),
            'public_files' => $project->files->where('is_public', true)->count(),
            'recent_files' => $project->files->take(5),
        ];

        // Get project timeline data
        $timelineData = [
            'days_since_start' => $project->start_date ? now()->diffInDays($project->start_date) : 0,
            'days_until_deadline' => $project->end_date ? $project->end_date->diffInDays(now()) : null,
            'is_overdue' => $project->end_date && $project->end_date < now() && $project->status !== 'completed',
        ];

        // Add estimated timeline if column exists
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
    
    // Properly decode JSON fields for editing
    $project->services_used = $this->decodeProjectJsonField($project->services_used);
    $project->technologies_used = $this->decodeProjectJsonField($project->technologies_used);
    $project->team_members = $this->decodeProjectJsonField($project->team_members);

    $project->load(['images' => function($query) {
        $query->orderBy('sort_order')->orderBy('created_at');
    }, 'files']);
    
    return view('admin.projects.edit', compact(
        'project', 
        'categories', 
        'services', 
        'clients',
    ));
}

    /**
     * Update the specified project.
     */
    public function update(UpdateProjectRequest $request, Project $project)
{
    $validated = $request->validated();

    // Store old status for comparison
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

    // Handle JSON fields properly - each field handles its own data
    if ($this->hasColumn('services_used') && array_key_exists('services_used', $validated)) {
        $validated['services_used'] = $this->processJsonField($validated['services_used']);
    }
    
    if ($this->hasColumn('technologies_used') && array_key_exists('technologies_used', $validated)) {
        $validated['technologies_used'] = $this->processJsonField($validated['technologies_used']);
    }
    
    if ($this->hasColumn('team_members') && array_key_exists('team_members', $validated)) {
        $validated['team_members'] = $this->processJsonField($validated['team_members']);
    }

    // Auto-set completion date if status changed to completed
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

    // Clear completion date if status changed from completed
    if ($validated['status'] !== 'completed' && $oldStatus === 'completed') {
        if ($this->hasColumn('actual_completion_date')) {
            $validated['actual_completion_date'] = null;
        }
    }

    // Filter to only existing columns
    $validated = $this->filterToExistingColumns($validated);

    $project->update($validated);

    // Handle new image uploads
    if ($request->hasFile('images')) {
        foreach ($request->file('images') as $index => $image) {
            try {
                $path = $this->fileUploadService->uploadImage(
                    $image, 
                    'projects/' . $project->id,
                    null,
                    1200,
                    800
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

    // Send notification if status changed
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
private function processJsonField($fieldData)
{
    // If null or empty, return null
    if (empty($fieldData)) {
        return null;
    }

    $cleanedArray = [];

    // Handle array input (from form)
    if (is_array($fieldData)) {
        $cleanedArray = $this->extractValidValues($fieldData);
    }
    // Handle string input (existing JSON or single value)
    elseif (is_string($fieldData)) {
        // Try to decode as JSON first
        $decoded = json_decode($fieldData, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            $cleanedArray = $this->extractValidValues($decoded);
        } else {
            // If not valid JSON, treat as single value
            $trimmed = trim($fieldData);
            if (!empty($trimmed)) {
                $cleanedArray = [$trimmed];
            }
        }
    }

    // Return JSON string or null
    return !empty($cleanedArray) ? json_encode(array_values($cleanedArray)) : null;
}

/**
 * Extract valid string values from mixed array data
 */
private function extractValidValues($data)
{
    $validValues = [];

    foreach ($data as $item) {
        // Handle string values
        if (is_string($item)) {
            $trimmed = trim($item);
            if (!empty($trimmed)) {
                $validValues[] = $trimmed;
            }
        }
        // Handle nested arrays (from double encoding issues)
        elseif (is_array($item)) {
            $nestedValues = $this->extractValidValues($item);
            $validValues = array_merge($validValues, $nestedValues);
        }
        // Handle other types by converting to string
        elseif (!is_null($item) && $item !== '') {
            $stringValue = trim((string) $item);
            if (!empty($stringValue)) {
                $validValues[] = $stringValue;
            }
        }
    }

    // Remove duplicates and return unique values
    return array_unique($validValues);
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
    private function decodeProjectJsonField($field)
{
    // If already an array, return as is
    if (is_array($field)) {
        return array_filter($field, function($item) {
            return is_string($item) && trim($item) !== '';
        });
    }
    
    // If null or empty, return empty array
    if (empty($field)) {
        return [];
    }
    
    // If string, try to decode
    if (is_string($field)) {
        $decoded = json_decode($field, true);
        
        // If successful decode and is array
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return $this->flattenJsonArray($decoded);
        }
        
        // If not valid JSON, treat as single value
        $trimmed = trim($field);
        return !empty($trimmed) ? [$trimmed] : [];
    }
    
    return [];
}
private function flattenJsonArray($array)
{
    $result = [];
    
    foreach ($array as $item) {
        if (is_string($item)) {
            $trimmed = trim($item);
            if ($trimmed !== '') {
                $result[] = $trimmed;
            }
        } elseif (is_array($item)) {
            // Recursively flatten nested arrays
            $nested = $this->flattenJsonArray($item);
            $result = array_merge($result, $nested);
        } elseif (!is_null($item)) {
            $stringValue = trim((string) $item);
            if ($stringValue !== '') {
                $result[] = $stringValue;
            }
        }
    }
    
    // Remove duplicates and return clean array
    return array_values(array_unique($result));
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
     * Export projects to CSV.
     */
    public function export(Request $request)
    {
        $query = Project::with(['client', 'category', 'service']);

        // Apply same filters as index
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }
        if ($request->filled('client')) {
            $query->where('client_id', $request->client);
        }
        if ($request->filled('priority') && $this->hasColumn('priority')) {
            $query->where('priority', $request->priority);
        }

        $projects = $query->orderBy('created_at', 'desc')->get();

        $csvData = [];
        $headers = [
            'ID',
            'Title',
            'Client',
            'Category',
            'Service',
            'Status',
            'Start Date',
            'End Date',
            'Location',
            'Featured',
            'Created At'
        ];

        // Add optional headers if columns exist
        if ($this->hasColumn('priority')) {
            $headers[] = 'Priority';
        }
        if ($this->hasColumn('progress_percentage')) {
            $headers[] = 'Progress %';
        }
        if ($this->hasColumn('budget')) {
            $headers[] = 'Budget';
        }
        if ($this->hasColumn('actual_cost')) {
            $headers[] = 'Actual Cost';
        }
        if ($this->hasColumn('is_active')) {
            $headers[] = 'Active';
        }
        if ($this->hasColumn('client_name')) {
            $headers[] = 'Client Name';
        }

        $csvData[] = $headers;

        foreach ($projects as $project) {
            $row = [
                $project->id,
                $project->title,
                $project->client?->name ?? '',
                $project->category?->name ?? '',
                $project->service?->title ?? '',
                ucfirst(str_replace('_', ' ', $project->status)),
                $project->start_date?->format('Y-m-d') ?? '',
                $project->end_date?->format('Y-m-d') ?? '',
                $project->location ?? '',
                $project->featured ? 'Yes' : 'No',
                $project->created_at->format('Y-m-d H:i:s'),
            ];

            // Add optional data if columns exist
            if ($this->hasColumn('priority')) {
                $row[] = ucfirst($project->priority ?? 'normal');
            }
            if ($this->hasColumn('progress_percentage')) {
                $row[] = ($project->progress_percentage ?? 0) . '%';
            }
            if ($this->hasColumn('budget')) {
                $row[] = $project->budget ? 'Rp ' . number_format($project->budget, 0, ',', '.') : '';
            }
            if ($this->hasColumn('actual_cost')) {
                $row[] = $project->actual_cost ? 'Rp ' . number_format($project->actual_cost, 0, ',', '.') : '';
            }
            if ($this->hasColumn('is_active')) {
                $row[] = ($project->is_active ?? true) ? 'Yes' : 'No';
            }
            if ($this->hasColumn('client_name')) {
                $row[] = $project->client_name ?? '';
            }

            $csvData[] = $row;
        }

        $filename = 'projects-export-' . now()->format('Y-m-d-H-i-s') . '.csv';

        $handle = fopen('php://temp', 'r+');
        foreach ($csvData as $row) {
            fputcsv($handle, $row);
        }
        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
    }

    /**
     * Bulk operations on projects.
     */
    public function bulkAction(Request $request)
    {
        $rules = [
            'action' => 'required|in:activate,deactivate,feature,unfeature,delete,change_status,change_priority',
            'project_ids' => 'required|array|min:1',
            'project_ids.*' => 'exists:projects,id',
        ];

        // Add conditional rules only if columns exist
        if ($this->hasColumn('status')) {
            $rules['status'] = 'required_if:action,change_status|in:planning,in_progress,on_hold,completed,cancelled';
        }
        if ($this->hasColumn('priority')) {
            $rules['priority'] = 'required_if:action,change_priority|in:low,normal,high,urgent';
        }

        $validated = $request->validate($rules);
        $projects = Project::whereIn('id', $validated['project_ids'])->get();
        $affectedCount = 0;

        DB::transaction(function () use ($validated, $projects, &$affectedCount) {
            foreach ($projects as $project) {
                switch ($validated['action']) {
                    case 'activate':
                        if ($this->hasColumn('is_active')) {
                            $project->update(['is_active' => true]);
                            $affectedCount++;
                        }
                        break;
                    case 'deactivate':
                        if ($this->hasColumn('is_active')) {
                            $project->update(['is_active' => false]);
                            $affectedCount++;
                        }
                        break;
                    case 'feature':
                        $project->update(['featured' => true]);
                        $affectedCount++;
                        break;
                    case 'unfeature':
                        $project->update(['featured' => false]);
                        $affectedCount++;
                        break;
                    case 'change_status':
                        if ($this->hasColumn('status') && isset($validated['status'])) {
                            $oldStatus = $project->status;
                            $updateData = ['status' => $validated['status']];

                            if ($validated['status'] === 'completed' && $oldStatus !== 'completed') {
                                if ($this->hasColumn('actual_completion_date')) {
                                    $updateData['actual_completion_date'] = now();
                                }
                                if ($this->hasColumn('progress_percentage')) {
                                    $updateData['progress_percentage'] = 100;
                                }
                            }

                            $project->update($updateData);
                            $affectedCount++;
                        }
                        break;
                    case 'change_priority':
                        if ($this->hasColumn('priority') && isset($validated['priority'])) {
                            $project->update(['priority' => $validated['priority']]);
                            $affectedCount++;
                        }
                        break;
                    case 'delete':
                        $project->delete();
                        $affectedCount++;
                        break;
                }
            }
        });

        $actionLabels = [
            'activate' => 'activated',
            'deactivate' => 'deactivated',
            'feature' => 'featured',
            'unfeature' => 'unfeatured',
            'change_status' => 'status updated',
            'change_priority' => 'priority updated',
            'delete' => 'deleted',
        ];

        $message = "{$affectedCount} project(s) " . $actionLabels[$validated['action']] . " successfully!";

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'affected_count' => $affectedCount
            ]);
        }

        return redirect()->back()->with('success', $message);
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
}