<?php

namespace App\Services;

use App\Models\PostCategory;
use App\Facades\Notifications;
use Illuminate\Support\Str;

class PostCategoryService
{
    public function getFilteredCategories(array $filters = [], int $perPage = 15)
    {
        $query = PostCategory::withCount('posts');

        if (!empty($filters['search'])) {
            $query->where('name', 'like', '%' . $filters['search'] . '%');
        }

        return $query->orderBy('name')->paginate($perPage);
    }

    public function createCategory(array $data): PostCategory
    {
        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);

        $category = PostCategory::create($data);

        // Send notification
        Notifications::send('post_category.created', $category);

        return $category;
    }

    public function updateCategory(PostCategory $category, array $data): PostCategory
    {
        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);

        $category->update($data);

        // Send notification
        Notifications::send('post_category.updated', $category);

        return $category;
    }

    public function deleteCategory(PostCategory $category): bool
    {
        if ($category->posts()->count() > 0) {
            throw new \Exception('Cannot delete category with associated posts');
        }

        // Send notification before deletion
        Notifications::send('post_category.deleted', $category);

        return $category->delete();
    }

    public function getStatistics(): array
    {
        return [
            'total' => PostCategory::count(),
            'with_posts' => PostCategory::has('posts')->count(),
            'empty' => PostCategory::doesntHave('posts')->count(),
            'most_used' => PostCategory::withCount('posts')
                ->orderBy('posts_count', 'desc')
                ->first(),
        ];
    }

    public function bulkDelete(array $categoryIds): int
    {
        $categories = PostCategory::whereIn('id', $categoryIds)
            ->doesntHave('posts')
            ->get();

        $deleted = $categories->count();

        if ($deleted > 0) {
            PostCategory::whereIn('id', $categories->pluck('id'))->delete();
            
            Notifications::send('post_category.bulk_deleted', [
                'count' => $deleted
            ]);
        }

        return $deleted;
    }

    public function getPopularCategories(int $limit = 5): \Illuminate\Database\Eloquent\Collection
    {
        return PostCategory::withCount('posts')
            ->orderBy('posts_count', 'desc')
            ->limit($limit)
            ->get();
    }
}