<?php

namespace App\Repositories\Interfaces;

use App\Models\Service;

interface ServiceRepositoryInterface
{
    /**
     * Get all services.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all();
    
    /**
     * Get service by ID.
     *
     * @param int $id
     * @return \App\Models\Service
     */
    public function findById($id);
    
    /**
     * Create new service.
     *
     * @param array $data
     * @return \App\Models\Service
     */
    public function create(array $data);
    
    /**
     * Update service.
     *
     * @param \App\Models\Service $service
     * @param array $data
     * @return \App\Models\Service
     */
    public function update(Service $service, array $data);
    
    /**
     * Delete service.
     *
     * @param \App\Models\Service $service
     * @return bool
     */
    public function delete(Service $service);
    
    /**
     * Toggle service active status.
     *
     * @param \App\Models\Service $service
     * @return \App\Models\Service
     */
    public function toggleActive(Service $service);
    
    /**
     * Toggle service featured status.
     *
     * @param \App\Models\Service $service
     * @return \App\Models\Service
     */
    public function toggleFeatured(Service $service);
}