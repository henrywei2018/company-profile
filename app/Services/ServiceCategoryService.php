<?php

namespace App\Services;

use App\Models\ServiceCategory;
use App\Services\FileUploadService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ServiceCategoryService
{
    protected FileUploadService $fileUploadService;

    public function __construct(FileUploadService $fileUploadService)
    {
        $this->fileUploadService = $fileUploadService;
    }

    public function getFilteredCategories(array $filters = [], int $perPage = 15)
    {
        $query = ServiceCategory::withCount('services');

        if (!empty($filters['search'])) {
            $query->where('name', 'like', '%' . $filters['search'] . '%');
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        return $query->orderBy('name')->paginate($perPage);
    }

    public function createCategory(array $data, ?UploadedFile $icon = null): ServiceCategory
    {
        $data['slug'] = Str::slug($data['name']);
        
        $category = ServiceCategory::create($data);

        if ($icon) {
            $path = $icon->store('service-categories', 'public');
            $category->update(['icon' => $path]);
        }

        return $category;
    }

    public function updateCategory(ServiceCategory $category, array $data, ?UploadedFile $icon = null): ServiceCategory
    {
        $data['slug'] = Str::slug($data['name']);

        if ($icon) {
            if ($category->icon) {
                Storage::disk('public')->delete($category->icon);
            }

            $path = $icon->store('service-categories', 'public');
            $data['icon'] = $path;
        }

        $category->update($data);
        return $category;
    }

    public function deleteCategory(ServiceCategory $category): bool
    {
        if ($category->services()->count() > 0) {
            throw new \Exception('Cannot delete category with associated services');
        }

        if ($category->icon) {
            Storage::disk('public')->delete($category->icon);
        }

        return $category->delete();
    }

    public function toggleActive(ServiceCategory $category): ServiceCategory
    {
        $category->update(['is_active' => !$category->is_active]);
        return $category;
    }

    public function getStatistics(): array
    {
        return [
            'total' => ServiceCategory::count(),
            'active' => ServiceCategory::where('is_active', true)->count(),
            'with_services' => ServiceCategory::has('services')->count(),
            'empty' => ServiceCategory::doesntHave('services')->count(),
        ];
    }
}