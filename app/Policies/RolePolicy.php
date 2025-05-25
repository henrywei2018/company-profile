<?php

namespace App\Policies;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Auth\Access\HandlesAuthorization;

class RolePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any roles.
     */
    public function viewAny(User $user)
    {
        return $user->can('view roles');
    }

    /**
     * Determine whether the user can view the role.
     */
    public function view(User $user, Role $role)
    {
        return $user->can('view roles');
    }

    /**
     * Determine whether the user can create roles.
     */
    public function create(User $user)
    {
        return $user->can('create roles');
    }

    /**
     * Determine whether the user can update the role.
     */
    public function update(User $user, Role $role)
    {
        // Super admin role can only be edited by super admins
        if ($role->name === 'super-admin' && !$user->hasRole('super-admin')) {
            return false;
        }

        return $user->can('edit roles');
    }

    /**
     * Determine whether the user can delete the role.
     */
    public function delete(User $user, Role $role)
    {
        // Cannot delete system roles
        if ($role->is_system && !$user->hasRole('super-admin')) {
            return false;
        }

        // Cannot delete super-admin role
        if ($role->name === 'super-admin') {
            return false;
        }

        // Cannot delete roles with users
        if ($role->users()->count() > 0) {
            return false;
        }

        return $user->can('delete roles');
    }

    /**
     * Determine whether the user can assign permissions to role.
     */
    public function assignPermissions(User $user, Role $role)
    {
        // Super admin role can only be modified by super admins
        if ($role->name === 'super-admin' && !$user->hasRole('super-admin')) {
            return false;
        }

        return $user->can('assign permissions') || $user->can('edit roles');
    }
}

// File: app/Policies/PermissionPolicy.php
namespace App\Policies;

use App\Models\User;
use Spatie\Permission\Models\Permission;
use Illuminate\Auth\Access\HandlesAuthorization;

class PermissionPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any permissions.
     */
    public function viewAny(User $user)
    {
        return $user->can('view permissions');
    }

    /**
     * Determine whether the user can view the permission.
     */
    public function view(User $user, Permission $permission)
    {
        return $user->can('view permissions');
    }

    /**
     * Determine whether the user can create permissions.
     */
    public function create(User $user)
    {
        return $user->can('create permissions');
    }

    /**
     * Determine whether the user can update the permission.
     */
    public function update(User $user, Permission $permission)
    {
        // System permissions can only be edited by super admins
        if ($permission->is_system && !$user->hasRole('super-admin')) {
            return false;
        }

        return $user->can('edit permissions');
    }

    /**
     * Determine whether the user can delete the permission.
     */
    public function delete(User $user, Permission $permission)
    {
        // Cannot delete system permissions
        if ($permission->is_system && !$user->hasRole('super-admin')) {
            return false;
        }

        // Cannot delete permissions that are assigned to roles
        if ($permission->roles()->count() > 0) {
            return false;
        }

        return $user->can('delete permissions');
    }
}

// File: app/Policies/UserPolicy.php (Enhanced version)
namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any users.
     */
    public function viewAny(User $user)
    {
        return $user->can('view users');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model)
    {
        // Users can always view their own profile
        if ($user->id === $model->id) {
            return true;
        }

        return $user->can('view users');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user)
    {
        return $user->can('create users');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model)
    {
        // Users can always edit their own profile
        if ($user->id === $model->id) {
            return true;
        }

        // Cannot edit users with higher role hierarchy
        if ($this->hasHigherRole($model, $user)) {
            return false;
        }

        return $user->can('edit users');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model)
    {
        // Cannot delete self
        if ($user->id === $model->id) {
            return false;
        }

        // Cannot delete users with higher role hierarchy
        if ($this->hasHigherRole($model, $user)) {
            return false;
        }

        // Cannot delete the last super admin
        if ($model->hasRole('super-admin')) {
            $superAdminCount = User::role('super-admin')->count();
            if ($superAdminCount <= 1) {
                return false;
            }
        }

        return $user->can('delete users');
    }

    /**
     * Determine whether the user can assign roles to the model.
     */
    public function assignRoles(User $user, User $model)
    {
        // Cannot assign roles to self unless super admin
        if ($user->id === $model->id && !$user->hasRole('super-admin')) {
            return false;
        }

        // Cannot assign roles to users with higher role hierarchy
        if ($this->hasHigherRole($model, $user)) {
            return false;
        }

        return $user->can('assign roles') || $user->can('edit users');
    }

    /**
     * Determine whether the user can activate/deactivate the model.
     */
    public function toggleActive(User $user, User $model)
    {
        // Cannot deactivate self
        if ($user->id === $model->id) {
            return false;
        }

        // Cannot deactivate users with higher role hierarchy
        if ($this->hasHigherRole($model, $user)) {
            return false;
        }

        return $user->can('activate users') || $user->can('deactivate users');
    }

    /**
     * Check if target user has higher role than current user.
     */
    private function hasHigherRole(User $targetUser, User $currentUser)
    {
        // Super admin has highest priority
        if ($targetUser->hasRole('super-admin') && !$currentUser->hasRole('super-admin')) {
            return true;
        }

        // Admin cannot be modified by non-super-admin
        if ($targetUser->hasRole('admin') && !$currentUser->hasAnyRole(['super-admin', 'admin'])) {
            return true;
        }

        // Manager cannot be modified by editor or client
        if ($targetUser->hasRole('manager') && !$currentUser->hasAnyRole(['super-admin', 'admin', 'manager'])) {
            return true;
        }

        return false;
    }
}

// File: app/Providers/AuthServiceProvider.php (Update to register policies)
namespace App\Providers;

use App\Models\Project;
use App\Models\Quotation;
use App\Models\Message;
use App\Models\User;
use App\Policies\ProjectPolicy;
use App\Policies\QuotationPolicy;
use App\Policies\MessagePolicy;
use App\Policies\UserPolicy;
use App\Policies\RolePolicy;
use App\Policies\PermissionPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Project::class => ProjectPolicy::class,
        Quotation::class => QuotationPolicy::class,
        Message::class => MessagePolicy::class,
        User::class => UserPolicy::class,
        Role::class => RolePolicy::class,
        Permission::class => PermissionPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Define gates for admin access
        Gate::define('access-admin', function ($user) {
            return $user->hasAnyRole(['super-admin', 'admin', 'manager', 'editor']);
        });

        // Define gates for client access
        Gate::define('access-client', function ($user) {
            return $user->hasRole('client') && $user->is_active;
        });

        // Define gate for super admin actions
        Gate::define('super-admin', function ($user) {
            return $user->hasRole('super-admin');
        });

        // Define gate for role management
        Gate::define('manage-roles', function ($user) {
            return $user->hasAnyRole(['super-admin', 'admin']);
        });

        // Define gate for permission management  
        Gate::define('manage-permissions', function ($user) {
            return $user->hasRole('super-admin');
        });

        // Define gate for user management
        Gate::define('manage-users', function ($user) {
            return $user->hasAnyRole(['super-admin', 'admin', 'manager']);
        });

        // Implicit model policy discovery
        Gate::guessPolicyNamesUsing(function ($modelClass) {
            return 'App\\Policies\\' . class_basename($modelClass) . 'Policy';
        });
    }
}