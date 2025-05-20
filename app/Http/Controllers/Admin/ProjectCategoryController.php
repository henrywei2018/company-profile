<?php
// File: app/Http/Controllers/Admin/ProjectCategoryController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProjectCategory;
use App\Http\Requests\StoreProjectCategoryRequest;
use App\Http\Requests\UpdateProjectCategoryRequest;
use App\Repositories\Interfaces\ProjectCategoryRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ProjectCategoryController extends Controller
{
    protected $projectCategoryRepository;

    /**
     * Create a new controller instance.
     *
     * @param ProjectCategoryRepositoryInterface $projectCategoryRepository
     */
    public function __construct(ProjectCategoryRepositoryInterface $projectCategoryRepository)
    {
        $this->projectCategoryRepository = $projectCategoryRepository;
    }
    /**
     * Display a listing of the categories.
     */
    public function index()
    {
        $categories = ProjectCategory::withCount('projects')->paginate(10);
        
        // Get notification counts for header
        $unreadMessages = \App\Models\Message::unread()->count();
        $pendingQuotations = \App\Models\Quotation::pending()->count();
        
        return view('admin.project-categories.index', compact('categories', 'unreadMessages', 'pendingQuotations'));
    }

    /**
     * Show the form for creating a new category.
     */
    public function create()
    {
        // Get notification counts for header
        $unreadMessages = \App\Models\Message::unread()->count();
        $pendingQuotations = \App\Models\Quotation::pending()->count();
        
        return view('admin.project-categories.create', compact('unreadMessages', 'pendingQuotations'));
    }

    /**
     * Store a newly created category.
     */
    public function store(StoreProjectCategoryRequest $request)
    {
        $validated = $request->validated();
        
        // Generate slug from name
        $validated['slug'] = Str::slug($validated['name']);
        
        // Create category
        $category = $this->projectCategoryRepository->create($validated);
        
        // Handle icon
        if ($request->hasFile('icon')) {
            $path = $request->file('icon')->store('project-categories', 'public');
            $category->update(['icon' => $path]);
        }
        
        return redirect()->route('admin.project-categories.index')
            ->with('success', 'Category created successfully!');
    }

    /**
     * Show the form for editing the specified category.
     */
    public function edit(ProjectCategory $projectCategory)
    {
        // Get notification counts for header
        $unreadMessages = \App\Models\Message::unread()->count();
        $pendingQuotations = \App\Models\Quotation::pending()->count();
        
        return view('admin.project-categories.edit', compact('projectCategory', 'unreadMessages', 'pendingQuotations'));
    }

    /**
     * Update the specified category.
     */
    public function update(UpdateProjectCategoryRequest $request, ProjectCategory $projectCategory)
    {
        $validated = $request->validated();
        
        // Generate slug from name
        $validated['slug'] = Str::slug($validated['name']);
        
        // Update category
        $this->projectCategoryRepository->update($projectCategory, $validated);
        
        // Handle icon
        if ($request->hasFile('icon')) {
            // Delete old icon if exists
            if ($projectCategory->icon) {
                Storage::disk('public')->delete($projectCategory->icon);
            }
            
            // Store new icon
            $path = $request->file('icon')->store('project-categories', 'public');
            $projectCategory->update(['icon' => $path]);
        }
        
        return redirect()->route('admin.project-categories.index')
            ->with('success', 'Category updated successfully!');
    }

    /**
     * Remove the specified category.
     */
    public function destroy(ProjectCategory $projectCategory)
    {
        // Check if category has projects
        if ($projectCategory->projects()->count() > 0) {
            return redirect()->route('admin.project-categories.index')
                ->with('error', 'Cannot delete category with associated projects!');
        }
        
        // Delete icon
        if ($projectCategory->icon) {
            Storage::disk('public')->delete($projectCategory->icon);
        }
        
        // Delete category
        $this->projectCategoryRepository->delete($projectCategory);
        
        return redirect()->route('admin.project-categories.index')
            ->with('success', 'Category deleted successfully!');
    }
    
    /**
     * Update active status
     */
    public function toggleActive(ProjectCategory $projectCategory)
    {
        $projectCategory->update([
            'is_active' => !$projectCategory->is_active
        ]);
        
        return redirect()->back()
            ->with('success', 'Category status updated!');
    }
}