<?php

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
    public function index(Request $request)
    {
        // Add validation for filters
        $request->validate([
            'search' => 'nullable|string|max:255',
            'status' => 'nullable|in:active,inactive,0,1',
        ]);

        $query = ServiceCategory::withCount('services');

        // Apply filters
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('description', 'like', "%{$request->search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active' || $request->status === '1');
        }

        $categories = $query->orderBy('sort_order')->orderBy('name')->paginate(10)->withQueryString();
        
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
        
        // Generate slug from name if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }
        
        // Create category
        $category = $this->serviceCategoryRepository->create($validated);
        
        // Handle icon upload
        if ($request->hasFile('icon')) {
            $path = $request->file('icon')->store('categories', 'public');
            $category->update(['icon' => $path]);
        }
        
        return redirect()->route('admin.service-categories.index')
            ->with('success', 'Category created successfully!');
    }

    /**
     * Display the specified category.
     */
    public function show(ServiceCategory $category)
    {
        $category->load('services');
        
        // Get notification counts for header
        $unreadMessages = \App\Models\Message::unread()->count();
        $pendingQuotations = \App\Models\Quotation::pending()->count();
        
        return view('admin.service-categories.show', compact('category', 'unreadMessages', 'pendingQuotations'));
    }

    /**
     * Show the form for editing the specified category.
     */
    public function edit(ServiceCategory $category)
    {
        // Get notification counts for header
        $unreadMessages = \App\Models\Message::unread()->count();
        $pendingQuotations = \App\Models\Quotation::pending()->count();
        
        return view('admin.service-categories.edit', compact('category', 'unreadMessages', 'pendingQuotations'));
    }

    /**
     * Update the specified category.
     */
    public function update(UpdateServiceCategoryRequest $request, ServiceCategory $category)
    {
        $validated = $request->validated();
        
        // Generate slug from name if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }
        
        // Update category
        $this->serviceCategoryRepository->update($category, $validated);
        
        // Handle icon upload
        if ($request->hasFile('icon')) {
            // Delete old icon if exists
            if ($category->icon) {
                Storage::disk('public')->delete($category->icon);
            }
            
            // Store new icon
            $path = $request->file('icon')->store('categories', 'public');
            $category->update(['icon' => $path]);
        }
        
        return redirect()->route('admin.service-categories.index')
            ->with('success', 'Category updated successfully!');
    }

    /**
     * Remove the specified category.
     */
    public function destroy(ServiceCategory $category)
    {
        // Check if category has services
        if ($category->services()->count() > 0) {
            return redirect()->route('admin.service-categories.index')
                ->with('error', 'Cannot delete category with associated services!');
        }
        
        // Delete icon if exists
        if ($category->icon) {
            Storage::disk('public')->delete($category->icon);
        }
        
        // Delete category
        $this->serviceCategoryRepository->delete($category);
        
        return redirect()->route('admin.service-categories.index')
            ->with('success', 'Category deleted successfully!');
    }
    
    /**
     * Toggle active status.
     */
    public function toggleActive(ServiceCategory $category)
    {
        $this->serviceCategoryRepository->toggleActive($category);
        
        return redirect()->back()
            ->with('success', 'Category status updated successfully!');
    }

    /**
     * Update sort order.
     */
    public function updateOrder(Request $request)
    {
        $request->validate([
            'order' => 'required|array',
            'order.*' => 'integer|exists:service_categories,id',
        ]);

        foreach ($request->order as $index => $id) {
            ServiceCategory::where('id', $id)->update(['sort_order' => $index + 1]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Bulk actions for categories.
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:activate,deactivate,delete',
            'categories' => 'required|array',
            'categories.*' => 'exists:service_categories,id',
        ]);

        $categories = ServiceCategory::whereIn('id', $request->categories);
        $count = $categories->count();

        switch ($request->action) {
            case 'activate':
                $categories->update(['is_active' => true]);
                $message = "{$count} categories activated successfully!";
                break;
                
            case 'deactivate':
                $categories->update(['is_active' => false]);
                $message = "{$count} categories deactivated successfully!";
                break;
                
            case 'delete':
                // Check if any category has services
                $categoriesWithServices = $categories->has('services')->count();
                if ($categoriesWithServices > 0) {
                    return redirect()->back()
                        ->with('error', 'Cannot delete categories that have associated services!');
                }
                
                // Delete icons for each category
                foreach ($categories->get() as $category) {
                    if ($category->icon) {
                        Storage::disk('public')->delete($category->icon);
                    }
                }
                
                $categories->delete();
                $message = "{$count} categories deleted successfully!";
                break;
        }

        return redirect()->back()->with('success', $message);
    }
}