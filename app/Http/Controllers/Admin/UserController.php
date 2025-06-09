<?php
// File: app/Http/Controllers/Admin/UserController.php - REFACTORED VERSION

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\UserService;
use App\Facades\Notifications;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * ✅ KEEP - Display a listing of the users (admin-specific with filters)
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
            ->when($request->filled('status'), function ($query) use ($request) {
                switch ($request->status) {
                    case 'active':
                        return $query->where('is_active', true);
                    case 'inactive':
                        return $query->where('is_active', false);
                    case 'verified':
                        return $query->whereNotNull('email_verified_at');
                    case 'unverified':
                        return $query->whereNull('email_verified_at');
                }
            })
            ->paginate(15);

        $roles = Role::pluck('name', 'name');

        return view('admin.users.index', compact('users', 'roles'));
    }

    /**
     * ✅ KEEP - Show the form for creating a new user (admin-only)
     */
    public function create()
    {
        $roles = Role::all();

        return view('admin.users.create', compact('roles'));
    }

    /**
     * ✅ KEEP - Store a newly created user (admin-only)
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
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'position' => 'nullable|string|max:255',
            'website' => 'nullable|url|max:255',
            'bio' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();

        try {
            // Use UserService for user creation
            $user = $this->userService->createUser($validated, $request->file('avatar'));

            DB::commit();

            return redirect()->route('admin.users.index')
                ->with('success', 'User created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Admin user creation failed', [
                'admin_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Error creating user: ' . $e->getMessage());
        }
    }

    /**
     * 🚫 REMOVED - Delegated to UnifiedProfileController
     * Use route: profile.show (for users to view their own profile)
     */
    // public function show(User $user) { ... }

    /**
     * 🚫 REMOVED - Delegated to UnifiedProfileController  
     * Use route: profile.edit (for users to edit their own profile)
     */
    // public function edit(User $user) { ... }

    /**
     * 🚫 REMOVED - Delegated to UnifiedProfileController
     * Use route: profile.update (for users to update their own profile)
     */
    // public function update(Request $request, User $user) { ... }

    /**
     * ✅ ENHANCED - Remove user (admin-only deletion with enhanced checks)
     */
    public function destroy(User $user)
    {
        // Enhanced security checks
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'You cannot delete your own account!');
        }

        if ($user->hasRole('super-admin')) {
            $superAdminCount = Role::where('name', 'super-admin')->first()->users()->count();
            if ($superAdminCount <= 1) {
                return redirect()->route('admin.users.index')
                    ->with('error', 'Cannot delete the last super-admin user!');
            }
        }

        try {
            $this->userService->deleteUser($user);

            return redirect()->route('admin.users.index')
                ->with('success', 'User deleted successfully!');

        } catch (\Exception $e) {
            Log::error('Admin user deletion failed', [
                'admin_id' => auth()->id(),
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->route('admin.users.index')
                ->with('error', 'Error deleting user: ' . $e->getMessage());
        }
    }

    /**
     * 🚫 REMOVED - Delegated to UnifiedProfileController
     * Use route: profile.change-password (for users to change their own password)
     */
    // public function showChangePasswordForm(User $user) { ... }

    /**
     * 🚫 REMOVED - Delegated to UnifiedProfileController  
     * Use route: profile.password.update (for users to update their own password)
     */
    // public function changePassword(Request $request, User $user) { ... }

    /**
     * ✅ KEEP - Toggle user active status (admin-only)
     */
    public function toggleActive(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'You cannot deactivate your own account!');
        }

        try {
            $this->userService->toggleActive($user);

            return redirect()->back()
                ->with('success', 'User status updated!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to update user status.');
        }
    }

    /**
     * ✅ KEEP - Verify a client account (admin-only)
     */
    public function verifyClient(User $user)
    {
        if (!$user->hasRole('client')) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Only client accounts can be verified!');
        }

        try {
            $this->userService->verifyEmail($user);

            return redirect()->back()
                ->with('success', 'Client account verified successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to verify client account.');
        }
    }

    // 🚫 ROLE MANAGEMENT REMOVED - Use RoleController instead
    // Role-specific operations should be handled in:
    // - app/Http/Controllers/Admin/RoleController.php
    // - Routes: admin/roles/* for role management
    // - Users can be assigned roles through RoleController

    /**
     * ✅ NEW - Bulk actions on multiple users
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:activate,deactivate,verify,delete',
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        $userIds = $request->user_ids;
        $currentUserId = auth()->id();

        // Remove current user from bulk actions
        $userIds = array_filter($userIds, fn($id) => $id != $currentUserId);

        if (empty($userIds)) {
            return redirect()->back()
                ->with('error', 'No valid users selected for bulk action.');
        }

        try {
            switch ($request->action) {
                case 'activate':
                    $affected = $this->userService->bulkToggleActive($userIds, true);
                    break;
                case 'deactivate':
                    $affected = $this->userService->bulkToggleActive($userIds, false);
                    break;
                case 'verify':
                    $affected = $this->userService->bulkSendVerificationEmail($userIds);
                    break;
                case 'delete':
                    $affected = $this->bulkDeleteUsers($userIds);
                    break;
                default:
                    throw new \Exception('Invalid bulk action');
            }

            return redirect()->back()
                ->with('success', "Bulk action completed. {$affected} users processed.");

        } catch (\Exception $e) {
            Log::error('Bulk action failed', [
                'admin_id' => $currentUserId,
                'action' => $request->action,
                'user_ids' => $userIds,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('error', 'Bulk action failed: ' . $e->getMessage());
        }
    }

    /**
     * ✅ NEW - Send welcome email to user (admin action)
     */
    public function sendWelcomeEmail(User $user)
    {
        try {
            $this->userService->sendWelcomeEmail($user);

            return redirect()->back()
                ->with('success', 'Welcome email sent successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to send welcome email.');
        }
    }

    /**
     * ✅ NEW - Reset user password (admin action - no current password required)
     */
    public function resetPassword(Request $request, User $user)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
            'notify_user' => 'boolean',
        ]);

        if ($user->id === auth()->id()) {
            return redirect()->back()
                ->with('error', 'Use the regular password change for your own account.');
        }

        try {
            $this->userService->changePassword($user, $request->password);

            // Optionally notify user
            if ($request->notify_user) {
                Notifications::send('user.password_changed_by_admin', [
                    'user' => $user,
                    'admin' => auth()->user(),
                    'timestamp' => now()
                ], $user);
            }

            Log::info('Admin password reset', [
                'admin_id' => auth()->id(),
                'user_id' => $user->id,
                'notified' => $request->notify_user
            ]);

            return redirect()->back()
                ->with('success', 'Password reset successfully!' .
                    ($request->notify_user ? ' User has been notified.' : ''));

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to reset password.');
        }
    }

    /**
     * ✅ NEW - Impersonate user (admin feature)
     */
    public function impersonate(User $user)
    {
        if (!auth()->user()->hasRole('super-admin')) {
            abort(403, 'Only super-admins can impersonate users.');
        }

        if ($user->hasRole('super-admin')) {
            return redirect()->back()
                ->with('error', 'Cannot impersonate another super-admin.');
        }

        try {
            // Store original user ID in session
            session(['impersonator_id' => auth()->id()]);

            // Login as the target user
            auth()->login($user);

            Log::info('User impersonation started', [
                'admin_id' => session('impersonator_id'),
                'target_user_id' => $user->id
            ]);

            return redirect()->route('dashboard')
                ->with('info', "You are now impersonating {$user->name}. Click here to stop impersonation.");

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to impersonate user.');
        }
    }

    /**
     * ✅ NEW - Stop impersonation
     */
    public function stopImpersonation()
    {
        $impersonatorId = session('impersonator_id');

        if (!$impersonatorId) {
            return redirect()->route('dashboard');
        }

        $impersonator = User::find($impersonatorId);

        if (!$impersonator) {
            session()->forget('impersonator_id');
            return redirect()->route('login');
        }

        Log::info('User impersonation stopped', [
            'admin_id' => $impersonatorId,
            'target_user_id' => auth()->id()
        ]);

        session()->forget('impersonator_id');
        auth()->login($impersonator);

        return redirect()->route('admin.users.index')
            ->with('success', 'Impersonation stopped successfully.');
    }

    /**
     * ✅ NEW - Export users data (admin bulk export)
     */
    public function exportUsers(Request $request)
    {
        $request->validate([
            'format' => 'required|in:csv,xlsx,json',
            'user_ids' => 'nullable|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        try {
            $users = $request->user_ids
                ? User::whereIn('id', $request->user_ids)->get()
                : User::all();

            // Generate export data based on format
            $exportData = $this->generateExportData($users, $request->format);

            $filename = 'users_export_' . now()->format('Y-m-d_H-i-s') . '.' . $request->format;

            return response()->streamDownload(
                function () use ($exportData) {
                    echo $exportData;
                },
                $filename,
                ['Content-Type' => $this->getContentType($request->format)]
            );

        } catch (\Exception $e) {
            Log::error('User export failed', [
                'admin_id' => auth()->id(),
                'format' => $request->format,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('error', 'Export failed: ' . $e->getMessage());
        }
    }

    /**
     * ✅ NEW - User statistics for admin dashboard
     */
    public function getUserStatistics()
    {
        try {
            $stats = $this->userService->getStatistics();

            return response()->json($stats);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to load statistics'], 500);
        }
    }

    /**
     * ✅ NEW - Search users (AJAX endpoint)
     */
    public function searchUsers(Request $request)
    {
        $request->validate([
            'query' => 'required|string|min:2',
            'limit' => 'nullable|integer|min:1|max:50',
        ]);

        try {
            $users = User::where('name', 'like', '%' . $request->query . '%')
                ->orWhere('email', 'like', '%' . $request->query . '%')
                ->orWhere('company', 'like', '%' . $request->query . '%')
                ->with('roles')
                ->limit($request->limit ?? 10)
                ->get(['id', 'name', 'email', 'company', 'avatar']);

            return response()->json([
                'users' => $users->map(function ($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'company' => $user->company,
                        'avatar_url' => $user->avatar_url,
                        'roles' => $user->roles->pluck('name'),
                        'url' => route('admin.users.show', $user),
                    ];
                })
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Search failed'], 500);
        }
    }

    private function bulkDeleteUsers(array $userIds): int
    {
        $deleted = 0;
        foreach ($userIds as $userId) {
            $user = User::find($userId);
            if ($user && !$user->hasRole('super-admin')) {
                try {
                    $this->userService->deleteUser($user);
                    $deleted++;
                } catch (\Exception $e) {
                    Log::warning("Failed to delete user {$userId}: " . $e->getMessage());
                }
            }
        }
        return $deleted;
    }

    private function bulkAssignRole(array $userIds, int $roleId): int
    {
        $role = Role::find($roleId);
        if (!$role) {
            throw new \Exception('Role not found');
        }

        $assigned = 0;
        foreach ($userIds as $userId) {
            $user = User::find($userId);
            if ($user) {
                try {
                    $user->assignRole($role);
                    $assigned++;
                } catch (\Exception $e) {
                    Log::warning("Failed to assign role to user {$userId}: " . $e->getMessage());
                }
            }
        }
        return $assigned;
    }

    private function generateExportData($users, string $format)
    {
        switch ($format) {
            case 'json':
                return $users->toJson(JSON_PRETTY_PRINT);
            case 'csv':
                $csv = fopen('php://temp', 'r+');
                fputcsv($csv, ['ID', 'Name', 'Email', 'Company']);
                foreach ($users as $user) {
                    fputcsv($csv, [$user->id, $user->name, $user->email, $user->company]);
                }
                rewind($csv);
                return stream_get_contents($csv);
            case 'xlsx':
                // Placeholder; for production use Laravel Excel package
                return json_encode(['error' => 'XLSX export requires Laravel Excel package']);
            default:
                throw new \Exception('Unsupported format');
        }
    }

    private function getContentType(string $format): string
    {
        return match ($format) {
            'json' => 'application/json',
            'csv' => 'text/csv',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            default => 'application/octet-stream',
        };
    }
}
