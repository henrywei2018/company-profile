<?php
// File: app/Http/Controllers/Client/ProjectController.php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectFile;
use App\Models\Testimonial;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    /**
     * Display a listing of the client's projects.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        
        $projects = Project::where('client_id', $user->id)
            ->when($request->filled('status'), function ($query) use ($request) {
                return $query->where('status', $request->status);
            })
            ->when($request->filled('search'), function ($query) use ($request) {
                return $query->where(function ($q) use ($request) {
                    $q->where('title', 'like', "%{$request->search}%")
                      ->orWhere('description', 'like', "%{$request->search}%");
                });
            })
            ->latest()
            ->paginate(10);
        
        $statuses = ['planning', 'in_progress', 'completed', 'on_hold', 'cancelled'];
        
        return view('client.projects.index', compact('projects', 'statuses'));
    }

    /**
     * Display the specified project.
     */
    public function show(Project $project)
    {
        // Ensure the project belongs to the authenticated client
        $this->authorize('view', $project);
        
        $project->load(['images', 'files', 'testimonial']);
        
        // Get project milestones
        $milestones = $project->milestones()->orderBy('due_date')->get();
        
        // Get project related messages
        $messages = $project->messages()->latest()->get();
        
        return view('client.projects.show', compact('project', 'milestones', 'messages'));
    }
    
    /**
     * Download a project file.
     */
    public function downloadFile(Project $project, ProjectFile $file)
    {
        // Ensure the project belongs to the authenticated client
        $this->authorize('view', $project);
        
        // Ensure the file belongs to the project
        if ($file->project_id !== $project->id) {
            abort(404);
        }
        
        // Log the download
        $file->increment('download_count');
        
        // Return file for download
        return Storage::disk('public')->download($file->file_path, $file->file_name);
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
        
        $user = auth()->user();
        
        $validated = $request->validate([
            'content' => 'required|string|min:10',
            'rating' => 'required|integer|min:1|max:5',
            'image' => 'nullable|image|max:1024',
        ]);
        
        // Create testimonial
        $testimonial = Testimonial::create([
            'project_id' => $project->id,
            'client_name' => $user->name,
            'client_company' => $user->company,
            'client_position' => $request->client_position ?? 'Client',
            'content' => $validated['content'],
            'rating' => $validated['rating'],
            'is_active' => false, // Needs admin approval
        ]);
        
        // Handle image upload
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('testimonials', 'public');
            $testimonial->update(['image' => $path]);
        }
        
        return redirect()->route('client.projects.show', $project)
            ->with('success', 'Thank you for your testimonial! It will be reviewed by our team before being published.');
    }
}