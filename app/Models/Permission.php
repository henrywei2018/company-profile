<?php

namespace App\Models;

use Spatie\Permission\Models\Permission as SpatiePermission;
use Illuminate\Database\Eloquent\Builder;

class Permission extends SpatiePermission
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
        'module',
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
        'action',
        'resource',
        'roles_count',
    ];

    /**
     * Get formatted permission name.
     */
    public function getFormattedNameAttribute()
    {
        return ucfirst(str_replace(['-', '_'], ' ', $this->name));
    }

    /**
     * Get action part of permission (first word).
     */
    public function getActionAttribute()
    {
        return explode(' ', $this->name)[0] ?? '';
    }

    /**
     * Get resource part of permission (second word onwards).
     */
    public function getResourceAttribute()
    {
        $parts = explode(' ', $this->name);
        return implode(' ', array_slice($parts, 1));
    }

    /**
     * Get module from permission name or attribute.
     */
    public function getModuleAttribute($value)
    {
        // Return stored module if exists
        if ($value) {
            return $value;
        }

        // Extract from permission name
        $parts = explode(' ', $this->name);
        return $parts[1] ?? 'general';
    }

    /**
     * Get roles count for this permission.
     */
    public function getRolesCountAttribute()
    {
        return $this->roles()->count();
    }

    /**
     * Scope to filter system permissions.
     */
    public function scopeSystem(Builder $query)
    {
        return $query->where('is_system', true);
    }

    /**
     * Scope to filter custom permissions.
     */
    public function scopeCustom(Builder $query)
    {
        return $query->where('is_system', false);
    }

    /**
     * Scope to filter by module.
     */
    public function scopeByModule(Builder $query, $module)
    {
        return $query->where('module', $module)
            ->orWhere('name', 'like', "% {$module}");
    }

    /**
     * Scope to filter by action.
     */
    public function scopeByAction(Builder $query, $action)
    {
        return $query->where('name', 'like', "{$action} %");
    }

    /**
     * Scope to group by module.
     */
    public function scopeGroupedByModule(Builder $query)
    {
        return $query->selectRaw('
            COALESCE(module, SUBSTRING_INDEX(SUBSTRING_INDEX(name, " ", 2), " ", -1)) as module_name,
            COUNT(*) as permissions_count
        ')
        ->groupBy('module_name')
        ->orderBy('module_name');
    }

    /**
     * Get all available modules.
     */
    public static function getAvailableModules()
    {
        return [
            'dashboard' => 'Dashboard',
            'users' => 'Users Management',
            'roles' => 'Roles Management',
            'permissions' => 'Permissions Management',
            'projects' => 'Projects',
            'project-categories' => 'Project Categories',
            'quotations' => 'Quotations',
            'messages' => 'Messages',
            'services' => 'Services',
            'service-categories' => 'Service Categories',
            'team' => 'Team Members',
            'team-departments' => 'Team Departments',
            'testimonials' => 'Testimonials',
            'certifications' => 'Certifications',
            'blog' => 'Blog Posts',
            'blog-categories' => 'Blog Categories',
            'company-profile' => 'Company Profile',
            'settings' => 'System Settings',
            'reports' => 'Reports',
        ];
    }

    /**
     * Get all available actions.
     */
    public static function getAvailableActions()
    {
        return [
            'view' => 'View/Read',
            'create' => 'Create/Add',
            'edit' => 'Edit/Update',
            'delete' => 'Delete/Remove',
            'manage' => 'Full Management',
            'approve' => 'Approve',
            'reject' => 'Reject',
            'export' => 'Export',
            'import' => 'Import',
            'assign' => 'Assign',
            'unassign' => 'Unassign',
            'activate' => 'Activate',
            'deactivate' => 'Deactivate',
            'publish' => 'Publish',
            'unpublish' => 'Unpublish',
        ];
    }

    /**
     * Create permissions for a module.
     */
    public static function createForModule($module, $actions = ['view', 'create', 'edit', 'delete'])
    {
        $createdPermissions = [];
        $moduleLabel = self::getAvailableModules()[$module] ?? ucfirst($module);

        foreach ($actions as $action) {
            $permissionName = $action . ' ' . $module;
            
            if (!self::where('name', $permissionName)->exists()) {
                $permission = self::create([
                    'name' => $permissionName,
                    'guard_name' => 'web',
                    'description' => "Permission to {$action} {$moduleLabel}",
                    'module' => $module,
                    'is_system' => true,
                ]);
                
                $createdPermissions[] = $permission;
            }
        }

        return $createdPermissions;
    }

    /**
     * Get permission usage statistics.
     */
    public function getUsageStatsAttribute()
    {
        return [
            'roles_count' => $this->roles_count,
            'users_with_permission' => \App\Models\User::permission($this->name)->count(),
            'is_used' => $this->roles_count > 0,
        ];
    }

    /**
     * Check if permission can be deleted.
     */
    public function isDeletable()
    {
        // System permissions cannot be deleted
        if ($this->is_system) {
            return false;
        }

        // Permissions assigned to roles cannot be deleted
        if ($this->roles_count > 0) {
            return false;
        }

        return true;
    }

    /**
     * Get related permissions (same module).
     */
    public function getRelatedPermissionsAttribute()
    {
        return self::where('module', $this->module)
            ->where('id', '!=', $this->id)
            ->orderBy('name')
            ->get();
    }

    /**
     * Get permission color based on action.
     */
    public function getActionColorAttribute()
    {
        $colors = [
            'view' => 'blue',
            'create' => 'green',
            'edit' => 'yellow',
            'delete' => 'red',
            'manage' => 'purple',
            'approve' => 'emerald',
            'reject' => 'red',
            'export' => 'indigo',
            'import' => 'cyan',
            'assign' => 'pink',
            'unassign' => 'orange',
            'activate' => 'lime',
            'deactivate' => 'gray',
            'publish' => 'teal',
            'unpublish' => 'slate',
        ];

        return $colors[$this->action] ?? 'gray';
    }

    /**
     * Bulk create permissions from array.
     */
    public static function bulkCreate(array $permissions)
    {
        $created = [];
        $skipped = [];

        foreach ($permissions as $permissionData) {
            $name = $permissionData['name'] ?? null;
            
            if (!$name) {
                continue;
            }

            if (self::where('name', $name)->exists()) {
                $skipped[] = $name;
                continue;
            }

            $permission = self::create(array_merge([
                'guard_name' => 'web',
                'is_system' => false,
            ], $permissionData));
            
            $created[] = $permission;
        }

        return [
            'created' => $created,
            'skipped' => $skipped,
        ];
    }

    /**
     * Get permissions by pattern.
     */
    public static function getByPattern($pattern)
    {
        return self::where('name', 'like', str_replace('*', '%', $pattern))
            ->orderBy('name')
            ->get();
    }

    /**
     * Check if permission is required by system.
     */
    public function isRequired()
    {
        $requiredPermissions = [
            'view dashboard',
            'view users',
            'edit users',
        ];

        return in_array($this->name, $requiredPermissions);
    }

    /**
     * Get permission dependency tree.
     */
    public function getDependenciesAttribute()
    {
        $dependencies = [
            'edit' => ['view'],
            'delete' => ['view', 'edit'],
            'manage' => ['view', 'create', 'edit', 'delete'],
        ];

        $action = $this->action;
        $resource = $this->resource;
        
        if (!isset($dependencies[$action])) {
            return collect();
        }

        $requiredActions = $dependencies[$action];
        $dependencyPermissions = [];

        foreach ($requiredActions as $requiredAction) {
            $dependencyName = $requiredAction . ' ' . $resource;
            $dependency = self::where('name', $dependencyName)->first();
            
            if ($dependency) {
                $dependencyPermissions[] = $dependency;
            }
        }

        return collect($dependencyPermissions);
    }

    /**
     * Bootstrap the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Prevent deletion of system permissions
        static::deleting(function ($permission) {
            if ($permission->is_system) {
                throw new \Exception("Cannot delete system permission: {$permission->name}");
            }

            if ($permission->roles_count > 0) {
                throw new \Exception("Cannot delete permission assigned to roles: {$permission->name}");
            }
        });

        // Auto-populate module from name if not set
        static::creating(function ($permission) {
            if (!$permission->module) {
                $parts = explode(' ', $permission->name);
                $permission->module = $parts[1] ?? 'general';
            }
        });

        // Update module when name changes
        static::updating(function ($permission) {
            if ($permission->isDirty('name') && !$permission->isDirty('module')) {
                $parts = explode(' ', $permission->name);
                $permission->module = $parts[1] ?? 'general';
            }
        });
    }
}