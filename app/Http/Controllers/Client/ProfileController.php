<?php
// File: app/Http/Controllers/Client/ProfileController.php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use App\Services\ClientAccessService;
use App\Services\ClientNotificationService;
use App\Services\UserService;
use App\Facades\Notifications;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;

class ProfileController extends Controller
{
    protected DashboardService $dashboardService;
    protected ClientAccessService $clientAccessService;
    protected ClientNotificationService $clientNotificationService;
    protected UserService $userService;

    public function __construct(
        DashboardService $dashboardService,
        ClientAccessService $clientAccessService,
        ClientNotificationService $clientNotificationService,
        UserService $userService
    ) {
        $this->dashboardService = $dashboardService;
        $this->clientAccessService = $clientAccessService;
        $this->clientNotificationService = $clientNotificationService;
        $this->userService = $userService;
    }

    /**
     * Display the client's profile.
     */
    public function show()
    {
        $user = auth()->user();
        
        // Get profile completion status
        $profileCompletion = $this->getProfileCompletionStatus($user);
        
        // Get client statistics using existing service
        $dashboardData = $this->dashboardService->getDashboardData($user);
        $statistics = $dashboardData['statistics'] ?? [];
        
        return view('client.profile.show', compact('user', 'profileCompletion', 'statistics'));
    }
    
    /**
     * Show the form for editing the client's profile.
     */
    public function edit()
    {
        $user = auth()->user();
        
        return view('client.profile.edit', compact('user'));
    }
    
    /**
     * Update the client's profile.
     */
    public function update(Request $request)
    {
        $user = auth()->user();
        
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:20'],
            'company' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'country' => ['nullable', 'string', 'max:100'],
            'avatar' => ['nullable', 'image', 'max:1024'],
            'bio' => ['nullable', 'string', 'max:500'],
            'website' => ['nullable', 'url', 'max:255'],
            'linkedin' => ['nullable', 'url', 'max:255'],
            'twitter' => ['nullable', 'url', 'max:255'],
        ]);
        
        try {
            // Use UserService for updating profile
            $updatedUser = $this->userService->updateUser($user, $validated, $request->file('avatar'));
            
            // Clear dashboard cache
            $this->dashboardService->clearCache($updatedUser);
            
            // If email changed, send verification notification
            if ($user->email !== $validated['email']) {
                try {
                    Notifications::send('user.email_verification_required', $updatedUser, $updatedUser);
                    
                    return redirect()->route('client.profile.show')
                        ->with('success', 'Profile updated successfully! Please verify your new email address.');
                } catch (\Exception $e) {
                    Log::warning('Failed to send email verification notification', [
                        'user_id' => $updatedUser->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }
            
            return redirect()->route('client.profile.show')
                ->with('success', 'Profile updated successfully!');
                
        } catch (\Exception $e) {
            Log::error('Failed to update profile', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update profile. Please try again.');
        }
    }
    
    /**
     * Show form to change password.
     */
    public function showChangePasswordForm()
    {
        return view('client.profile.change-password');
    }
    
    /**
     * Change client password.
     */
    public function changePassword(Request $request)
    {
        $user = auth()->user();
        
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
        
        try {
            // Use UserService for password change
            $this->userService->changePassword($user, $validated['password']);
            
            // Clear dashboard cache
            $this->dashboardService->clearCache($user);
            
            return redirect()->route('client.profile.show')
                ->with('success', 'Password changed successfully!');
                
        } catch (\Exception $e) {
            Log::error('Failed to change password', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            
            return redirect()->back()
                ->with('error', 'Failed to change password. Please try again.');
        }
    }

    /**
     * Show client preferences form (merged from settings).
     */
    public function preferences()
    {
        $user = auth()->user();
        
        // Get notification preferences using existing service
        $notificationPreferences = $this->clientNotificationService->getClientNotificationPreferences($user);
        
        // Get other user preferences
        $userPreferences = [
            'language' => $user->language ?? 'en',
            'timezone' => $user->timezone ?? config('app.timezone'),
            'date_format' => $user->date_format ?? 'Y-m-d',
            'time_format' => $user->time_format ?? 'H:i',
            'receive_newsletters' => $user->newsletter_subscription ?? false,
            'dashboard_layout' => $user->dashboard_layout ?? 'default',
            'items_per_page' => $user->items_per_page ?? 20,
        ];
        
        return view('client.profile.preferences', compact('notificationPreferences', 'userPreferences'));
    }

    /**
     * Update client preferences (merged from settings).
     */
    public function updatePreferences(Request $request)
    {
        $user = auth()->user();
        
        $validated = $request->validate([
            // Notification preferences
            'email_notifications' => 'boolean',
            'project_update_notifications' => 'boolean',
            'quotation_update_notifications' => 'boolean',
            'message_reply_notifications' => 'boolean',
            'deadline_alert_notifications' => 'boolean',
            'marketing_notifications' => 'boolean',
            'sms_notifications' => 'boolean',
            'newsletter_subscription' => 'boolean',
            'notification_frequency' => 'nullable|string|in:immediate,daily,weekly',
            'quiet_hours' => 'nullable|array',
            
            // General preferences
            'language' => 'nullable|string|in:en,id',
            'timezone' => 'nullable|string',
            'date_format' => 'nullable|string|in:Y-m-d,d/m/Y,m/d/Y,d-m-Y',
            'time_format' => 'nullable|string|in:H:i,h:i A',
            'dashboard_layout' => 'nullable|string|in:default,compact,detailed',
            'items_per_page' => 'nullable|integer|min:10|max:100',
        ]);
        
        try {
            // Update notification preferences using existing service
            $notificationPrefs = array_intersect_key($validated, array_flip([
                'email_notifications', 'project_update_notifications', 
                'quotation_update_notifications', 'message_reply_notifications',
                'deadline_alert_notifications', 'marketing_notifications',
                'sms_notifications', 'newsletter_subscription',
                'notification_frequency', 'quiet_hours'
            ]));
            
            $this->clientNotificationService->updateClientNotificationPreferences($user, $notificationPrefs);
            
            // Update other preferences
            $otherPrefs = array_intersect_key($validated, array_flip([
                'language', 'timezone', 'date_format', 'time_format',
                'dashboard_layout', 'items_per_page'
            ]));
            
            if (!empty($otherPrefs)) {
                $user->update($otherPrefs);
            }
            
            // Clear dashboard cache
            $this->dashboardService->clearCache($user);
            
            return redirect()->route('client.profile.preferences')
                ->with('success', 'Preferences updated successfully!');
                
        } catch (\Exception $e) {
            Log::error('Failed to update preferences', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            
            return redirect()->back()
                ->with('error', 'Failed to update preferences. Please try again.');
        }
    }

    /**
     * Show privacy settings form (merged from settings).
     */
    public function privacy()
    {
        $user = auth()->user();
        
        return view('client.profile.privacy', compact('user'));
    }

    /**
     * Update privacy settings (merged from settings).
     */
    public function updatePrivacy(Request $request)
    {
        $user = auth()->user();
        
        $validated = $request->validate([
            'profile_visibility' => 'required|in:public,private,contacts_only',
            'show_email' => 'boolean',
            'show_phone' => 'boolean',
            'show_company' => 'boolean',
            'allow_testimonial_display' => 'boolean',
            'allow_project_showcase' => 'boolean',
            'data_sharing_consent' => 'boolean',
            'marketing_consent' => 'boolean',
        ]);
        
        try {
            // Update privacy settings
            $user->update([
                'profile_visibility' => $validated['profile_visibility'],
                'show_email' => $validated['show_email'] ?? false,
                'show_phone' => $validated['show_phone'] ?? false,
                'show_company' => $validated['show_company'] ?? false,
                'allow_testimonial_display' => $validated['allow_testimonial_display'] ?? true,
                'allow_project_showcase' => $validated['allow_project_showcase'] ?? true,
                'data_sharing_consent' => $validated['data_sharing_consent'] ?? false,
                'marketing_consent' => $validated['marketing_consent'] ?? false,
                'privacy_updated_at' => now(),
            ]);
            
            return redirect()->route('client.profile.privacy')
                ->with('success', 'Privacy settings updated successfully!');
                
        } catch (\Exception $e) {
            Log::error('Failed to update privacy settings', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            
            return redirect()->back()
                ->with('error', 'Failed to update privacy settings. Please try again.');
        }
    }

    /**
     * Show security settings (merged from settings).
     */
    public function security()
    {
        $user = auth()->user();
        
        // Get user's security information
        $securityInfo = [
            'two_factor_enabled' => $user->two_factor_enabled ?? false,
            'last_password_change' => $user->password_changed_at,
            'active_sessions' => $this->getActiveSessions($user),
            'login_history' => $this->getRecentLoginHistory($user),
            'security_events' => $this->getSecurityEvents($user),
        ];
        
        return view('client.profile.security', compact('user', 'securityInfo'));
    }

    /**
     * Update security settings (merged from settings).
     */
    public function updateSecurity(Request $request)
    {
        $user = auth()->user();
        
        $validated = $request->validate([
            'two_factor_enabled' => 'boolean',
            'login_notifications' => 'boolean',
            'suspicious_activity_alerts' => 'boolean',
            'session_timeout' => 'nullable|integer|min:15|max:480', // 15 minutes to 8 hours
        ]);
        
        try {
            // Update security settings
            $user->update([
                'two_factor_enabled' => $validated['two_factor_enabled'] ?? false,
                'login_notifications' => $validated['login_notifications'] ?? true,
                'suspicious_activity_alerts' => $validated['suspicious_activity_alerts'] ?? true,
                'session_timeout' => $validated['session_timeout'] ?? 120, // 2 hours default
                'security_updated_at' => now(),
            ]);
            
            // Send notification about security changes
            try {
                Notifications::send('user.security_updated', $user, $user);
            } catch (\Exception $e) {
                Log::warning('Failed to send security update notification', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage()
                ]);
            }
            
            return redirect()->route('client.profile.security')
                ->with('success', 'Security settings updated successfully!');
                
        } catch (\Exception $e) {
            Log::error('Failed to update security settings', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            
            return redirect()->back()
                ->with('error', 'Failed to update security settings. Please try again.');
        }
    }

    /**
     * Show account deletion confirmation.
     */
    public function showDeleteForm()
    {
        $user = auth()->user();
        
        // Get user's data summary
        $dataSummary = [
            'projects_count' => $this->clientAccessService->getClientProjects($user)->count(),
            'quotations_count' => $this->clientAccessService->getClientQuotations($user)->count(),
            'messages_count' => $this->clientAccessService->getClientMessages($user)->count(),
            'testimonials_count' => \App\Models\Testimonial::whereHas('project', function($query) use ($user) {
                $query->where('client_id', $user->id);
            })->count(),
        ];
        
        return view('client.profile.delete', compact('user', 'dataSummary'));
    }

    /**
     * Delete user account.
     */
    public function deleteAccount(Request $request)
    {
        $user = auth()->user();
        
        $request->validate([
            'password' => ['required', 'current_password'],
            'confirmation' => ['required', 'accepted'],
            'reason' => ['nullable', 'string', 'max:500'],
        ]);
        
        try {
            // Log account deletion
            Log::info('User account deletion initiated', [
                'user_id' => $user->id,
                'email' => $user->email,
                'reason' => $request->reason,
            ]);
            
            // Use UserService for account deletion
            $this->userService->deleteUser($user);
            
            // Logout user
            Auth::logout();
            
            return redirect()->route('login')
                ->with('success', 'Your account has been successfully deleted.');
                
        } catch (\Exception $e) {
            Log::error('Failed to delete user account', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            
            return redirect()->back()
                ->with('error', 'Failed to delete account. Please contact support.');
        }
    }

    /**
     * Export user data (GDPR compliance).
     */
    public function exportData()
    {
        $user = auth()->user();
        
        try {
            // Get all user data
            $userData = [
                'profile' => $user->toArray(),
                'projects' => $this->clientAccessService->getClientProjects($user)->get()->toArray(),
                'quotations' => $this->clientAccessService->getClientQuotations($user)->get()->toArray(),
                'messages' => $this->clientAccessService->getClientMessages($user)->get()->toArray(),
                'testimonials' => \App\Models\Testimonial::whereHas('project', function($query) use ($user) {
                    $query->where('client_id', $user->id);
                })->get()->toArray(),
                'notifications' => $user->notifications()->get()->toArray(),
                'export_date' => now()->toISOString(),
            ];
            
            $filename = 'user_data_' . $user->id . '_' . now()->format('Y-m-d_H-i-s') . '.json';
            
            return response()->json($userData)
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
                ->header('Content-Type', 'application/json');
                
        } catch (\Exception $e) {
            Log::error('Failed to export user data', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            
            return redirect()->back()
                ->with('error', 'Failed to export data. Please try again.');
        }
    }

    /**
     * Get activity log.
     */
    public function activity(Request $request)
    {
        $user = auth()->user();
        
        // Get recent activities using existing DashboardService
        $dashboardData = $this->dashboardService->getDashboardData($user);
        $allActivities = [];
        
        // Combine all activity types
        if (isset($dashboardData['recent_activities'])) {
            foreach ($dashboardData['recent_activities'] as $activityType => $activities) {
                $allActivities = array_merge($allActivities, $activities);
            }
        }
        
        // Sort by date
        usort($allActivities, function($a, $b) {
            return $b['date'] <=> $a['date'];
        });
        
        // Filter by type if specified
        if ($request->filled('type')) {
            $allActivities = array_filter($allActivities, function($activity) use ($request) {
                return $activity['type'] === $request->type;
            });
        }
        
        // Paginate activities (manual pagination for array)
        $page = $request->get('page', 1);
        $perPage = 15;
        $total = count($allActivities);
        $activities = array_slice($allActivities, ($page - 1) * $perPage, $perPage);
        
        $pagination = [
            'current_page' => $page,
            'per_page' => $perPage,
            'total' => $total,
            'last_page' => ceil($total / $perPage),
        ];
        
        return view('client.profile.activity', compact('activities', 'pagination'));
    }

    /**
     * Test notification settings.
     */
    public function testNotification(): JsonResponse
    {
        try {
            $user = auth()->user();
            $success = $this->clientNotificationService->sendTestNotification($user);
            
            if ($success) {
                return response()->json([
                    'success' => true,
                    'message' => 'Test notification sent successfully! Check your email and notifications.'
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to send test notification.'
            ], 500);
            
        } catch (\Exception $e) {
            Log::error('Failed to send test notification', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to send test notification.'
            ], 500);
        }
    }

    /**
     * Get profile completion status.
     */
    protected function getProfileCompletionStatus($user): array
    {
        $fields = [
            'name' => !empty($user->name),
            'email' => !empty($user->email),
            'phone' => !empty($user->phone),
            'company' => !empty($user->company),
            'address' => !empty($user->address),
            'avatar' => !empty($user->avatar),
            'bio' => !empty($user->bio),
        ];
        
        $completed = count(array_filter($fields));
        $total = count($fields);
        
        return [
            'percentage' => round(($completed / $total) * 100),
            'completed_fields' => $completed,
            'total_fields' => $total,
            'missing_fields' => array_keys(array_filter($fields, fn($v) => !$v)),
            'fields' => $fields,
        ];
    }

    /**
     * Get active sessions for security page.
     */
    protected function getActiveSessions($user): array
    {
        // This would ideally query a sessions table
        // For now, return current session info
        return [
            [
                'id' => session()->getId(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'last_activity' => now(),
                'is_current' => true,
            ]
        ];
    }

    /**
     * Get recent login history for security page.
     */
    protected function getRecentLoginHistory($user): array
    {
        // This would ideally query a login_history table
        // For now, return basic info
        return [
            [
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'login_at' => $user->last_login_at ?? now(),
                'successful' => true,
            ]
        ];
    }

    /**
     * Get security events for security page.
     */
    protected function getSecurityEvents($user): array
    {
        // This would ideally query a security_events table
        // For now, return empty array
        return [];
    }
}