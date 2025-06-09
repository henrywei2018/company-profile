<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class UserProfileService
{
    /**
     * Get profile completion status for a user
     */
    public function getProfileCompletionStatus(User $user): array
    {
        // Define essential fields based on your migration
        $essentialFields = [
            'name' => !empty($user->name),
            'email' => !empty($user->email),
            'phone' => !empty($user->phone),
            'company' => !empty($user->company),
            'address' => !empty($user->address),
            'city' => !empty($user->city),
            'state' => !empty($user->state),
            'country' => !empty($user->country),
            'avatar' => !empty($user->avatar),
        ];

        // Additional fields for enhanced profile
        $enhancedFields = [
            'postal_code' => !empty($user->postal_code),
            'bio' => !empty($user->bio),
            'website' => !empty($user->website),
            'position' => !empty($user->position),
        ];

        $allFields = array_merge($essentialFields, $enhancedFields);
        
        $completedEssential = count(array_filter($essentialFields));
        $totalEssential = count($essentialFields);
        $completedAll = count(array_filter($allFields));
        $totalAll = count($allFields);

        return [
            'essential_percentage' => round(($completedEssential / $totalEssential) * 100),
            'overall_percentage' => round(($completedAll / $totalAll) * 100),
            'completed_essential' => $completedEssential,
            'total_essential' => $totalEssential,
            'completed_all' => $completedAll,
            'total_all' => $totalAll,
            'missing_essential' => array_keys(array_filter($essentialFields, fn($v) => !$v)),
            'missing_enhanced' => array_keys(array_filter($enhancedFields, fn($v) => !$v)),
            'is_essential_complete' => $completedEssential === $totalEssential,
            'fields_status' => $allFields,
        ];
    }

    /**
     * Update user profile information
     */
    public function updateProfile(User $user, array $data, ?UploadedFile $avatar = null): User
    {
        DB::beginTransaction();

        try {
            // Handle avatar upload
            if ($avatar) {
                $this->handleAvatarUpload($user, $avatar);
            }

            // Update user data
            $user->update($this->sanitizeProfileData($data));

            DB::commit();
            
            Log::info('User profile updated', [
                'user_id' => $user->id,
                'updated_fields' => array_keys($data)
            ]);

            return $user->fresh();

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update user profile', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Update notification preferences
     */
    public function updateNotificationPreferences(User $user, array $preferences): User
    {
        $allowedPreferences = [
            'email_notifications',
            'project_update_notifications',
            'quotation_update_notifications',
            'message_reply_notifications',
            'deadline_alert_notifications',
            'chat_notifications',
            'system_notifications',
            'marketing_notifications',
            'testimonial_notifications',
            'urgent_notifications',
            'user_registration_notifications',
            'security_alert_notifications',
            'notification_frequency',
            'quiet_hours',
        ];

        $filteredPreferences = array_intersect_key($preferences, array_flip($allowedPreferences));
        
        // Convert boolean strings to actual booleans
        foreach ($filteredPreferences as $key => $value) {
            if (in_array($key, ['notification_frequency', 'quiet_hours'])) {
                continue; // Keep these as-is
            }
            $filteredPreferences[$key] = filter_var($value, FILTER_VALIDATE_BOOLEAN);
        }

        $user->update($filteredPreferences);

        Log::info('User notification preferences updated', [
            'user_id' => $user->id,
            'preferences' => array_keys($filteredPreferences)
        ]);

        return $user->fresh();
    }

    /**
     * Handle avatar upload and cleanup
     */
    protected function handleAvatarUpload(User $user, UploadedFile $avatar): string
    {
        // Delete old avatar if exists
        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        // Store new avatar
        $path = $avatar->store('avatars', 'public');
        $user->avatar = $path;

        return $path;
    }

    /**
     * Sanitize and validate profile data
     */
    protected function sanitizeProfileData(array $data): array
    {
        $allowedFields = [
            'name', 'email', 'phone', 'company', 'address', 'city', 
            'state', 'postal_code', 'country', 'bio', 'website', 
            'position', 'is_active', 'allow_testimonials', 
            'allow_public_profile'
        ];

        $sanitized = array_intersect_key($data, array_flip($allowedFields));

        // Clean and validate specific fields
        if (isset($sanitized['email'])) {
            $sanitized['email'] = strtolower(trim($sanitized['email']));
        }

        if (isset($sanitized['phone'])) {
            $sanitized['phone'] = preg_replace('/[^\d\+\-\(\)\s]/', '', $sanitized['phone']);
        }

        if (isset($sanitized['website'])) {
            $sanitized['website'] = filter_var($sanitized['website'], FILTER_VALIDATE_URL) 
                ? $sanitized['website'] : null;
        }

        // Convert boolean values
        foreach (['is_active', 'allow_testimonials', 'allow_public_profile'] as $boolField) {
            if (isset($sanitized[$boolField])) {
                $sanitized[$boolField] = filter_var($sanitized[$boolField], FILTER_VALIDATE_BOOLEAN);
            }
        }

        return $sanitized;
    }

    /**
     * Get user activity summary
     */
    public function getUserActivitySummary(User $user): array
    {
        return [
            'profile_completion' => $this->getProfileCompletionStatus($user),
            'account_stats' => [
                'created_at' => $user->created_at,
                'last_login_at' => $user->last_login_at,
                'login_count' => $user->login_count ?? 0,
                'email_verified' => !is_null($user->email_verified_at),
                'email_verified_at' => $user->email_verified_at,
                'is_active' => $user->is_active,
                'roles_count' => $user->roles()->count(),
                'permissions_count' => $user->getAllPermissions()->count(),
            ],
            'content_stats' => [
                'projects_count' => $user->projects()->count(),
                'quotations_count' => $user->quotations()->count(),
                'messages_count' => $user->messages()->count(),
                'posts_count' => $user->posts()->count(),
            ],
            'notification_preferences' => $user->getNotificationPreferences(),
        ];
    }

    /**
     * Get profile suggestions based on completion status
     */
    public function getProfileSuggestions(User $user): array
    {
        $completion = $this->getProfileCompletionStatus($user);
        $suggestions = [];

        if (in_array('avatar', $completion['missing_essential'])) {
            $suggestions[] = [
                'type' => 'avatar',
                'title' => 'Add Profile Picture',
                'description' => 'Upload a professional photo to personalize your account',
                'priority' => 'high',
                'action_url' => route('profile.edit'),
            ];
        }

        if (in_array('company', $completion['missing_essential'])) {
            $suggestions[] = [
                'type' => 'company',
                'title' => 'Add Company Information',
                'description' => 'Help us better serve you by adding your company details',
                'priority' => 'medium',
                'action_url' => route('profile.edit'),
            ];
        }

        if (in_array('phone', $completion['missing_essential'])) {
            $suggestions[] = [
                'type' => 'contact',
                'title' => 'Add Contact Number',
                'description' => 'Provide a phone number for important communications',
                'priority' => 'medium',
                'action_url' => route('profile.edit'),
            ];
        }

        if (in_array('address', $completion['missing_essential'])) {
            $suggestions[] = [
                'type' => 'address',
                'title' => 'Complete Address Information',
                'description' => 'Add your location for better service delivery',
                'priority' => 'low',
                'action_url' => route('profile.edit'),
            ];
        }

        // Check notification preferences
        if ($user->email_notifications === null) {
            $suggestions[] = [
                'type' => 'notifications',
                'title' => 'Configure Notifications',
                'description' => 'Set up your notification preferences to stay informed',
                'priority' => 'medium',
                'action_url' => route('profile.preferences'),
            ];
        }

        return $suggestions;
    }

    /**
     * Export user profile data (GDPR compliance)
     */
    public function exportUserData(User $user): array
    {
        return [
            'profile' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'company' => $user->company,
                'position' => $user->position,
                'website' => $user->website,
                'address' => $user->address,
                'city' => $user->city,
                'state' => $user->state,
                'postal_code' => $user->postal_code,
                'country' => $user->country,
                'bio' => $user->bio,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
                'email_verified_at' => $user->email_verified_at,
                'last_login_at' => $user->last_login_at,
                'login_count' => $user->login_count,
            ],
            'roles' => $user->roles()->pluck('name')->toArray(),
            'permissions' => $user->getAllPermissions()->pluck('name')->toArray(),
            'notification_preferences' => $user->getNotificationPreferences(),
            'settings' => $user->settings,
            'export_date' => now()->toISOString(),
        ];
    }

    /**
     * Validate profile completion for role requirements
     */
    public function validateProfileForRole(User $user, string $roleName): array
    {
        $completion = $this->getProfileCompletionStatus($user);
        $requirements = $this->getRoleRequirements($roleName);
        
        $missing = [];
        foreach ($requirements as $field) {
            if (!($completion['fields_status'][$field] ?? false)) {
                $missing[] = $field;
            }
        }

        return [
            'is_valid' => empty($missing),
            'missing_fields' => $missing,
            'completion_percentage' => $completion['essential_percentage'],
            'requirements' => $requirements,
        ];
    }

    /**
     * Get profile requirements for specific roles
     */
    protected function getRoleRequirements(string $roleName): array
    {
        $requirements = [
            'client' => ['name', 'email', 'phone', 'company'],
            'admin' => ['name', 'email', 'phone'],
            'manager' => ['name', 'email', 'phone', 'company'],
            'editor' => ['name', 'email'],
            'super-admin' => ['name', 'email', 'phone'],
        ];

        return $requirements[$roleName] ?? ['name', 'email'];
    }

    /**
     * Check if user should receive profile completion reminder
     */
    public function shouldSendCompletionReminder(User $user): bool
    {
        $completion = $this->getProfileCompletionStatus($user);
        
        // Don't send if profile is complete
        if ($completion['is_essential_complete']) {
            return false;
        }

        // Don't send if reminder was sent recently
        if ($user->profile_reminder_sent_at && 
            $user->profile_reminder_sent_at->diffInDays(now()) < 7) {
            return false;
        }

        // Don't send if account is too new (less than 3 days)
        if ($user->created_at->diffInDays(now()) < 3) {
            return false;
        }

        return true;
    }

    /**
     * Mark profile completion reminder as sent
     */
    public function markCompletionReminderSent(User $user): void
    {
        $user->update(['profile_reminder_sent_at' => now()]);
    }
}