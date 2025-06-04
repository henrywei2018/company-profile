<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PostCategory;
use App\Facades\Notifications;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PostCategoryController extends Controller
{
    /**
     * Display a listing of the categories.
     */
    public function index(Request $request)
    {
        try {
            $categories = PostCategory::withCount(['posts', 'publishedPosts'])
                ->when($request->filled('search'), function ($query) use ($request) {
                    return $query->search($request->search);
                })
                ->orderBy('name')
                ->paginate(15);

            return view('admin.post-categories.index', compact('categories'));
        } catch (\Exception $e) {
            Log::error('Failed to load post categories: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load post categories.');
        }
    }

    /**
     * Show the form for creating a new category.
     */
    public function create()
    {
        try {
            return view('admin.post-categories.create');
        } catch (\Exception $e) {
            Log::error('Failed to load post category create form: ' . $e->getMessage());
            return redirect()->route('admin.post-categories.index')->with('error', 'Failed to load create form.');
        }
    }

    /**
     * Store a newly created category.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:post_categories,slug',
            'description' => 'nullable|string|max:500',
            'seo_title' => 'nullable|string|max:60',
            'seo_description' => 'nullable|string|max:160',
            'seo_keywords' => 'nullable|string|max:255',
        ]);

        try {
            // Generate slug if not provided
            if (empty($validated['slug'])) {
                $validated['slug'] = $this->generateUniqueSlug($validated['name']);
            }

            // Create category
            $category = PostCategory::create([
                'name' => $validated['name'],
                'slug' => $validated['slug'],
                'description' => $validated['description'],
            ]);

            // Handle SEO data
            if ($request->filled(['seo_title', 'seo_description', 'seo_keywords'])) {
                $seoData = array_filter([
                    'title' => $request->seo_title,
                    'description' => $request->seo_description,
                    'keywords' => $request->seo_keywords,
                ]);

                if (!empty($seoData)) {
                    $category->updateSeo($seoData);
                }
            }

            Log::info('Post category created successfully', [
                'category_id' => $category->id,
                'name' => $category->name,
                'created_by' => Auth::id()
            ]);

            // Send notification
            try {
                Notifications::send('post_category.created', $category);
            } catch (\Exception $e) {
                Log::warning('Failed to send post category notification: ' . $e->getMessage());
            }

            return redirect()->route('admin.post-categories.index')
                ->with('success', 'Post category created successfully!');

        } catch (\Exception $e) {
            Log::error('Failed to create post category: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create post category. Please try again.');
        }
    }

    /**
     * Display the specified category.
     */
    public function show(PostCategory $postCategory)
    {
        try {
            $postCategory->loadCount(['posts', 'publishedPosts']);
            $recentPosts = $postCategory->posts()
                                       ->with('author')
                                       ->latest()
                                       ->limit(10)
                                       ->get();

            return view('admin.post-categories.show', compact('postCategory', 'recentPosts'));
        } catch (\Exception $e) {
            Log::error('Failed to show post category: ' . $e->getMessage());
            return redirect()->route('admin.post-categories.index')->with('error', 'Failed to load post category.');
        }
    }

    /**
     * Show the form for editing the specified category.
     */
    public function edit(PostCategory $postCategory)
    {
        try {
            $postCategory->load('seo');
            return view('admin.post-categories.edit', compact('postCategory'));
        } catch (\Exception $e) {
            Log::error('Failed to load post category edit form: ' . $e->getMessage());
            return redirect()->route('admin.post-categories.index')->with('error', 'Failed to load edit form.');
        }
    }

    /**
     * Update the specified category.
     */
    public function update(Request $request, PostCategory $postCategory)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('post_categories', 'slug')->ignore($postCategory->id)
            ],
            'description' => 'nullable|string|max:500',
            'seo_title' => 'nullable|string|max:60',
            'seo_description' => 'nullable|string|max:160',
            'seo_keywords' => 'nullable|string|max:255',
        ]);

        try {
            // Generate slug if not provided
            if (empty($validated['slug'])) {
                $validated['slug'] = $this->generateUniqueSlug($validated['name'], $postCategory->id);
            }

            // Update category
            $postCategory->update([
                'name' => $validated['name'],
                'slug' => $validated['slug'],
                'description' => $validated['description'],
            ]);

            // Handle SEO data
            $seoData = array_filter([
                'title' => $request->seo_title,
                'description' => $request->seo_description,
                'keywords' => $request->seo_keywords,
            ]);

            if (!empty($seoData)) {
                $postCategory->updateSeo($seoData);
            }

            Log::info('Post category updated successfully', [
                'category_id' => $postCategory->id,
                'name' => $postCategory->name,
                'updated_by' => Auth::id()
            ]);

            // Send notification
            try {
                Notifications::send('post_category.updated', $postCategory);
            } catch (\Exception $e) {
                Log::warning('Failed to send post category update notification: ' . $e->getMessage());
            }

            return redirect()->route('admin.post-categories.index')
                ->with('success', 'Post category updated successfully!');

        } catch (\Exception $e) {
            Log::error('Failed to update post category: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update post category. Please try again.');
        }
    }

    /**
     * Remove the specified category.
     */
    public function destroy(PostCategory $postCategory)
    {
        try {
            // Check if category has posts
            if ($postCategory->posts()->count() > 0) {
                return redirect()->route('admin.post-categories.index')
                    ->with('error', 'Cannot delete category that has posts associated with it!');
            }

            $categoryName = $postCategory->name;
            $postCategory->delete();

            Log::info('Post category deleted successfully', [
                'category_name' => $categoryName,
                'deleted_by' => Auth::id()
            ]);

            return redirect()->route('admin.post-categories.index')
                ->with('success', 'Post category deleted successfully!');

        } catch (\Exception $e) {
            Log::error('Failed to delete post category: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to delete post category.');
        }
    }

    /**
     * Bulk delete categories
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'categories' => 'required|array',
            'categories.*' => 'exists:post_categories,id'
        ]);

        try {
            $categories = PostCategory::whereIn('id', $request->categories)
                                    ->withCount('posts')
                                    ->get();

            $deletedCount = 0;
            $skippedCount = 0;

            foreach ($categories as $category) {
                if ($category->posts_count > 0) {
                    $skippedCount++;
                } else {
                    $category->delete();
                    $deletedCount++;
                }
            }

            $message = "Deleted {$deletedCount} categories.";
            if ($skippedCount > 0) {
                $message .= " Skipped {$skippedCount} categories that have associated posts.";
            }

            Log::info('Bulk delete post categories', [
                'deleted_count' => $deletedCount,
                'skipped_count' => $skippedCount,
                'performed_by' => Auth::id()
            ]);

            return redirect()->back()->with('success', $message);

        } catch (\Exception $e) {
            Log::error('Failed to bulk delete post categories: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to delete categories.');
        }
    }

    /**
     * Export categories
     */
    public function export()
    {
        try {
            $categories = PostCategory::withCount(['posts', 'publishedPosts'])->get();

            $exportData = [
                'exported_at' => now()->toISOString(),
                'exported_by' => Auth::user()->name,
                'total_categories' => $categories->count(),
                'categories' => $categories->map(function ($category) {
                    return [
                        'id' => $category->id,
                        'name' => $category->name,
                        'slug' => $category->slug,
                        'description' => $category->description,
                        'posts_count' => $category->posts_count,
                        'published_posts_count' => $category->published_posts_count,
                        'created_at' => $category->created_at->toISOString(),
                        'updated_at' => $category->updated_at->toISOString(),
                        'seo' => $category->seo ? [
                            'title' => $category->seo->title,
                            'description' => $category->seo->description,
                            'keywords' => $category->seo->keywords,
                        ] : null,
                    ];
                })
            ];

            $filename = 'post-categories-export-' . now()->format('Y-m-d-H-i-s') . '.json';

            return response()->json($exportData)
                ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");

        } catch (\Exception $e) {
            Log::error('Failed to export post categories: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to export categories.');
        }
    }

    /**
     * Get categories statistics
     */
    public function statistics()
    {
        try {
            $stats = [
                'total_categories' => PostCategory::count(),
                'categories_with_posts' => PostCategory::has('posts')->count(),
                'empty_categories' => PostCategory::doesntHave('posts')->count(),
                'average_posts_per_category' => round(PostCategory::withCount('posts')->avg('posts_count'), 2),
                'most_popular_categories' => PostCategory::withCount('publishedPosts')
                                                        ->having('published_posts_count', '>', 0)
                                                        ->orderByDesc('published_posts_count')
                                                        ->limit(5)
                                                        ->get()
                                                        ->map(function ($category) {
                                                            return [
                                                                'name' => $category->name,
                                                                'posts_count' => $category->published_posts_count,
                                                            ];
                                                        }),
                'recent_categories' => PostCategory::latest()
                                                  ->limit(5)
                                                  ->get()
                                                  ->map(function ($category) {
                                                      return [
                                                          'name' => $category->name,
                                                          'created_at' => $category->created_at->diffForHumans(),
                                                      ];
                                                  }),
            ];

            return response()->json(['success' => true, 'data' => $stats]);

        } catch (\Exception $e) {
            Log::error('Failed to get post categories statistics: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => 'Failed to get statistics.'], 500);
        }
    }

    /**
     * Generate unique slug
     */
    private function generateUniqueSlug(string $name, ?int $excludeId = null): string
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $counter = 1;

        while (true) {
            $query = PostCategory::where('slug', $slug);
            
            if ($excludeId) {
                $query->where('id', '!=', $excludeId);
            }
            
            if (!$query->exists()) {
                break;
            }
            
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}