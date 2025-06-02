<?php
// File: app/Observers/UserObserver.php

namespace App\Observers;

use App\Models\User;
use App\Traits\SendsNotifications;
use App\Facades\Notifications;
use Illuminate\Support\Facades\Log;

class UserObserver
{
    use SendsNotifications;

    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        try {
            Log::info('User created, sending welcome notification', [
                'user_id' => $user->id,
                'email' => $user->email,
                'name' => $user->name
            ]);

            // Send welcome notification to new user
            $this->sendIfEnabled('user.welcome', $user, $user);
            
            // Notify admins about new user registration
            $this->notifyAdmins('user.registered', $user);

            // Check if profile is incomplete and schedule reminder
            if ($this->hasIncompleteProfile($user)) {
                $this->scheduleProfileCompletionReminder($user);
            }

        } catch (\Exception $e) {
            Log::error('Failed to send user created notifications', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        try {
            // Check if email was verified
            if ($user->isDirty('email_verified_at') && $user->email_verified_at) {
                $this->handleEmailVerification($user);
            }

            // Check if password was changed
            if ($user->isDirty('password')) {
                $this->handlePasswordChange($user);
            }

            // Check if user was activated/deactivated
            if ($user->isDirty('is_active')) {
                $this->handleActiveStatusChange($user);
            }

            // Check if profile was completed
            if ($this->wasProfileCompleted($user)) {
                $this->handleProfileCompletion($user);
            }

            // Check if role changed
            if ($this->didRoleChange($user)) {
                $this->handleRoleChange($user);
            }

            // Check if important profile info changed
            if ($this->hasImportantProfileChanges($user)) {
                $this->handleProfileUpdate($user);
            }

        } catch (\Exception $e) {
            Log::error('Failed to send user update notifications', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle email verification
     */
    protected function handleEmailVerification(User $user): void
    {
        // Send email verified notification
        $this->sendNotification('user.email_verified', $user, $user);

        // Notify admins
        $this->notifyAdmins('user.email_verified', $user);

        Log::info('Email verification notifications sent', [
            'user_id' => $user->id
        ]);
    }

    /**
     * Handle password changes
     */
    protected function handlePasswordChange(User $user): void
    {
        // Send password changed notification for security
        $this->sendNotification('user.password_changed', $user, $user);

        Log::info('Password change notification sent', [
            'user_id' => $user->id
        ]);
    }

    /**
     * Handle active status changes
     */
    protected function handleActiveStatusChange(User $user): void
    {
        if ($user->is_active) {
            // User activated
            $this->sendNotification('user.activated', $user, $user);
            $this->notifyAdmins('user.activated', $user);
        } else {
            // User deactivated
            $this->sendNotification('user.deactivated', $user, $user);
            $this->notifyAdmins('user.deactivated', $user);
        }

        Log::info('User status change notifications sent', [
            'user_id' => $user->id,
            'is_active' => $user->is_active
        ]);
    }

    /**
     * Handle profile completion
     */
    protected function handleProfileCompletion(User $user): void
    {
        // Send profile completed notification
        $this->sendNotification('user.profile_completed', $user, $user);

        Log::info('Profile completion notification sent', [
            'user_id' => $user->id
        ]);
    }

    /**
     * Handle role changes
     */
    protected function handleRoleChange(User $user): void
    {
        // Notify user about role change
        $this->sendNotification('user.role_changed', $user, $user);

        // Notify admins
        $this->notifyAdmins('user.role_changed', $user);

        Log::info('Role change notifications sent', [
            'user_id' => $user->id
        ]);
    }

    /**
     * Handle profile updates
     */
    protected function handleProfileUpdate(User $user): void
    {
        // Send profile update confirmation
        $this->sendNotification('user.profile_updated', $user, $user);

        Log::info('Profile update notification sent', [
            'user_id' => $user->id
        ]);
    }

    /**
     * Check if profile has incomplete fields
     */
    protected function hasIncompleteProfile(User $user): bool
    {
        $requiredFields = ['phone', 'address'];
        
        foreach ($requiredFields as $field) {
            if (empty($user->getAttribute($field))) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Check if profile was just completed
     */
    protected function wasProfileCompleted(User $user): bool
    {
        $requiredFields = ['phone', 'address'];
        
        // Check if all required fields are now filled
        $allFieldsFilled = true;
        foreach ($requiredFields as $field) {
            if (empty($user->getAttribute($field))) {
                $allFieldsFilled = false;
                break;
            }
        }
        
        if (!$allFieldsFilled) {
            return false;
        }
        
        // Check if any of these fields were just updated
        foreach ($requiredFields as $field) {
            if ($user->isDirty($field) && !empty($user->getAttribute($field))) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Check if user role changed
     */
    protected function didRoleChange(User $user): bool
    {
        // This would need to be implemented based on your role system
        // If using Spatie Permission or similar
        try {
            if (method_exists($user, 'getRoleNames')) {
                $currentRoles = $user->getRoleNames()->toArray();
                $originalRoles = $user->getOriginal('roles') ?? [];
                
                return $currentRoles !== $originalRoles;
            }
        } catch (\Exception $e) {
            Log::warning('Could not check role changes', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
        }
        
        return false;
    }

    /**
     * Check if important profile info changed
     */
    protected function hasImportantProfileChanges(User $user): bool
    {
        $importantFields = ['name', 'email', 'phone', 'company', 'address'];
        
        foreach ($importantFields as $field) {
            if ($user->isDirty($field)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Schedule profile completion reminder
     */
    protected function scheduleProfileCompletionReminder(User $user): void
    {
        // Schedule reminder after 3 days if profile still incomplete
        $reminderDate = now()->addDays(3);
        
        Log::info('Profile completion reminder scheduled', [
            'user_id' => $user->id,
            'reminder_date' => $reminderDate->toDateString()
        ]);
        
        // Here you would integrate with job scheduling
        // You could create a job to check and send the reminder
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        try {
            // Notify admins about user deletion (if soft delete)
            if (method_exists($user, 'trashed') && !$user->trashed()) {
                $this->notifyAdmins('user.deleted', $user);
            }

            Log::info('User deletion notification sent', [
                'user_id' => $user->id
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send user deletion notification', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        try {
            // Notify about user restoration
            $this->sendNotification('user.restored', $user, $user);
            $this->notifyAdmins('user.restored', $user);

            Log::info('User restoration notifications sent', [
                'user_id' => $user->id
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send user restoration notification', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Check for users with incomplete profiles (called by scheduled job)
     */
    public static function checkIncompleteProfiles(): void
    {
        $usersWithIncompleteProfiles = User::where('created_at', '<', now()->subDays(7))
            ->where(function ($query) {
                $query->whereNull('phone')
                      ->orWhereNull('address')
                      ->orWhere('phone', '')
                      ->orWhere('address', '');
            })
            ->get();

        foreach ($usersWithIncompleteProfiles as $user) {
            Notifications::send('user.profile_incomplete', $user, $user);
        }

        Log::info('Incomplete profiles check completed', [
            'incomplete_count' => $usersWithIncompleteProfiles->count()
        ]);
    }

    /**
     * Send birthday notifications (if you have birthday field)
     */
    public static function sendBirthdayNotifications(): void
    {
        if (!User::getConnection()->getSchemaBuilder()->hasColumn('users', 'birthday')) {
            return; // Skip if no birthday field
        }

        $birthdayUsers = User::whereRaw('DATE_FORMAT(birthday, "%m-%d") = ?', [
            now()->format('m-d')
        ])->get();

        foreach ($birthdayUsers as $user) {
            Notifications::send('user.birthday', $user, $user);
        }

        Log::info('Birthday notifications sent', [
            'birthday_count' => $birthdayUsers->count()
        ]);
    }
}