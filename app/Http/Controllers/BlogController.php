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
        // Get all categories with post count
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
                      ->orWhere('excerpt', 'like', "%{$request->search}%")
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
        
        // Increment view count if tracking
        if (method_exists($post, 'incrementViews')) {
            $post->incrementViews();
        }
        
        return view('pages.blog-single', compact('post', 'recentPosts', 'relatedPosts'));
    }
    
    /**
     * Display posts by category.
     */
    public function category($slug)
    {
        // Find the category
        $category = PostCategory::where('slug', $slug)->firstOrFail();
        
        // Get posts in this category
        $posts = Post::published()
            ->whereHas('categories', function ($query) use ($slug) {
                $query->where('slug', $slug);
            })
            ->latest('published_at')
            ->paginate(9);
        
        // Get all categories with post count
        $categories = PostCategory::withCount(['posts' => function ($query) {
            $query->published();
        }])->get();
        
        return view('pages.blog-category', compact('posts', 'categories', 'category'));
    }
    
    /**
     * Display posts archive by year/month.
     */
    public function archive(Request $request, $year, $month = null)
    {
        $posts = Post::published()
            ->whereYear('published_at', $year)
            ->when($month, function ($query) use ($month) {
                return $query->whereMonth('published_at', $month);
            })
            ->latest('published_at')
            ->paginate(9);
        
        // Get all categories
        $categories = PostCategory::withCount(['posts' => function ($query) {
            $query->published();
        }])->get();
        
        // Format archive title
        $archiveTitle = $month 
            ? date('F Y', mktime(0, 0, 0, $month, 1, $year)) 
            : $year;
        
        return view('pages.blog-archive', compact('posts', 'categories', 'archiveTitle', 'year', 'month'));
    }
    
    /**
     * Display search results.
     */
    public function search(Request $request)
    {
        $query = $request->input('query');
        
        if (empty($query)) {
            return redirect()->route('blog.index');
        }
        
        $posts = Post::published()
            ->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('excerpt', 'like', "%{$query}%")
                  ->orWhere('content', 'like', "%{$query}%");
            })
            ->latest('published_at')
            ->paginate(9);
        
        // Get all categories
        $categories = PostCategory::withCount(['posts' => function ($query) {
            $query->published();
        }])->get();
        
        return view('pages.blog-search', compact('posts', 'categories', 'query'));
    }
}