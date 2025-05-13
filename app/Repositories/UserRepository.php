<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;

class UserRepository implements UserRepositoryInterface
{
    /**
     * @var User
     */
    protected $model;
    
    /**
     * UserRepository constructor.
     * 
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->model = $user;
    }
    
    /**
     * Get all users
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all()
    {
        return $this->model->all();
    }
    
    /**
     * Find user by ID
     * 
     * @param int $id
     * @return \App\Models\User
     */
    public function find($id)
    {
        return $this->model->findOrFail($id);
    }
    
    /**
     * Find user by email
     * 
     * @param string $email
     * @return \App\Models\User
     */
    public function findByEmail($email)
    {
        return $this->model->where('email', $email)->first();
    }
    
    /**
     * Create a new user
     * 
     * @param array $data
     * @return \App\Models\User
     */
    public function create(array $data)
    {
        // Ensure password is hashed
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }
        
        $user = $this->model->create($data);
        
        // Assign roles if provided
        if (isset($data['roles']) && !empty($data['roles'])) {
            $user->syncRoles($data['roles']);
        }
        
        return $user;
    }
    
    /**
     * Update existing user
     * 
     * @param int $id
     * @param array $data
     * @return \App\Models\User
     */
    public function update($id, array $data)
    {
        $user = $this->find($id);
        
        // Hash password if provided
        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']); // Don't update password if not provided
        }
        
        $user->update($data);
        
        // Sync roles if provided
        if (isset($data['roles'])) {
            $user->syncRoles($data['roles']);
        }
        
        return $user;
    }
    
    /**
     * Delete user
     * 
     * @param int $id
     * @return bool
     */
    public function delete($id)
    {
        return $this->find($id)->delete();
    }
    
    /**
     * Get users by role
     * 
     * @param string $role
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getByRole($role)
    {
        return $this->model->role($role)->get();
    }
    
    /**
     * Get active users
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActive()
    {
        return $this->model->where('is_active', true)->get();
    }
    
    /**
     * Get paginated users with filters
     * 
     * @param array $filters
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getPaginated(array $filters = [], $perPage = 15)
    {
        $query = $this->model->with('roles');
        
        // Apply filters
        if (isset($filters['role']) && !empty($filters['role'])) {
            $query->whereHas('roles', function ($q) use ($filters) {
                $q->where('name', $filters['role']);
            });
        }
        
        if (isset($filters['search']) && !empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', "%{$filters['search']}%")
                  ->orWhere('email', 'like', "%{$filters['search']}%");
            });
        }
        
        if (isset($filters['active']) && $filters['active'] !== null) {
            $query->where('is_active', (bool) $filters['active']);
        }
        
        return $query->latest()->paginate($perPage);
    }
}