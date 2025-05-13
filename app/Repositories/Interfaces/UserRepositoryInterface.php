<?php

namespace App\Repositories\Interfaces;

interface UserRepositoryInterface
{
    /**
     * Get all users
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all();
    
    /**
     * Find user by ID
     * 
     * @param int $id
     * @return \App\Models\User
     */
    public function find($id);
    
    /**
     * Find user by email
     * 
     * @param string $email
     * @return \App\Models\User
     */
    public function findByEmail($email);
    
    /**
     * Create a new user
     * 
     * @param array $data
     * @return \App\Models\User
     */
    public function create(array $data);
    
    /**
     * Update existing user
     * 
     * @param int $id
     * @param array $data
     * @return \App\Models\User
     */
    public function update($id, array $data);
    
    /**
     * Delete user
     * 
     * @param int $id
     * @return bool
     */
    public function delete($id);
    
    /**
     * Get users by role
     * 
     * @param string $role
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getByRole($role);
    
    /**
     * Get active users
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActive();
    
    /**
     * Get paginated users with filters
     * 
     * @param array $filters
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getPaginated(array $filters = [], $perPage = 15);
}