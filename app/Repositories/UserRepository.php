<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;

class UserRepository implements UserRepositoryInterface
{
    /**
     * Get all users
     *
     * @return Collection
     */
    public function all(): Collection
    {
        return User::with('roles')->get();
    }
    
    /**
     * Get paginated users
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return User::with('roles')->paginate($perPage);
    }
    
    /**
     * Find a user by ID
     *
     * @param int $id
     * @return User|null
     */
    public function find(int $id): ?User
    {
        return User::with('roles')->find($id);
    }
    
    /**
     * Find a user by email
     *
     * @param string $email
     * @return User|null
     */
    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }
    
    /**
     * Create a new user
     *
     * @param array $data
     * @return User
     */
    public function create(array $data): User
    {
        // Ensure password is hashed
        if (isset($data['password']) && !Hash::info($data['password'])['algo']) {
            $data['password'] = Hash::make($data['password']);
        }
        
        return User::create($data);
    }
    
    /**
     * Update a user
     *
     * @param User $user
     * @param array $data
     * @return User
     */
    public function update(User $user, array $data): User
    {
        // Ensure password is hashed if provided
        if (isset($data['password']) && !Hash::info($data['password'])['algo']) {
            $data['password'] = Hash::make($data['password']);
        }
        
        $user->update($data);
        return $user;
    }
    
    /**
     * Delete a user
     *
     * @param User $user
     * @return bool
     */
    public function delete(User $user): bool
    {
        return $user->delete();
    }
    
    /**
     * Get users by role
     *
     * @param string $role
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getByRole(string $role, int $perPage = 15): LengthAwarePaginator
    {
        return User::role($role)->paginate($perPage);
    }
    
    /**
     * Assign role to user
     *
     * @param User $user
     * @param string|array $roles
     * @return User
     */
    public function assignRole(User $user, $roles): User
    {
        $user->assignRole($roles);
        return $user;
    }
    
    /**
     * Remove role from user
     *
     * @param User $user
     * @param string|array $roles
     * @return User
     */
    public function removeRole(User $user, $roles): User
    {
        $user->removeRole($roles);
        return $user;
    }
    
    /**
     * Toggle user active status
     *
     * @param User $user
     * @return User
     */
    public function toggleActive(User $user): User
    {
        $user->update([
            'is_active' => !$user->is_active
        ]);
        
        return $user;
    }
    
    /**
     * Search users
     *
     * @param string $query
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function search(string $query, int $perPage = 15): LengthAwarePaginator
    {
        return User::where('name', 'like', "%{$query}%")
            ->orWhere('email', 'like', "%{$query}%")
            ->orWhere('company', 'like', "%{$query}%")
            ->paginate($perPage);
    }
}