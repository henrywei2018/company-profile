<?php

namespace App\Repositories\Interfaces;

use App\Models\ServiceCategory;

interface ServiceCategoryRepositoryInterface
{
    /**
     * Get all service categories.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all();
    
    /**
     * Get all active service categories.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActive();
    
    /**
     * Get service category by ID.
     *
     * @param int $id
     * @return \App\Models\ServiceCategory
     */
    public function findById($id);
    
    /**
     * Create new service category.
     *
     * @param array $data
     * @return \App\Models\ServiceCategory
     */
    public function create(array $data);
    
    /**
     * Update service category.
     *
     * @param \App\Models\ServiceCategory $category
     * @param array $data
     * @return \App\Models\ServiceCategory
     */
    public function update(ServiceCategory $category, array $data);
    
    /**
     * Delete service category.
     *
     * @param \App\Models\ServiceCategory $category
     * @return bool
     */
    public function delete(ServiceCategory $category);
    
    /**
     * Toggle service category active status.
     *
     * @param \App\Models\ServiceCategory $category
     * @return \App\Models\ServiceCategory
     */
    public function toggleActive(ServiceCategory $category);
}