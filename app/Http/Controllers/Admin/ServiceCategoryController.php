<?php
// File: app/Http/Controllers/Admin/ServiceCategoryController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceCategory;
use App\Http\Requests\StoreServiceCategoryRequest;
use App\Http\Requests\UpdateServiceCategoryRequest;
use App\Repositories\Interfaces\ServiceCategoryRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ServiceCategoryController extends Controller
{
    protected $serviceCategoryRepository;

    /**
     * Create a new controller instance.
     *
     * @param ServiceCategoryRepositoryInterface $serviceCategoryRepository
     */
    public function __construct(ServiceCategoryRepositoryInterface $serviceCategoryRepository)
    {
        $this->serviceCategoryRepository = $serviceCategoryRepository;
    }

    /**
     * Display a listing of the categories.
     */
    public function index()
    {
        $categories = ServiceCategory::withCount('services')->paginate(10);
        
        // Get notification counts for header
        $unreadMessages = \App\Models\Message::unread()->count();
        $pendingQuotations = \App\Models\Quotation::pending()->count();
        
        return view('admin.service-categories.index', compact('categories', 'unreadMessages', 'pendingQuotations'));
    }

    /**
     * Show the form for creating a new category.
     */
    public function create()
    {
        // Get notification counts for header
        $unreadMessages = \App\Models\Message::unread()->count();
        $pendingQuotations = \App\Models\Quotation::pending()->count();
        
        return view('admin.service-categories.create', compact('unreadMessages', 'pendingQuotations'));
    }

    /**
     * Store a newly created category.
     */
    public function store(StoreServiceCategoryRequest $request)
    {
        $validated = $request->validated();
        
        // Generate slug from name
        $validated['slug'] = Str::slug($validated['name']);
        
        // Create category
        $category = $this->serviceCategoryRepository->create($validated);
        
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
        // Get notification counts for header
        $unreadMessages = \App\Models\Message::unread()->count();
        $pendingQuotations = \App\Models\Quotation::pending()->count();
        
        return view('admin.service-categories.edit', compact('serviceCategory', 'unreadMessages', 'pendingQuotations'));
    }

    /**
     * Update the specified category.
     */
    public function update(UpdateServiceCategoryRequest $request, ServiceCategory $serviceCategory)
    {
        $validated = $request->validated();
        
        // Generate slug from name
        $validated['slug'] = Str::slug($validated['name']);
        
        // Update category
        $this->serviceCategoryRepository->update($serviceCategory, $validated);
        
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
        $this->serviceCategoryRepository->delete($serviceCategory);
        
        return redirect()->route('admin.service-categories.index')
            ->with('success', 'Category deleted successfully!');
    }
    
    /**
     * Update active status
     */
    public function toggleActive(ServiceCategory $serviceCategory)
    {
        $this->serviceCategoryRepository->toggleActive($serviceCategory);
        
        return redirect()->back()
            ->with('success', 'Category status updated!');
    }
}