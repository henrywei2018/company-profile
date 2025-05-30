<?php

namespace App\Services;

use App\Models\ProjectCategory;
use App\Services\FileUploadService;
use App\Facades\Notifications;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProjectCategoryService
{
    protected FileUploadService $fileUploadService;

    public function __construct(FileUploadService $fileUploadService)
    {
        $this->fileUploadService = $fileUploadService;
    }

    public function getFilteredCategories(array $filters = [], int $perPage = 15)
    {
        $query = ProjectCategory::withCount('projects');

        if (!empty($filters['search'])) {
            $query->where('name', 'like', '%' . $filters['search'] . '%');
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        return $query->ordered()->paginate($perPage);
    }

    public function createCategory(array $data, ?UploadedFile $icon = null): ProjectCategory
    {
        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);
        
        if (!isset($data['sort_order'])) {
            $data['sort_order'] = ProjectCategory::max('sort_order') + 1;
        }

        $category = ProjectCategory::create($data);

        if ($icon) {
            $path = $this->fileUploadService->uploadImage(
                $icon,
                'project-categories',
                null,
                200,
                200
            );
            $category->update(['icon' => $path]);
        }

        // Send notification to admins
        Notifications::send('project_category.created', $category);

        return $category;
    }

    public function updateCategory(ProjectCategory $category, array $data, ?UploadedFile $icon = null): ProjectCategory
    {
        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);

        if ($icon) {
            if ($category->icon) {
                Storage::disk('public')->delete($category->icon);
            }

            $path = $this->fileUploadService->uploadImage(
                $icon,
                'project-categories',
                null,
                200,
                200
            );
            $data['icon'] = $path;
        }

        $category->update($data);

        // Send notification to admins
        Notifications::send('project_category.updated', $category);

        return $category;
    }

    public function deleteCategory(ProjectCategory $category): bool
    {
        if ($category->projects()->count() > 0) {
            throw new \Exception('Cannot delete category with associated projects');
        }

        if ($category->icon) {
            Storage::disk('public')->delete($category->icon);
        }

        // Send notification to admins
        Notifications::send('project_category.deleted', $category);

        return $category->delete();
    }

    public function toggleActive(ProjectCategory $category): ProjectCategory
    {
        $wasActive = $category->is_active;
        $category->update(['is_active' => !$category->is_active]);

        // Send notification for status change
        $notificationType = $category->is_active ? 'project_category.activated' : 'project_category.deactivated';
        Notifications::send($notificationType, $category);

        return $category;
    }

    public function updateOrder(array $order): bool
    {
        foreach ($order as $index => $id) {
            ProjectCategory::where('id', $id)->update(['sort_order' => $index + 1]);
        }

        // Send notification about reordering
        Notifications::send('project_category.reordered', [
            'message' => 'Project categories have been reordered',
            'count' => count($order)
        ]);

        return true;
    }

    public function getStatistics(): array
    {
        return [
            'total' => ProjectCategory::count(),
            'active' => ProjectCategory::where('is_active', true)->count(),
            'with_projects' => ProjectCategory::has('projects')->count(),
            'empty' => ProjectCategory::doesntHave('projects')->count(),
            'most_used' => ProjectCategory::withCount('projects')
                ->orderBy('projects_count', 'desc')
                ->first(),
        ];
    }

    public function bulkToggleActive(array $categoryIds, bool $active): int
    {
        $updated = ProjectCategory::whereIn('id', $categoryIds)
            ->update(['is_active' => $active]);

        if ($updated > 0) {
            $notificationType = $active ? 'project_category.bulk_activated' : 'project_category.bulk_deactivated';
            Notifications::send($notificationType, [
                'count' => $updated,
                'status' => $active ? 'activated' : 'deactivated'
            ]);
        }

        return $updated;
    }

    public function bulkDelete(array $categoryIds): int
    {
        $categories = ProjectCategory::whereIn('id', $categoryIds)
            ->doesntHave('projects')
            ->get();

        $deleted = 0;
        foreach ($categories as $category) {
            if ($category->icon) {
                Storage::disk('public')->delete($category->icon);
            }
            $category->delete();
            $deleted++;
        }

        if ($deleted > 0) {
            Notifications::send('project_category.bulk_deleted', [
                'count' => $deleted
            ]);
        }

        return $deleted;
    }

    public function duplicate(ProjectCategory $category, array $overrides = []): ProjectCategory
    {
        $data = array_merge($category->toArray(), $overrides, [
            'name' => $overrides['name'] ?? $category->name . ' (Copy)',
            'slug' => Str::slug($overrides['name'] ?? $category->name . ' Copy'),
            'sort_order' => ProjectCategory::max('sort_order') + 1,
        ]);

        unset($data['id'], $data['created_at'], $data['updated_at']);

        $newCategory = ProjectCategory::create($data);

        // Copy icon if exists
        if ($category->icon) {
            $extension = pathinfo($category->icon, PATHINFO_EXTENSION);
            $newPath = 'project-categories/' . time() . '_' . uniqid() . '.' . $extension;
            
            if (Storage::disk('public')->copy($category->icon, $newPath)) {
                $newCategory->update(['icon' => $newPath]);
            }
        }

        // Send notification
        Notifications::send('project_category.duplicated', $newCategory);

        return $newCategory;
    }
}