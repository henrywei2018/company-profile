<?php
// File: app/Http/Controllers/Admin/UserController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    /**
     * Display a listing of the users.
     */
    public function index(Request $request)
{
    $users = User::with('roles')
        ->when($request->filled('role'), function ($query) use ($request) {
            return $query->whereHas('roles', function ($q) use ($request) {
                $q->where('name', $request->role);
            });
        })
        ->when($request->filled('search'), function ($query) use ($request) {
            return $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%");
            });
        })
        ->paginate(15);
    
    $roles = Role::pluck('name', 'name'); // For filter dropdown
    
    return view('admin.users.index', compact('users', 'roles'));
}

    /**
     * Show the form for creating a new user.
     */
    public function create()
{
    $roles = Role::all();
    
    return view('admin.users.create', compact('roles'));
}


    /**
     * Store a newly created user.
     */
    public function store(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:8|confirmed',
        'roles' => 'required|array',
        'roles.*' => 'exists:roles,id',
        'is_active' => 'boolean',
        'avatar' => 'nullable|image|max:1024',
        'phone' => 'nullable|string|max:20',
        'address' => 'nullable|string',
        'company' => 'nullable|string|max:255',
    ]);
    
    DB::beginTransaction();
    
    try {
        // Create user
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'is_active' => $validated['is_active'] ?? false,
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
            'company' => $validated['company'] ?? null,
        ]);
        
        // Assign roles
        $roles = Role::whereIn('id', $validated['roles'])->get();
        $user->syncRoles($roles);
        
        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->update(['avatar' => $path]);
        }
        
        DB::commit();
        
        return redirect()->route('admin.users.index')
            ->with('success', 'User created successfully!');
            
    } catch (\Exception $e) {
        DB::rollBack();
        
        return redirect()->back()
            ->withInput()
            ->with('error', 'Error creating user: ' . $e->getMessage());
    }
}

    /**
     * Display the specified user.
     */
    public function show(User $user)
{
    $user->load(['roles', 'roles.permissions']);
    
    // Get user's permissions grouped by module
    $userPermissions = $user->getAllPermissions()
        ->groupBy(function($permission) {
            return explode(' ', $permission->name)[1] ?? 'general';
        });
    
    return view('admin.users.show', compact('user', 'userPermissions'));
}

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
{
    $roles = Role::all();
    $userRoles = $user->roles->pluck('id')->toArray();
    
    return view('admin.users.edit', compact('user', 'roles', 'userRoles'));
}

    /**
     * Update the specified user.
     */
    public function update(Request $request, User $user)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
        'roles' => 'required|array',
        'roles.*' => 'exists:roles,id',
        'is_active' => 'boolean',
        'avatar' => 'nullable|image|max:1024',
        'phone' => 'nullable|string|max:20',
        'address' => 'nullable|string',
        'company' => 'nullable|string|max:255',
    ]);
    
    DB::beginTransaction();
    
    try {
        // Update user
        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'is_active' => $validated['is_active'] ?? false,
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
            'company' => $validated['company'] ?? null,
        ]);
        
        // Sync roles
        $roles = Role::whereIn('id', $validated['roles'])->get();
        $user->syncRoles($roles);
        
        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            
            // Store new avatar
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->update(['avatar' => $path]);
        }
        
        DB::commit();
        
        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully!');
            
    } catch (\Exception $e) {
        DB::rollBack();
        
        return redirect()->back()
            ->withInput()
            ->with('error', 'Error updating user: ' . $e->getMessage());
    }
}

    /**
     * Remove the specified user.
     */
    public function destroy(User $user)
{
    // Prevent deleting self
    if ($user->id === auth()->id()) {
        return redirect()->route('admin.users.index')
            ->with('error', 'You cannot delete your own account!');
    }
    
    // Prevent deleting the last super-admin
    if ($user->hasRole('super-admin')) {
        $superAdminCount = Role::where('name', 'super-admin')->first()->users()->count();
        if ($superAdminCount <= 1) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Cannot delete the last super-admin user!');
        }
    }
    
    DB::beginTransaction();
    
    try {
        // Delete avatar
        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }
        
        // Remove all roles (this will also clean up permissions)
        $user->syncRoles([]);
        
        // Delete user
        $user->delete();
        
        DB::commit();
        
        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully!');
            
    } catch (\Exception $e) {
        DB::rollBack();
        
        return redirect()->route('admin.users.index')
            ->with('error', 'Error deleting user: ' . $e->getMessage());
    }
}
    
    /**
     * Show form to change password.
     */
    public function showChangePasswordForm(User $user)
    {
        return view('admin.users.change-password', compact('user'));
    }
    
    /**
     * Change user password.
     */
    public function changePassword(Request $request, User $user)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);
        
        $user->update([
            'password' => Hash::make($request->password),
        ]);
        
        return redirect()->route('admin.users.index')
            ->with('success', 'Password changed successfully!');
    }
    
    
    /**
     * Toggle user active status.
     */
    public function toggleActive(User $user)
    {
        // Prevent deactivating self
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'You cannot deactivate your own account!');
        }
        
        $user->update([
            'is_active' => !$user->is_active
        ]);
        
        return redirect()->back()
            ->with('success', 'User status updated!');
    }
    
    /**
     * Verify a client account.
     */
    public function verifyClient(User $user)
{
    // Check if user is a client
    if (!$user->hasRole('client')) {
        return redirect()->route('admin.users.index')
            ->with('error', 'Only client accounts can be verified!');
    }
    
    $user->update([
        'email_verified_at' => $user->email_verified_at ? null : now(),
    ]);
    
    $status = $user->email_verified_at ? 'verified' : 'unverified';
    
    return redirect()->back()
        ->with('success', "Client account {$status} successfully!");
}
    
}