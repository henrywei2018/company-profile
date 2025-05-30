<?php

namespace App\Services;

use App\Models\Service;
use App\Models\ServiceCategory;
use App\Services\FileUploadService;
use App\Facades\Notifications;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ServiceService
{
    protected FileUploadService $fileUploadService;

    public function __construct(FileUploadService $fileUploadService)
    {
        $this->fileUploadService = $fileUploadService;
    }

    public function getFilteredServices(array $filters = [], int $perPage = 15)
    {
        $query = Service::with(['category']);

        if (!empty($filters['search'])) {
            $query->where(function($q) use ($filters) {
                $q->where('title', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('short_description', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('description', 'like', '%' . $filters['search'] . '%');
            });
        }

        if (!empty($filters['category'])) {
            $query->where('category_id', $filters['category']);
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        if (isset($filters['featured'])) {
            $query->where('featured', $filters['featured']);
        }

        return $query->ordered()->paginate($perPage);
    }

    public function createService(array $data, ?UploadedFile $image = null, ?UploadedFile $icon = null): Service
    {
        $data['slug'] = $data['slug'] ?? Str::slug($data['title']);
        
        if (!isset($data['sort_order'])) {
            $data['sort_order'] = Service::max('sort_order') + 1;
        }

        $service = Service::create($data);

        // Handle image upload
        if ($image) {
            $path = $this->fileUploadService->uploadImage(
                $image,
                'services',
                null,
                1200,
                800
            );
            $service->update(['image' => $path]);
        }

        // Handle icon upload
        if ($icon) {
            $path = $this->fileUploadService->uploadImage(
                $icon,
                'services/icons',
                null,
                200,
                200
            );
            $service->update(['icon' => $path]);
        }

        // Send notification to admins
        Notifications::send('service.created', $service);

        return $service;
    }

    public function updateService(Service $service, array $data, ?UploadedFile $image = null, ?UploadedFile $icon = null): Service
    {
        $data['slug'] = $data['slug'] ?? Str::slug($data['title']);

        // Handle image upload
        if ($image) {
            if ($service->image) {
                Storage::disk('public')->delete($service->image);
            }

            $path = $this->fileUploadService->uploadImage(
                $image,
                'services',
                null,
                1200,
                800
            );
            $data['image'] = $path;
        }

        // Handle icon upload
        if ($icon) {
            if ($service->icon) {
                Storage::disk('public')->delete($service->icon);
            }

            $path = $this->fileUploadService->uploadImage(
                $icon,
                'services/icons',
                null,
                200,
                200
            );
            $data['icon'] = $path;
        }

        $service->update($data);

        // Send notification to admins
        Notifications::send('service.updated', $service);

        return $service;
    }

    public function deleteService(Service $service): bool
    {
        // Check if service has related quotations or projects
        if ($service->quotations()->count() > 0) {
            throw new \Exception('Cannot delete service with existing quotations');
        }

        if ($service->projects()->count() > 0) {
            throw new \Exception('Cannot delete service with existing projects');
        }

        // Delete associated files
        if ($service->image) {
            Storage::disk('public')->delete($service->image);
        }

        if ($service->icon) {
            Storage::disk('public')->delete($service->icon);
        }

        // Send notification before deletion
        Notifications::send('service.deleted', $service);

        return $service->delete();
    }

    public function toggleActive(Service $service): Service
    {
        $wasActive = $service->is_active;
        $service->update(['is_active' => !$service->is_active]);

        // Send notification for status change
        $notificationType = $service->is_active ? 'service.activated' : 'service.deactivated';
        Notifications::send($notificationType, $service);

        return $service;
    }

    public function toggleFeatured(Service $service): Service
    {
        $wasFeatured = $service->featured;
        $service->update(['featured' => !$service->featured]);

        // Send notification for featured status change
        $notificationType = $service->featured ? 'service.featured' : 'service.unfeatured';
        Notifications::send($notificationType, $service);

        return $service;
    }

    public function updateOrder(array $order): bool
    {
        foreach ($order as $index => $id) {
            Service::where('id', $id)->update(['sort_order' => $index + 1]);
        }

        // Send notification about reordering
        Notifications::send('service.reordered', [
            'message' => 'Services have been reordered',
            'count' => count($order)
        ]);

        return true;
    }

    public function getStatistics(): array
    {
        return [
            'total' => Service::count(),
            'active' => Service::where('is_active', true)->count(),
            'featured' => Service::where('featured', true)->count(),
            'by_category' => Service::join('service_categories', 'services.category_id', '=', 'service_categories.id')
                ->selectRaw('service_categories.name, COUNT(*) as count')
                ->groupBy('service_categories.name')
                ->pluck('count', 'name')
                ->toArray(),
            'with_quotations' => Service::has('quotations')->count(),
            'with_projects' => Service::has('projects')->count(),
            'popular' => Service::withCount('quotations')
                ->orderBy('quotations_count', 'desc')
                ->first(),
        ];
    }

    public function bulkToggleActive(array $serviceIds, bool $active): int
    {
        $updated = Service::whereIn('id', $serviceIds)
            ->update(['is_active' => $active]);

        if ($updated > 0) {
            $notificationType = $active ? 'service.bulk_activated' : 'service.bulk_deactivated';
            Notifications::send($notificationType, [
                'count' => $updated,
                'status' => $active ? 'activated' : 'deactivated'
            ]);
        }

        return $updated;
    }

    public function bulkToggleFeatured(array $serviceIds, bool $featured): int
    {
        $updated = Service::whereIn('id', $serviceIds)
            ->update(['featured' => $featured]);

        if ($updated > 0) {
            $notificationType = $featured ? 'service.bulk_featured' : 'service.bulk_unfeatured';
            Notifications::send($notificationType, [
                'count' => $updated,
                'status' => $featured ? 'featured' : 'unfeatured'
            ]);
        }

        return $updated;
    }

    public function bulkDelete(array $serviceIds): int
    {
        $services = Service::whereIn('id', $serviceIds)
            ->doesntHave('quotations')
            ->doesntHave('projects')
            ->get();

        $deleted = 0;
        foreach ($services as $service) {
            if ($service->image) {
                Storage::disk('public')->delete($service->image);
            }
            if ($service->icon) {
                Storage::disk('public')->delete($service->icon);
            }
            $service->delete();
            $deleted++;
        }

        if ($deleted > 0) {
            Notifications::send('service.bulk_deleted', [
                'count' => $deleted
            ]);
        }

        return $deleted;
    }

    public function duplicate(Service $service, array $overrides = []): Service
    {
        $data = array_merge($service->toArray(), $overrides, [
            'title' => $overrides['title'] ?? $service->title . ' (Copy)',
            'slug' => Str::slug($overrides['title'] ?? $service->title . ' Copy'),
            'sort_order' => Service::max('sort_order') + 1,
            'featured' => false, // Don't duplicate featured status
        ]);

        unset($data['id'], $data['created_at'], $data['updated_at']);

        $newService = Service::create($data);

        // Copy images if they exist
        if ($service->image) {
            $extension = pathinfo($service->image, PATHINFO_EXTENSION);
            $newPath = 'services/' . time() . '_' . uniqid() . '.' . $extension;
            
            if (Storage::disk('public')->copy($service->image, $newPath)) {
                $newService->update(['image' => $newPath]);
            }
        }

        if ($service->icon) {
            $extension = pathinfo($service->icon, PATHINFO_EXTENSION);
            $newPath = 'services/icons/' . time() . '_' . uniqid() . '.' . $extension;
            
            if (Storage::disk('public')->copy($service->icon, $newPath)) {
                $newService->update(['icon' => $newPath]);
            }
        }

        // Send notification
        Notifications::send('service.duplicated', $newService);

        return $newService;
    }

    public function getPopularServices(int $limit = 5): \Illuminate\Database\Eloquent\Collection
    {
        return Service::withCount('quotations')
            ->where('is_active', true)
            ->orderBy('quotations_count', 'desc')
            ->limit($limit)
            ->get();
    }

    public function searchServices(string $query, int $limit = 10): \Illuminate\Database\Eloquent\Collection
    {
        return Service::where('is_active', true)
            ->where(function($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('short_description', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%");
            })
            ->limit($limit)
            ->get();
    }

    public function getServicesByCategory(ServiceCategory $category): \Illuminate\Database\Eloquent\Collection
    {
        return Service::where('category_id', $category->id)
            ->where('is_active', true)
            ->ordered()
            ->get();
    }

    public function getFeaturedServices(int $limit = 6): \Illuminate\Database\Eloquent\Collection
    {
        return Service::where('is_active', true)
            ->where('featured', true)
            ->ordered()
            ->limit($limit)
            ->get();
    }

    public function updateSeo(Service $service, array $seoData): Service
    {
        $service->updateSeo($seoData);

        // Send notification about SEO update
        Notifications::send('service.seo_updated', $service);

        return $service;
    }
}