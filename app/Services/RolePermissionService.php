<?php

namespace App\Services;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class RolePermissionService
{
    /**
     * Get all available permissions grouped by module.
     */
    public function getGroupedPermissions(): Collection
    {
        return Cache::remember('grouped_permissions', 3600, function () {
            return Permission::orderBy('name')->get()->groupBy(function($permission) {
                return explode(' ', $permission->name)[1] ?? 'general';
            });
        });
    }

    /**
     * Get all available roles with their permissions count.
     */
    public function getRolesWithStats(): Collection
    {
        return Role::withCount(['permissions', 'users'])
            ->orderBy('name')
            ->get();
    }

    /**
     * Create a new role with permissions.
     */
    public function createRole(array $data): Role
    {
        DB::beginTransaction();
        
        try {
            $role = Role::create([
                'name' => $data['name'],
                'guard_name' => $data['guard_name'] ?? 'web',
                'description' => $data['description'] ?? null,
                'color' => $data['color'] ?? null,
                'is_system' => $data['is_system'] ?? false,
            ]);

            if (!empty($data['permissions'])) {
                $permissions = Permission::whereIn('id', $data['permissions'])->get();
                $role->syncPermissions($permissions);
            }

            DB::commit();
            
            // Clear cache
            $this->clearCache();
            
            return $role;
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Update an existing role.
     */
    public function updateRole(Role $role, array $data): Role
    {
        DB::beginTransaction();
        
        try {
            $role->update([
                'name' => $data['name'],
                'guard_name' => $data['guard_name'] ?? $role->guard_name,
                'description' => $data['description'] ?? null,
                'color' => $data['color'] ?? null,
            ]);

            if (isset($data['permissions'])) {
                if (!empty($data['permissions'])) {
                    $permissions = Permission::whereIn('id', $data['permissions'])->get();
                    $role->syncPermissions($permissions);
                } else {
                    $role->syncPermissions([]);
                }
            }

            DB::commit();
            
            // Clear cache
            $this->clearCache();
            
            return $role;
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Delete a role safely.
     */
    public function deleteRole(Role $role): bool
    {
        // Check if role is system role
        if ($role->is_system) {
            throw new \Exception('Cannot delete system role.');
        }

        // Check if role has users
        if ($role->users()->count() > 0) {
            throw new \Exception('Cannot delete role that has assigned users.');
        }

        DB::beginTransaction();
        
        try {
            // Remove all permissions
            $role->syncPermissions([]);
            
            // Delete role
            $role->delete();

            DB::commit();
            
            // Clear cache
            $this->clearCache();
            
            return true;
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Create bulk permissions for a module.
     */
    public function createBulkPermissions(string $module, array $actions, string $guardName = 'web'): array
    {
        DB::beginTransaction();
        
        try {
            $created = [];
            $skipped = [];

            foreach ($actions as $action) {
                $permissionName = $action . ' ' . $module;
                
                if (Permission::where('name', $permissionName)->where('guard_name', $guardName)->exists()) {
                    $skipped[] = $permissionName;
                    continue;
                }

                $permission = Permission::create([
                    'name' => $permissionName,
                    'guard_name' => $guardName,
                    'description' => "Permission to {$action} {$module}",
                    'module' => $module,
                ]);
                
                $created[] = $permission;
            }

            DB::commit();
            
            // Clear cache
            $this->clearCache();
            
            return [
                'created' => $created,
                'skipped' => $skipped
            ];
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Assign role to user.
     */
    public function assignRoleToUser(User $user, Role $role): bool
    {
        try {
            $user->assignRole($role);
            
            // Clear user permissions cache
            $user->forgetCachedPermissions();
            
            return true;
            
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Remove role from user.
     */
    public function removeRoleFromUser(User $user, Role $role): bool
    {
        try {
            $user->removeRole($role);
            
            // Clear user permissions cache
            $user->forgetCachedPermissions();
            
            return true;
            
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Sync user roles.
     */
    public function syncUserRoles(User $user, array $roleIds): bool
    {
        try {
            $roles = Role::whereIn('id', $roleIds)->get();
            $user->syncRoles($roles);
            
            // Clear user permissions cache
            $user->forgetCachedPermissions();
            
            return true;
            
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get user permissions tree.
     */
    public function getUserPermissionsTree(User $user): array
    {
        $permissions = $user->getAllPermissions()->groupBy(function($permission) {
            return explode(' ', $permission->name)[1] ?? 'general';
        });

        $tree = [];
        foreach ($permissions as $module => $modulePermissions) {
            $tree[$module] = $modulePermissions->pluck('name', 'id')->toArray();
        }

        return $tree;
    }

    /**
     * Check if user can perform action on resource.
     */
    public function userCanPerformAction(User $user, string $action, string $resource): bool
    {
        $permission = $action . ' ' . $resource;
        return $user->can($permission);
    }

    /**
     * Get role hierarchy for display.
     */
    public function getRoleHierarchy(): array
    {
        return [
            'super-admin' => [
                'level' => 1,
                'name' => 'Super Administrator',
                'description' => 'Full system access',
                'color' => 'red'
            ],
            'admin' => [
                'level' => 2,
                'name' => 'Administrator',
                'description' => 'Administrative access',
                'color' => 'blue'
            ],
            'manager' => [
                'level' => 3,
                'name' => 'Manager',
                'description' => 'Management access',
                'color' => 'purple'
            ],
            'editor' => [
                'level' => 4,
                'name' => 'Editor',
                'description' => 'Content management',
                'color' => 'green'
            ],
            'client' => [
                'level' => 5,
                'name' => 'Client',
                'description' => 'Client access',
                'color' => 'yellow'
            ],
        ];
    }

    /**
     * Get available actions for permissions.
     */
    public function getAvailableActions(): array
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
     * Get available modules for permissions.
     */
    public function getAvailableModules(): array
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
     * Get permission statistics.
     */
    public function getPermissionStats(): array
    {
        return [
            'total_permissions' => Permission::count(),
            'total_roles' => Role::count(),
            'system_roles' => Role::where('is_system', true)->count(),
            'custom_roles' => Role::where('is_system', false)->count(),
            'users_with_roles' => User::role(Role::all())->count(),
            'permissions_by_module' => Permission::selectRaw('
                SUBSTRING_INDEX(SUBSTRING_INDEX(name, " ", 2), " ", -1) as module,
                COUNT(*) as count
            ')
            ->groupBy('module')
            ->pluck('count', 'module')
            ->toArray(),
        ];
    }

    /**
     * Clear all RBAC related caches.
     */
    public function clearCache(): void
    {
        Cache::forget('grouped_permissions');
        Cache::forget('roles_with_stats');
        
        // Clear Spatie permission cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }

    /**
     * Validate role permissions configuration.
     */
    public function validateRoleConfiguration(array $roleData): array
    {
        $errors = [];

        if (empty($roleData['name'])) {
            $errors[] = 'Role name is required.';
        }

        if (Role::where('name', $roleData['name'])->exists()) {
            $errors[] = 'Role name already exists.';
        }

        if (!empty($roleData['permissions'])) {
            $validPermissions = Permission::whereIn('id', $roleData['permissions'])->count();
            if ($validPermissions !== count($roleData['permissions'])) {
                $errors[] = 'Some permissions are invalid.';
            }
        }

        return $errors;
    }
}