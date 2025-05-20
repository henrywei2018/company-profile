<?php

namespace App\Repositories\Interfaces;

use App\Models\ProjectCategory;

interface ProjectCategoryRepositoryInterface
{
    /**
     * Get all project categories.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all();
    
    /**
     * Get all active project categories.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActive();
    
    /**
     * Get project category by ID.
     *
     * @param int $id
     * @return \App\Models\ProjectCategory
     */
    public function findById($id);
    
    /**
     * Create new project category.
     *
     * @param array $data
     * @return \App\Models\ProjectCategory
     */
    public function create(array $data);
    
    /**
     * Update project category.
     *
     * @param \App\Models\ProjectCategory $category
     * @param array $data
     * @return \App\Models\ProjectCategory
     */
    public function update(ProjectCategory $category, array $data);
    
    /**
     * Delete project category.
     *
     * @param \App\Models\ProjectCategory $category
     * @return bool
     */
    public function delete(ProjectCategory $category);
    
    /**
     * Toggle project category active status.
     *
     * @param \App\Models\ProjectCategory $category
     * @return \App\Models\ProjectCategory
     */
    public function toggleActive(ProjectCategory $category);
}