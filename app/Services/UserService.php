<?php

namespace App\Services;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class UserService
{
    public function getFilteredUsers(array $filters = [], int $perPage = 15)
    {
        $query = User::with('roles');

        if (!empty($filters['search'])) {
            $query->where(function($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('email', 'like', '%' . $filters['search'] . '%');
            });
        }

        if (!empty($filters['role'])) {
            $query->whereHas('roles', function($q) use ($filters) {
                $q->where('name', $filters['role']);
            });
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        if (isset($filters['email_verified'])) {
            if ($filters['email_verified']) {
                $query->whereNotNull('email_verified_at');
            } else {
                $query->whereNull('email_verified_at');
            }
        }

        return $query->latest()->paginate($perPage);
    }

    public function createUser(array $data, ?UploadedFile $avatar = null): User
    {
        DB::beginTransaction();

        try {
            $userData = [
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'is_active' => $data['is_active'] ?? true,
                'phone' => $data['phone'] ?? null,
                'address' => $data['address'] ?? null,
                'company' => $data['company'] ?? null,
            ];

            $user = User::create($userData);

            // Assign roles
            if (!empty($data['roles'])) {
                $roles = Role::whereIn('id', $data['roles'])->get();
                $user->syncRoles($roles);
            }

            // Handle avatar upload
            if ($avatar) {
                $path = $avatar->store('avatars', 'public');
                $user->update(['avatar' => $path]);
            }

            DB::commit();
            return $user;

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function updateUser(User $user, array $data, ?UploadedFile $avatar = null): User
    {
        DB::beginTransaction();

        try {
            $userData = [
                'name' => $data['name'],
                'email' => $data['email'],
                'is_active' => $data['is_active'] ?? $user->is_active,
                'phone' => $data['phone'] ?? null,
                'address' => $data['address'] ?? null,
                'company' => $data['company'] ?? null,
            ];

            // Only update password if provided
            if (!empty($data['password'])) {
                $userData['password'] = Hash::make($data['password']);
            }

            $user->update($userData);

            // Sync roles
            if (isset($data['roles'])) {
                $roles = Role::whereIn('id', $data['roles'])->get();
                $user->syncRoles($roles);
            }

            // Handle avatar upload
            if ($avatar) {
                if ($user->avatar) {
                    Storage::disk('public')->delete($user->avatar);
                }

                $path = $avatar->store('avatars', 'public');
                $user->update(['avatar' => $path]);
            }

            DB::commit();
            return $user;

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function deleteUser(User $user): bool
    {
        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        $user->syncRoles([]);
        return $user->delete();
    }

    public function toggleActive(User $user): User
    {
        $user->update(['is_active' => !$user->is_active]);
        return $user;
    }

    public function changePassword(User $user, string $password): User
    {
        $user->update(['password' => Hash::make($password)]);
        return $user;
    }

    public function verifyEmail(User $user): User
    {
        if (!$user->email_verified_at) {
            $user->update(['email_verified_at' => now()]);
        }
        return $user;
    }

    public function getStatistics(): array
    {
        return [
            'total' => User::count(),
            'active' => User::where('is_active', true)->count(),
            'verified' => User::whereNotNull('email_verified_at')->count(),
            'by_role' => User::join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                ->selectRaw('roles.name, COUNT(*) as count')
                ->groupBy('roles.name')
                ->pluck('count', 'name')
                ->toArray(),
            'recent' => User::where('created_at', '>=', now()->subDays(30))->count(),
        ];
    }
}