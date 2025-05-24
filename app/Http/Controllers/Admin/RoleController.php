<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin');
        
        // Apply specific permissions for role management
        $this->middleware('can:view roles')->only(['index', 'show']);
        $this->middleware('can:create roles')->only(['create', 'store']);
        $this->middleware('can:edit roles')->only(['edit', 'update']);
        $this->middleware('can:delete roles')->only(['destroy']);
        $this->middleware('can:assign permissions')->only(['permissions', 'updatePermissions']);
    }

    /**
     * Display a listing of roles.
     */
    public function index(Request $request)
    {
        $query = Role::with(['permissions', 'users']);
        
        // Search functionality
        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }
        
        // Filter by guard
        if ($request->filled('guard')) {
            $query->where('guard_name', $request->guard);
        }
        
        $roles = $query->orderBy('name')->paginate(15);
        
        // Get available guards
        $guards = collect(config('auth.guards'))->keys();
        
        return view('admin.roles.index', compact('roles', 'guards'));
    }

    /**
     * Show the form for creating a new role.
     */
    public function create()
    {
        $permissions = Permission::orderBy('name')->get()->groupBy(function($permission) {
            return explode(' ', $permission->name)[1] ?? 'general';
        });
        
        $guards = collect(config('auth.guards'))->keys();
        
        return view('admin.roles.create', compact('permissions', 'guards'));
    }

    /**
     * Store a newly created role.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'guard_name' => 'required|string|in:' . implode(',', config('auth.guards') ? array_keys(config('auth.guards')) : ['web']),
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
            'description' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        
        try {
            // Create role
            $role = Role::create([
                'name' => $validated['name'],
                'guard_name' => $validated['guard_name'],
                'description' => $validated['description'] ?? null,
            ]);

            // Assign permissions if provided
            if (!empty($validated['permissions'])) {
                $permissions = Permission::whereIn('id', $validated['permissions'])->get();
                $role->syncPermissions($permissions);
            }

            DB::commit();

            return redirect()->route('admin.roles.index')
                ->with('success', 'Role created successfully!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error creating role: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified role.
     */
    public function show(Role $role)
    {
        $role->load(['permissions', 'users']);
        
        // Get users count with this role
        $usersCount = $role->users()->count();
        
        // Get permissions grouped by module
        $permissions = $role->permissions->groupBy(function($permission) {
            return explode(' ', $permission->name)[1] ?? 'general';
        });
        
        return view('admin.roles.show', compact('role', 'usersCount', 'permissions'));
    }

    /**
     * Show the form for editing the specified role.
     */
    public function edit(Role $role)
    {
        // Prevent editing of super-admin role by non-super-admins
        if ($role->name === 'super-admin' && !auth()->user()->hasRole('super-admin')) {
            abort(403, 'Cannot edit super-admin role.');
        }
        
        $permissions = Permission::orderBy('name')->get()->groupBy(function($permission) {
            return explode(' ', $permission->name)[1] ?? 'general';
        });
        
        $rolePermissions = $role->permissions->pluck('id')->toArray();
        $guards = collect(config('auth.guards'))->keys();
        
        return view('admin.roles.edit', compact('role', 'permissions', 'rolePermissions', 'guards'));
    }

    /**
     * Update the specified role.
     */
    public function update(Request $request, Role $role)
    {
        // Prevent editing of super-admin role by non-super-admins
        if ($role->name === 'super-admin' && !auth()->user()->hasRole('super-admin')) {
            abort(403, 'Cannot edit super-admin role.');
        }
        
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('roles', 'name')->ignore($role->id)
            ],
            'guard_name' => 'required|string|in:' . implode(',', config('auth.guards') ? array_keys(config('auth.guards')) : ['web']),
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
            'description' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        
        try {
            // Update role
            $role->update([
                'name' => $validated['name'],
                'guard_name' => $validated['guard_name'],
                'description' => $validated['description'] ?? null,
            ]);

            // Sync permissions
            if (isset($validated['permissions'])) {
                $permissions = Permission::whereIn('id', $validated['permissions'])->get();
                $role->syncPermissions($permissions);
            } else {
                $role->syncPermissions([]);
            }

            DB::commit();

            return redirect()->route('admin.roles.index')
                ->with('success', 'Role updated successfully!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error updating role: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified role.
     */
    public function destroy(Role $role)
    {
        // Prevent deletion of super-admin role
        if ($role->name === 'super-admin') {
            return redirect()->route('admin.roles.index')
                ->with('error', 'Cannot delete super-admin role.');
        }
        
        // Prevent deletion if role has users
        if ($role->users()->count() > 0) {
            return redirect()->route('admin.roles.index')
                ->with('error', 'Cannot delete role that has assigned users. Please reassign users first.');
        }

        DB::beginTransaction();
        
        try {
            // Remove all permissions from role
            $role->syncPermissions([]);
            
            // Delete role
            $role->delete();

            DB::commit();

            return redirect()->route('admin.roles.index')
                ->with('success', 'Role deleted successfully!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->route('admin.roles.index')
                ->with('error', 'Error deleting role: ' . $e->getMessage());
        }
    }

    /**
     * Show role permissions management form.
     */
    public function permissions(Role $role)
    {
        $permissions = Permission::orderBy('name')->get()->groupBy(function($permission) {
            return explode(' ', $permission->name)[1] ?? 'general';
        });
        
        $rolePermissions = $role->permissions->pluck('id')->toArray();
        
        return view('admin.roles.permissions', compact('role', 'permissions', 'rolePermissions'));
    }

    /**
     * Update role permissions.
     */
    public function updatePermissions(Request $request, Role $role)
    {
        $validated = $request->validate([
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        DB::beginTransaction();
        
        try {
            if (isset($validated['permissions'])) {
                $permissions = Permission::whereIn('id', $validated['permissions'])->get();
                $role->syncPermissions($permissions);
            } else {
                $role->syncPermissions([]);
            }

            DB::commit();

            return redirect()->route('admin.roles.show', $role)
                ->with('success', 'Role permissions updated successfully!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Error updating permissions: ' . $e->getMessage());
        }
    }

    /**
     * Get users with specific role (AJAX).
     */
    public function users(Role $role)
    {
        $users = $role->users()
            ->select('id', 'name', 'email', 'is_active')
            ->orderBy('name')
            ->get();
        
        return response()->json($users);
    }

    /**
     * Duplicate a role.
     */
    public function duplicate(Role $role)
    {
        DB::beginTransaction();
        
        try {
            $newRole = Role::create([
                'name' => $role->name . ' (Copy)',
                'guard_name' => $role->guard_name,
                'description' => ($role->description ?? '') . ' (Duplicated)',
            ]);

            // Copy permissions
            $permissions = $role->permissions;
            $newRole->syncPermissions($permissions);

            DB::commit();

            return redirect()->route('admin.roles.edit', $newRole)
                ->with('success', 'Role duplicated successfully! Please update the name and description.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Error duplicating role: ' . $e->getMessage());
        }
    }
}