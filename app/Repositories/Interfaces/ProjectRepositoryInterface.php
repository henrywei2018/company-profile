<?php

namespace App\Repositories\Interfaces;

use App\Models\Project;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface ProjectRepositoryInterface
{
    /**
     * Get all projects
     *
     * @return Collection
     */
    public function all(): Collection;
    
    /**
     * Get paginated projects
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function paginate(int $perPage = 10): LengthAwarePaginator;
    
    /**
     * Find a project by ID
     *
     * @param int $id
     * @return Project|null
     */
    public function find(int $id): ?Project;
    
    /**
     * Find a project by slug
     *
     * @param string $slug
     * @return Project|null
     */
    public function findBySlug(string $slug): ?Project;
    
    /**
     * Create a new project
     *
     * @param array $data
     * @return Project
     */
    public function create(array $data): Project;
    
    /**
     * Update a project
     *
     * @param Project $project
     * @param array $data
     * @return Project
     */
    public function update(Project $project, array $data): Project;
    
    /**
     * Delete a project
     *
     * @param Project $project
     * @return bool
     */
    public function delete(Project $project): bool;
    
    /**
     * Get featured projects
     *
     * @param int $limit
     * @return Collection
     */
    public function getFeatured(int $limit = 6): Collection;
    
    /**
     * Get projects by category
     *
     * @param string $category
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getByCategory(string $category, int $perPage = 10): LengthAwarePaginator;
    
    /**
     * Get related projects
     *
     * @param Project $project
     * @param int $limit
     * @return Collection
     */
    public function getRelated(Project $project, int $limit = 3): Collection;
    
    /**
     * Get projects by client
     *
     * @param int $clientId
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getByClient(int $clientId, int $perPage = 10): LengthAwarePaginator;
    
    /**
     * Search projects
     *
     * @param string $query
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function search(string $query, int $perPage = 10): LengthAwarePaginator;
}