<?php

namespace App\Services;

use App\Models\Post;
use App\Models\PostCategory;
use App\Services\FileUploadService;
use App\Facades\Notifications;
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

        if (!empty($filters['author'])) {
            $query->where('user_id', $filters['author']);
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

        // Send notifications based on status
        if ($data['status'] === 'published') {
            Notifications::send('post.published', $post);
        } else {
            Notifications::send('post.created', $post);
        }

        return $post;
    }

    public function updatePost(Post $post, array $data, ?UploadedFile $featuredImage = null): Post
    {
        $oldStatus = $post->status;

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

        // Send notifications based on status changes
        if ($oldStatus !== $post->status) {
            if ($post->status === 'published') {
                Notifications::send('post.published', $post);
            } elseif ($post->status === 'archived') {
                Notifications::send('post.archived', $post);
            } else {
                Notifications::send('post.status_updated', $post);
            }
        } else {
            Notifications::send('post.updated', $post);
        }

        return $post;
    }

    public function deletePost(Post $post): bool
    {
        // Send notification before deletion
        Notifications::send('post.deleted', $post);

        if ($post->featured_image) {
            Storage::disk('public')->delete($post->featured_image);
            
            $thumbPath = 'posts/thumbnails/' . basename($post->featured_image);
            if (Storage::disk('public')->exists($thumbPath)) {
                Storage::disk('public')->delete($thumbPath);
            }
        }

        return $post->delete();
    }

    public function publishPost(Post $post): Post
    {
        $post->update([
            'status' => 'published',
            'published_at' => now(),
        ]);

        // Send publication notification
        Notifications::send('post.published', $post);

        return $post;
    }

    public function unpublishPost(Post $post): Post
    {
        $post->update(['status' => 'draft']);

        // Send unpublish notification
        Notifications::send('post.unpublished', $post);

        return $post;
    }

    public function archivePost(Post $post): Post
    {
        $post->update(['status' => 'archived']);

        // Send archive notification
        Notifications::send('post.archived', $post);

        return $post;
    }

    public function toggleFeatured(Post $post): Post
    {
        $wasFeatured = $post->featured;
        $post->update(['featured' => !$post->featured]);

        // Send notification for featured status change
        $notificationType = $post->featured ? 'post.featured' : 'post.unfeatured';
        Notifications::send($notificationType, $post);

        return $post;
    }

    public function changeStatus(Post $post, string $status): Post
    {
        $oldStatus = $post->status;
        $updateData = ['status' => $status];
        
        if ($status === 'published' && $post->status !== 'published') {
            $updateData['published_at'] = now();
        }

        $post->update($updateData);

        // Send status-specific notification
        switch ($status) {
            case 'published':
                Notifications::send('post.published', $post);
                break;
            case 'archived':
                Notifications::send('post.archived', $post);
                break;
            default:
                Notifications::send('post.status_updated', $post);
        }

        return $post;
    }

    public function bulkChangeStatus(array $postIds, string $status): int
    {
        $posts = Post::whereIn('id', $postIds)->get();
        $updated = 0;

        foreach ($posts as $post) {
            $this->changeStatus($post, $status);
            $updated++;
        }

        // Send bulk notification
        if ($updated > 0) {
            Notifications::send('post.bulk_status_updated', [
                'count' => $updated,
                'status' => $status,
                'message' => "{$updated} post(s) status changed to {$status}"
            ]);
        }

        return $updated;
    }

    public function bulkToggleFeatured(array $postIds, bool $featured): int
    {
        $updated = Post::whereIn('id', $postIds)
            ->update(['featured' => $featured]);

        if ($updated > 0) {
            $notificationType = $featured ? 'post.bulk_featured' : 'post.bulk_unfeatured';
            Notifications::send($notificationType, [
                'count' => $updated,
                'status' => $featured ? 'featured' : 'unfeatured'
            ]);
        }

        return $updated;
    }

    public function bulkDelete(array $postIds): int
    {
        $posts = Post::whereIn('id', $postIds)->get();
        $deleted = 0;

        foreach ($posts as $post) {
            if ($post->featured_image) {
                Storage::disk('public')->delete($post->featured_image);
                
                $thumbPath = 'posts/thumbnails/' . basename($post->featured_image);
                if (Storage::disk('public')->exists($thumbPath)) {
                    Storage::disk('public')->delete($thumbPath);
                }
            }
            $post->delete();
            $deleted++;
        }

        if ($deleted > 0) {
            Notifications::send('post.bulk_deleted', [
                'count' => $deleted
            ]);
        }

        return $deleted;
    }

    public function schedulePost(Post $post, string $publishDate): Post
    {
        $post->update([
            'status' => 'scheduled',
            'published_at' => $publishDate,
        ]);

        // Send scheduling notification
        Notifications::send('post.scheduled', $post);

        return $post;
    }

    public function duplicatePost(Post $post, array $overrides = []): Post
    {
        $data = array_merge($post->toArray(), $overrides, [
            'title' => $overrides['title'] ?? $post->title . ' (Copy)',
            'slug' => Str::slug($overrides['title'] ?? $post->title . ' Copy'),
            'status' => 'draft', // Always create duplicates as drafts
            'published_at' => null,
            'featured' => false, // Don't duplicate featured status
        ]);

        unset($data['id'], $data['created_at'], $data['updated_at']);

        $newPost = Post::create($data);

        // Copy categories
        $newPost->categories()->attach($post->categories->pluck('id'));

        // Copy featured image if exists
        if ($post->featured_image) {
            $extension = pathinfo($post->featured_image, PATHINFO_EXTENSION);
            $newPath = 'posts/' . time() . '_' . uniqid() . '.' . $extension;
            
            if (Storage::disk('public')->copy($post->featured_image, $newPath)) {
                $newPost->update(['featured_image' => $newPath]);
            }
        }

        // Send notification
        Notifications::send('post.duplicated', $newPost);

        return $newPost;
    }

    public function getStatistics(): array
    {
        return [
            'total' => Post::count(),
            'published' => Post::where('status', 'published')->count(),
            'draft' => Post::where('status', 'draft')->count(),
            'archived' => Post::where('status', 'archived')->count(),
            'scheduled' => Post::where('status', 'scheduled')->count(),
            'featured' => Post::where('featured', true)->count(),
            'this_month' => Post::whereMonth('created_at', now()->month)->count(),
            'published_this_month' => Post::where('status', 'published')
                ->whereMonth('published_at', now()->month)
                ->count(),
            'by_author' => Post::join('users', 'posts.user_id', '=', 'users.id')
                ->selectRaw('users.name, COUNT(*) as count')
                ->groupBy('users.name')
                ->pluck('count', 'name')
                ->toArray(),
            'by_category' => Post::join('post_post_category', 'posts.id', '=', 'post_post_category.post_id')
                ->join('post_categories', 'post_post_category.post_category_id', '=', 'post_categories.id')
                ->selectRaw('post_categories.name, COUNT(*) as count')
                ->groupBy('post_categories.name')
                ->pluck('count', 'name')
                ->toArray(),
        ];
    }

    public function getPublishedPosts(int $limit = 10): \Illuminate\Database\Eloquent\Collection
    {
        return Post::where('status', 'published')
            ->with(['author', 'categories'])
            ->latest('published_at')
            ->limit($limit)
            ->get();
    }

    public function getFeaturedPosts(int $limit = 5): \Illuminate\Database\Eloquent\Collection
    {
        return Post::where('status', 'published')
            ->where('featured', true)
            ->with(['author', 'categories'])
            ->latest('published_at')
            ->limit($limit)
            ->get();
    }

    public function getRelatedPosts(Post $post, int $limit = 3): \Illuminate\Database\Eloquent\Collection
    {
        $categoryIds = $post->categories->pluck('id');

        return Post::where('status', 'published')
            ->where('id', '!=', $post->id)
            ->whereHas('categories', function($query) use ($categoryIds) {
                $query->whereIn('post_categories.id', $categoryIds);
            })
            ->with(['author', 'categories'])
            ->latest('published_at')
            ->limit($limit)
            ->get();
    }

    public function searchPosts(string $query, int $limit = 10): \Illuminate\Database\Eloquent\Collection
    {
        return Post::where('status', 'published')
            ->where(function($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('excerpt', 'like', "%{$query}%")
                  ->orWhere('content', 'like', "%{$query}%");
            })
            ->with(['author', 'categories'])
            ->latest('published_at')
            ->limit($limit)
            ->get();
    }

    public function getPostsByCategory(PostCategory $category, int $limit = 10): \Illuminate\Database\Eloquent\Collection
    {
        return Post::where('status', 'published')
            ->whereHas('categories', function($query) use ($category) {
                $query->where('post_categories.id', $category->id);
            })
            ->with(['author', 'categories'])
            ->latest('published_at')
            ->limit($limit)
            ->get();
    }

    public function getPostsByAuthor(\App\Models\User $author, int $limit = 10): \Illuminate\Database\Eloquent\Collection
    {
        return Post::where('status', 'published')
            ->where('user_id', $author->id)
            ->with(['author', 'categories'])
            ->latest('published_at')
            ->limit($limit)
            ->get();
    }

    public function updateSeo(Post $post, array $seoData): Post
    {
        $post->updateSeo($seoData);

        // Send notification about SEO update
        Notifications::send('post.seo_updated', $post);

        return $post;
    }

    public function getScheduledPosts(): \Illuminate\Database\Eloquent\Collection
    {
        return Post::where('status', 'scheduled')
            ->where('published_at', '<=', now())
            ->get();
    }

    public function publishScheduledPosts(): int
    {
        $scheduledPosts = $this->getScheduledPosts();
        $published = 0;

        foreach ($scheduledPosts as $post) {
            $this->publishPost($post);
            $published++;
        }

        if ($published > 0) {
            Notifications::send('post.auto_published', [
                'count' => $published,
                'message' => "{$published} scheduled post(s) have been automatically published"
            ]);
        }

        return $published;
    }
}