<?php
// File: app/Http/Controllers/BlogController.php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PostCategory;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    /**
     * Display a listing of blog posts.
     */
    public function index(Request $request)
    {
        // Get all categories
        $categories = PostCategory::withCount(['posts' => function ($query) {
            $query->published();
        }])->get();
        
        // Apply filters and pagination
        $posts = Post::published()
            ->with('author')
            ->when($request->filled('category'), function ($query) use ($request) {
                return $query->whereHas('categories', function ($q) use ($request) {
                    $q->where('slug', $request->category);
                });
            })
            ->when($request->filled('search'), function ($query) use ($request) {
                return $query->where(function ($q) use ($request) {
                    $q->where('title', 'like', "%{$request->search}%")
                      ->orWhere('content', 'like', "%{$request->search}%");
                });
            })
            ->latest('published_at')
            ->paginate(9);
        
        return view('pages.blog', compact('posts', 'categories'));
    }
    
    /**
     * Display the specified blog post.
     */
    public function show($slug)
    {
        // Find the post by slug
        $post = Post::where('slug', $slug)
            ->published()
            ->with(['author', 'categories'])
            ->firstOrFail();
        
        // Get recent posts
        $recentPosts = Post::published()
            ->where('id', '!=', $post->id)
            ->latest('published_at')
            ->take(4)
            ->get();
        
        // Get related posts
        $relatedPosts = Post::published()
            ->where('id', '!=', $post->id)
            ->whereHas('categories', function ($query) use ($post) {
                $query->whereIn('post_categories.id', $post->categories->pluck('id'));
            })
            ->latest('published_at')
            ->take(3)
            ->get();
        
        return view('pages.post-single', compact('post', 'recentPosts', 'relatedPosts'));
    }
}