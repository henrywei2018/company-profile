<?php

namespace App\Services;

use App\Models\PostCategory;
use App\Facades\Notifications;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class PostCategoryService
{
    /**
     * Get filtered categories with improved query efficiency
     */
    public function getFilteredCategories(array $filters = [], int $perPage = 15)
    {
        $query = PostCategory::withCount(['posts']);

        // Search filter - improved with multiple fields
        if (!empty($filters['search'])) {
            $searchTerm = trim($filters['search']);
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%")
                  ->orWhere('slug', 'like', "%{$searchTerm}%");
            });
        }

        // Sorting with validation
        $sortField = $filters['sort'] ?? 'name';
        $sortDirection = in_array($filters['direction'] ?? 'asc', ['asc', 'desc']) 
            ? $filters['direction'] 
            : 'asc';
        
        switch ($sortField) {
            case 'posts_count':
                $query->orderBy('posts_count', $sortDirection);
                break;
            case 'created_at':
                $query->orderBy('created_at', $sortDirection);
                break;
            case 'updated_at':
                $query->orderBy('updated_at', $sortDirection);
                break;
            case 'name':
            default:
                $query->orderBy('name', $sortDirection);
                break;
        }

        return $query->paginate($perPage);
    }

    /**
     * Create new category with improved validation
     */
    public function createCategory(array $data): PostCategory
    {
        // Generate slug if not provided
        if (empty($data['slug'])) {
            $data['slug'] = $this->generateUniqueSlug($data['name']);
        }

        // Clean and validate data
        $data = $this->cleanCategoryData($data);

        $category = PostCategory::create($data);

        // Send notification
        try {
            Notifications::send('post_category.created', $category);
        } catch (\Exception $e) {
            Log::warning('Failed to send category creation notification: ' . $e->getMessage());
        }

        return $category;
    }

    /**
     * Update category with improved handling
     */
    public function updateCategory(PostCategory $category, array $data): PostCategory
    {
        // Generate slug if empty or changed
        if (empty($data['slug']) || ($data['name'] !== $category->name && $data['slug'] === $category->slug)) {
            $data['slug'] = $this->generateUniqueSlug($data['name'], $category->id);
        }

        // Clean and validate data
        $data = $this->cleanCategoryData($data);

        $category->update($data);

        // Send notification
        try {
            Notifications::send('post_category.updated', $category);
        } catch (\Exception $e) {
            Log::warning('Failed to send category update notification: ' . $e->getMessage());
        }

        return $category;
    }

    /**
     * Delete category with safety checks
     */
    public function deleteCategory(PostCategory $category): bool
    {
        // Check if category has posts
        $postsCount = $category->posts()->count();
        if ($postsCount > 0) {
            throw new \Exception("Cannot delete category '{$category->name}' because it has {$postsCount} associated posts.");
        }

        // Send notification before deletion
        try {
            Notifications::send('post_category.deleted', $category);
        } catch (\Exception $e) {
            Log::warning('Failed to send category deletion notification: ' . $e->getMessage());
        }

        return $category->delete();
    }

    /**
     * Get comprehensive statistics
     */
    public function getStatistics(): array
    {
        $totalCategories = PostCategory::count();
        $categoriesWithPosts = PostCategory::has('posts')->count();
        $emptyCategories = $totalCategories - $categoriesWithPosts;

        // Get categories with published posts count
        $categoriesWithPublishedPosts = PostCategory::whereHas('posts', function($q) {
            $q->where('status', 'published')->where('published_at', '<=', now());
        })->count();

        // Get most used category
        $mostUsedCategory = PostCategory::withCount(['posts' => function($q) {
            $q->where('status', 'published')->where('published_at', '<=', now());
        }])
        ->orderBy('posts_count', 'desc')
        ->first();

        // Get average posts per category
        $avgPostsPerCategory = $totalCategories > 0 
            ? round(PostCategory::withCount('posts')->avg('posts_count'), 2) 
            : 0;

        return [
            'total' => $totalCategories,
            'with_posts' => $categoriesWithPosts,
            'empty' => $emptyCategories,
            'with_published_posts' => $categoriesWithPublishedPosts,
            'most_used' => $mostUsedCategory ? [
                'name' => $mostUsedCategory->name,
                'posts_count' => $mostUsedCategory->posts_count
            ] : null,
            'average_posts_per_category' => $avgPostsPerCategory,
            'recently_created' => PostCategory::where('created_at', '>=', now()->subDays(30))->count(),
            'recently_updated' => PostCategory::where('updated_at', '>=', now()->subDays(7))->count(),
        ];
    }

    /**
     * Bulk delete with safety checks
     */
    public function bulkDelete(array $categoryIds): int
    {
        $deletedCount = 0;

        foreach ($categoryIds as $categoryId) {
            try {
                $category = PostCategory::find($categoryId);
                if ($category && $category->posts()->count() === 0) {
                    $category->delete();
                    $deletedCount++;
                }
            } catch (\Exception $e) {
                Log::warning("Failed to delete category ID {$categoryId}: " . $e->getMessage());
            }
        }

        // Send bulk notification
        if ($deletedCount > 0) {
            try {
                Notifications::send('post_category.bulk_deleted', [
                    'count' => $deletedCount
                ]);
            } catch (\Exception $e) {
                Log::warning('Failed to send bulk deletion notification: ' . $e->getMessage());
            }
        }

        return $deletedCount;
    }

    /**
     * Get popular categories (by published posts count)
     */
    public function getPopularCategories(int $limit = 5): \Illuminate\Database\Eloquent\Collection
    {
        return PostCategory::withCount(['posts' => function($q) {
                $q->where('status', 'published')->where('published_at', '<=', now());
            }])
            ->having('posts_count', '>', 0)
            ->orderBy('posts_count', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Search categories with advanced filtering
     */
    public function searchCategories(string $query, array $options = []): \Illuminate\Database\Eloquent\Collection
    {
        $searchQuery = PostCategory::where(function ($q) use ($query) {
            $q->where('name', 'like', "%{$query}%")
              ->orWhere('description', 'like', "%{$query}%")
              ->orWhere('slug', 'like', "%{$query}%");
        });

        // Apply filters
        if (isset($options['has_posts']) && $options['has_posts']) {
            $searchQuery->has('posts');
        }

        if (isset($options['min_posts']) && is_numeric($options['min_posts'])) {
            $searchQuery->withCount('posts')
                       ->having('posts_count', '>=', $options['min_posts']);
        }

        $limit = $options['limit'] ?? 20;
        
        return $searchQuery->limit($limit)->get();
    }

    /**
     * Get category suggestions based on name similarity
     */
    public function getSuggestions(string $name, int $limit = 5): \Illuminate\Database\Eloquent\Collection
    {
        return PostCategory::where('name', 'like', "%{$name}%")
            ->orWhere('slug', 'like', "%" . Str::slug($name) . "%")
            ->limit($limit)
            ->get();
    }

    /**
     * Merge categories - move all posts from source to target
     */
    public function mergeCategories(PostCategory $sourceCategory, PostCategory $targetCategory): bool
    {
        try {
            // Get all posts from source category
            $posts = $sourceCategory->posts;
            
            // Attach posts to target category (many-to-many, so posts can have multiple categories)
            foreach ($posts as $post) {
                $post->categories()->syncWithoutDetaching($targetCategory->id);
                // Optionally remove from source category
                $post->categories()->detach($sourceCategory->id);
            }

            // Delete source category
            $sourceCategory->delete();

            Log::info('Categories merged successfully', [
                'source_category' => $sourceCategory->name,
                'target_category' => $targetCategory->name,
                'posts_moved' => $posts->count()
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to merge categories: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Generate unique slug
     */
    protected function generateUniqueSlug(string $name, ?int $excludeId = null): string
    {
        $baseSlug = Str::slug($name);
        $slug = $baseSlug;
        $counter = 1;

        while (true) {
            $query = PostCategory::where('slug', $slug);
            
            if ($excludeId) {
                $query->where('id', '!=', $excludeId);
            }
            
            if (!$query->exists()) {
                break;
            }
            
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Clean and validate category data
     */
    protected function cleanCategoryData(array $data): array
    {
        // Trim strings
        foreach (['name', 'slug', 'description'] as $field) {
            if (isset($data[$field]) && is_string($data[$field])) {
                $data[$field] = trim($data[$field]);
                
                // Convert empty strings to null for optional fields
                if ($field !== 'name' && empty($data[$field])) {
                    $data[$field] = null;
                }
            }
        }

        // Validate slug format
        if (isset($data['slug']) && !empty($data['slug'])) {
            $data['slug'] = Str::slug($data['slug']);
        }

        return $data;
    }

    /**
     * Get category tree (if you implement hierarchical categories later)
     */
    public function getCategoryTree(): array
    {
        // For now, just return flat list, but structure is ready for hierarchy
        return PostCategory::withCount('posts')
            ->orderBy('name')
            ->get()
            ->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'slug' => $category->slug,
                    'posts_count' => $category->posts_count,
                    'children' => [] // Ready for future hierarchy
                ];
            })
            ->toArray();
    }
}