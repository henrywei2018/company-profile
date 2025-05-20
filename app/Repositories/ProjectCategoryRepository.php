<?php

namespace App\Repositories;

use App\Models\ProjectCategory;
use App\Repositories\Interfaces\ProjectCategoryRepositoryInterface;

class ProjectCategoryRepository implements ProjectCategoryRepositoryInterface
{
    /**
     * Get all project categories.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all()
    {
        return ProjectCategory::all();
    }
    
    /**
     * Get all active project categories.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActive()
    {
        return ProjectCategory::where('is_active', true)->orderBy('sort_order')->get();
    }
    
    /**
     * Get project category by ID.
     *
     * @param int $id
     * @return \App\Models\ProjectCategory
     */
    public function findById($id)
    {
        return ProjectCategory::findOrFail($id);
    }
    
    /**
     * Create new project category.
     *
     * @param array $data
     * @return \App\Models\ProjectCategory
     */
    public function create(array $data)
    {
        return ProjectCategory::create($data);
    }
    
    /**
     * Update project category.
     *
     * @param \App\Models\ProjectCategory $category
     * @param array $data
     * @return \App\Models\ProjectCategory
     */
    public function update(ProjectCategory $category, array $data)
    {
        $category->update($data);
        return $category;
    }
    
    /**
     * Delete project category.
     *
     * @param \App\Models\ProjectCategory $category
     * @return bool
     */
    public function delete(ProjectCategory $category)
    {
        return $category->delete();
    }
    
    /**
     * Toggle project category active status.
     *
     * @param \App\Models\ProjectCategory $category
     * @return \App\Models\ProjectCategory
     */
    public function toggleActive(ProjectCategory $category)
    {
        $category->is_active = !$category->is_active;
        $category->save();
        
        return $category;
    }
}