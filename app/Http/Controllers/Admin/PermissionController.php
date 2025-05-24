<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PermissionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin');
        
        // Apply specific permissions
        $this->middleware('can:view permissions')->only(['index', 'show']);
        $this->middleware('can:create permissions')->only(['create', 'store']);
        $this->middleware('can:edit permissions')->only(['edit', 'update']);
        $this->middleware('can:delete permissions')->only(['destroy']);
    }

    /**
     * Display a listing of permissions.
     */
    public function index(Request $request)
    {
        $query = Permission::with('roles');
        
        // Search functionality
        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }
        
        // Filter by guard
        if ($request->filled('guard')) {
            $query->where('guard_name', $request->guard);
        }
        
        // Filter by module/category
        if ($request->filled('module')) {
            $query->where('name', 'like', "%{$request->module}%");
        }
        
        $permissions = $query->orderBy('name')->paginate(20);
        
        // Group permissions by module for display
        $groupedPermissions = $permissions->getCollection()->groupBy(function($permission) {
            return explode(' ', $permission->name)[1] ?? 'general';
        });
        
        // Get available guards and modules
        $guards = collect(config('auth.guards'))->keys();
        $modules = Permission::selectRaw("SUBSTRING_INDEX(SUBSTRING_INDEX(name, ' ', 2), ' ', -1) as module")
            ->distinct()
            ->pluck('module')
            ->filter()
            ->sort();
        
        return view('admin.permissions.index', compact('permissions', 'groupedPermissions', 'guards', 'modules'));
    }

    /**
     * Show the form for creating a new permission.
     */
    public function create()
    {
        $guards = collect(config('auth.guards'))->keys();
        $modules = $this->getAvailableModules();
        $actions = $this->getAvailableActions();
        
        return view('admin.permissions.create', compact('guards', 'modules', 'actions'));
    }

    /**
     * Store a newly created permission.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name',
            'guard_name' => 'required|string|in:' . implode(',', config('auth.guards') ? array_keys(config('auth.guards')) : ['web']),
            'description' => 'nullable|string|max:500',
            'module' => 'nullable|string|max:50',
            'action' => 'nullable|string|max:50',
        ]);

        DB::beginTransaction();
        
        try {
            // If module and action are provided, construct the permission name
            if (!empty($validated['module']) && !empty($validated['action'])) {
                $permissionName = $validated['action'] . ' ' . $validated['module'];
            } else {
                $permissionName = $validated['name'];
            }

            $permission = Permission::create([
                'name' => $permissionName,
                'guard_name' => $validated['guard_name'],
                'description' => $validated['description'] ?? null,
            ]);

            DB::commit();

            return redirect()->route('admin.permissions.index')
                ->with('success', 'Permission created successfully!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error creating permission: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified permission.
     */
    public function show(Permission $permission)
    {
        $permission->load('roles');
        
        // Get roles count with this permission
        $rolesCount = $permission->roles()->count();
        
        return view('admin.permissions.show', compact('permission', 'rolesCount'));
    }

    /**
     * Show the form for editing the specified permission.
     */
    public function edit(Permission $permission)
    {
        $guards = collect(config('auth.guards'))->keys();
        $modules = $this->getAvailableModules();
        $actions = $this->getAvailableActions();
        
        // Parse current permission name to extract module and action
        $nameParts = explode(' ', $permission->name);
        $currentAction = $nameParts[0] ?? '';
        $currentModule = $nameParts[1] ?? '';
        
        return view('admin.permissions.edit', compact('permission', 'guards', 'modules', 'actions', 'currentAction', 'currentModule'));
    }

    /**
     * Update the specified permission.
     */
    public function update(Request $request, Permission $permission)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('permissions', 'name')->ignore($permission->id)
            ],
            'guard_name' => 'required|string|in:' . implode(',', config('auth.guards') ? array_keys(config('auth.guards')) : ['web']),
            'description' => 'nullable|string|max:500',
            'module' => 'nullable|string|max:50',
            'action' => 'nullable|string|max:50',
        ]);

        DB::beginTransaction();
        
        try {
            // If module and action are provided, construct the permission name
            if (!empty($validated['module']) && !empty($validated['action'])) {
                $permissionName = $validated['action'] . ' ' . $validated['module'];
            } else {
                $permissionName = $validated['name'];
            }

            $permission->update([
                'name' => $permissionName,
                'guard_name' => $validated['guard_name'],
                'description' => $validated['description'] ?? null,
            ]);

            DB::commit();

            return redirect()->route('admin.permissions.index')
                ->with('success', 'Permission updated successfully!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error updating permission: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified permission.
     */
    public function destroy(Permission $permission)
    {
        // Check if permission is assigned to any roles
        if ($permission->roles()->count() > 0) {
            return redirect()->route('admin.permissions.index')
                ->with('error', 'Cannot delete permission that is assigned to roles. Please remove from roles first.');
        }

        DB::beginTransaction();
        
        try {
            $permission->delete();

            DB::commit();

            return redirect()->route('admin.permissions.index')
                ->with('success', 'Permission deleted successfully!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->route('admin.permissions.index')
                ->with('error', 'Error deleting permission: ' . $e->getMessage());
        }
    }

    /**
     * Bulk create permissions for a module.
     */
    public function bulkCreate(Request $request)
    {
        $validated = $request->validate([
            'module' => 'required|string|max:50',
            'actions' => 'required|array|min:1',
            'actions.*' => 'required|string|max:50',
            'guard_name' => 'required|string|in:' . implode(',', config('auth.guards') ? array_keys(config('auth.guards')) : ['web']),
        ]);

        DB::beginTransaction();
        
        try {
            $createdCount = 0;
            $skippedCount = 0;

            foreach ($validated['actions'] as $action) {
                $permissionName = $action . ' ' . $validated['module'];
                
                // Check if permission already exists
                if (Permission::where('name', $permissionName)->where('guard_name', $validated['guard_name'])->exists()) {
                    $skippedCount++;
                    continue;
                }

                Permission::create([
                    'name' => $permissionName,
                    'guard_name' => $validated['guard_name'],
                    'description' => "Permission to {$action} {$validated['module']}",
                ]);
                
                $createdCount++;
            }

            DB::commit();

            $message = "Created {$createdCount} permissions.";
            if ($skippedCount > 0) {
                $message .= " Skipped {$skippedCount} existing permissions.";
            }

            return redirect()->route('admin.permissions.index')
                ->with('success', $message);
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error creating permissions: ' . $e->getMessage());
        }
    }

    /**
     * Show bulk create form.
     */
    public function showBulkCreate()
    {
        $guards = collect(config('auth.guards'))->keys();
        $modules = $this->getAvailableModules();
        $actions = $this->getAvailableActions();
        
        return view('admin.permissions.bulk-create', compact('guards', 'modules', 'actions'));
    }

    /**
     * Get available modules from existing permissions.
     */
    private function getAvailableModules()
    {
        $modules = Permission::selectRaw("SUBSTRING_INDEX(SUBSTRING_INDEX(name, ' ', 2), ' ', -1) as module")
            ->distinct()
            ->pluck('module')
            ->filter()
            ->sort();

        // Add default modules if not present
        $defaultModules = [
            'users', 'roles', 'permissions', 'projects', 'quotations', 
            'messages', 'services', 'testimonials', 'blog', 'settings',
            'dashboard', 'reports', 'team', 'certifications'
        ];

        return $modules->merge($defaultModules)->unique()->sort()->values();
    }

    /**
     * Get available actions.
     */
    private function getAvailableActions()
    {
        return collect([
            'view', 'create', 'edit', 'delete', 'manage',
            'approve', 'reject', 'export', 'import',
            'assign', 'unassign', 'activate', 'deactivate'
        ]);
    }

    /**
     * Get roles for permission (AJAX).
     */
    public function roles(Permission $permission)
    {
        $roles = $permission->roles()
            ->select('id', 'name', 'guard_name')
            ->orderBy('name')
            ->get();
        
        return response()->json($roles);
    }
}