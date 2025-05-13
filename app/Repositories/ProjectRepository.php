<?php

namespace App\Repositories;

use App\Models\Project;
use App\Repositories\Interfaces\ProjectRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class ProjectRepository implements ProjectRepositoryInterface
{
    /**
     * Get all projects
     *
     * @return Collection
     */
    public function all(): Collection
    {
        return Project::with('images')->latest()->get();
    }
    
    /**
     * Get paginated projects
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function paginate(int $perPage = 10): LengthAwarePaginator
    {
        return Project::with('images')->latest()->paginate($perPage);
    }
    
    /**
 * Find a project by ID
 *
 * @param int $id
 * @return Project|null
 */
public function find(int $id): ?Project
{
    return Project::find($id)?->load(['images', 'client', 'testimonial']);
}
    
    /**
     * Find a project by slug
     *
     * @param string $slug
     * @return Project|null
     */
    public function findBySlug(string $slug): ?Project
    {
        return Project::with(['images', 'client', 'testimonial'])
            ->where('slug', $slug)
            ->first();
    }
    
    /**
     * Create a new project
     *
     * @param array $data
     * @return Project
     */
    public function create(array $data): Project
    {
        return Project::create($data);
    }
    
    /**
     * Update a project
     *
     * @param Project $project
     * @param array $data
     * @return Project
     */
    public function update(Project $project, array $data): Project
    {
        $project->update($data);
        return $project;
    }
    
    /**
     * Delete a project
     *
     * @param Project $project
     * @return bool
     */
    public function delete(Project $project): bool
    {
        return $project->delete();
    }
    
    /**
     * Get featured projects
     *
     * @param int $limit
     * @return Collection
     */
    public function getFeatured(int $limit = 6): Collection
    {
        return Project::with('images')
            ->where('featured', true)
            ->latest()
            ->take($limit)
            ->get();
    }
    
    /**
     * Get projects by category
     *
     * @param string $category
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getByCategory(string $category, int $perPage = 10): LengthAwarePaginator
    {
        return Project::with('images')
            ->where('category', $category)
            ->latest()
            ->paginate($perPage);
    }
    
    /**
     * Get related projects
     *
     * @param Project $project
     * @param int $limit
     * @return Collection
     */
    public function getRelated(Project $project, int $limit = 3): Collection
    {
        return Project::with('images')
            ->where('id', '!=', $project->id)
            ->where('category', $project->category)
            ->take($limit)
            ->get();
    }
    
    /**
     * Get projects by client
     *
     * @param int $clientId
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getByClient(int $clientId, int $perPage = 10): LengthAwarePaginator
    {
        return Project::with('images')
            ->where('client_id', $clientId)
            ->latest()
            ->paginate($perPage);
    }
    
    /**
     * Search projects
     *
     * @param string $query
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function search(string $query, int $perPage = 10): LengthAwarePaginator
    {
        return Project::with('images')
            ->where('title', 'like', "%{$query}%")
            ->orWhere('description', 'like', "%{$query}%")
            ->orWhere('client_name', 'like', "%{$query}%")
            ->orWhere('location', 'like', "%{$query}%")
            ->latest()
            ->paginate($perPage);
    }
}