<?php

namespace App\Services;

use App\Models\User;
use App\Facades\Notifications;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CentralizedProfileService
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Get profile completion status for any user
     */
    public function getProfileCompletionStatus(User $user): array
    {
        // Define essential fields based on existing migration structure
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

        // Additional optional fields
        $optionalFields = [
            'postal_code' => !empty($user->postal_code),
            'bio' => !empty($user->bio),
            'website' => !empty($user->website),
            'position' => !empty($user->position),
        ];

        $allFields = array_merge($essentialFields, $optionalFields);
        
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
            'missing_optional' => array_keys(array_filter($optionalFields, fn($v) => !$v)),
            'is_essential_complete' => $completedEssential === $totalEssential,
            'fields_status' => $allFields,
        ];
    }

    /**
     * Update user profile with context awareness (admin vs user)
     */
    public function updateProfile(User $user, array $data, ?UploadedFile $avatar = null, ?User $updatedBy = null): User
    {
        $updatedBy = $updatedBy ?? auth()->user();
        $isOwnProfile = $updatedBy->id === $user->id;
        $isAdminUpdate = $updatedBy->hasAdminAccess() && !$isOwnProfile;

        DB::beginTransaction();

        try {
            // Use existing UserService for the actual update
            $updatedUser = $this->userService->updateUser($user, $data, $avatar);

            // Send appropriate notifications based on context
            $this->sendProfileUpdateNotifications($updatedUser, $data, $isOwnProfile, $isAdminUpdate, $updatedBy);

            // Track profile completion improvement
            $this->trackProfileCompletionProgress($updatedUser);

            DB::commit();
            
            Log::info('Profile updated via centralized service', [
                'user_id' => $user->id,
                'updated_by' => $updatedBy->id,
                'is_own_profile' => $isOwnProfile,
                'is_admin_update' => $isAdminUpdate,
                'updated_fields' => array_keys($data)
            ]);

            return $updatedUser;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update profile via centralized service', [
                'user_id' => $user->id,
                'updated_by' => $updatedBy->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Update notification preferences with proper validation
     */
    public function updateNotificationPreferences(User $user, array $preferences, ?User $updatedBy = null): User
    {
        $updatedBy = $updatedBy ?? auth()->user();
        $isOwnProfile = $updatedBy->id === $user->id;

        // Use existing UserService method but with enhanced tracking
        $updatedUser = $this->userService->updateNotificationPreferences($user, $preferences);

        Log::info('Notification preferences updated', [
            'user_id' => $user->id,
            'updated_by' => $updatedBy->id,
            'is_own_profile' => $isOwnProfile,
            'preferences' => array_keys($preferences)
        ]);

        return $updatedUser;
    }

    /**
     * Get user activity summary with role-based information
     */
    public function getUserActivitySummary(User $user, ?User $viewedBy = null): array
    {
        $viewedBy = $viewedBy ?? auth()->user();
        $isOwnProfile = $viewedBy->id === $user->id;
        $canViewFullDetails = $isOwnProfile || $viewedBy->hasAdminAccess();

        $summary = [
            'profile_completion' => $this->getProfileCompletionStatus($user),
            'account_stats' => [
                'created_at' => $user->created_at,
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
        ];

        // Add sensitive information only for own profile or admin view
        if ($canViewFullDetails) {
            $summary['account_stats']['last_login_at'] = $user->last_login_at;
            $summary['account_stats']['login_count'] = $user->login_count ?? 0;
            $summary['notification_preferences'] = $user->getNotificationPreferences();
        }

        return $summary;
    }

    /**
     * Get profile suggestions based on completion status and user role
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
                'action_url' => $this->getProfileEditUrl($user),
            ];
        }

        if (in_array('company', $completion['missing_essential'])) {
            $suggestions[] = [
                'type' => 'company',
                'title' => 'Add Company Information',
                'description' => 'Help us better serve you by adding your company details',
                'priority' => 'medium',
                'action_url' => $this->getProfileEditUrl($user),
            ];
        }

        if (in_array('phone', $completion['missing_essential'])) {
            $suggestions[] = [
                'type' => 'contact',
                'title' => 'Add Contact Number',
                'description' => 'Provide a phone number for important communications',
                'priority' => 'medium',
                'action_url' => $this->getProfileEditUrl($user),
            ];
        }

        if (in_array('address', $completion['missing_essential'])) {
            $suggestions[] = [
                'type' => 'address',
                'title' => 'Complete Address Information',
                'description' => 'Add your location for better service delivery',
                'priority' => 'low',
                'action_url' => $this->getProfileEditUrl($user),
            ];
        }

        // Check notification preferences
        if ($user->email_notifications === null) {
            $suggestions[] = [
                'type' => 'notifications',
                'title' => 'Configure Notifications',
                'description' => 'Set up your notification preferences to stay informed',
                'priority' => 'medium',
                'action_url' => $this->getPreferencesUrl($user),
            ];
        }

        return $suggestions;
    }

    /**
     * Get appropriate redirect URL after profile update
     */
    public function getRedirectUrl(User $user, string $action = 'show', ?User $currentUser = null): string
    {
        $currentUser = $currentUser ?? auth()->user();
        $isOwnProfile = $currentUser->id === $user->id;
        $isAdmin = $currentUser->hasAdminAccess();

        // Determine the appropriate route based on context
        if ($isOwnProfile) {
            // User managing their own profile
            return match($action) {
                'edit' => route('profile.edit'),
                'preferences' => route('profile.preferences'),
                'completion' => route('profile.completion'),
                'password' => route('profile.change-password'),
                default => route('profile.show'),
            };
        } elseif ($isAdmin) {
            // Admin managing another user's profile
            return match($action) {
                'edit' => route('admin.users.edit', $user),
                'show' => route('admin.users.show', $user),
                'password' => route('admin.users.password.form', $user),
                'index' => route('admin.users.index'),
                default => route('admin.users.show', $user),
            };
        } else {
            // Fallback for unauthorized access
            return route('dashboard');
        }
    }

    /**
     * Check if profile completion reminder should be sent
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
     * Send profile completion reminder
     */
    public function sendCompletionReminder(User $user): bool
    {
        if (!$this->shouldSendCompletionReminder($user)) {
            return false;
        }

        try {
            Notifications::send('user.profile_incomplete', $user, $user);
            $user->update(['profile_reminder_sent_at' => now()]);
            
            Log::info('Profile completion reminder sent', [
                'user_id' => $user->id
            ]);
            
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send profile completion reminder', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Export user profile data with proper permissions
     */
    public function exportUserData(User $user, ?User $requestedBy = null): array
    {
        $requestedBy = $requestedBy ?? auth()->user();
        $isOwnProfile = $requestedBy->id === $user->id;
        $canExport = $isOwnProfile || $requestedBy->hasPermission('export user data');

        if (!$canExport) {
            throw new \Exception('Unauthorized to export user data');
        }

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
                'last_login_at' => $isOwnProfile ? $user->last_login_at : null,
                'login_count' => $isOwnProfile ? $user->login_count : null,
            ],
            'roles' => $user->roles()->pluck('name')->toArray(),
            'permissions' => $user->getAllPermissions()->pluck('name')->toArray(),
            'notification_preferences' => $isOwnProfile ? $user->getNotificationPreferences() : null,
            'settings' => $isOwnProfile ? $user->settings : null,
            'export_date' => now()->toISOString(),
            'exported_by' => $requestedBy->id,
        ];
    }

    /**
     * Track profile completion progress
     */
    protected function trackProfileCompletionProgress(User $user): void
    {
        $completion = $this->getProfileCompletionStatus($user);
        
        // If profile was just completed, send congratulations
        if ($completion['is_essential_complete'] && 
            $user->wasChanged() && 
            !$user->getOriginal('profile_reminder_sent_at')) {
            
            try {
                Notifications::send('user.profile_completed', $user, $user);
                Log::info('Profile completion notification sent', [
                    'user_id' => $user->id,
                    'completion_percentage' => $completion['essential_percentage']
                ]);
            } catch (\Exception $e) {
                Log::warning('Failed to send profile completion notification', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }

    /**
     * Get profile edit URL based on user context
     */
    protected function getProfileEditUrl(User $user): string
    {
        $currentUser = auth()->user();
        
        if ($currentUser->id === $user->id) {
            return route('profile.edit');
        } elseif ($currentUser->hasAdminAccess()) {
            return route('admin.users.edit', $user);
        }
        
        return route('profile.show');
    }

    /**
     * Get preferences URL based on user context
     */
    protected function getPreferencesUrl(User $user): string
    {
        $currentUser = auth()->user();
        
        if ($currentUser->id === $user->id) {
            return route('profile.preferences');
        }
        
        return route('profile.show');
    }
    public function sendTestNotification(User $user): bool
{
    try {
        Notifications::send('user.test_notification', [
            'message' => 'This is a test notification to verify your settings are working correctly.',
            'sent_at' => now(),
            'user_name' => $user->name,
        ], $user);
        
        Log::info('Test notification sent', [
            'user_id' => $user->id
        ]);
        
        return true;
    } catch (\Exception $e) {
        Log::error('Failed to send test notification', [
            'user_id' => $user->id,
            'error' => $e->getMessage()
        ]);
        return false;
    }
}

/**
 * Check and send profile completion reminders to eligible users
 */
public function sendProfileCompletionReminders(): int
{
    $usersNeedingReminders = User::where('is_active', true)
        ->whereNull('email_verified_at') // Only verified users
        ->where('created_at', '<=', now()->subDays(3)) // Account older than 3 days
        ->where(function($query) {
            // Users who haven't received reminder in last 7 days
            $query->whereNull('profile_reminder_sent_at')
                  ->orWhere('profile_reminder_sent_at', '<=', now()->subDays(7));
        })
        ->get();

    $sent = 0;
    
    foreach ($usersNeedingReminders as $user) {
        $completion = $this->getProfileCompletionStatus($user);
        
        // Only send if profile is incomplete
        if (!$completion['is_essential_complete']) {
            if ($this->sendCompletionReminder($user)) {
                $sent++;
            }
        }
    }

    if ($sent > 0) {
        Log::info('Profile completion reminders sent', [
            'total_sent' => $sent,
            'total_eligible' => $usersNeedingReminders->count()
        ]);
    }

    return $sent;
}

/**
 * Enhanced notification sending based on profile completion events
 */
protected function sendProfileUpdateNotifications(User $user, array $data, bool $isOwnProfile, bool $isAdminUpdate, User $updatedBy): void
{
    $completion = $this->getProfileCompletionStatus($user);
    
    // Send different notifications based on the update context
    if ($isOwnProfile) {
        // User updated their own profile
        if ($completion['essential_percentage'] >= 100 && !$user->getOriginal('profile_reminder_sent_at')) {
            // Profile just became complete
            Notifications::send('user.profile_completed', [
                'user' => $user,
                'completion_percentage' => $completion['essential_percentage'],
                'completed_fields' => $completion['completed_essential'],
                'total_fields' => $completion['total_essential']
            ], $user);
        } elseif ($completion['essential_percentage'] >= 75) {
            // Profile is mostly complete
            Notifications::send('user.profile_nearly_complete', [
                'user' => $user,
                'completion_percentage' => $completion['essential_percentage'],
                'missing_fields' => $completion['missing_essential']
            ], $user);
        } else {
            // Regular profile update
            Notifications::send('user.profile_updated', $user, $user);
        }
    } elseif ($isAdminUpdate) {
        // Admin updated user's profile
        Notifications::send('user.profile_updated_by_admin', [
            'user' => $user,
            'admin' => $updatedBy,
            'updated_fields' => array_keys($data),
            'completion_status' => $completion
        ], $user);
        
        // Also notify relevant admins
        $this->notifyAdminsOfUserProfileUpdate($user, $updatedBy, $data);
    }

    // Check for specific important changes
    $this->handleSpecificFieldNotifications($user, $data, $isOwnProfile);
}

/**
 * Handle notifications for specific field changes
 */
protected function handleSpecificFieldNotifications(User $user, array $data, bool $isOwnProfile): void
{
    // Email change notification
    if (isset($data['email']) && $data['email'] !== $user->getOriginal('email')) {
        Notifications::send('user.email_changed', [
            'user' => $user,
            'old_email' => $user->getOriginal('email'),
            'new_email' => $data['email'],
            'requires_verification' => true
        ], $user);
        
        // Send verification email to new address
        Notifications::send('user.verify_email', $user, $user);
    }

    // Phone number change notification
    if (isset($data['phone']) && $data['phone'] !== $user->getOriginal('phone')) {
        Notifications::send('user.phone_changed', [
            'user' => $user,
            'old_phone' => $user->getOriginal('phone'),
            'new_phone' => $data['phone']
        ], $user);
    }

    // Important company/address changes for business users
    if ($user->hasRole('client') && (isset($data['company']) || isset($data['address']))) {
        if ($data['company'] !== $user->getOriginal('company') || 
            $data['address'] !== $user->getOriginal('address')) {
            
            Notifications::send('user.business_info_changed', [
                'user' => $user,
                'changes' => array_intersect_key($data, array_flip(['company', 'address', 'city', 'state', 'country']))
            ], $user);
        }
    }
}

/**
 * Notify admins about user profile updates
 */
protected function notifyAdminsOfUserProfileUpdate(User $user, User $admin, array $data): void
{
    try {
        $relevantAdmins = User::whereHas('roles', function($query) {
            $query->whereIn('name', ['super-admin', 'admin', 'manager']);
        })
        ->where('id', '!=', $admin->id) // Don't notify the admin who made the change
        ->where('user_registration_notifications', true)
        ->get();

        foreach ($relevantAdmins as $adminUser) {
            Notifications::send('admin.user_profile_updated', [
                'user' => $user,
                'admin' => $admin,
                'updated_fields' => array_keys($data),
                'timestamp' => now()
            ], $adminUser);
        }
    } catch (\Exception $e) {
        Log::warning('Failed to notify admins of profile update', [
            'user_id' => $user->id,
            'admin_id' => $admin->id,
            'error' => $e->getMessage()
        ]);
    }
}

/**
 * Send welcome sequence based on profile completion
 */
public function sendWelcomeSequence(User $user): void
{
    $completion = $this->getProfileCompletionStatus($user);
    
    // Immediate welcome
    Notifications::send('user.welcome', $user, $user);
    
    // Schedule follow-up based on completion status
    if ($completion['essential_percentage'] < 50) {
        // Schedule profile completion reminder in 2 days
        Log::info('Scheduling profile completion reminder', [
            'user_id' => $user->id,
            'current_completion' => $completion['essential_percentage']
        ]);
        
        // You would integrate with your job scheduler here
        // dispatch(new SendProfileReminderJob($user))->delay(now()->addDays(2));
    }
    
    // Send role-specific welcome information
    if ($user->hasRole('client')) {
        Notifications::send('user.client_welcome_guide', $user, $user);
    } elseif ($user->hasAdminAccess()) {
        Notifications::send('user.admin_welcome_guide', $user, $user);
    }
}

/**
 * Handle notification preferences validation
 */
public function validateNotificationPreferences(array $preferences): array
{
    $validatedPreferences = [];
    
    // Define valid notification types
    $validTypes = [
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
    ];
    
    foreach ($validTypes as $type) {
        if (isset($preferences[$type])) {
            $validatedPreferences[$type] = filter_var($preferences[$type], FILTER_VALIDATE_BOOLEAN);
        }
    }
    
    // Handle frequency
    if (isset($preferences['notification_frequency'])) {
        $validFrequencies = ['immediate', 'hourly', 'daily', 'weekly'];
        if (in_array($preferences['notification_frequency'], $validFrequencies)) {
            $validatedPreferences['notification_frequency'] = $preferences['notification_frequency'];
        }
    }
    
    // Handle quiet hours
    if (isset($preferences['quiet_hours']) && is_array($preferences['quiet_hours'])) {
        $quietHours = $preferences['quiet_hours'];
        if (isset($quietHours['enabled']) && $quietHours['enabled']) {
            $validatedPreferences['quiet_hours'] = [
                'enabled' => true,
                'start' => $quietHours['start'] ?? '22:00',
                'end' => $quietHours['end'] ?? '08:00'
            ];
        } else {
            $validatedPreferences['quiet_hours'] = ['enabled' => false];
        }
    }
    
    return $validatedPreferences;
}

/**
 * Get notification statistics for user
 */
public function getNotificationStatistics(User $user): array
{
    return [
        'total_notifications' => $user->notifications()->count(),
        'unread_notifications' => $user->unreadNotifications()->count(),
        'notifications_last_30_days' => $user->notifications()
            ->where('created_at', '>=', now()->subDays(30))
            ->count(),
        'most_recent_notification' => $user->notifications()
            ->latest()
            ->first()?->created_at,
        'notification_types_received' => $user->notifications()
            ->selectRaw('type, COUNT(*) as count')
            ->groupBy('type')
            ->orderByDesc('count')
            ->limit(5)
            ->get()
            ->pluck('count', 'type')
            ->toArray(),
        'email_notifications_enabled' => $user->email_notifications ?? true,
        'notification_frequency' => $user->notification_frequency ?? 'immediate',
    ];
}

/**
 * Batch process profile completion reminders (for scheduled jobs)
 */
public function batchProcessCompletionReminders(int $batchSize = 50): array
{
    $stats = [
        'processed' => 0,
        'sent' => 0,
        'skipped' => 0,
        'errors' => 0
    ];
    
    $users = User::where('is_active', true)
        ->whereNotNull('email_verified_at')
        ->where('created_at', '<=', now()->subDays(3))
        ->where(function($query) {
            $query->whereNull('profile_reminder_sent_at')
                  ->orWhere('profile_reminder_sent_at', '<=', now()->subDays(7));
        })
        ->limit($batchSize)
        ->get();
    
    foreach ($users as $user) {
        $stats['processed']++;
        
        try {
            if ($this->shouldSendCompletionReminder($user)) {
                if ($this->sendCompletionReminder($user)) {
                    $stats['sent']++;
                } else {
                    $stats['errors']++;
                }
            } else {
                $stats['skipped']++;
            }
        } catch (\Exception $e) {
            $stats['errors']++;
            Log::error('Error processing completion reminder', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
        }
    }
    
    Log::info('Batch processed profile completion reminders', $stats);
    
    return $stats;
}

    /**
     * Delegate other UserService methods
     */
    public function __call($method, $parameters)
    {
        return $this->userService->$method(...$parameters);
    }
}