<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\RolePermissionService;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class RBACController extends Controller
{
    protected $rbacService;

    public function __construct(RolePermissionService $rbacService)
    {
        $this->middleware('auth');
        $this->middleware('admin');
        $this->middleware('can:view roles')->only(['dashboard']);
        $this->middleware('can:manage roles')->only(['clearCache']);
        
        $this->rbacService = $rbacService;
    }

    /**
     * Display RBAC dashboard with statistics and overview.
     */
    public function dashboard(Request $request)
    {
        // Get basic statistics
        $stats = $this->rbacService->getPermissionStats();
        
        // Get role distribution
        $roleDistribution = User::selectRaw('
            roles.name as role_name,
            COUNT(model_has_roles.model_id) as user_count
        ')
        ->leftJoin('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
        ->leftJoin('roles', 'model_has_roles.role_id', '=', 'roles.id')
        ->where('users.is_active', true)
        ->groupBy('roles.name')
        ->pluck('user_count', 'role_name')
        ->toArray();

        // Get recent role assignments
        $recentAssignments = DB::table('model_has_roles')
            ->join('users', 'model_has_roles.model_id', '=', 'users.id')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->select('users.name as user_name', 'users.email', 'roles.name as role_name')
            ->orderBy('model_has_roles.created_at', 'desc')
            ->limit(10)
            ->get();

        // Get permissions by module
        $permissionsByModule = Permission::selectRaw('
            SUBSTRING_INDEX(SUBSTRING_INDEX(name, " ", 2), " ", -1) as module,
            COUNT(*) as count
        ')
        ->groupBy('module')
        ->orderBy('count', 'desc')
        ->get();

        // Get roles with most permissions
        $rolesWithMostPermissions = Role::withCount('permissions')
            ->orderBy('permissions_count', 'desc')
            ->limit(5)
            ->get();

        // Get users with multiple roles
        $usersWithMultipleRoles = User::has('roles', '>', 1)
            ->with('roles')
            ->limit(10)
            ->get();

        // Get system health data
        $systemHealth = [
            'users_without_roles' => User::doesntHave('roles')->where('is_active', true)->count(),
            'inactive_users_with_roles' => User::has('roles')->where('is_active', false)->count(),
            'empty_roles' => Role::doesntHave('users')->where('is_system', false)->count(),
            'unused_permissions' => Permission::doesntHave('roles')->count(),
        ];

        return view('admin.rbac.dashboard', compact(
            'stats',
            'roleDistribution',
            'recentAssignments',
            'permissionsByModule',
            'rolesWithMostPermissions',
            'usersWithMultipleRoles',
            'systemHealth'
        ));
    }

    /**
     * Display audit log for role and permission changes.
     */
    public function auditLog(Request $request)
    {
        // This would require an audit log system implementation
        // For now, we'll return a basic view with recent model changes
        
        $query = collect();
        
        // Get recent role changes (if you have model events or audit system)
        // This is a placeholder - you'd implement proper audit logging
        
        $recentChanges = [
            [
                'type' => 'role_created',
                'description' => 'New role created',
                'user' => auth()->user()->name,
                'timestamp' => now()->subHours(2),
                'details' => 'Created role: Editor'
            ],
            [
                'type' => 'permission_assigned',
                'description' => 'Permission assigned to role',
                'user' => auth()->user()->name,
                'timestamp' => now()->subHours(5),
                'details' => 'Assigned "edit blog" permission to Editor role'
            ],
            [
                'type' => 'user_role_assigned',
                'description' => 'Role assigned to user',
                'user' => auth()->user()->name,
                'timestamp' => now()->subDay(),
                'details' => 'Assigned Editor role to john@example.com'
            ]
        ];

        return view('admin.rbac.audit-log', compact('recentChanges'));
    }

    /**
     * Clear all RBAC caches.
     */
    public function clearCache(Request $request)
    {
        try {
            $this->rbacService->clearCache();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'RBAC cache cleared successfully!'
                ]);
            }

            return redirect()->back()->with('success', 'RBAC cache cleared successfully!');

        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error clearing cache: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Error clearing cache: ' . $e->getMessage());
        }
    }

    /**
     * Get RBAC statistics (API endpoint).
     */
    public function getStats(Request $request)
    {
        $stats = $this->rbacService->getPermissionStats();
        
        $additionalStats = [
            'role_hierarchy' => $this->rbacService->getRoleHierarchy(),
            'recent_role_assignments' => DB::table('model_has_roles')
                ->join('users', 'model_has_roles.model_id', '=', 'users.id')
                ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                ->select('users.name as user_name', 'roles.name as role_name')
                ->orderBy('model_has_roles.created_at', 'desc')
                ->limit(5)
                ->get(),
        ];

        return response()->json(array_merge($stats, $additionalStats));
    }

    /**
     * Get permissions for a specific module (API endpoint).
     */
    public function getModulePermissions(Request $request, $module)
    {
        $permissions = Permission::where('name', 'like', "% {$module}")
            ->orderBy('name')
            ->get();

        return response()->json($permissions);
    }

    /**
     * Bulk assign permissions to role.
     */
    public function bulkAssignPermissions(Request $request)
    {
        $validated = $request->validate([
            'role_id' => 'required|exists:roles,id',
            'permission_ids' => 'required|array',
            'permission_ids.*' => 'exists:permissions,id',
        ]);

        try {
            $role = Role::findOrFail($validated['role_id']);
            $permissions = Permission::whereIn('id', $validated['permission_ids'])->get();
            
            $role->givePermissionTo($permissions);
            
            $this->rbacService->clearCache();

            return response()->json([
                'success' => true,
                'message' => 'Permissions assigned successfully!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error assigning permissions: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk remove permissions from role.
     */
    public function bulkRemovePermissions(Request $request)
    {
        $validated = $request->validate([
            'role_id' => 'required|exists:roles,id',
            'permission_ids' => 'required|array',
            'permission_ids.*' => 'exists:permissions,id',
        ]);

        try {
            $role = Role::findOrFail($validated['role_id']);
            $permissions = Permission::whereIn('id', $validated['permission_ids'])->get();
            
            $role->revokePermissionTo($permissions);
            
            $this->rbacService->clearCache();

            return response()->json([
                'success' => true,
                'message' => 'Permissions removed successfully!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error removing permissions: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get role comparison data.
     */
    public function compareRoles(Request $request)
    {
        $validated = $request->validate([
            'role_ids' => 'required|array|min:2|max:5',
            'role_ids.*' => 'exists:roles,id',
        ]);

        $roles = Role::whereIn('id', $validated['role_ids'])
            ->with('permissions')
            ->get();

        $comparison = [];
        $allPermissions = collect();

        foreach ($roles as $role) {
            $rolePermissions = $role->permissions->pluck('name')->toArray();
            $comparison[$role->name] = $rolePermissions;
            $allPermissions = $allPermissions->merge($rolePermissions);
        }

        $allPermissions = $allPermissions->unique()->sort()->values();

        return response()->json([
            'roles' => $comparison,
            'all_permissions' => $allPermissions,
            'comparison_matrix' => $this->buildComparisonMatrix($roles, $allPermissions)
        ]);
    }

    /**
     * Build comparison matrix for roles.
     */
    private function buildComparisonMatrix($roles, $permissions)
    {
        $matrix = [];

        foreach ($permissions as $permission) {
            $matrix[$permission] = [];
            
            foreach ($roles as $role) {
                $matrix[$permission][$role->name] = $role->hasPermissionTo($permission);
            }
        }

        return $matrix;
    }

    /**
     * Export RBAC configuration.
     */
    public function exportConfiguration(Request $request)
    {
        $roles = Role::with('permissions')->get();
        $permissions = Permission::all();

        $config = [
            'exported_at' => now()->toISOString(),
            'roles' => $roles->map(function ($role) {
                return [
                    'name' => $role->name,
                    'guard_name' => $role->guard_name,
                    'description' => $role->description,
                    'permissions' => $role->permissions->pluck('name')->toArray(),
                ];
            }),
            'permissions' => $permissions->map(function ($permission) {
                return [
                    'name' => $permission->name,
                    'guard_name' => $permission->guard_name,
                    'description' => $permission->description,
                    'module' => $permission->module,
                ];
            }),
        ];

        $filename = 'rbac_configuration_' . now()->format('Y-m-d_H-i-s') . '.json';

        return response()->json($config)
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
}