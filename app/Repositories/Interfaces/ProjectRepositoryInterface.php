<?php

namespace App\Repositories\Interfaces;

interface ProjectRepositoryInterface
{
    /**
     * Get all projects
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all();
    
    /**
     * Find project by ID
     * 
     * @param int $id
     * @return \App\Models\Project
     */
    public function find($id);
    
    /**
     * Find project by slug
     * 
     * @param string $slug
     * @return \App\Models\Project
     */
    public function findBySlug($slug);
    
    /**
     * Create a new project
     * 
     * @param array $data
     * @return \App\Models\Project
     */
    public function create(array $data);
    
    /**
     * Update existing project
     * 
     * @param int $id
     * @param array $data
     * @return \App\Models\Project
     */
    public function update($id, array $data);
    
    /**
     * Delete project
     * 
     * @param int $id
     * @return bool
     */
    public function delete($id);
    
    /**
     * Get featured projects
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getFeatured();
    
    /**
     * Get projects by category
     * 
     * @param string $category
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getByCategory($category);
    
    /**
     * Get projects by year
     * 
     * @param int $year
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getByYear($year);
    
    /**
     * Get paginated projects with filters
     * 
     * @param array $filters
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getPaginated(array $filters = [], $perPage = 10);
}