<?php
// File: app/Http/Controllers/Admin/PostController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\PostCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Services\FileUploadService;

class PostController extends Controller
{
    protected $fileUploadService;

    /**
     * Create a new controller instance.
     *
     * @param FileUploadService $fileUploadService
     */
    public function __construct(FileUploadService $fileUploadService)
    {
        $this->fileUploadService = $fileUploadService;
    }

    /**
     * Display a listing of the posts.
     */
    public function index(Request $request)
    {
        $posts = Post::with(['author', 'categories'])
            ->when($request->filled('category'), function ($query) use ($request) {
                return $query->whereHas('categories', function ($q) use ($request) {
                    $q->where('post_categories.id', $request->category);
                });
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                return $query->where('status', $request->status);
            })
            ->when($request->filled('search'), function ($query) use ($request) {
                return $query->where(function ($q) use ($request) {
                    $q->where('title', 'like', "%{$request->search}%")
                        ->orWhere('excerpt', 'like', "%{$request->search}%")
                        ->orWhere('content', 'like', "%{$request->search}%");
                });
            })
            ->latest()
            ->paginate(10);
        
        $categories = PostCategory::withCount('posts')->get();
        
        return view('admin.posts.index', compact('posts', 'categories'));
    }

    /**
     * Show the form for creating a new post.
     */
    public function create()
    {
        $categories = PostCategory::all();
        
        return view('admin.posts.create', compact('categories'));
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
            'seo_title' => 'nullable|string|max:255',
            'seo_description' => 'nullable|string',
            'seo_keywords' => 'nullable|string',
        ]);
        
        // Generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }
        
        // Set current user as author
        $validated['user_id'] = Auth::id();
        
        // Set published_at date if status is published and date not provided
        if ($validated['status'] === 'published' && empty($validated['published_at'])) {
            $validated['published_at'] = now();
        }
        
        // Create post
        $post = Post::create($validated);
        
        // Attach categories
        if (!empty($validated['categories'])) {
            $post->categories()->attach($validated['categories']);
        }
        
        // Handle featured image upload
        if ($request->hasFile('featured_image')) {
            $path = $this->fileUploadService->uploadImage(
                $request->file('featured_image'),
                'posts',
                null,
                1200
            );
            
            // Create thumbnail
            $thumbPath = $this->fileUploadService->createThumbnail(
                $request->file('featured_image'),
                'posts/thumbnails',
                null,
                400,
                300
            );
            
            $post->update(['featured_image' => $path]);
        }
        
        // Handle SEO
        if ($request->filled('seo_title') || $request->filled('seo_description') || $request->filled('seo_keywords')) {
            $post->updateSeo([
                'title' => $request->seo_title,
                'description' => $request->seo_description,
                'keywords' => $request->seo_keywords,
            ]);
        }
        
        return redirect()->route('admin.posts.index')
            ->with('success', 'Post created successfully!');
    }

    /**
     * Display the specified post.
     */
    public function show(Post $post)
    {
        $post->load(['author', 'categories', 'seo']);
        
        return view('admin.posts.show', compact('post'));
    }

    /**
     * Show the form for editing the specified post.
     */
    public function edit(Post $post)
    {
        $post->load(['categories', 'seo']);
        $categories = PostCategory::all();
        
        return view('admin.posts.edit', compact('post', 'categories'));
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
            'seo_title' => 'nullable|string|max:255',
            'seo_description' => 'nullable|string',
            'seo_keywords' => 'nullable|string',
        ]);
        
        // Generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }
        
        // Set published_at date if status changed to published and date not provided
        if ($validated['status'] === 'published' && $post->status !== 'published' && empty($validated['published_at'])) {
            $validated['published_at'] = now();
        }
        
        // Update post
        $post->update($validated);
        
        // Sync categories
        if (isset($validated['categories'])) {
            $post->categories()->sync($validated['categories']);
        } else {
            $post->categories()->detach();
        }
        
        // Handle featured image upload
        if ($request->hasFile('featured_image')) {
            // Delete old image if exists
            if ($post->featured_image) {
                Storage::disk('public')->delete($post->featured_image);
                
                // Try to delete thumbnail too
                $thumbPath = 'posts/thumbnails/' . basename($post->featured_image);
                if (Storage::disk('public')->exists($thumbPath)) {
                    Storage::disk('public')->delete($thumbPath);
                }
            }
            
            $path = $this->fileUploadService->uploadImage(
                $request->file('featured_image'),
                'posts',
                null,
                1200
            );
            
            // Create thumbnail
            $thumbPath = $this->fileUploadService->createThumbnail(
                $request->file('featured_image'),
                'posts/thumbnails',
                null,
                400,
                300
            );
            
            $post->update(['featured_image' => $path]);
        }
        
        // Handle SEO
        $post->updateSeo([
            'title' => $request->seo_title,
            'description' => $request->seo_description,
            'keywords' => $request->seo_keywords,
        ]);
        
        return redirect()->route('admin.posts.index')
            ->with('success', 'Post updated successfully!');
    }

    /**
     * Remove the specified post.
     */
    public function destroy(Post $post)
    {
        // Delete featured image
        if ($post->featured_image) {
            Storage::disk('public')->delete($post->featured_image);
            
            // Try to delete thumbnail too
            $thumbPath = 'posts/thumbnails/' . basename($post->featured_image);
            if (Storage::disk('public')->exists($thumbPath)) {
                Storage::disk('public')->delete($thumbPath);
            }
        }
        
        // Delete post
        $post->delete();
        
        return redirect()->route('admin.posts.index')
            ->with('success', 'Post deleted successfully!');
    }
    
    /**
     * Toggle featured status
     */
    public function toggleFeatured(Post $post)
    {
        $post->update([
            'featured' => !$post->featured
        ]);
        
        return redirect()->back()
            ->with('success', 'Post featured status updated!');
    }
    
    /**
     * Change post status
     */
    public function changeStatus(Request $request, Post $post)
    {
        $request->validate([
            'status' => 'required|in:draft,published,archived',
        ]);
        
        $oldStatus = $post->status;
        $newStatus = $request->status;
        
        // If changing to published, set published_at date
        if ($newStatus === 'published' && $oldStatus !== 'published') {
            $post->update([
                'status' => $newStatus,
                'published_at' => now(),
            ]);
        } else {
            $post->update(['status' => $newStatus]);
        }
        
        return redirect()->back()
            ->with('success', 'Post status changed to ' . ucfirst($newStatus));
    }
}