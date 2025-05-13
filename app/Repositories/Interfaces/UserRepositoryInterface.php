<?php

namespace App\Repositories\Interfaces;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface UserRepositoryInterface
{
    /**
     * Get all users
     *
     * @return Collection
     */
    public function all(): Collection;
    
    /**
     * Get paginated users
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function paginate(int $perPage = 15): LengthAwarePaginator;
    
    /**
     * Find a user by ID
     *
     * @param int $id
     * @return User|null
     */
    public function find(int $id): ?User;
    
    /**
     * Find a user by email
     *
     * @param string $email
     * @return User|null
     */
    public function findByEmail(string $email): ?User;
    
    /**
     * Create a new user
     *
     * @param array $data
     * @return User
     */
    public function create(array $data): User;
    
    /**
     * Update a user
     *
     * @param User $user
     * @param array $data
     * @return User
     */
    public function update(User $user, array $data): User;
    
    /**
     * Delete a user
     *
     * @param User $user
     * @return bool
     */
    public function delete(User $user): bool;
    
    /**
     * Get users by role
     *
     * @param string $role
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getByRole(string $role, int $perPage = 15): LengthAwarePaginator;
    
    /**
     * Assign role to user
     *
     * @param User $user
     * @param string|array $roles
     * @return User
     */
    public function assignRole(User $user, $roles): User;
    
    /**
     * Remove role from user
     *
     * @param User $user
     * @param string|array $roles
     * @return User
     */
    public function removeRole(User $user, $roles): User;
    
    /**
     * Toggle user active status
     *
     * @param User $user
     * @return User
     */
    public function toggleActive(User $user): User;
    
    /**
     * Search users
     *
     * @param string $query
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function search(string $query, int $perPage = 15): LengthAwarePaginator;
}