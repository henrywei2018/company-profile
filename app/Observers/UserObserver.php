<?php

namespace App\Observers;

use App\Models\User;
use App\Services\NotificationService;

class UserObserver
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        // Send welcome notification to new user
        $this->notificationService->send('user.welcome', $user);
        
        // Check if profile is incomplete and schedule reminder
        if ($this->hasIncompleteProfile($user)) {
            // This will be handled by scheduled task later
            // but we can mark it for tracking
        }
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        // Check if email was verified
        if ($user->isDirty('email_verified_at') && $user->email_verified_at) {
            $this->notificationService->send('user.email_verified', $user);
        }

        // Check if password was changed
        if ($user->isDirty('password')) {
            $this->notificationService->send('user.password_changed', $user);
        }

        // Check if user was activated/deactivated
        if ($user->isDirty('is_active')) {
            $this->handleActiveStatusChange($user);
        }

        // Check if profile was completed
        if ($this->wasProfileCompleted($user)) {
            $this->notificationService->send('user.profile_completed', $user);
        }

        // Check if user role changed
        if ($this->didRoleChange($user)) {
            $this->handleRoleChange($user);
        }
    }

    /**
     * Handle active status changes
     */
    protected function handleActiveStatusChange(User $user): void
    {
        if ($user->is_active) {
            $this->notificationService->send('user.activated', $user);
        } else {
            $this->notificationService->send('user.deactivated', $user);
        }
    }

    /**
     * Check if profile has incomplete fields
     */
    protected function hasIncompleteProfile(User $user): bool
    {
        return empty($user->phone) || 
               empty($user->address) || 
               empty($user->company);
    }

    /**
     * Check if profile was just completed
     */
    protected function wasProfileCompleted(User $user): bool
    {
        $requiredFields = ['phone', 'address', 'company'];
        
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
        // This is complex because roles are in pivot table
        // We'll handle this in a separate service if needed
        return false;
    }

    /**
     * Handle role changes
     */
    protected function handleRoleChange(User $user): void
    {
        // Notify about role change
        $this->notificationService->send('user.role_changed', $user);
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        // Notify admins about user deletion (if soft delete)
        if (method_exists($user, 'trashed') && !$user->trashed()) {
            $this->notificationService->send('user.deleted', $user);
        }
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        // Notify about user restoration
        $this->notificationService->send('user.restored', $user);
    }
}