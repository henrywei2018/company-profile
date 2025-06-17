<?php
// File: app/Http/Controllers/BlogController.php

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
            'Read our latest insights, industry news, and project updates.',
            'blog, news, insights, articles'
        );

        // Set breadcrumb
        $this->setBreadcrumb([
            ['name' => 'Blog', 'url' => route('blog.index')]
        ]);

        // Build query dengan filter
        $query = Post::published()->with(['author', 'categories']);

        if ($request->filled('category')) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('excerpt', 'like', '%' . $request->search . '%')
                  ->orWhere('content', 'like', '%' . $request->search . '%');
            });
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
            default:
                $query->latest('published_at');
        }

        $posts = $query->paginate(12)->withQueryString();

        // Sidebar data
        $categories = PostCategory::withCount(['publishedPosts'])
            ->orderBy('name')
            ->get();

        $recentPosts = Post::published()
            ->with(['author', 'categories'])
            ->latest('published_at')
            ->limit(5)
            ->get();

        $featuredPosts = Post::published()
            ->featured()
            ->with(['author', 'categories'])
            ->limit(3)
            ->get();

        return view('pages.blog.index', compact(
            'posts',
            'categories',
            'recentPosts',
            'featuredPosts'
        ));
    }

    public function show(Post $post)
    {
        // Check if post is published
        if (!$post->is_published) {
            abort(404);
        }

        // Set page meta
        $this->setPageMeta(
            $post->title,
            $post->excerpt,
            'blog, article, ' . $post->title
        );

        // Set breadcrumb
        $this->setBreadcrumb([
            ['name' => 'Blog', 'url' => route('blog.index')],
            ['name' => $post->title, 'url' => route('blog.show', $post->slug)]
        ]);

        $post->load(['author', 'categories']);

        // Get related posts
        $relatedPosts = Post::published()
            ->where('id', '!=', $post->id)
            ->whereHas('categories', function ($query) use ($post) {
                $query->whereIn('categories.id', $post->categories->pluck('id'));
            })
            ->with(['author', 'categories'])
            ->limit(3)
            ->get();

        return view('pages.blog.show', compact(
            'post',
            'relatedPosts'
        ));
    }
}