<?php

namespace App\Repositories\Interfaces;

interface ServiceRepositoryInterface
{
    /**
     * Get all services
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all();
    
    /**
     * Find service by ID
     * 
     * @param int $id
     * @return \App\Models\Service
     */
    public function find($id);
    
    /**
     * Find service by slug
     * 
     * @param string $slug
     * @return \App\Models\Service
     */
    public function findBySlug($slug);
    
    /**
     * Create a new service
     * 
     * @param array $data
     * @return \App\Models\Service
     */
    public function create(array $data);
    
    /**
     * Update existing service
     * 
     * @param int $id
     * @param array $data
     * @return \App\Models\Service
     */
    public function update($id, array $data);
    
    /**
     * Delete service
     * 
     * @param int $id
     * @return bool
     */
    public function delete($id);
    
    /**
     * Get active services
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActive();
    
    /**
     * Get featured services
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getFeatured();
    
    /**
     * Get services by category
     * 
     * @param int $categoryId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getByCategory($categoryId);
    
    /**
     * Get paginated services with filters
     * 
     * @param array $filters
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getPaginated(array $filters = [], $perPage = 10);
}