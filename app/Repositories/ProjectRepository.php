<?php

namespace App\Repositories;

use App\Models\Project;
use App\Repositories\Interfaces\ProjectRepositoryInterface;

class ProjectRepository implements ProjectRepositoryInterface
{
    /**
     * @var Project
     */
    protected $model;
    
    /**
     * ProjectRepository constructor.
     * 
     * @param Project $project
     */
    public function __construct(Project $project)
    {
        $this->model = $project;
    }
    
    /**
     * Get all projects
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all()
    {
        return $this->model->all();
    }
    
    /**
     * Find project by ID
     * 
     * @param int $id
     * @return \App\Models\Project
     */
    public function find($id)
    {
        return $this->model->findOrFail($id);
    }
    
    /**
     * Find project by slug
     * 
     * @param string $slug
     * @return \App\Models\Project
     */
    public function findBySlug($slug)
    {
        return $this->model->where('slug', $slug)->firstOrFail();
    }
    
    /**
     * Create a new project
     * 
     * @param array $data
     * @return \App\Models\Project
     */
    public function create(array $data)
    {
        return $this->model->create($data);
    }
    
    /**
     * Update existing project
     * 
     * @param int $id
     * @param array $data
     * @return \App\Models\Project
     */
    public function update($id, array $data)
    {
        $project = $this->find($id);
        $project->update($data);
        
        return $project;
    }
    
    /**
     * Delete project
     * 
     * @param int $id
     * @return bool
     */
    public function delete($id)
    {
        return $this->find($id)->delete();
    }
    
    /**
     * Get featured projects
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getFeatured()
    {
        return $this->model->featured()->latest()->get();
    }
    
    /**
     * Get projects by category
     * 
     * @param string $category
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getByCategory($category)
    {
        return $this->model->where('category', $category)->latest()->get();
    }
    
    /**
     * Get projects by year
     * 
     * @param int $year
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getByYear($year)
    {
        return $this->model->where('year', $year)->latest()->get();
    }
    
    /**
     * Get paginated projects with filters
     * 
     * @param array $filters
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getPaginated(array $filters = [], $perPage = 10)
    {
        return $this->model->filter($filters)->latest()->paginate($perPage);
    }
}