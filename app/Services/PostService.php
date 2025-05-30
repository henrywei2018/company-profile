<?php

namespace App\Services;

use App\Models\Post;
use App\Models\PostCategory;
use App\Services\FileUploadService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PostService
{
    protected FileUploadService $fileUploadService;

    public function __construct(FileUploadService $fileUploadService)
    {
        $this->fileUploadService = $fileUploadService;
    }

    public function getFilteredPosts(array $filters = [], int $perPage = 15)
    {
        $query = Post::with(['author', 'categories']);

        if (!empty($filters['search'])) {
            $query->where(function($q) use ($filters) {
                $q->where('title', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('excerpt', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('content', 'like', '%' . $filters['search'] . '%');
            });
        }

        if (!empty($filters['category'])) {
            $query->whereHas('categories', function($q) use ($filters) {
                $q->where('post_categories.id', $filters['category']);
            });
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['featured'])) {
            $query->where('featured', $filters['featured']);
        }

        return $query->latest()->paginate($perPage);
    }

    public function createPost(array $data, ?UploadedFile $featuredImage = null): Post
    {
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['title']);
        }

        if ($data['status'] === 'published' && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        $post = Post::create($data);

        if (!empty($data['categories'])) {
            $post->categories()->attach($data['categories']);
        }

        if ($featuredImage) {
            $path = $this->fileUploadService->uploadImage(
                $featuredImage,
                'posts',
                null,
                1200
            );

            $thumbPath = $this->fileUploadService->uploadImage(
                $featuredImage,
                'posts/thumbnails',
                null,
                400,
                300
            );

            $post->update(['featured_image' => $path]);
        }

        return $post;
    }

    public function updatePost(Post $post, array $data, ?UploadedFile $featuredImage = null): Post
    {
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['title']);
        }

        if ($data['status'] === 'published' && $post->status !== 'published' && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        $post->update($data);

        if (isset($data['categories'])) {
            $post->categories()->sync($data['categories']);
        }

        if ($featuredImage) {
            if ($post->featured_image) {
                Storage::disk('public')->delete($post->featured_image);
                
                $thumbPath = 'posts/thumbnails/' . basename($post->featured_image);
                if (Storage::disk('public')->exists($thumbPath)) {
                    Storage::disk('public')->delete($thumbPath);
                }
            }

            $path = $this->fileUploadService->uploadImage(
                $featuredImage,
                'posts',
                null,
                1200
            );

            $post->update(['featured_image' => $path]);
        }

        return $post;
    }

    public function deletePost(Post $post): bool
    {
        if ($post->featured_image) {
            Storage::disk('public')->delete($post->featured_image);
            
            $thumbPath = 'posts/thumbnails/' . basename($post->featured_image);
            if (Storage::disk('public')->exists($thumbPath)) {
                Storage::disk('public')->delete($thumbPath);
            }
        }

        return $post->delete();
    }

    public function toggleFeatured(Post $post): Post
    {
        $post->update(['featured' => !$post->featured]);
        return $post;
    }

    public function changeStatus(Post $post, string $status): Post
    {
        $updateData = ['status' => $status];
        
        if ($status === 'published' && $post->status !== 'published') {
            $updateData['published_at'] = now();
        }

        $post->update($updateData);
        return $post;
    }

    public function getStatistics(): array
    {
        return [
            'total' => Post::count(),
            'published' => Post::where('status', 'published')->count(),
            'draft' => Post::where('status', 'draft')->count(),
            'archived' => Post::where('status', 'archived')->count(),
            'featured' => Post::where('featured', true)->count(),
            'this_month' => Post::whereMonth('created_at', now()->month)->count(),
        ];
    }
}