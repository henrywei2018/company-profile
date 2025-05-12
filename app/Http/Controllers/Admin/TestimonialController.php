<?php
// File: app/Http/Controllers/Admin/TestimonialController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TestimonialController extends Controller
{
    /**
     * Display a listing of the testimonials.
     */
    public function index(Request $request)
    {
        $testimonials = Testimonial::with('project')
            ->when($request->filled('search'), function ($query) use ($request) {
                return $query->where('client_name', 'like', "%{$request->search}%")
                            ->orWhere('client_company', 'like', "%{$request->search}%");
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                return $query->where('is_active', $request->status === 'active');
            })
            ->latest()
            ->paginate(10);
        
        return view('admin.testimonials.index', compact('testimonials'));
    }

    /**
     * Show the form for creating a new testimonial.
     */
    public function create()
    {
        $projects = Project::completed()->get();
        
        return view('admin.testimonials.create', compact('projects'));
    }

    /**
     * Store a newly created testimonial.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'nullable|exists:projects,id',
            'client_name' => 'required|string|max:255',
            'client_position' => 'nullable|string|max:255',
            'client_company' => 'nullable|string|max:255',
            'content' => 'required|string',
            'rating' => 'required|integer|min:1|max:5',
            'is_active' => 'boolean',
            'featured' => 'boolean',
            'image' => 'nullable|image|max:1024',
        ]);
        
        // Create testimonial
        $testimonial = Testimonial::create($validated);
        
        // Handle image
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('testimonials', 'public');
            $testimonial->update(['image' => $path]);
        }
        
        return redirect()->route('admin.testimonials.index')
            ->with('success', 'Testimonial created successfully!');
    }

    /**
     * Display the specified testimonial.
     */
    public function show(Testimonial $testimonial)
    {
        $testimonial->load('project');
        
        return view('admin.testimonials.show', compact('testimonial'));
    }

    /**
     * Show the form for editing the specified testimonial.
     */
    public function edit(Testimonial $testimonial)
    {
        $projects = Project::completed()->get();
        
        return view('admin.testimonials.edit', compact('testimonial', 'projects'));
    }

    /**
     * Update the specified testimonial.
     */
    public function update(Request $request, Testimonial $testimonial)
    {
        $validated = $request->validate([
            'project_id' => 'nullable|exists:projects,id',
            'client_name' => 'required|string|max:255',
            'client_position' => 'nullable|string|max:255',
            'client_company' => 'nullable|string|max:255',
            'content' => 'required|string',
            'rating' => 'required|integer|min:1|max:5',
            'is_active' => 'boolean',
            'featured' => 'boolean',
            'image' => 'nullable|image|max:1024',
        ]);
        
        // Update testimonial
        $testimonial->update($validated);
        
        // Handle image
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($testimonial->image) {
                Storage::disk('public')->delete($testimonial->image);
            }
            
            // Store new image
            $path = $request->file('image')->store('testimonials', 'public');
            $testimonial->update(['image' => $path]);
        }
        
        return redirect()->route('admin.testimonials.index')
            ->with('success', 'Testimonial updated successfully!');
    }

    /**
     * Remove the specified testimonial.
     */
    public function destroy(Testimonial $testimonial)
    {
        // Delete image
        if ($testimonial->image) {
            Storage::disk('public')->delete($testimonial->image);
        }
        
        // Delete testimonial
        $testimonial->delete();
        
        return redirect()->route('admin.testimonials.index')
            ->with('success', 'Testimonial deleted successfully!');
    }
    
    /**
     * Toggle active status
     */
    public function toggleActive(Testimonial $testimonial)
    {
        $testimonial->update([
            'is_active' => !$testimonial->is_active
        ]);
        
        return redirect()->back()
            ->with('success', 'Testimonial status updated!');
    }
    
    /**
     * Toggle featured status
     */
    public function toggleFeatured(Testimonial $testimonial)
    {
        $testimonial->update([
            'featured' => !$testimonial->featured
        ]);
        
        return redirect()->back()
            ->with('success', 'Testimonial featured status updated!');
    }
}