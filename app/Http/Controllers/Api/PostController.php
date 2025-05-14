<?php
// File: app/Http/Controllers/Api/PostController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Models\PostCategory;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class PostController extends Controller
{
    /**
     * Display a listing of the posts.
     *
     * @param Request $request
     * @return ResourceCollection
     */
    public function index(Request $request)
    {
        $posts = Post::query()
            ->published()
            ->with(['author', 'categories'])
            ->when($request->filled('category'), function ($query) use ($request) {
                return $query->whereHas('categories', function ($q) use ($request) {
                    $q->where('slug', $request->category);
                });
            })
            ->when($request->filled('featured'), function ($query) {
                return $query->where('featured', true);
            })
            ->when($request->filled('search'), function ($query) use ($request) {
                return $query->where(function ($q) use ($request) {
                    $q->where('title', 'like', "%{$request->search}%")
                      ->orWhere('excerpt', 'like', "%{$request->search}%")
                      ->orWhere('content', 'like', "%{$request->search}%");
                });
            })
            ->latest('published_at')
            ->paginate($request->input('per_page', 12));
        
        return PostResource::collection($posts);
    }

    /**
     * Display the specified post.
     *
     * @param string $slug
     * @return PostResource
     */
    public function show($slug)
    {
        $post = Post::where('slug', $slug)
            ->published()
            ->with(['author', 'categories'])
            ->firstOrFail();
        
        return new PostResource($post);
    }
    
    /**
     * Get recent posts.
     *
     * @param Request $request
     * @return ResourceCollection
     */
    public function recent(Request $request)
    {
        $limit = $request->input('limit', 5);
        
        $posts = Post::published()
            ->with(['author', 'categories'])
            ->latest('published_at')
            ->take($limit)
            ->get();
        
        return PostResource::collection($posts);
    }
    
    /**
     * Get featured posts.
     *
     * @param Request $request
     * @return ResourceCollection
     */
    public function featured(Request $request)
    {
        $limit = $request->input('limit', 5);
        
        $posts = Post::published()
            ->where('featured', true)
            ->with(['author', 'categories'])
            ->latest('published_at')
            ->take($limit)
            ->get();
        
        return PostResource::collection($posts);
    }
    
    /**
     * Get related posts.
     *
     * @param string $slug
     * @param Request $request
     * @return ResourceCollection
     */
    public function related($slug, Request $request)
    {
        $post = Post::where('slug', $slug)
            ->published()
            ->with('categories')
            ->firstOrFail();
        
        $limit = $request->input('limit', 3);
        
        $relatedPosts = Post::published()
            ->where('id', '!=', $post->id)
            ->whereHas('categories', function ($query) use ($post) {
                $query->whereIn('post_categories.id', $post->categories->pluck('id'));
            })
            ->with(['author', 'categories'])
            ->latest('published_at')
            ->take($limit)
            ->get();
        
        return PostResource::collection($relatedPosts);
    }
    
    /**
     * Get post categories.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function categories()
    {
        $categories = PostCategory::withCount(['posts' => function ($query) {
                $query->published();
            }])
            ->get()
            ->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'slug' => $category->slug,
                    'description' => $category->description,
                    'posts_count' => $category->posts_count
                ];
            });
            
        return response()->json([
            'data' => $categories
        ]);
    }
    
    /**
     * Get post archives (years and months).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function archives()
    {
        $archives = Post::published()
            ->selectRaw('YEAR(published_at) as year, MONTH(published_at) as month, COUNT(*) as post_count')
            ->groupBy('year', 'month')
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->get()
            ->map(function ($item) {
                return [
                    'year' => $item->year,
                    'month' => $item->month,
                    'month_name' => date('F', mktime(0, 0, 0, $item->month, 1)),
                    'post_count' => $item->post_count
                ];
            });
            
        return response()->json([
            'data' => $archives
        ]);
    }
}