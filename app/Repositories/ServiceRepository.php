<?php

namespace App\Repositories;

use App\Models\Service;
use App\Repositories\Interfaces\ServiceRepositoryInterface;

class ServiceRepository implements ServiceRepositoryInterface
{
    /**
     * Get all services.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all()
    {
        return Service::all();
    }
    
    /**
     * Get service by ID.
     *
     * @param int $id
     * @return \App\Models\Service
     */
    public function findById($id)
    {
        return Service::findOrFail($id);
    }
    
    /**
     * Create new service.
     *
     * @param array $data
     * @return \App\Models\Service
     */
    public function create(array $data)
    {
        return Service::create($data);
    }
    
    /**
     * Update service.
     *
     * @param \App\Models\Service $service
     * @param array $data
     * @return \App\Models\Service
     */
    public function update(Service $service, array $data)
    {
        $service->update($data);
        return $service;
    }
    
    /**
     * Delete service.
     *
     * @param \App\Models\Service $service
     * @return bool
     */
    public function delete(Service $service)
    {
        return $service->delete();
    }
    
    /**
     * Toggle service active status.
     *
     * @param \App\Models\Service $service
     * @return \App\Models\Service
     */
    public function toggleActive(Service $service)
    {
        $service->is_active = !$service->is_active;
        $service->save();
        
        return $service;
    }
    
    /**
     * Toggle service featured status.
     *
     * @param \App\Models\Service $service
     * @return \App\Models\Service
     */
    public function toggleFeatured(Service $service)
    {
        $service->featured = !$service->featured;
        $service->save();
        
        return $service;
    }
}