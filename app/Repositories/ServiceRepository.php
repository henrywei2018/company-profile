<?php

namespace App\Repositories;

use App\Models\Service;
use App\Repositories\Interfaces\ServiceRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class ServiceRepository implements ServiceRepositoryInterface
{
    /**
     * Get all active services
     *
     * @return Collection
     */
    public function getAllActive(): Collection
    {
        return Service::active()->ordered()->get();
    }
    
    /**
     * Get all services (including inactive)
     *
     * @return Collection
     */
    public function all(): Collection
    {
        return Service::ordered()->get();
    }
    
    /**
     * Get paginated services
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function paginate(int $perPage = 10): LengthAwarePaginator
    {
        return Service::ordered()->paginate($perPage);
    }
    
    /**
     * Find a service by ID
     *
     * @param int $id
     * @return Service|null
     */
    public function find(int $id): ?Service
    {
        return Service::find($id)?->load(['category', 'images']);        
    }
    
    /**
 * Find a service by slug
 *
 * @param string $slug
 * @return Service|null
 */
public function findBySlug(string $slug): ?Service
{
    return Service::where('slug', $slug)->first();
}
    
    /**
     * Create a new service
     *
     * @param array $data
     * @return Service
     */
    public function create(array $data): Service
    {
        return Service::create($data);
    }
    
    /**
     * Update a service
     *
     * @param Service $service
     * @param array $data
     * @return Service
     */
    public function update(Service $service, array $data): Service
    {
        $service->update($data);
        return $service;
    }
    
    /**
     * Delete a service
     *
     * @param Service $service
     * @return bool
     */
    public function delete(Service $service): bool
    {
        return $service->delete();
    }
    
    /**
     * Get featured services
     *
     * @param int $limit
     * @return Collection
     */
    public function getFeatured(int $limit = 6): Collection
    {
        return Service::active()
            ->where('featured', true)
            ->ordered()
            ->take($limit)
            ->get();
    }
    
    /**
     * Get services by category
     *
     * @param int $categoryId
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getByCategory(int $categoryId, int $perPage = 10): LengthAwarePaginator
    {
        return Service::active()
            ->where('category_id', $categoryId)
            ->ordered()
            ->paginate($perPage);
    }
    
    /**
     * Get related services
     *
     * @param Service $service
     * @param int $limit
     * @return Collection
     */
    public function getRelated(Service $service, int $limit = 3): Collection
    {
        return Service::active()
            ->where('id', '!=', $service->id)
            ->when($service->category_id, function ($query) use ($service) {
                return $query->where('category_id', $service->category_id);
            })
            ->take($limit)
            ->get();
    }
    
    /**
     * Toggle service active status
     *
     * @param Service $service
     * @return Service
     */
    public function toggleActive(Service $service): Service
    {
        $service->update([
            'is_active' => !$service->is_active
        ]);
        
        return $service;
    }
    
    /**
     * Toggle service featured status
     *
     * @param Service $service
     * @return Service
     */
    public function toggleFeatured(Service $service): Service
    {
        $service->update([
            'featured' => !$service->featured
        ]);
        
        return $service;
    }
}