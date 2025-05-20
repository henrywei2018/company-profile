<?php

namespace App\Repositories;

use App\Models\ServiceCategory;
use App\Repositories\Interfaces\ServiceCategoryRepositoryInterface;

class ServiceCategoryRepository implements ServiceCategoryRepositoryInterface
{
    /**
     * Get all service categories.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all()
    {
        return ServiceCategory::all();
    }
    
    /**
     * Get all active service categories.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActive()
    {
        return ServiceCategory::where('is_active', true)->orderBy('sort_order')->get();
    }
    
    /**
     * Get service category by ID.
     *
     * @param int $id
     * @return \App\Models\ServiceCategory
     */
    public function findById($id)
    {
        return ServiceCategory::findOrFail($id);
    }
    
    /**
     * Create new service category.
     *
     * @param array $data
     * @return \App\Models\ServiceCategory
     */
    public function create(array $data)
    {
        return ServiceCategory::create($data);
    }
    
    /**
     * Update service category.
     *
     * @param \App\Models\ServiceCategory $category
     * @param array $data
     * @return \App\Models\ServiceCategory
     */
    public function update(ServiceCategory $category, array $data)
    {
        $category->update($data);
        return $category;
    }
    
    /**
     * Delete service category.
     *
     * @param \App\Models\ServiceCategory $category
     * @return bool
     */
    public function delete(ServiceCategory $category)
    {
        return $category->delete();
    }
    
    /**
     * Toggle service category active status.
     *
     * @param \App\Models\ServiceCategory $category
     * @return \App\Models\ServiceCategory
     */
    public function toggleActive(ServiceCategory $category)
    {
        $category->is_active = !$category->is_active;
        $category->save();
        
        return $category;
    }
}