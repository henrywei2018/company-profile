<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PostCategory;
use App\Services\PostCategoryService;
use App\Facades\Notifications;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PostCategoryController extends Controller
{
    protected PostCategoryService $postCategoryService;

    public function __construct(PostCategoryService $postCategoryService)
    {
        $this->postCategoryService = $postCategoryService;
    }

    /**
     * Display a listing of the categories.
     */
    public function index(Request $request)
    {
        try {
            $query = PostCategory::withCount(['posts', 'publishedPosts']);

            // Handle search
            if ($request->filled('search')) {
                $searchTerm = $request->search;
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('name', 'like', "%{$searchTerm}%")
                      ->orWhere('description', 'like', "%{$searchTerm}%")
                      ->orWhere('slug', 'like', "%{$searchTerm}%");
                });
            }

            // Handle sorting
            $sortField = $request->get('sort', 'name');
            $sortDirection = $request->get('direction', 'asc');
            
            // Validate sort field
            $allowedSortFields = ['name', 'slug', 'posts_count', 'published_posts_count', 'created_at', 'updated_at'];
            if (!in_array($sortField, $allowedSortFields)) {
                $sortField = 'name';
            }
            
            // Validate sort direction
            if (!in_array($sortDirection, ['asc', 'desc'])) {
                $sortDirection = 'asc';
            }

            $query->orderBy($sortField, $sortDirection);

            $categories = $query->paginate(15)->withQueryString();

            return view('admin.post-categories.index', compact('categories'));
        } catch (\Exception $e) {
            Log::error('Failed to load post categories: ' . $e->getMessage(), [
                'exception' => $e,
                'request_data' => $request->all()
            ]);
            
            return redirect()->back()->with('error', 'Failed to load post categories.');
        }
    }

    /**
     * Show the form for creating a new category.
     */
    public function create()
    {
        return view('admin.post-categories.create');
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
        ]);

        try {
            $category = $this->postCategoryService->createCategory($validated);

            Log::info('Post category created successfully', [
                'category_id' => $category->id,
                'name' => $category->name,
                'created_by' => Auth::id()
            ]);

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
            $postCategory->loadCount(['posts']);
            
            // Get recent posts in this category
            $recentPosts = $postCategory->posts()
                                       ->with('author')
                                       ->latest()
                                       ->limit(10)
                                       ->get();

            // Get category statistics
            $statistics = [
                'total_posts' => $postCategory->posts()->count(),
                'published_posts' => $postCategory->posts()->where('status', 'published')->count(),
                'draft_posts' => $postCategory->posts()->where('status', 'draft')->count(),
                'archived_posts' => $postCategory->posts()->where('status', 'archived')->count(),
                'featured_posts' => $postCategory->posts()->where('featured', true)->count(),
                'latest_post' => $postCategory->posts()->where('status', 'published')->latest('published_at')->first(),
            ];

            return view('admin.post-categories.show', compact('postCategory', 'recentPosts', 'statistics'));
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
        return view('admin.post-categories.edit', compact('postCategory'));
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
        ]);

        try {
            $category = $this->postCategoryService->updateCategory($postCategory, $validated);

            Log::info('Post category updated successfully', [
                'category_id' => $category->id,
                'name' => $category->name,
                'updated_by' => Auth::id()
            ]);

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
            $categoryName = $postCategory->name;
            $this->postCategoryService->deleteCategory($postCategory);

            Log::info('Post category deleted successfully', [
                'category_name' => $categoryName,
                'deleted_by' => Auth::id()
            ]);

            return redirect()->route('admin.post-categories.index')
                ->with('success', 'Post category deleted successfully!');

        } catch (\Exception $e) {
            Log::error('Failed to delete post category: ' . $e->getMessage());
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Bulk actions for categories
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:delete,publish,unpublish,archive,feature,unfeature',
            'posts' => 'required|array',
            'posts.*' => 'exists:posts,id'
        ]);

        try {
            $posts = PostCategory::whereIn('id', $request->posts)->get();
            $action = $request->action;
            $count = $posts->count();

            foreach ($posts as $post) {
                switch ($action) {
                    case 'delete':
                        $post->delete();
                        break;
                    case 'publish':
                        $post->publish();
                        break;
                    case 'unpublish':
                        $post->unpublish();
                        break;
                    case 'archive':
                        $post->archive();
                        break;
                    case 'feature':
                        $post->update(['featured' => true]);
                        break;
                    case 'unfeature':
                        $post->update(['featured' => false]);
                        break;
                }
            }

            Log::info("Bulk action performed on posts", [
                'action' => $action,
                'post_count' => $count,
                'performed_by' => Auth::id()
            ]);

            return redirect()->back()
                ->with('success', "Successfully {$action}d {$count} post(s).");

        } catch (\Exception $e) {
            Log::error('Failed to perform bulk action: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to perform bulk action.');
        }
    }

    /**
     * Export categories
     */
    public function export(Request $request)
    {
        try {
            $format = $request->get('format', 'json');
            $categories = PostCategory::withCount(['posts'])->get();

            $exportData = [
                'exported_at' => now()->toISOString(),
                'exported_by' => Auth::user()->name,
                'total_categories' => $categories->count(),
                'export_format' => $format,
                'categories' => $categories->map(function ($category) {
                    return [
                        'id' => $category->id,
                        'name' => $category->name,
                        'slug' => $category->slug,
                        'description' => $category->description,
                        'posts_count' => $category->posts_count,
                        'published_posts_count' => $category->posts()->where('status', 'published')->count(),
                        'created_at' => $category->created_at->toISOString(),
                        'updated_at' => $category->updated_at->toISOString(),
                    ];
                })
            ];

            $filename = 'post-categories-export-' . now()->format('Y-m-d-H-i-s');

            switch ($format) {
                case 'csv':
                    return $this->exportToCsv($exportData, $filename);
                case 'json':
                default:
                    return response()->json($exportData)
                        ->header('Content-Disposition', "attachment; filename=\"{$filename}.json\"");
            }

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
            $stats = $this->postCategoryService->getStatistics();
            
            // Add recent activity
            $recentActivity = PostCategory::latest('updated_at')
                                         ->limit(5)
                                         ->get()
                                         ->map(function ($category) {
                                             return [
                                                 'id' => $category->id,
                                                 'name' => $category->name,
                                                 'posts_count' => $category->posts()->count(),
                                                 'updated_at' => $category->updated_at->diffForHumans(),
                                             ];
                                         });

            $stats['recent_activity'] = $recentActivity;

            return response()->json(['success' => true, 'data' => $stats]);

        } catch (\Exception $e) {
            Log::error('Failed to get post categories statistics: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => 'Failed to get statistics.'], 500);
        }
    }

    /**
     * Get popular categories for dashboard widgets
     */
    public function getPopularCategories(Request $request)
    {
        try {
            $limit = $request->get('limit', 5);
            $categories = $this->postCategoryService->getPopularCategories($limit);

            return response()->json([
                'success' => true,
                'data' => $categories->map(function ($category) {
                    return [
                        'id' => $category->id,
                        'name' => $category->name,
                        'posts_count' => $category->posts_count,
                        'url' => route('admin.post-categories.show', $category),
                    ];
                })
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get popular categories: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => 'Failed to get categories.'], 500);
        }
    }

    /**
     * Export to CSV format
     */
    private function exportToCsv(array $data, string $filename): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}.csv\"",
        ];

        return response()->stream(function () use ($data) {
            $file = fopen('php://output', 'w');
            
            // Write CSV headers
            fputcsv($file, [
                'ID', 'Name', 'Slug', 'Description', 'Posts Count', 
                'Published Posts Count', 'Created At', 'Updated At'
            ]);
            
            // Write data rows
            foreach ($data['categories'] as $category) {
                fputcsv($file, [
                    $category['id'],
                    $category['name'],
                    $category['slug'],
                    $category['description'],
                    $category['posts_count'],
                    $category['published_posts_count'],
                    $category['created_at'],
                    $category['updated_at'],
                ]);
            }
            
            fclose($file);
        }, 200, $headers);
    }
}