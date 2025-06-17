<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PostCategory;
use Illuminate\Http\Request;

class BlogController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index(Request $request)
    {
        // Set page meta
        $this->setPageMeta(
            'Blog - ' . $this->siteConfig['site_title'],
            'Read our latest insights, industry news, and project updates from our construction and engineering experts.',
            'blog, construction news, engineering insights, project updates',
            asset($this->siteConfig['site_logo'])
        );

        // Get filter parameters
        $search = $request->get('search');
        $category = $request->get('category');
        $sortBy = $request->get('sort', 'latest');
        $perPage = $request->get('per_page', 9);

        // Build posts query
        $postsQuery = Post::published()
            ->with(['author', 'categories']);

        // Apply search filter
        if ($search) {
            $postsQuery->where(function ($query) use ($search) {
                $query->where('title', 'like', "%{$search}%")
                      ->orWhere('excerpt', 'like', "%{$search}%")
                      ->orWhere('content', 'like', "%{$search}%");
            });
        }

        // Apply category filter
        if ($category && $category !== 'all') {
            $postsQuery->whereHas('categories', function ($query) use ($category) {
                $query->where('slug', $category);
            });
        }

        // Apply sorting
        switch ($sortBy) {
            case 'oldest':
                $postsQuery->oldest('published_at');
                break;
            case 'title':
                $postsQuery->orderBy('title', 'asc');
                break;
            case 'featured':
                $postsQuery->orderBy('featured', 'desc')
                          ->orderBy('published_at', 'desc');
                break;
            case 'latest':
            default:
                $postsQuery->latest('published_at');
                break;
        }

        // Get paginated posts
        $posts = $postsQuery->paginate($perPage)->withQueryString();

        // Get categories for filter dropdown
        $categories = PostCategory::withPublishedPostsCount()
            ->hasPublishedPosts()
            ->orderBy('name', 'asc')
            ->get();

        // Sidebar data
        $recentPosts = Post::published()
            ->with(['author', 'categories'])
            ->latest('published_at')
            ->limit(5)
            ->get();

        $featuredPosts = Post::published()
            ->featured()
            ->with(['author', 'categories'])
            ->latest('published_at')
            ->limit(3)
            ->get();

        // Blog statistics
        $stats = [
            'total_posts' => Post::published()->count(),
            'total_categories' => $categories->count(),
            'this_month_posts' => Post::published()
                ->whereMonth('published_at', now()->month)
                ->whereYear('published_at', now()->year)
                ->count(),
        ];

        return view('pages.blog.index', compact(
            'posts',
            'categories',
            'recentPosts',
            'featuredPosts',
            'stats',
            'search',
            'category',
            'sortBy'
        ));
    }

    public function show(Post $post)
    {
        // Check if post is published
        if ($post->status !== 'published' || $post->published_at > now()) {
            abort(404);
        }

        // Set page meta
        $this->setPageMeta(
            $post->title . ' - ' . $this->siteConfig['site_title'],
            $post->excerpt ?: strip_tags(substr($post->content, 0, 160)),
            'blog, article, ' . $post->categories->pluck('name')->implode(', '),
            $post->featured_image ? asset('storage/' . $post->featured_image) : asset($this->siteConfig['site_logo'])
        );

        $post->load(['author', 'categories']);

        // Get related posts (same categories or featured)
        $relatedPosts = Post::published()
            ->where('id', '!=', $post->id)
            ->where(function($query) use ($post) {
                $query->whereHas('categories', function ($q) use ($post) {
                    $q->whereIn('post_categories.id', $post->categories->pluck('id'));
                })->orWhere('featured', true);
            })
            ->with(['author', 'categories'])
            ->latest('published_at')
            ->limit(3)
            ->get();

        // Sidebar data for single post
        $recentPosts = Post::published()
            ->where('id', '!=', $post->id)
            ->with(['author', 'categories'])
            ->latest('published_at')
            ->limit(5)
            ->get();

        $categories = PostCategory::withPublishedPostsCount()
            ->hasPublishedPosts()
            ->orderBy('name', 'asc')
            ->get();

        return view('pages.blog.show', compact(
            'post',
            'relatedPosts',
            'recentPosts',
            'categories'
        ));
    }
}