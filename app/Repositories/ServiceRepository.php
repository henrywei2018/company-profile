<?php

namespace App\Repositories;

use App\Models\Service;
use App\Repositories\Interfaces\ServiceRepositoryInterface;

class ServiceRepository implements ServiceRepositoryInterface
{
    /**
     * @var Service
     */
    protected $model;
    
    /**
     * ServiceRepository constructor.
     * 
     * @param Service $service
     */
    public function __construct(Service $service)
    {
        $this->model = $service;
    }
    
    /**
     * Get all services
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all()
    {
        return $this->model->all();
    }
    
    /**
     * Find service by ID
     * 
     * @param int $id
     * @return \App\Models\Service
     */
    public function find($id)
    {
        return $this->model->findOrFail($id);
    }
    
    /**
     * Find service by slug
     * 
     * @param string $slug
     * @return \App\Models\Service
     */
    public function findBySlug($slug)
    {
        return $this->model->where('slug', $slug)->firstOrFail();
    }
    
    /**
     * Create a new service
     * 
     * @param array $data
     * @return \App\Models\Service
     */
    public function create(array $data)
    {
        return $this->model->create($data);
    }
    
    /**
     * Update existing service
     * 
     * @param int $id
     * @param array $data
     * @return \App\Models\Service
     */
    public function update($id, array $data)
    {
        $service = $this->find($id);
        $service->update($data);
        
        return $service;
    }
    
    /**
     * Delete service
     * 
     * @param int $id
     * @return bool
     */
    public function delete($id)
    {
        return $this->find($id)->delete();
    }
    
    /**
     * Get active services
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActive()
    {
        return $this->model->active()->ordered()->get();
    }
    
    /**
     * Get featured services
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getFeatured()
    {
        return $this->model->active()->featured()->ordered()->get();
    }
    
    /**
     * Get services by category
     * 
     * @param int $categoryId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getByCategory($categoryId)
    {
        return $this->model->active()->where('category_id', $categoryId)->ordered()->get();
    }
    
    /**
     * Get paginated services with filters
     * 
     * @param array $filters
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getPaginated(array $filters = [], $perPage = 10)
    {
        return $this->model->filter($filters)->ordered()->paginate($perPage);
    }
}