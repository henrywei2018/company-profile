<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\PostCategory;
use App\Services\FileUploadService;
use App\Facades\Notifications;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PostController extends Controller
{
    protected FileUploadService $fileUploadService;

    public function __construct(FileUploadService $fileUploadService)
    {
        $this->fileUploadService = $fileUploadService;
    }

    /**
     * Display a listing of the posts.
     */
    public function index(Request $request)
    {
        try {
            $posts = Post::with(['author', 'categories'])
                ->when($request->filled('category'), function ($query) use ($request) {
                    return $query->byCategory($request->category);
                })
                ->when($request->filled('status'), function ($query) use ($request) {
                    return $query->byStatus($request->status);
                })
                ->when($request->filled('search'), function ($query) use ($request) {
                    return $query->search($request->search);
                })
                ->latest()
                ->paginate(10);

            $categories = PostCategory::withPostsCount()->orderBy('name')->get();

            return view('admin.posts.index', compact('posts', 'categories'));
        } catch (\Exception $e) {
            Log::error('Failed to load posts: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load posts.');
        }
    }

    /**
     * Show the form for creating a new post.
     */
    public function create()
    {
        try {
            $categories = PostCategory::orderBy('name')->get();
            return view('admin.posts.create', compact('categories'));
        } catch (\Exception $e) {
            Log::error('Failed to load post create form: ' . $e->getMessage());
            return redirect()->route('admin.posts.index')->with('error', 'Failed to load create form.');
        }
    }

    /**
     * Store a newly created post.
     */
    public function store(Request $request)
{
    $validated = $request->validate([
        'title' => 'required|string|max:255',
        'slug' => 'nullable|string|max:255|unique:posts,slug',
        'excerpt' => 'nullable|string',
        'content' => 'required|string',
        'categories' => 'nullable|array',
        'categories.*' => 'exists:post_categories,id',
        'status' => 'required|in:draft,published,archived',
        'featured' => 'boolean',
        'featured_image' => 'nullable|image|max:2048',
        'published_at' => 'nullable|date',
    ]);

    try {
        // Generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
            
            // Ensure slug is unique
            $originalSlug = $validated['slug'];
            $counter = 1;
            while (Post::where('slug', $validated['slug'])->exists()) {
                $validated['slug'] = $originalSlug . '-' . $counter;
                $counter++;
            }
        }

        // Set current user as author
        $validated['user_id'] = auth()->id();
        
        // Handle featured checkbox
        $validated['featured'] = $request->has('featured') ? true : false;
        
        // Set published_at date if status is published and date not provided
        if ($validated['status'] === 'published' && empty($validated['published_at'])) {
            $validated['published_at'] = now();
        }

        // Create post (remove categories from main data)
        $postData = collect($validated)->except(['categories', 'featured_image'])->toArray();
        $post = Post::create($postData);

        // Handle featured image upload
        if ($request->hasFile('featured_image')) {
            $image = $request->file('featured_image');
            $filename = time() . '_' . $image->getClientOriginalName();
            $path = $image->storeAs('posts', $filename, 'public');
            $post->update(['featured_image' => $path]);
        }

        // Attach categories if provided
        if (!empty($validated['categories'])) {
            $post->categories()->attach($validated['categories']);
        }

        return redirect()->route('admin.posts.index')
            ->with('success', 'Post created successfully!');
            
    } catch (\Exception $e) {
        \Log::error('Failed to create post: ' . $e->getMessage());
        
        return redirect()->back()
            ->withInput()
            ->with('error', 'Failed to create post. Please try again.');
    }
}

    /**
     * Display the specified post.
     */
    public function show(Post $post)
    {
        try {
            $post->load(['author', 'categories', 'seo']);
            return view('admin.posts.show', compact('post'));
        } catch (\Exception $e) {
            Log::error('Failed to show post: ' . $e->getMessage());
            return redirect()->route('admin.posts.index')->with('error', 'Failed to load post.');
        }
    }

    /**
     * Show the form for editing the specified post.
     */
    public function edit(Post $post)
    {
        try {
            $post->load(['categories', 'seo']);
            $categories = PostCategory::orderBy('name')->get();
            
            return view('admin.posts.edit', compact('post', 'categories'));
        } catch (\Exception $e) {
            Log::error('Failed to load post edit form: ' . $e->getMessage());
            return redirect()->route('admin.posts.index')->with('error', 'Failed to load edit form.');
        }
    }

    /**
     * Update the specified post.
     */
    public function update(Request $request, Post $post)
{
    $validated = $request->validate([
        'title' => 'required|string|max:255',
        'slug' => 'nullable|string|max:255|unique:posts,slug,' . $post->id,
        'excerpt' => 'nullable|string',
        'content' => 'required|string',
        'categories' => 'nullable|array',
        'categories.*' => 'exists:post_categories,id',
        'status' => 'required|in:draft,published,archived',
        'featured' => 'boolean',
        'featured_image' => 'nullable|image|max:2048',
        'published_at' => 'nullable|date',
    ]);

    try {
        // Generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        // Handle featured checkbox
        $validated['featured'] = $request->has('featured') ? true : false;
        
        // Set published_at date if status changed to published and date not provided
        if ($validated['status'] === 'published' && $post->status !== 'published' && empty($validated['published_at'])) {
            $validated['published_at'] = now();
        }

        // Update post (remove categories and image from main data)
        $postData = collect($validated)->except(['categories', 'featured_image'])->toArray();
        $post->update($postData);

        // Handle featured image upload
        if ($request->hasFile('featured_image')) {
            // Delete old image
            if ($post->featured_image) {
                \Storage::disk('public')->delete($post->featured_image);
            }
            
            $image = $request->file('featured_image');
            $filename = time() . '_' . $image->getClientOriginalName();
            $path = $image->storeAs('posts', $filename, 'public');
            $post->update(['featured_image' => $path]);
        }

        // Sync categories
        if (isset($validated['categories'])) {
            $post->categories()->sync($validated['categories']);
        } else {
            $post->categories()->detach();
        }

        return redirect()->route('admin.posts.index')
            ->with('success', 'Post updated successfully!');
            
    } catch (\Exception $e) {
        \Log::error('Failed to update post: ' . $e->getMessage());
        
        return redirect()->back()
            ->withInput()
            ->with('error', 'Failed to update post. Please try again.');
    }
}

    /**
     * Remove the specified post.
     */
    public function destroy(Post $post)
    {
        try {
            $postTitle = $post->title;
            
            // Delete will automatically clean up images via model boot method
            $post->delete();

            Log::info('Post deleted successfully', [
                'post_title' => $postTitle,
                'deleted_by' => Auth::id()
            ]);

            return redirect()->route('admin.posts.index')
                ->with('success', 'Post deleted successfully!');

        } catch (\Exception $e) {
            Log::error('Failed to delete post: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to delete post.');
        }
    }

    /**
     * Toggle featured status
     */
    public function toggleFeatured(Post $post)
    {
        try {
            $post->toggleFeatured();

            $status = $post->featured ? 'featured' : 'unfeatured';
            
            Log::info("Post {$status}", [
                'post_id' => $post->id,
                'title' => $post->title,
                'updated_by' => Auth::id()
            ]);

            return redirect()->back()
                ->with('success', "Post {$status} successfully!");

        } catch (\Exception $e) {
            Log::error('Failed to toggle featured status: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to update featured status.');
        }
    }

    /**
     * Change post status
     */
    public function changeStatus(Request $request, Post $post)
    {
        $request->validate([
            'status' => 'required|in:draft,published,archived',
        ]);

        try {
            $oldStatus = $post->status;
            $newStatus = $request->status;

            // Update status with proper published_at handling
            if ($newStatus === 'published' && $oldStatus !== 'published') {
                $post->publish();
            } else {
                $post->update(['status' => $newStatus]);
            }

            Log::info('Post status changed', [
                'post_id' => $post->id,
                'title' => $post->title,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'updated_by' => Auth::id()
            ]);

            // Send notification
            try {
                if ($newStatus === 'published') {
                    Notifications::send('post.published', $post);
                } else {
                    Notifications::send('post.status_changed', $post);
                }
            } catch (\Exception $e) {
                Log::warning('Failed to send post status change notification: ' . $e->getMessage());
            }

            return redirect()->back()
                ->with('success', 'Post status changed to ' . ucfirst($newStatus));

        } catch (\Exception $e) {
            Log::error('Failed to change post status: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to change post status.');
        }
    }

    /**
     * Bulk actions for posts
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:delete,publish,unpublish,archive,feature,unfeature',
            'posts' => 'required|array',
            'posts.*' => 'exists:posts,id'
        ]);

        try {
            $posts = Post::whereIn('id', $request->posts)->get();
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
     * Duplicate a post
     */
    public function duplicate(Post $post)
    {
        try {
            $newPost = $post->replicate();
            $newPost->title = $post->title . ' (Copy)';
            $newPost->slug = $this->generateUniqueSlug($newPost->title);
            $newPost->status = 'draft';
            $newPost->published_at = null;
            $newPost->featured = false;
            $newPost->user_id = Auth::id();
            
            // Handle featured image duplication
            if ($post->featured_image) {
                try {
                    $originalPath = $post->featured_image;
                    $extension = pathinfo($originalPath, PATHINFO_EXTENSION);
                    $newFilename = 'posts/' . Str::random(40) . '.' . $extension;
                    
                    if (Storage::disk('public')->exists($originalPath)) {
                        Storage::disk('public')->copy($originalPath, $newFilename);
                        $newPost->featured_image = $newFilename;
                        
                        // Copy thumbnail too
                        $originalThumbnail = 'posts/thumbnails/' . basename($originalPath);
                        $newThumbnail = 'posts/thumbnails/' . basename($newFilename);
                        
                        if (Storage::disk('public')->exists($originalThumbnail)) {
                            Storage::disk('public')->copy($originalThumbnail, $newThumbnail);
                        }
                    }
                } catch (\Exception $e) {
                    Log::warning('Failed to duplicate featured image: ' . $e->getMessage());
                    $newPost->featured_image = null;
                }
            }
            
            $newPost->save();
            
            // Duplicate categories
            $newPost->categories()->attach($post->categories->pluck('id'));
            
            // Duplicate SEO data
            if ($post->seo) {
                $newPost->updateSeo([
                    'title' => $post->seo->title,
                    'description' => $post->seo->description,
                    'keywords' => $post->seo->keywords,
                ]);
            }

            Log::info('Post duplicated successfully', [
                'original_post_id' => $post->id,
                'new_post_id' => $newPost->id,
                'duplicated_by' => Auth::id()
            ]);

            return redirect()->route('admin.posts.edit', $newPost)
                ->with('success', 'Post duplicated successfully! You can now edit the copy.');

        } catch (\Exception $e) {
            Log::error('Failed to duplicate post: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to duplicate post.');
        }
    }

    /**
     * Export posts
     */
    public function export(Request $request)
    {
        try {
            $query = Post::with(['author', 'categories']);
            
            // Apply filters
            if ($request->filled('status')) {
                $query->byStatus($request->status);
            }
            
            if ($request->filled('category')) {
                $query->byCategory($request->category);
            }
            
            if ($request->filled('from_date')) {
                $query->where('created_at', '>=', $request->from_date);
            }
            
            if ($request->filled('to_date')) {
                $query->where('created_at', '<=', $request->to_date);
            }
            
            $posts = $query->get();
            
            $exportData = [
                'exported_at' => now()->toISOString(),
                'exported_by' => Auth::user()->name,
                'total_posts' => $posts->count(),
                'posts' => $posts->map(function ($post) {
                    return [
                        'id' => $post->id,
                        'title' => $post->title,
                        'slug' => $post->slug,
                        'excerpt' => $post->excerpt,
                        'content' => $post->content,
                        'status' => $post->status,
                        'featured' => $post->featured,
                        'published_at' => $post->published_at?->toISOString(),
                        'author' => $post->author->name,
                        'categories' => $post->categories->pluck('name'),
                        'featured_image_url' => $post->featured_image_url,
                        'reading_time' => $post->reading_time,
                        'created_at' => $post->created_at->toISOString(),
                        'updated_at' => $post->updated_at->toISOString(),
                        'seo' => $post->seo ? [
                            'title' => $post->seo->title,
                            'description' => $post->seo->description,
                            'keywords' => $post->seo->keywords,
                        ] : null,
                    ];
                })
            ];

            $filename = 'posts-export-' . now()->format('Y-m-d-H-i-s') . '.json';

            return response()->json($exportData)
                ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");

        } catch (\Exception $e) {
            Log::error('Failed to export posts: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to export posts.');
        }
    }

    /**
     * Get posts statistics
     */
    public function statistics()
    {
        try {
            $stats = [
                'total_posts' => Post::count(),
                'published_posts' => Post::published()->count(),
                'draft_posts' => Post::byStatus('draft')->count(),
                'archived_posts' => Post::byStatus('archived')->count(),
                'featured_posts' => Post::featured()->count(),
                'posts_this_month' => Post::whereMonth('created_at', now()->month)
                                         ->whereYear('created_at', now()->year)
                                         ->count(),
                'posts_last_month' => Post::whereMonth('created_at', now()->subMonth()->month)
                                         ->whereYear('created_at', now()->subMonth()->year)
                                         ->count(),
                'recent_posts' => Post::with('author')
                                    ->latest()
                                    ->limit(5)
                                    ->get()
                                    ->map(function ($post) {
                                        return [
                                            'id' => $post->id,
                                            'title' => $post->title,
                                            'status' => $post->status,
                                            'author' => $post->author->name,
                                            'created_at' => $post->created_at->diffForHumans(),
                                        ];
                                    }),
                'popular_categories' => PostCategory::withCount('publishedPosts')
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
            ];

            return response()->json(['success' => true, 'data' => $stats]);

        } catch (\Exception $e) {
            Log::error('Failed to get posts statistics: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => 'Failed to get statistics.'], 500);
        }
    }

    /**
     * Generate unique slug
     */
    private function generateUniqueSlug(string $title, ?int $excludeId = null): string
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $counter = 1;

        while (true) {
            $query = Post::where('slug', $slug);
            
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

    /**
     * Remove featured image
     */
    public function removeFeaturedImage(Post $post)
    {
        try {
            if ($post->featured_image) {
                // Delete main image
                Storage::disk('public')->delete($post->featured_image);
                
                // Delete thumbnail
                $thumbnailPath = 'posts/thumbnails/' . basename($post->featured_image);
                if (Storage::disk('public')->exists($thumbnailPath)) {
                    Storage::disk('public')->delete($thumbnailPath);
                }
                
                $post->update(['featured_image' => null]);
                
                Log::info('Featured image removed', [
                    'post_id' => $post->id,
                    'removed_by' => Auth::id()
                ]);
            }

            return redirect()->back()->with('success', 'Featured image removed successfully.');

        } catch (\Exception $e) {
            Log::error('Failed to remove featured image: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to remove featured image.');
        }
    }
}