<?php
// File: app/Http/Controllers/Admin/ServiceCategoryController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ServiceCategoryController extends Controller
{
    /**
     * Display a listing of the categories.
     */
    public function index()
    {
        $categories = ServiceCategory::withCount('services')->paginate(10);
        
        return view('admin.service-categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new category.
     */
    public function create()
    {
        return view('admin.service-categories.create');
    }

    /**
     * Store a newly created category.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'nullable|image|max:1024',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);
        
        // Generate slug from name
        $validated['slug'] = Str::slug($validated['name']);
        
        // Create category
        $category = ServiceCategory::create($validated);
        
        // Handle icon
        if ($request->hasFile('icon')) {
            $path = $request->file('icon')->store('categories', 'public');
            $category->update(['icon' => $path]);
        }
        
        return redirect()->route('admin.service-categories.index')
            ->with('success', 'Category created successfully!');
    }

    /**
     * Show the form for editing the specified category.
     */
    public function edit(ServiceCategory $serviceCategory)
    {
        return view('admin.service-categories.edit', compact('serviceCategory'));
    }

    /**
     * Update the specified category.
     */
    public function update(Request $request, ServiceCategory $serviceCategory)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'nullable|image|max:1024',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);
        
        // Generate slug from name
        $validated['slug'] = Str::slug($validated['name']);
        
        // Update category
        $serviceCategory->update($validated);
        
        // Handle icon
        if ($request->hasFile('icon')) {
            // Delete old icon if exists
            if ($serviceCategory->icon) {
                Storage::disk('public')->delete($serviceCategory->icon);
            }
            
            // Store new icon
            $path = $request->file('icon')->store('categories', 'public');
            $serviceCategory->update(['icon' => $path]);
        }
        
        return redirect()->route('admin.service-categories.index')
            ->with('success', 'Category updated successfully!');
    }

    /**
     * Remove the specified category.
     */
    public function destroy(ServiceCategory $serviceCategory)
    {
        // Check if category has services
        if ($serviceCategory->services()->count() > 0) {
            return redirect()->route('admin.service-categories.index')
                ->with('error', 'Cannot delete category with associated services!');
        }
        
        // Delete icon
        if ($serviceCategory->icon) {
            Storage::disk('public')->delete($serviceCategory->icon);
        }
        
        // Delete category
        $serviceCategory->delete();
        
        return redirect()->route('admin.service-categories.index')
            ->with('success', 'Category deleted successfully!');
    }
    
    /**
     * Update active status
     */
    public function toggleActive(ServiceCategory $serviceCategory)
    {
        $serviceCategory->update([
            'is_active' => !$serviceCategory->is_active
        ]);
        
        return redirect()->back()
            ->with('success', 'Category status updated!');
    }
}