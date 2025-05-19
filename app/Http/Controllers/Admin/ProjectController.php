<?php
// File: app/Http/Controllers/Admin/ProjectController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectImage;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProjectController extends Controller
{
    /**
     * Display a listing of the projects.
     */
    public function index(Request $request)
    {
        $query = Project::with('client', 'testimonial', 'images')
            ->when($request->filled('category'), function ($query) use ($request) {
                return $query->where('category', $request->category);
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                return $query->where('status', $request->status);
            })
            ->when($request->filled('year'), function ($query) use ($request) {
                // Filter by project year field
                return $query->where('year', $request->year);
            })
            ->when($request->filled('search'), function ($query) use ($request) {
                return $query->where(function ($q) use ($request) {
                    $q->where('title', 'like', "%{$request->search}%")
                        ->orWhere('description', 'like', "%{$request->search}%")
                        ->orWhere('location', 'like', "%{$request->search}%");
                });
            });

        // Apply sorting if provided
        if ($request->filled('sort')) {
            $direction = $request->input('direction', 'asc');
            $query->orderBy($request->sort, $direction);
        } else {
            // Default sorting
            $query->latest();
        }

        $projects = $query->paginate(10)->withQueryString();
        // Apply filters and pagination
        $projects = Project::with('images')
            ->filter($request->only(['category', 'year', 'status', 'search']))
            ->latest()
            ->paginate(10);
        
        // Get categories for filter dropdown
        $categories = Project::select('category')
            ->distinct()
            ->pluck('category')
            ->filter();
        
        // Get years for filter dropdown
        $years = Project::select('year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->filter();
        
        return view('admin.projects.index', compact('projects', 'categories', 'years'));
    }

    /**
     * Show the form for creating a new project.
     */
    public function create()
    {
        return view('admin.projects.create');
    }

    /**
     * Store a newly created project.
     */
    public function store(StoreProjectRequest $request)
    {
        // Create the project
        $project = Project::create($request->validated());
        
        // Handle images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('projects', 'public');
                
                ProjectImage::create([
                    'project_id' => $project->id,
                    'image_path' => $path,
                    'alt_text' => $request->alt_text[$index] ?? $project->title,
                    'is_featured' => $index === 0, // First image is featured
                    'sort_order' => $index + 1,
                ]);
            }
        }
        
        // Handle SEO
        if ($request->filled('seo_title') || $request->filled('seo_description') || $request->filled('seo_keywords')) {
            $project->updateSeo([
                'title' => $request->seo_title,
                'description' => $request->seo_description,
                'keywords' => $request->seo_keywords,
            ]);
        }
        
        return redirect()->route('admin.projects.index')
            ->with('success', 'Project created successfully!');
    }

    /**
     * Display the specified project.
     */
    public function show(Project $project)
    {
        $project->load('images', 'client', 'testimonial');
        
        return view('admin.projects.show', compact('project'));
    }

    /**
     * Show the form for editing the specified project.
     */
    public function edit(Project $project)
    {
        $project->load('images', 'seo');
        
        return view('admin.projects.edit', compact('project'));
    }

    /**
     * Update the specified project.
     */
    public function update(UpdateProjectRequest $request, Project $project)
    {
        // Update the project
        $project->update($request->validated());
        
        // Handle existing images
        if ($request->has('existing_images')) {
            foreach ($project->images as $image) {
                if (!in_array($image->id, $request->existing_images)) {
                    // Delete image file
                    Storage::disk('public')->delete($image->image_path);
                    
                    // Delete image record
                    $image->delete();
                } else {
                    // Update alt text and featured status
                    $index = array_search($image->id, $request->existing_images);
                    $image->update([
                        'alt_text' => $request->existing_alt_text[$index] ?? $project->title,
                        'is_featured' => $request->featured_image == $image->id,
                        'sort_order' => $index + 1,
                    ]);
                }
            }
        }
        
        // Handle new images
        if ($request->hasFile('new_images')) {
            $existingCount = $project->images->count();
            
            foreach ($request->file('new_images') as $index => $image) {
                $path = $image->store('projects', 'public');
                
                ProjectImage::create([
                    'project_id' => $project->id,
                    'image_path' => $path,
                    'alt_text' => $request->new_alt_text[$index] ?? $project->title,
                    'is_featured' => false, // New images are not featured by default
                    'sort_order' => $existingCount + $index + 1,
                ]);
            }
        }
        
        // Handle SEO
        $project->updateSeo([
            'title' => $request->seo_title,
            'description' => $request->seo_description,
            'keywords' => $request->seo_keywords,
        ]);
        
        return redirect()->route('admin.projects.index')
            ->with('success', 'Project updated successfully!');
    }

    /**
     * Remove the specified project.
     */
    public function destroy(Project $project)
    {
        // Delete associated images
        foreach ($project->images as $image) {
            Storage::disk('public')->delete($image->image_path);
        }
        
        // Delete the project (will cascade delete images and SEO due to relationships)
        $project->delete();
        
        return redirect()->route('admin.projects.index')
            ->with('success', 'Project deleted successfully!');
    }
    
    /**
     * Update feature status
     */
    public function toggleFeatured(Project $project)
    {
        $project->update([
            'featured' => !$project->featured
        ]);
        
        return redirect()->back()
            ->with('success', 'Project featured status updated!');
    }
}