<?php
// File: app/Http/Controllers/Client/TestimonialController.php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Testimonial;
use App\Services\ClientAccessService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TestimonialController extends Controller
{
    protected ClientAccessService $clientAccessService;

    public function __construct(ClientAccessService $clientAccessService)
    {
        $this->clientAccessService = $clientAccessService;
    }

    /**
     * Display a listing of the client's testimonials.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Get testimonials for projects belonging to this client
        $testimonials = Testimonial::whereHas('project', function($query) use ($user) {
                $query->where('client_id', $user->id);
            })
            ->with(['project'])
            ->when($request->filled('status'), function ($query) use ($request) {
                if ($request->status === 'active') {
                    return $query->where('is_active', true);
                } elseif ($request->status === 'inactive') {
                    return $query->where('is_active', false);
                }
                return $query;
            })
            ->when($request->filled('featured'), function ($query) use ($request) {
                return $query->where('featured', $request->featured === '1');
            })
            ->when($request->filled('search'), function ($query) use ($request) {
                return $query->where(function ($q) use ($request) {
                    $q->where('content', 'like', "%{$request->search}%")
                      ->orWhereHas('project', function ($pq) use ($request) {
                          $pq->where('title', 'like', "%{$request->search}%");
                      });
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Get statistics
        $statistics = [
            'total' => Testimonial::whereHas('project', function($query) use ($user) {
                $query->where('client_id', $user->id);
            })->count(),
            'active' => Testimonial::whereHas('project', function($query) use ($user) {
                $query->where('client_id', $user->id);
            })->where('is_active', true)->count(),
            'featured' => Testimonial::whereHas('project', function($query) use ($user) {
                $query->where('client_id', $user->id);
            })->where('featured', true)->count(),
            'pending' => Testimonial::whereHas('project', function($query) use ($user) {
                $query->where('client_id', $user->id);
            })->where('is_active', false)->count(),
        ];

        return view('client.testimonials.index', compact('testimonials', 'statistics'));
    }

    /**
     * Show the form for creating a new testimonial.
     */
    public function create(Request $request)
    {
        $user = auth()->user();
        
        // Get completed projects without testimonials
        $availableProjects = $this->clientAccessService->getClientProjects($user)
            ->where('status', 'completed')
            ->whereDoesntHave('testimonial')
            ->with(['category', 'images'])
            ->get();

        if ($availableProjects->isEmpty()) {
            return redirect()->route('client.projects.index')
                ->with('info', 'You don\'t have any completed projects available for testimonials. Complete a project first to leave a testimonial.');
        }

        // Pre-select project if specified
        $selectedProject = null;
        if ($request->filled('project_id')) {
            $selectedProject = $availableProjects->find($request->project_id);
            if (!$selectedProject) {
                return redirect()->route('client.testimonials.create')
                    ->with('error', 'Selected project is not available for testimonials.');
            }
        }

        return view('client.testimonials.create', compact('availableProjects', 'selectedProject'));
    }

    /**
     * Store a newly created testimonial.
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'content' => 'required|string|min:10|max:2000',
            'rating' => 'required|integer|min:1|max:5',
            'client_position' => 'nullable|string|max:100',
            'image' => 'nullable|image|max:1024|mimes:jpeg,png,jpg,gif',
        ]);

        // Verify project belongs to client and is completed
        $project = Project::where('id', $validated['project_id'])
            ->where('client_id', $user->id)
            ->where('status', 'completed')
            ->first();

        if (!$project) {
            return redirect()->route('client.testimonials.create')
                ->with('error', 'Selected project is not valid or not completed.');
        }

        // Check if testimonial already exists
        if ($project->testimonial) {
            return redirect()->route('client.testimonials.index')
                ->with('info', 'A testimonial already exists for this project.');
        }

        try {
            DB::beginTransaction();

            // Create testimonial
            $testimonial = Testimonial::create([
                'project_id' => $project->id,
                'client_name' => $user->name,
                'client_company' => $user->company,
                'client_position' => $validated['client_position'] ?? 'Client',
                'content' => $validated['content'],
                'rating' => $validated['rating'],
                'is_active' => false, // Requires admin approval
                'featured' => false,
            ]);

            // Handle image upload
            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('testimonials', 'public');
                $testimonial->update(['image' => $path]);
            }

            DB::commit();

            // Log testimonial creation
            Log::info('Client testimonial created', [
                'testimonial_id' => $testimonial->id,
                'project_id' => $project->id,
                'client_id' => $user->id,
                'rating' => $validated['rating']
            ]);

            return redirect()->route('client.testimonials.show', $testimonial)
                ->with('success', 'Thank you for your testimonial! It will be reviewed by our team before being published.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create testimonial: ' . $e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to submit testimonial. Please try again.');
        }
    }

    /**
     * Display the specified testimonial.
     */
    public function show(Testimonial $testimonial)
    {
        // Ensure testimonial belongs to client's project
        if ($testimonial->project->client_id !== auth()->id()) {
            abort(403, 'Unauthorized access to this testimonial.');
        }

        $testimonial->load(['project.category', 'project.images']);

        return view('client.testimonials.show', compact('testimonial'));
    }

    /**
     * Show the form for editing the specified testimonial.
     */
    public function edit(Testimonial $testimonial)
    {
        // Ensure testimonial belongs to client's project
        if ($testimonial->project->client_id !== auth()->id()) {
            abort(403, 'Unauthorized access to this testimonial.');
        }

        // Only allow editing if not yet approved or featured
        if ($testimonial->is_active && $testimonial->featured) {
            return redirect()->route('client.testimonials.show', $testimonial)
                ->with('info', 'This testimonial is already published and featured. Contact support if you need to make changes.');
        }

        $testimonial->load(['project']);

        return view('client.testimonials.edit', compact('testimonial'));
    }

    /**
     * Update the specified testimonial.
     */
    public function update(Request $request, Testimonial $testimonial)
    {
        // Ensure testimonial belongs to client's project
        if ($testimonial->project->client_id !== auth()->id()) {
            abort(403, 'Unauthorized access to this testimonial.');
        }

        // Only allow editing if not published yet or not featured
        if ($testimonial->is_active && $testimonial->featured) {
            return redirect()->route('client.testimonials.show', $testimonial)
                ->with('info', 'This testimonial is already published and featured. Contact support if you need to make changes.');
        }

        $validated = $request->validate([
            'content' => 'required|string|min:10|max:2000',
            'rating' => 'required|integer|min:1|max:5',
            'client_position' => 'nullable|string|max:100',
            'image' => 'nullable|image|max:1024|mimes:jpeg,png,jpg,gif',
        ]);

        try {
            DB::beginTransaction();

            // Update testimonial
            $testimonial->update([
                'content' => $validated['content'],
                'rating' => $validated['rating'],
                'client_position' => $validated['client_position'] ?? $testimonial->client_position,
                'is_active' => false, // Reset to pending approval after edit
            ]);

            // Handle image upload
            if ($request->hasFile('image')) {
                // Delete old image if exists
                if ($testimonial->image) {
                    Storage::disk('public')->delete($testimonial->image);
                }
                
                $path = $request->file('image')->store('testimonials', 'public');
                $testimonial->update(['image' => $path]);
            }

            DB::commit();

            // Log testimonial update
            Log::info('Client testimonial updated', [
                'testimonial_id' => $testimonial->id,
                'project_id' => $testimonial->project_id,
                'client_id' => auth()->id(),
                'rating' => $validated['rating']
            ]);

            return redirect()->route('client.testimonials.show', $testimonial)
                ->with('success', 'Testimonial updated successfully! It will be reviewed again before being published.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update testimonial: ' . $e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update testimonial. Please try again.');
        }
    }

    /**
     * Remove the specified testimonial.
     */
    public function destroy(Testimonial $testimonial)
    {
        // Ensure testimonial belongs to client's project
        if ($testimonial->project->client_id !== auth()->id()) {
            abort(403, 'Unauthorized access to this testimonial.');
        }

        // Only allow deletion if not featured
        if ($testimonial->featured) {
            return redirect()->route('client.testimonials.show', $testimonial)
                ->with('error', 'Featured testimonials cannot be deleted. Please contact support for assistance.');
        }

        try {
            DB::beginTransaction();

            // Delete image if exists
            if ($testimonial->image) {
                Storage::disk('public')->delete($testimonial->image);
            }

            // Log before deletion
            Log::info('Client testimonial deleted', [
                'testimonial_id' => $testimonial->id,
                'project_id' => $testimonial->project_id,
                'client_id' => auth()->id()
            ]);

            $testimonial->delete();

            DB::commit();

            return redirect()->route('client.testimonials.index')
                ->with('success', 'Testimonial deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete testimonial: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Failed to delete testimonial. Please try again.');
        }
    }

    /**
     * Show available projects for creating testimonials.
     */
    public function availableProjects()
    {
        $user = auth()->user();
        
        $completedProjects = $this->clientAccessService->getClientProjects($user)
            ->where('status', 'completed')
            ->whereDoesntHave('testimonial')
            ->with(['category', 'images'])
            ->orderBy('actual_completion_date', 'desc')
            ->get();

        $projectsWithTestimonials = $this->clientAccessService->getClientProjects($user)
            ->where('status', 'completed')
            ->whereHas('testimonial')
            ->with(['category', 'images', 'testimonial'])
            ->orderBy('actual_completion_date', 'desc')
            ->get();

        return view('client.testimonials.available-projects', compact(
            'completedProjects', 
            'projectsWithTestimonials'
        ));
    }

    /**
     * Preview how testimonial will look when published.
     */
    public function preview(Testimonial $testimonial)
    {
        // Ensure testimonial belongs to client's project
        if ($testimonial->project->client_id !== auth()->id()) {
            abort(403, 'Unauthorized access to this testimonial.');
        }

        $testimonial->load(['project.category', 'project.images']);

        return view('client.testimonials.preview', compact('testimonial'));
    }

    /**
     * Get testimonial statistics for dashboard.
     */
    public function getStatistics()
    {
        $user = auth()->user();
        
        return [
            'total_testimonials' => Testimonial::whereHas('project', function($query) use ($user) {
                $query->where('client_id', $user->id);
            })->count(),
            'active_testimonials' => Testimonial::whereHas('project', function($query) use ($user) {
                $query->where('client_id', $user->id);
            })->where('is_active', true)->count(),
            'pending_testimonials' => Testimonial::whereHas('project', function($query) use ($user) {
                $query->where('client_id', $user->id);
            })->where('is_active', false)->count(),
            'featured_testimonials' => Testimonial::whereHas('project', function($query) use ($user) {
                $query->where('client_id', $user->id);
            })->where('featured', true)->count(),
            'average_rating' => Testimonial::whereHas('project', function($query) use ($user) {
                $query->where('client_id', $user->id);
            })->avg('rating') ?? 0,
            'projects_without_testimonials' => Project::where('client_id', $user->id)
                ->where('status', 'completed')
                ->whereDoesntHave('testimonial')
                ->count(),
        ];
    }
}