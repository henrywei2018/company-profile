<?php

namespace App\Services;

use App\Models\User;
use Spatie\Permission\Models\Role;
use App\Facades\Notifications;
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
                  ->orWhere('email', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('company', 'like', '%' . $filters['search'] . '%');
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
                'city' => $data['city'] ?? null,
                'state' => $data['state'] ?? null,
                'postal_code' => $data['postal_code'] ?? null,
                'country' => $data['country'] ?? null,
            ];

            $user = User::create($userData);

            // Assign roles
            if (!empty($data['roles'])) {
                $roles = Role::whereIn('id', $data['roles'])->get();
                $user->syncRoles($roles);
            } else {
                // Assign default role if none specified
                $user->assignRole('client');
            }

            // Handle avatar upload
            if ($avatar) {
                $path = $avatar->store('avatars', 'public');
                $user->update(['avatar' => $path]);
            }

            // Send welcome notification
            Notifications::send('user.created', $user);

            // Send welcome email if user is active
            if ($user->is_active) {
                Notifications::send('user.welcome', $user, $user);
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
            $oldData = [
                'is_active' => $user->is_active,
                'roles' => $user->getRoleNames()->toArray(),
            ];

            $userData = [
                'name' => $data['name'],
                'email' => $data['email'],
                'is_active' => $data['is_active'] ?? $user->is_active,
                'phone' => $data['phone'] ?? null,
                'address' => $data['address'] ?? null,
                'company' => $data['company'] ?? null,
                'city' => $data['city'] ?? null,
                'state' => $data['state'] ?? null,
                'postal_code' => $data['postal_code'] ?? null,
                'country' => $data['country'] ?? null,
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

            // Send notifications for significant changes
            $this->sendUpdateNotifications($user, $oldData, $data);

            DB::commit();
            return $user;

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function deleteUser(User $user): bool
    {
        // Check if user can be deleted
        if ($user->hasRole('super-admin')) {
            throw new \Exception('Cannot delete super admin user');
        }

        // Check for related data
        if ($user->projects()->count() > 0 || $user->quotations()->count() > 0) {
            throw new \Exception('Cannot delete user with existing projects or quotations');
        }

        // Send notification before deletion
        Notifications::send('user.deleted', $user);

        // Clean up user data
        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        $user->syncRoles([]);
        return $user->delete();
    }

    public function toggleActive(User $user): User
    {
        $wasActive = $user->is_active;
        $user->update(['is_active' => !$user->is_active]);

        // Send notification for status change
        $notificationType = $user->is_active ? 'user.activated' : 'user.deactivated';
        Notifications::send($notificationType, $user);

        return $user;
    }

    public function changePassword(User $user, string $password): User
    {
        $user->update(['password' => Hash::make($password)]);

        // Send password change notification
        Notifications::send('user.password_changed', $user, $user);

        return $user;
    }

    public function verifyEmail(User $user): User
    {
        if (!$user->email_verified_at) {
            $user->update(['email_verified_at' => now()]);

            // Send email verification confirmation
            Notifications::send('user.email_verified', $user, $user);
        }
        return $user;
    }

    public function sendEmailVerification(User $user): bool
    {
        if ($user->email_verified_at) {
            return false; // Already verified
        }

        try {
            Notifications::send('user.verify_email', $user, $user);
            return true;
        } catch (\Exception $e) {
            \Log::error('Failed to send email verification', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    public function updateNotificationPreferences(User $user, array $preferences): User
    {
        $user->update([
            'email_notifications' => $preferences['email_notifications'] ?? true,
            'project_update_notifications' => $preferences['project_updates'] ?? true,
            'quotation_update_notifications' => $preferences['quotation_updates'] ?? true,
            'message_reply_notifications' => $preferences['message_replies'] ?? true,
            'deadline_alert_notifications' => $preferences['deadline_alerts'] ?? true,
            'system_notifications' => $preferences['system_notifications'] ?? false,
            'marketing_emails' => $preferences['marketing_emails'] ?? false,
            'notification_frequency' => $preferences['frequency'] ?? 'immediate',
            'quiet_hours' => $preferences['quiet_hours'] ?? null,
        ]);

        // Send confirmation of preference update
        Notifications::send('user.preferences_updated', $user, $user);

        return $user;
    }

    public function sendWelcomeEmail(User $user): bool
    {
        try {
            Notifications::send('user.welcome', $user, $user);
            return true;
        } catch (\Exception $e) {
            \Log::error('Failed to send welcome email', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    public function bulkToggleActive(array $userIds, bool $active): int
    {
        $users = User::whereIn('id', $userIds)
            ->where('id', '!=', auth()->id()) // Don't allow self-deactivation
            ->whereDoesntHave('roles', function($query) {
                $query->where('name', 'super-admin'); // Don't allow super-admin deactivation
            })
            ->get();

        $updated = 0;
        foreach ($users as $user) {
            $user->update(['is_active' => $active]);
            $updated++;
        }

        // Send bulk notification
        if ($updated > 0) {
            $notificationType = $active ? 'user.bulk_activated' : 'user.bulk_deactivated';
            Notifications::send($notificationType, [
                'count' => $updated,
                'status' => $active ? 'activated' : 'deactivated'
            ]);
        }

        return $updated;
    }

    public function bulkSendVerificationEmail(array $userIds): int
    {
        $users = User::whereIn('id', $userIds)
            ->whereNull('email_verified_at')
            ->get();

        $sent = 0;
        foreach ($users as $user) {
            if ($this->sendEmailVerification($user)) {
                $sent++;
            }
        }

        // Send bulk notification
        if ($sent > 0) {
            Notifications::send('user.bulk_verification_sent', [
                'count' => $sent
            ]);
        }

        return $sent;
    }

    public function getInactiveUsers(int $days = 30): \Illuminate\Database\Eloquent\Collection
    {
        return User::where('last_login_at', '<', now()->subDays($days))
            ->orWhereNull('last_login_at')
            ->where('is_active', true)
            ->get();
    }

    public function sendInactivityReminders(): int
    {
        $inactiveUsers = $this->getInactiveUsers(30);
        $sent = 0;

        foreach ($inactiveUsers as $user) {
            // Only send reminder if not sent in last 7 days
            if (!$user->profile_reminder_sent_at || 
                $user->profile_reminder_sent_at->diffInDays(now()) >= 7) {
                
                try {
                    Notifications::send('user.inactivity_reminder', $user, $user);
                    $user->update(['profile_reminder_sent_at' => now()]);
                    $sent++;
                } catch (\Exception $e) {
                    \Log::error('Failed to send inactivity reminder', [
                        'user_id' => $user->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }
        }

        return $sent;
    }

    public function getStatistics(): array
    {
        return [
            'total' => User::count(),
            'active' => User::where('is_active', true)->count(),
            'verified' => User::whereNotNull('email_verified_at')->count(),
            'unverified' => User::whereNull('email_verified_at')->count(),
            'recent_logins' => User::where('last_login_at', '>=', now()->subDays(7))->count(),
            'inactive_users' => $this->getInactiveUsers(30)->count(),
            'by_role' => User::join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                ->selectRaw('roles.name, COUNT(*) as count')
                ->groupBy('roles.name')
                ->pluck('count', 'name')
                ->toArray(),
            'new_this_month' => User::where('created_at', '>=', now()->startOfMonth())->count(),
            'registration_trend' => $this->getRegistrationTrend(),
        ];
    }

    protected function sendUpdateNotifications(User $user, array $oldData, array $newData): void
    {
        // Check for significant changes that warrant notifications
        
        // Status change notification
        if (isset($newData['is_active']) && $oldData['is_active'] !== $newData['is_active']) {
            $notificationType = $newData['is_active'] ? 'user.activated' : 'user.deactivated';
            Notifications::send($notificationType, $user);
        }

        // Role change notification
        if (isset($newData['roles'])) {
            $newRoles = Role::whereIn('id', $newData['roles'])->pluck('name')->toArray();
            if ($oldData['roles'] !== $newRoles) {
                Notifications::send('user.roles_updated', $user);
            }
        }

        // Password change notification
        if (!empty($newData['password'])) {
            Notifications::send('user.password_changed', $user, $user);
        }

        // General profile update notification to admins
        Notifications::send('user.updated', $user);
    }

    protected function getRegistrationTrend(): array
    {
        $months = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $count = User::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            
            $months[] = [
                'month' => $date->format('M Y'),
                'count' => $count
            ];
        }

        return $months;
    }

    public function getUserActivity(User $user): array
    {
        return [
            'last_login' => $user->last_login_at,
            'login_count' => $user->login_count ?? 0,
            'projects_count' => $user->projects()->count(),
            'quotations_count' => $user->quotations()->count(),
            'messages_count' => $user->messages()->count(),
            'unread_notifications' => $user->unreadNotifications()->count(),
            'account_age_days' => $user->created_at->diffInDays(now()),
            'email_verified' => !is_null($user->email_verified_at),
            'profile_completeness' => $this->calculateProfileCompleteness($user),
        ];
    }

    protected function calculateProfileCompleteness(User $user): int
    {
        $fields = ['name', 'email', 'phone', 'company', 'address', 'city'];
        $completed = 0;

        foreach ($fields as $field) {
            if (!empty($user->$field)) {
                $completed++;
            }
        }

        return round(($completed / count($fields)) * 100);
    }
}