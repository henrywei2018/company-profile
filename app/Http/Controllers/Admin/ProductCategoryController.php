<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductCategory;
use App\Models\ServiceCategory;
use App\Models\Product;
use App\Http\Requests\StoreProductCategoryRequest;
use App\Http\Requests\UpdateProductCategoryRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductCategoryController extends Controller
{
    /**
     * Display a listing of product categories.
     */
    public function index(Request $request)
    {
        $filters = $request->only(['search', 'service_category', 'parent', 'status']);
        
        $query = ProductCategory::with(['parent', 'serviceCategory'])
            ->withCount('products')
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->when($filters['service_category'] ?? null, function ($query, $serviceCategoryId) {
                $query->where('service_category_id', $serviceCategoryId);
            })
            ->when($filters['parent'] ?? null, function ($query, $parentId) {
                if ($parentId === 'root') {
                    $query->whereNull('parent_id');
                } else {
                    $query->where('parent_id', $parentId);
                }
            })
            ->when($filters['status'] ?? null, function ($query, $status) {
                if ($status === 'active') {
                    $query->where('is_active', true);
                } elseif ($status === 'inactive') {
                    $query->where('is_active', false);
                }
            });

        $categories = $query->orderBy('sort_order')
                           ->orderBy('name')
                           ->paginate(20);

        $serviceCategories = ServiceCategory::active()->ordered()->get();
        $parentCategories = ProductCategory::rootCategories()->active()->ordered()->get();

        return view('admin.product-categories.index', compact(
            'categories', 
            'serviceCategories', 
            'parentCategories', 
            'filters'
        ));
    }

    /**
     * Show the form for creating a new product category.
     */
    public function create()
    {
        $serviceCategories = ServiceCategory::active()->ordered()->get();
        $parentCategories = ProductCategory::rootCategories()->active()->ordered()->get();

        return view('admin.product-categories.create', compact('serviceCategories', 'parentCategories'));
    }

    /**
     * Store a newly created product category in storage.
     */
    public function store(StoreProductCategoryRequest $request)
    {
        $validated = $request->validated();

        try {
            DB::transaction(function () use ($request, $validated, &$category) {
                // Generate slug if not provided
                if (empty($validated['slug'])) {
                    $validated['slug'] = Str::slug($validated['name']);
                    
                    // Ensure unique slug
                    $baseSlug = $validated['slug'];
                    $counter = 1;
                    while (ProductCategory::where('slug', $validated['slug'])->exists()) {
                        $validated['slug'] = $baseSlug . '-' . $counter;
                        $counter++;
                    }
                }

                // Set default sort order
                if (empty($validated['sort_order'])) {
                    $parentId = $validated['parent_id'] ?? null;
                    $validated['sort_order'] = ProductCategory::where('parent_id', $parentId)
                        ->max('sort_order') + 1;
                }

                // Handle icon upload
                if ($request->hasFile('icon')) {
                    $validated['icon'] = $this->uploadIcon($request->file('icon'));
                }

                // Create category
                $category = ProductCategory::create($validated);
            });

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Product category created successfully!',
                    'category' => $category->fresh()->load(['parent', 'serviceCategory']),
                    'redirect' => route('admin.product-categories.edit', $category)
                ]);
            }

            return redirect()->route('admin.product-categories.index')
                ->with('success', 'Product category created successfully!');

        } catch (\Exception $e) {
            \Log::error('Product category creation failed: ' . $e->getMessage(), [
                'validated_data' => $validated
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create product category: ' . $e->getMessage()
                ], 422);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create product category. Please try again.');
        }
    }

    /**
     * Display the specified product category.
     */
    public function show(ProductCategory $productCategory)
    {
        $productCategory->load([
            'parent', 
            'serviceCategory', 
            'children' => function ($query) {
                $query->withCount('products')->ordered();
            },
            'products' => function ($query) {
                $query->with(['service'])->latest()->limit(10);
            }
        ]);

        // Get category statistics
        $stats = [
            'total_products' => $productCategory->products()->count(),
            'active_products' => $productCategory->products()->active()->count(),
            'published_products' => $productCategory->products()->published()->count(),
            'total_children' => $productCategory->children()->count(),
            'active_children' => $productCategory->children()->active()->count(),
        ];

        return view('admin.product-categories.show', compact('productCategory', 'stats'));
    }

    /**
     * Show the form for editing the specified product category.
     */
    public function edit(ProductCategory $productCategory)
    {
        $productCategory->load(['parent', 'serviceCategory']);
        
        $serviceCategories = ServiceCategory::active()->ordered()->get();
        
        // Get available parent categories (excluding self and descendants)
        $parentCategories = ProductCategory::rootCategories()
            ->active()
            ->where('id', '!=', $productCategory->id)
            ->ordered()
            ->get()
            ->filter(function ($category) use ($productCategory) {
                // Exclude if this category would create a circular reference
                return !$category->isDescendantOf($productCategory);
            });

        return view('admin.product-categories.edit', compact(
            'productCategory', 
            'serviceCategories', 
            'parentCategories'
        ));
    }

    /**
     * Update the specified product category in storage.
     */
    public function update(UpdateProductCategoryRequest $request, ProductCategory $productCategory)
    {
        $validated = $request->validated();

        try {
            DB::transaction(function () use ($request, $validated, $productCategory) {
                // Handle slug generation
                if (empty($validated['slug'])) {
                    $validated['slug'] = Str::slug($validated['name']);
                }

                // Ensure unique slug (excluding current category)
                if ($validated['slug'] !== $productCategory->slug) {
                    $baseSlug = $validated['slug'];
                    $counter = 1;
                    while (ProductCategory::where('slug', $validated['slug'])
                                        ->where('id', '!=', $productCategory->id)
                                        ->exists()) {
                        $validated['slug'] = $baseSlug . '-' . $counter;
                        $counter++;
                    }
                }

                // Handle icon upload
                if ($request->hasFile('icon')) {
                    // Delete old icon
                    if ($productCategory->icon && Storage::disk('public')->exists($productCategory->icon)) {
                        Storage::disk('public')->delete($productCategory->icon);
                    }
                    
                    $validated['icon'] = $this->uploadIcon($request->file('icon'));
                }

                // Update category
                $productCategory->update($validated);
            });

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Product category updated successfully!',
                    'category' => $productCategory->fresh()->load(['parent', 'serviceCategory'])
                ]);
            }

            return redirect()->route('admin.product-categories.index')
                ->with('success', 'Product category updated successfully.');

        } catch (\Exception $e) {
            \Log::error('Product category update failed: ' . $e->getMessage(), [
                'category_id' => $productCategory->id,
                'validated_data' => $validated
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update product category: ' . $e->getMessage()
                ], 422);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update product category. Please try again.');
        }
    }

    /**
     * Remove the specified product category from storage.
     */
    public function destroy(ProductCategory $productCategory)
    {
        try {
            // Check if category has products
            if ($productCategory->products()->count() > 0) {
                return redirect()->route('admin.product-categories.index')
                    ->with('error', 'Cannot delete category that contains products. Please move or delete the products first.');
            }

            // Check if category has children
            if ($productCategory->children()->count() > 0) {
                return redirect()->route('admin.product-categories.index')
                    ->with('error', 'Cannot delete category that has subcategories. Please delete or move the subcategories first.');
            }

            DB::transaction(function () use ($productCategory) {
                // Delete icon if exists
                if ($productCategory->icon && Storage::disk('public')->exists($productCategory->icon)) {
                    Storage::disk('public')->delete($productCategory->icon);
                }

                // Delete the category
                $productCategory->delete();
            });

            return redirect()->route('admin.product-categories.index')
                ->with('success', 'Product category deleted successfully.');

        } catch (\Exception $e) {
            \Log::error('Product category deletion failed: ' . $e->getMessage(), [
                'category_id' => $productCategory->id
            ]);

            return redirect()->route('admin.product-categories.index')
                ->with('error', 'Failed to delete product category. Please try again.');
        }
    }

    /**
     * Toggle category active status.
     */
    public function toggleActive(ProductCategory $productCategory)
    {
        try {
            $productCategory->update(['is_active' => !$productCategory->is_active]);
            
            $status = $productCategory->is_active ? 'activated' : 'deactivated';
            
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Category {$status} successfully.",
                    'is_active' => $productCategory->is_active
                ]);
            }

            return redirect()->back()
                ->with('success', "Category {$status} successfully.");

        } catch (\Exception $e) {
            \Log::error('Category toggle active failed: ' . $e->getMessage(), [
                'category_id' => $productCategory->id
            ]);

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update category status.'
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Failed to update category status.');
        }
    }

    /**
     * Update category sort order.
     */
    public function updateOrder(Request $request)
    {
        $request->validate([
            'category_ids' => 'required|array|min:1',
            'category_ids.*' => 'exists:product_categories,id',
            'parent_id' => 'nullable|exists:product_categories,id',
        ]);

        try {
            DB::transaction(function () use ($request) {
                $categoryIds = $request->input('category_ids');
                $parentId = $request->input('parent_id');
                
                foreach ($categoryIds as $index => $categoryId) {
                    ProductCategory::where('id', $categoryId)
                                  ->where('parent_id', $parentId)
                                  ->update(['sort_order' => $index + 1]);
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Category order updated successfully.'
            ]);

        } catch (\Exception $e) {
            \Log::error('Category reorder failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to reorder categories.'
            ], 500);
        }
    }

    /**
     * Bulk action handler.
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:activate,deactivate,delete',
            'category_ids' => 'required|array|min:1',
            'category_ids.*' => 'exists:product_categories,id',
        ]);

        $action = $request->input('action');
        $categoryIds = $request->input('category_ids');

        try {
            DB::transaction(function () use ($action, $categoryIds) {
                switch ($action) {
                    case 'activate':
                        ProductCategory::whereIn('id', $categoryIds)->update(['is_active' => true]);
                        break;
                        
                    case 'deactivate':
                        ProductCategory::whereIn('id', $categoryIds)->update(['is_active' => false]);
                        break;
                        
                    case 'delete':
                        $categories = ProductCategory::whereIn('id', $categoryIds)->get();
                        
                        foreach ($categories as $category) {
                            // Check if category has products or children
                            if ($category->products()->count() > 0 || $category->children()->count() > 0) {
                                throw new \Exception("Cannot delete category '{$category->name}' because it contains products or subcategories.");
                            }
                            
                            // Delete icon if exists
                            if ($category->icon && Storage::disk('public')->exists($category->icon)) {
                                Storage::disk('public')->delete($category->icon);
                            }
                            
                            $category->delete();
                        }
                        break;
                }
            });

            $count = count($categoryIds);
            $actionText = str_replace(['_'], [' '], $action);
            
            return redirect()->back()
                ->with('success', "{$count} categor" . ($count === 1 ? 'y' : 'ies') . " {$actionText}d successfully.");

        } catch (\Exception $e) {
            \Log::error('Category bulk action failed: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Get category statistics.
     */
    public function statistics()
    {
        try {
            $stats = [
                'total' => ProductCategory::count(),
                'active' => ProductCategory::where('is_active', true)->count(),
                'inactive' => ProductCategory::where('is_active', false)->count(),
                'root_categories' => ProductCategory::whereNull('parent_id')->count(),
                'with_products' => ProductCategory::has('products')->count(),
                'empty_categories' => ProductCategory::doesntHave('products')->count(),
            ];

            // Categories by service category
            $stats['by_service_category'] = ServiceCategory::withCount(['productCategories' => function ($query) {
                $query->where('is_active', true);
            }])
            ->get()
            ->map(function ($serviceCategory) {
                return [
                    'name' => $serviceCategory->name,
                    'count' => $serviceCategory->product_categories_count,
                ];
            });

            // Top categories by product count
            $stats['top_categories'] = ProductCategory::withCount('products')
                ->having('products_count', '>', 0)
                ->orderBy('products_count', 'desc')
                ->limit(10)
                ->get()
                ->map(function ($category) {
                    return [
                        'name' => $category->name,
                        'count' => $category->products_count,
                        'url' => route('admin.product-categories.show', $category),
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            \Log::error('Category statistics failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to get category statistics.'
            ], 500);
        }
    }

    /**
     * Export categories to CSV.
     */
    public function export(Request $request)
    {
        $filters = $request->only(['search', 'service_category', 'parent', 'status']);
        
        $query = ProductCategory::with(['parent', 'serviceCategory'])
            ->withCount('products')
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->when($filters['service_category'] ?? null, function ($query, $serviceCategoryId) {
                $query->where('service_category_id', $serviceCategoryId);
            })
            ->when($filters['parent'] ?? null, function ($query, $parentId) {
                if ($parentId === 'root') {
                    $query->whereNull('parent_id');
                } else {
                    $query->where('parent_id', $parentId);
                }
            })
            ->when($filters['status'] ?? null, function ($query, $status) {
                if ($status === 'active') {
                    $query->where('is_active', true);
                } elseif ($status === 'inactive') {
                    $query->where('is_active', false);
                }
            });

        $categories = $query->orderBy('sort_order')
                           ->orderBy('name')
                           ->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="product-categories-' . date('Y-m-d') . '.csv"',
        ];

        $callback = function () use ($categories) {
            $file = fopen('php://output', 'w');

            fputcsv($file, [
                'ID', 'Name', 'Slug', 'Description', 'Parent Category', 
                'Service Category', 'Products Count', 'Is Active', 
                'Sort Order', 'Created At', 'Updated At'
            ]);

            foreach ($categories as $category) {
                fputcsv($file, [
                    $category->id,
                    $category->name,
                    $category->slug,
                    $category->description,
                    $category->parent?->name,
                    $category->serviceCategory?->name,
                    $category->products_count,
                    $category->is_active ? 'Yes' : 'No',
                    $category->sort_order,
                    $category->created_at->format('Y-m-d H:i:s'),
                    $category->updated_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Search categories (AJAX endpoint).
     */
    public function search(Request $request)
    {
        $request->validate([
            'query' => 'required|string|min:1|max:255',
            'service_category' => 'nullable|exists:service_categories,id',
            'parent' => 'nullable|exists:product_categories,id',
            'limit' => 'nullable|integer|min:1|max:50',
        ]);

        try {
            $query = ProductCategory::with(['parent', 'serviceCategory'])
                ->withCount('products')
                ->where(function ($q) use ($request) {
                    $searchTerm = $request->input('query');
                    $q->where('name', 'like', "%{$searchTerm}%")
                      ->orWhere('description', 'like', "%{$searchTerm}%");
                });

            // Apply filters
            if ($request->filled('service_category')) {
                $query->where('service_category_id', $request->input('service_category'));
            }

            if ($request->filled('parent')) {
                $query->where('parent_id', $request->input('parent'));
            }

            $limit = $request->input('limit', 10);
            $categories = $query->orderBy('name')
                               ->limit($limit)
                               ->get();

            return response()->json([
                'success' => true,
                'categories' => $categories->map(function ($category) {
                    return [
                        'id' => $category->id,
                        'name' => $category->name,
                        'slug' => $category->slug,
                        'parent' => $category->parent?->name,
                        'service_category' => $category->serviceCategory?->name,
                        'products_count' => $category->products_count,
                        'is_active' => $category->is_active,
                        'icon_url' => $category->icon_url,
                        'edit_url' => route('admin.product-categories.edit', $category),
                        'created_at' => $category->created_at->format('M j, Y'),
                    ];
                }),
                'total' => $categories->count(),
            ]);

        } catch (\Exception $e) {
            \Log::error('Category search failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Search failed. Please try again.'
            ], 500);
        }
    }

    /**
     * Get categories for select dropdown (AJAX).
     */
    public function getSelectOptions(Request $request)
    {
        $excludeId = $request->input('exclude_id');
        $serviceCategory = $request->input('service_category');
        
        $query = ProductCategory::active()->ordered();
        
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        
        if ($serviceCategory) {
            $query->where('service_category_id', $serviceCategory);
        }
        
        $categories = $query->get();
        
        $options = [];
        foreach ($categories as $category) {
            $prefix = str_repeat('— ', $category->depth);
            $options[] = [
                'value' => $category->id,
                'text' => $prefix . $category->name,
                'depth' => $category->depth
            ];
        }
        
        return response()->json([
            'success' => true,
            'options' => $options
        ]);
    }

    /**
     * Upload category icon.
     */
    protected function uploadIcon($file)
    {
        try {
            $filename = 'category_icon_' . time() . '_' . Str::random(6) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('product-categories/icons', $filename, 'public');
            
            return $path;
            
        } catch (\Exception $e) {
            \Log::error('Category icon upload failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Delete category icon.
     */
    public function deleteIcon(ProductCategory $productCategory)
    {
        try {
            if ($productCategory->icon && Storage::disk('public')->exists($productCategory->icon)) {
                Storage::disk('public')->delete($productCategory->icon);
                $productCategory->update(['icon' => null]);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Icon deleted successfully!'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'No icon to delete'
            ], 404);

        } catch (\Exception $e) {
            \Log::error('Category icon deletion failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete icon: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get category tree for hierarchical display.
     */
    public function getTree(Request $request)
    {
        try {
            $serviceCategory = $request->input('service_category');
            
            $query = ProductCategory::with(['children' => function ($query) {
                $query->withCount('products')->ordered();
            }])
            ->withCount('products')
            ->whereNull('parent_id');
            
            if ($serviceCategory) {
                $query->where('service_category_id', $serviceCategory);
            }
            
            $rootCategories = $query->ordered()->get();
            
            $tree = $this->buildCategoryTree($rootCategories);
            
            return response()->json([
                'success' => true,
                'tree' => $tree
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Category tree failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to get category tree.'
            ], 500);
        }
    }

    /**
     * Build hierarchical category tree.
     */
    protected function buildCategoryTree($categories)
    {
        $tree = [];
        
        foreach ($categories as $category) {
            $node = [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
                'is_active' => $category->is_active,
                'products_count' => $category->products_count,
                'icon_url' => $category->icon_url,
                'edit_url' => route('admin.product-categories.edit', $category),
                'children' => []
            ];
            
            if ($category->children->isNotEmpty()) {
                $node['children'] = $this->buildCategoryTree($category->children);
            }
            
            $tree[] = $node;
        }
        
        return $tree;
    }
}