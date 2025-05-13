<?php

namespace App\Repositories\Interfaces;

use App\Models\Service;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface ServiceRepositoryInterface
{
    /**
     * Get all active services
     *
     * @return Collection
     */
    public function getAllActive(): Collection;
    
    /**
     * Get all services (including inactive)
     *
     * @return Collection
     */
    public function all(): Collection;
    
    /**
     * Get paginated services
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function paginate(int $perPage = 10): LengthAwarePaginator;
    
    /**
     * Find a service by ID
     *
     * @param int $id
     * @return Service|null
     */
    public function find(int $id): ?Service;
    
    /**
     * Find a service by slug
     *
     * @param string $slug
     * @return Service|null
     */
    public function findBySlug(string $slug): ?Service;
    
    /**
     * Create a new service
     *
     * @param array $data
     * @return Service
     */
    public function create(array $data): Service;
    
    /**
     * Update a service
     *
     * @param Service $service
     * @param array $data
     * @return Service
     */
    public function update(Service $service, array $data): Service;
    
    /**
     * Delete a service
     *
     * @param Service $service
     * @return bool
     */
    public function delete(Service $service): bool;
    
    /**
     * Get featured services
     *
     * @param int $limit
     * @return Collection
     */
    public function getFeatured(int $limit = 6): Collection;
    
    /**
     * Get services by category
     *
     * @param int $categoryId
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getByCategory(int $categoryId, int $perPage = 10): LengthAwarePaginator;
    
    /**
     * Get related services
     *
     * @param Service $service
     * @param int $limit
     * @return Collection
     */
    public function getRelated(Service $service, int $limit = 3): Collection;
    
    /**
     * Toggle service active status
     *
     * @param Service $service
     * @return Service
     */
    public function toggleActive(Service $service): Service;
    
    /**
     * Toggle service featured status
     *
     * @param Service $service
     * @return Service
     */
    public function toggleFeatured(Service $service): Service;
}