<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PostCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class BlogController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->shareBaseData();
    }
    /**
     * Display the blog page with filtering, search, and pagination.
     */
    public function index(Request $request)
    {
        // Build query with filters
        $query = Post::published()->with(['author', 'categories']);

        // Apply filters
        if ($request->filled('category')) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('featured') && $request->featured == '1') {
            $query->featured();
        }

        // Apply sorting
        $sortBy = $request->get('sort', 'latest');
        switch ($sortBy) {
            case 'oldest':
                $query->oldest('published_at');
                break;
            case 'popular':
                // You can implement view counts later
                $query->latest('published_at');
                break;
            default:
                $query->latest('published_at');
        }

        $posts = $query->paginate(12)->withQueryString();

        // Sidebar data
        $categories = PostCategory::query()
            ->hasPublishedPosts()
            ->withPublishedPostsCount()
            ->orderBy('name')
            ->get();

        $recentPosts = Post::query()
            ->published()
            ->with(['author', 'categories'])
            ->latest('published_at')
            ->limit(5)
            ->get();

        $featuredPosts = Post::query()
            ->published()
            ->featured()
            ->with(['author', 'categories'])
            ->limit(3)
            ->get();

        // Archive data for sidebar
        $archiveData = Post::query()
            ->published()
            ->selectRaw('YEAR(published_at) as year, MONTH(published_at) as month, COUNT(*) as count')
            ->groupByRaw('YEAR(published_at), MONTH(published_at)')
            ->orderByRaw('YEAR(published_at) DESC, MONTH(published_at) DESC')
            ->limit(12)
            ->get();

        // SEO Data
        $seoData = [
            'title' => 'Blog - CV Usaha Prima Lestari',
            'description' => 'Read our latest insights, industry news, and project updates. Stay informed about construction trends and our company developments.',
            'keywords' => 'blog, articles, construction, insights, CV Usaha Prima Lestari',
            'breadcrumbs' => [
                ['name' => 'Home', 'url' => route('home')],
                ['name' => 'Blog', 'url' => route('blog.index'), 'active' => true]
            ]
        ];

        // Customize SEO based on filters
        if ($request->filled('search')) {
            $seoData['title'] = "Search Results for '{$request->search}' - Blog";
            $seoData['description'] = "Search results for '{$request->search}' in our blog articles.";
        }

        if ($request->filled('category')) {
            $category = $categories->where('slug', $request->category)->first();
            if ($category) {
                $seoData['title'] = "{$category->name} - Blog Category";
                $seoData['description'] = $category->description ?: "Browse all posts in the {$category->name} category.";
                $seoData['breadcrumbs'][] = ['name' => $category->name, 'url' => route('blog.index', ['category' => $category->slug]), 'active' => true];
            }
        }

        return view('pages.blog.index', compact(
            'posts',
            'categories',
            'recentPosts',
            'featuredPosts',
            'archiveData',
            'seoData'
        ));
    }

    /**
     * Display single blog post.
     */
    public function show(Post $post)
    {
        // Only show published posts
        if ($post->status !== 'published' || $post->published_at > now()) {
            abort(404);
        }

        $post->load(['author', 'categories']);

        // Get related posts
        $relatedPosts = $this->getRelatedPosts($post, 4);

        // Sidebar data
        $categories = PostCategory::query()
            ->hasPublishedPosts()
            ->withPublishedPostsCount()
            ->orderBy('name')
            ->get();

        $recentPosts = Post::query()
            ->published()
            ->with(['author', 'categories'])
            ->where('id', '!=', $post->id)
            ->latest('published_at')
            ->limit(5)
            ->get();

        // SEO Data
        $seoData = [
            'title' => $post->title,
            'description' => $post->excerpt ?: Str::limit(strip_tags($post->content), 160),
            'keywords' => $post->categories->pluck('name')->join(', '),
            'breadcrumbs' => [
                ['name' => 'Home', 'url' => route('home')],
                ['name' => 'Blog', 'url' => route('blog.index')],
                ['name' => Str::limit($post->title, 50), 'url' => route('blog.show', $post->slug), 'active' => true]
            ]
        ];

        return view('pages.blog.show', compact(
            'post',
            'relatedPosts',
            'categories',
            'recentPosts',
            'seoData'
        ));
    }

    /**
     * Get related posts based on categories.
     */
    private function getRelatedPosts(Post $post, int $limit = 4)
    {
        $categoryIds = $post->categories->pluck('id');

        if ($categoryIds->isEmpty()) {
            return Post::query()
                ->published()
                ->with(['author', 'categories'])
                ->where('id', '!=', $post->id)
                ->latest('published_at')
                ->limit($limit)
                ->get();
        }

        return Post::query()
            ->published()
            ->with(['author', 'categories'])
            ->where('id', '!=', $post->id)
            ->whereHas('categories', function ($query) use ($categoryIds) {
                $query->whereIn('post_categories.id', $categoryIds);
            })
            ->latest('published_at')
            ->limit($limit)
            ->get();
    }
}