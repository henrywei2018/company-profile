<?php

namespace App\Models;

use Spatie\Permission\Models\Role as SpatieRole;
use Illuminate\Database\Eloquent\Builder;

class Role extends SpatieRole
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'guard_name',
        'description',
        'color',
        'is_system',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_system' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * The attributes that should be appended to arrays.
     *
     * @var array
     */
    protected $appends = [
        'formatted_name',
        'users_count',
        'permissions_count',
        'badge_color',
    ];

    /**
     * Role hierarchy for priority ordering.
     *
     * @var array
     */
    protected static $hierarchy = [
        'super-admin' => 1,
        'admin' => 2,
        'manager' => 3,
        'editor' => 4,
        'client' => 5,
    ];

    /**
     * Get formatted role name.
     */
    public function getFormattedNameAttribute()
    {
        $names = [
            'super-admin' => 'Super Administrator',
            'admin' => 'Administrator',
            'manager' => 'Manager',
            'editor' => 'Content Editor',
            'client' => 'Client',
        ];

        return $names[$this->name] ?? ucfirst(str_replace(['-', '_'], ' ', $this->name));
    }

    /**
     * Get users count for this role.
     */
    public function getUsersCountAttribute()
    {
        return $this->users()->count();
    }

    /**
     * Get permissions count for this role.
     */
    public function getPermissionsCountAttribute()
    {
        return $this->permissions()->count();
    }

    /**
     * Get badge color for role.
     */
    public function getBadgeColorAttribute()
    {
        if ($this->color) {
            return $this->color;
        }

        $colors = [
            'super-admin' => 'red',
            'admin' => 'blue',
            'manager' => 'purple',
            'editor' => 'green',
            'client' => 'yellow',
        ];

        return $colors[$this->name] ?? 'gray';
    }

    /**
     * Get role hierarchy level.
     */
    public function getHierarchyLevelAttribute()
    {
        return self::$hierarchy[$this->name] ?? 999;
    }

    /**
     * Check if role is higher than another role.
     */
    public function isHigherThan($role)
    {
        if (is_string($role)) {
            $compareLevel = self::$hierarchy[$role] ?? 999;
        } else {
            $compareLevel = $role->hierarchy_level;
        }

        return $this->hierarchy_level < $compareLevel;
    }

    /**
     * Check if role is lower than another role.
     */
    public function isLowerThan($role)
    {
        if (is_string($role)) {
            $compareLevel = self::$hierarchy[$role] ?? 999;
        } else {
            $compareLevel = $role->hierarchy_level;
        }

        return $this->hierarchy_level > $compareLevel;
    }

    /**
     * Scope to filter system roles.
     */
    public function scopeSystem(Builder $query)
    {
        return $query->where('is_system', true);
    }

    /**
     * Scope to filter custom roles.
     */
    public function scopeCustom(Builder $query)
    {
        return $query->where('is_system', false);
    }

    /**
     * Scope to order by hierarchy.
     */
    public function scopeOrderedByHierarchy(Builder $query)
    {
        return $query->orderByRaw("
            CASE name
                WHEN 'super-admin' THEN 1
                WHEN 'admin' THEN 2
                WHEN 'manager' THEN 3
                WHEN 'editor' THEN 4
                WHEN 'client' THEN 5
                ELSE 999
            END
        ");
    }

    /**
     * Scope to filter assignable roles for current user.
     */
    public function scopeAssignableBy(Builder $query, $user)
    {
        if ($user->hasRole('super-admin')) {
            return $query; // Can assign any role
        }

        if ($user->hasRole('admin')) {
            return $query->where('name', '!=', 'super-admin');
        }

        if ($user->hasRole('manager')) {
            return $query->whereIn('name', ['editor', 'client']);
        }

        if ($user->hasRole('editor')) {
            return $query->where('name', 'client');
        }

        return $query->whereRaw('1 = 0'); // No roles assignable
    }

    /**
     * Get permissions grouped by module.
     */
    public function getGroupedPermissionsAttribute()
    {
        return $this->permissions->groupBy(function($permission) {
            return explode(' ', $permission->name)[1] ?? 'general';
        });
    }

    /**
     * Get role statistics.
     */
    public function getStatsAttribute()
    {
        return [
            'users_count' => $this->users_count,
            'permissions_count' => $this->permissions_count,
            'active_users_count' => $this->users()->where('is_active', true)->count(),
            'verified_users_count' => $this->users()->whereNotNull('email_verified_at')->count(),
        ];
    }

    /**
     * Check if role can be deleted.
     */
    public function isDeletable()
    {
        // System roles cannot be deleted
        if ($this->is_system) {
            return false;
        }

        // Roles with users cannot be deleted
        if ($this->users_count > 0) {
            return false;
        }

        return true;
    }

    /**
     * Check if role can be edited.
     */
    public function isEditable()
    {
        // Super admin role can only be edited by super admins
        if ($this->name === 'super-admin' && !auth()->user()?->hasRole('super-admin')) {
            return false;
        }

        return true;
    }

    /**
     * Get available colors for roles.
     */
    public static function getAvailableColors()
    {
        return [
            'red' => 'Red',
            'blue' => 'Blue',
            'green' => 'Green',
            'yellow' => 'Yellow',
            'purple' => 'Purple',
            'pink' => 'Pink',
            'indigo' => 'Indigo',
            'gray' => 'Gray',
            'orange' => 'Orange',
            'teal' => 'Teal',
        ];
    }

    /**
     * Create a new role with default permissions.
     */
    public static function createWithPermissions($name, $permissions = [], $attributes = [])
    {
        $role = self::create(array_merge([
            'name' => $name,
            'guard_name' => 'web',
        ], $attributes));

        if (!empty($permissions)) {
            $permissionModels = Permission::whereIn('name', $permissions)->get();
            $role->syncPermissions($permissionModels);
        }

        return $role;
    }

    /**
     * Duplicate role with new name.
     */
    public function duplicate($newName, $description = null)
    {
        $newRole = self::create([
            'name' => $newName,
            'guard_name' => $this->guard_name,
            'description' => $description ?? $this->description . ' (Copy)',
            'color' => $this->color,
            'is_system' => false, // Duplicated roles are never system roles
        ]);

        // Copy permissions
        $newRole->syncPermissions($this->permissions);

        return $newRole;
    }

    /**
     * Bootstrap the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Prevent deletion of system roles
        static::deleting(function ($role) {
            if ($role->is_system) {
                throw new \Exception("Cannot delete system role: {$role->name}");
            }

            if ($role->users_count > 0) {
                throw new \Exception("Cannot delete role with assigned users: {$role->name}");
            }
        });
    }
}