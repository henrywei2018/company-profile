<?php
// File: app/Policies/ProjectPolicy.php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProjectPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isClient() || $user->isAdmin();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Project $project): bool
    {
        // Admin can view any project
        if ($user->isAdmin()) {
            return true;
        }
        
        // Client can only view their projects
        if ($user->isClient()) {
            return $project->client_id === $user->id;
        }
        
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Only admin can create projects
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Project $project): bool
    {
        // Only admin can update projects
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Project $project): bool
    {
        // Only admin can delete projects
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can view project files.
     */
    public function viewFiles(User $user, Project $project): bool
    {
        // Admin can view any project files
        if ($user->isAdmin()) {
            return true;
        }
        
        // Client can only view files for their projects
        if ($user->isClient()) {
            return $project->client_id === $user->id;
        }
        
        return false;
    }

    /**
     * Determine whether the user can add a testimonial to the project.
     */
    public function addTestimonial(User $user, Project $project): bool
    {
        // Client can add testimonial to their completed projects
        if ($user->isClient() && $project->client_id === $user->id) {
            return $project->status === 'completed';
        }
        
        return false;
    }
}