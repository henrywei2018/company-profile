<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BannerCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class BannerCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = BannerCategory::withCount('banners');

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        // Apply sorting
        $sortField = $request->input('sort', 'display_order');
        $sortDirection = $request->input('direction', 'asc');
        
        if (in_array($sortField, ['name', 'created_at', 'display_order'])) {
            $query->orderBy($sortField, $sortDirection);
        } else {
            $query->orderBy('display_order', 'asc');
        }

        $categories = $query->paginate(10);
        
        return view('admin.banner-categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.banner-categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:banner_categories',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'display_order' => 'nullable|integer',
        ]);

        try {
            // Generate slug if not provided
            if (empty($validated['slug'])) {
                $validated['slug'] = Str::slug($validated['name']);
                
                // Ensure slug is unique
                $originalSlug = $validated['slug'];
                $counter = 1;
                while (BannerCategory::where('slug', $validated['slug'])->exists()) {
                    $validated['slug'] = $originalSlug . '-' . $counter;
                    $counter++;
                }
            }

            // Set default display order if not provided
            if (empty($validated['display_order'])) {
                $validated['display_order'] = BannerCategory::max('display_order') + 1;
            }

            BannerCategory::create($validated);

            return redirect()->route('admin.banner-categories.index')
                ->with('success', 'Banner category created successfully.');

        } catch (\Exception $e) {
            \Log::error('Banner category creation failed: ' . $e->getMessage(), [
                'validated_data' => $validated
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create banner category. Please try again.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(BannerCategory $bannerCategory)
    {
        $bannerCategory->loadCount('banners');
        
        // Get some banners from this category for display
        $banners = $bannerCategory->banners()
            ->orderBy('display_order')
            ->limit(10)
            ->get();
            
        return view('admin.banner-categories.show', compact('bannerCategory', 'banners'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BannerCategory $bannerCategory)
    {
        // Load banners count for statistics
        $bannerCategory->loadCount('banners');
        
        return view('admin.banner-categories.edit', compact('bannerCategory'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BannerCategory $bannerCategory)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:banner_categories,slug,' . $bannerCategory->id,
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'display_order' => 'nullable|integer',
        ]);

        try {
            // Generate slug if not provided
            if (empty($validated['slug'])) {
                $validated['slug'] = Str::slug($validated['name']);
                
                // Ensure slug is unique (excluding current record)
                $originalSlug = $validated['slug'];
                $counter = 1;
                while (BannerCategory::where('slug', $validated['slug'])
                    ->where('id', '!=', $bannerCategory->id)
                    ->exists()) {
                    $validated['slug'] = $originalSlug . '-' . $counter;
                    $counter++;
                }
            }

            $bannerCategory->update($validated);

            return redirect()->route('admin.banner-categories.index')
                ->with('success', 'Banner category updated successfully.');

        } catch (\Exception $e) {
            \Log::error('Banner category update failed: ' . $e->getMessage(), [
                'category_id' => $bannerCategory->id,
                'validated_data' => $validated
            ]);

            // Reload the category with count for the view
            $bannerCategory->loadCount('banners');

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update banner category. Please try again.')
                ->with(compact('bannerCategory'));
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BannerCategory $bannerCategory)
    {
        try {
            // Check if category has banners
            if ($bannerCategory->banners()->count() > 0) {
                return redirect()->route('admin.banner-categories.index')
                    ->with('error', 'Cannot delete category with associated banners.');
            }
            
            $bannerCategory->delete();

            return redirect()->route('admin.banner-categories.index')
                ->with('success', 'Banner category deleted successfully.');

        } catch (\Exception $e) {
            \Log::error('Banner category deletion failed: ' . $e->getMessage(), [
                'category_id' => $bannerCategory->id
            ]);

            return redirect()->route('admin.banner-categories.index')
                ->with('error', 'Failed to delete banner category. Please try again.');
        }
    }

    /**
     * Toggle category status.
     */
    public function toggleStatus(BannerCategory $bannerCategory)
    {
        try {
            $bannerCategory->is_active = !$bannerCategory->is_active;
            $bannerCategory->save();

            $status = $bannerCategory->is_active ? 'activated' : 'deactivated';
            
            return redirect()->back()
                ->with('success', "Banner category {$status} successfully.");

        } catch (\Exception $e) {
            \Log::error('Banner category status toggle failed: ' . $e->getMessage(), [
                'category_id' => $bannerCategory->id
            ]);

            return redirect()->back()
                ->with('error', 'Failed to update category status. Please try again.');
        }
    }

    /**
     * Reorder categories.
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'category_ids' => 'required|array',
            'category_ids.*' => 'exists:banner_categories,id',
        ]);

        try {
            DB::transaction(function () use ($request) {
                $order = 1;
                foreach ($request->category_ids as $categoryId) {
                    BannerCategory::where('id', $categoryId)
                        ->update(['display_order' => $order]);
                    $order++;
                }
            });

            return response()->json([
                'success' => true, 
                'message' => 'Categories reordered successfully.'
            ]);

        } catch (\Exception $e) {
            \Log::error('Banner category reorder failed: ' . $e->getMessage(), [
                'category_ids' => $request->category_ids
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to reorder categories.'
            ], 500);
        }
    }

    /**
     * Get category statistics.
     */
    public function statistics()
    {
        try {
            $stats = [
                'total_categories' => BannerCategory::count(),
                'active_categories' => BannerCategory::where('is_active', true)->count(),
                'categories_with_banners' => BannerCategory::has('banners')->count(),
                'empty_categories' => BannerCategory::doesntHave('banners')->count(),
                'total_banners' => \App\Models\Banner::count(),
                'recent_categories' => BannerCategory::latest()
                    ->limit(5)
                    ->get()
                    ->map(function ($category) {
                        return [
                            'id' => $category->id,
                            'name' => $category->name,
                            'slug' => $category->slug,
                            'is_active' => $category->is_active,
                            'created_at' => $category->created_at->format('M j, Y'),
                        ];
                    }),
                'popular_categories' => BannerCategory::withCount('banners')
                    ->orderBy('banners_count', 'desc')
                    ->limit(5)
                    ->get()
                    ->map(function ($category) {
                        return [
                            'id' => $category->id,
                            'name' => $category->name,
                            'banners_count' => $category->banners_count,
                        ];
                    }),
            ];

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            \Log::error('Banner category statistics failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to load statistics.'
            ], 500);
        }
    }

    /**
     * Export categories data.
     */
    public function export(Request $request)
    {
        try {
            $query = BannerCategory::withCount('banners');

            // Apply search filter for export
            if ($request->filled('search')) {
                $search = $request->input('search');
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%")
                      ->orWhere('slug', 'like', "%{$search}%");
                });
            }

            $categories = $query->orderBy('display_order')->get();

            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="banner-categories-' . date('Y-m-d') . '.csv"',
            ];

            $callback = function() use ($categories) {
                $file = fopen('php://output', 'w');
                
                // CSV headers
                fputcsv($file, [
                    'ID', 'Name', 'Slug', 'Description', 'Is Active', 'Display Order', 
                    'Banners Count', 'Created At', 'Updated At'
                ]);

                foreach ($categories as $category) {
                    fputcsv($file, [
                        $category->id,
                        $category->name,
                        $category->slug,
                        $category->description,
                        $category->is_active ? 'Yes' : 'No',
                        $category->display_order,
                        $category->banners_count,
                        $category->created_at->format('Y-m-d H:i:s'),
                        $category->updated_at->format('Y-m-d H:i:s'),
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);

        } catch (\Exception $e) {
            \Log::error('Banner category export failed: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Failed to export categories. Please try again.');
        }
    }

    /**
     * Bulk actions for categories.
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:activate,deactivate,delete',
            'category_ids' => 'required|array|min:1',
            'category_ids.*' => 'exists:banner_categories,id',
        ]);

        try {
            $action = $request->input('action');
            $categoryIds = $request->input('category_ids');

            switch ($action) {
                case 'activate':
                    $count = BannerCategory::whereIn('id', $categoryIds)
                        ->update(['is_active' => true]);
                    $message = "{$count} category(s) activated successfully.";
                    break;

                case 'deactivate':
                    $count = BannerCategory::whereIn('id', $categoryIds)
                        ->update(['is_active' => false]);
                    $message = "{$count} category(s) deactivated successfully.";
                    break;

                case 'delete':
                    // Check if any categories have banners
                    $categoriesWithBanners = BannerCategory::whereIn('id', $categoryIds)
                        ->has('banners')
                        ->count();

                    if ($categoriesWithBanners > 0) {
                        return redirect()->back()
                            ->with('error', "Cannot delete {$categoriesWithBanners} category(s) that have associated banners.");
                    }

                    $count = BannerCategory::whereIn('id', $categoryIds)->delete();
                    $message = "{$count} category(s) deleted successfully.";
                    break;

                default:
                    return redirect()->back()
                        ->with('error', 'Invalid action selected.');
            }

            return redirect()->back()
                ->with('success', $message);

        } catch (\Exception $e) {
            \Log::error('Banner category bulk action failed: ' . $e->getMessage(), [
                'action' => $request->input('action'),
                'category_ids' => $request->input('category_ids')
            ]);

            return redirect()->back()
                ->with('error', 'Bulk action failed. Please try again.');
        }
    }
}