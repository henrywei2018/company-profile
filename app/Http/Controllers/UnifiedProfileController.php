<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\CentralizedProfileService;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Requests\UpdateNotificationPreferencesRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\User;

use Illuminate\Validation\Rule;

class UnifiedProfileController extends Controller
{
    protected CentralizedProfileService $profileService;

    public function __construct(CentralizedProfileService $profileService)
    {
        $this->profileService = $profileService;
    }

    /**
     * Display the user's profile (context-aware)
     */
    public function show(?User $user = null)
    {
        $user = $user ?? auth()->user();
        $currentUser = auth()->user();
        
        // Check permission to view profile
        if ($user->id !== $currentUser->id && !$currentUser->hasAdminAccess()) {
            abort(403, 'Unauthorized to view this profile');
        }

        $activitySummary = $this->profileService->getUserActivitySummary($user, $currentUser);
        $suggestions = $this->profileService->getProfileSuggestions($user);
        $isOwnProfile = $user->id === $currentUser->id;

        // Determine which view to use based on context
        $view = $isOwnProfile ? 'profile.show' : 'admin.users.profile.show';

        return view($view, compact('user', 'activitySummary', 'suggestions', 'isOwnProfile'));
    }

    /**
     * Show the form for editing the user's profile (context-aware)
     */
    public function edit(?User $user = null)
    {
        $user = $user ?? auth()->user();
        $currentUser = auth()->user();
        
        // Check permission to edit profile
        if ($user->id !== $currentUser->id && !$currentUser->can('edit users')) {
            abort(403, 'Unauthorized to edit this profile');
        }

        $completion = $this->profileService->getProfileCompletionStatus($user);
        $isOwnProfile = $user->id === $currentUser->id;

        // Determine which view to use based on context
        $view = $isOwnProfile ? 'profile.edit' : 'admin.users.edit';

        return view($view, compact('user', 'completion', 'isOwnProfile'));
    }

    /**
     * Update the user's profile (context-aware)
     */
    public function update(UpdateProfileRequest $request, ?User $user = null)
    {
        $user = $user ?? auth()->user();
        $currentUser = auth()->user();
        
        // Check permission to update profile
        if ($user->id !== $currentUser->id && !$currentUser->can('edit users')) {
            abort(403, 'Unauthorized to edit this profile');
        }

        try {
            $updatedUser = $this->profileService->updateProfile(
                $user, 
                $request->validated(), 
                $request->file('avatar'),
                $currentUser
            );

            $isOwnProfile = $user->id === $currentUser->id;
            $redirectUrl = $this->profileService->getRedirectUrl($updatedUser, 'show', $currentUser);

            // Handle email change verification
            if ($user->email !== $request->email && $request->has('email')) {
                $message = $isOwnProfile 
                    ? 'Profile updated successfully! Please verify your new email address.'
                    : 'User profile updated successfully! Email verification required for new email.';
                
                return redirect($redirectUrl)->with('success', $message);
            }

            $message = $isOwnProfile 
                ? 'Profile updated successfully!'
                : 'User profile updated successfully!';

            return redirect($redirectUrl)->with('success', $message);

        } catch (\Exception $e) {
            Log::error('Profile update failed', [
                'user_id' => $user->id,
                'updated_by' => $currentUser->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update profile. Please try again.');
        }
    }

    /**
     * Show the form for changing password (context-aware)
     */
    public function showChangePasswordForm(?User $user = null)
    {
        $user = $user ?? auth()->user();
        $currentUser = auth()->user();
        
        // Check permission
        if ($user->id !== $currentUser->id && !$currentUser->can('edit users')) {
            abort(403, 'Unauthorized to change this user\'s password');
        }

        $isOwnProfile = $user->id === $currentUser->id;
        $view = $isOwnProfile ? 'profile.change-password' : 'admin.users.change-password';

        return view($view, compact('user', 'isOwnProfile'));
    }

    /**
     * Update the user's password (context-aware)
     */
    public function updatePassword(Request $request, ?User $user = null)
    {
        $user = $user ?? auth()->user();
        $currentUser = auth()->user();
        $isOwnProfile = $user->id === $currentUser->id;

        // Check permission
        if (!$isOwnProfile && !$currentUser->can('edit users')) {
            abort(403, 'Unauthorized to change this user\'s password');
        }

        // Validation rules depend on context
        $rules = ['password' => ['required', 'string', 'min:8', 'confirmed']];
        
        if ($isOwnProfile) {
            $rules['current_password'] = ['required', 'current_password'];
        }

        $request->validate($rules);

        try {
            $this->profileService->changePassword($user, $request->password);

            $redirectUrl = $this->profileService->getRedirectUrl($user, 'show', $currentUser);
            $message = $isOwnProfile 
                ? 'Password updated successfully!'
                : 'User password updated successfully!';

            return redirect($redirectUrl)->with('success', $message);

        } catch (\Exception $e) {
            Log::error('Password update failed', [
                'user_id' => $user->id,
                'updated_by' => $currentUser->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('error', 'Failed to update password. Please try again.');
        }
    }

    /**
     * Show notification preferences (context-aware)
     */
    public function preferences(?User $user = null)
    {
        $user = $user ?? auth()->user();
        $currentUser = auth()->user();
        
        // Only own profile for preferences, or admin access
        if ($user->id !== $currentUser->id && !$currentUser->hasAdminAccess()) {
            abort(403, 'Unauthorized to view notification preferences');
        }

        $completion = $this->profileService->getProfileCompletionStatus($user);
        $isOwnProfile = $user->id === $currentUser->id;

        $view = $isOwnProfile ? 'profile.preferences' : 'admin.users.preferences';

        return view($view, compact('user', 'completion', 'isOwnProfile'));
    }

    /**
     * Update notification preferences (context-aware)
     */
    public function updatePreferences(UpdateNotificationPreferencesRequest $request, ?User $user = null)
    {
        $user = $user ?? auth()->user();
        $currentUser = auth()->user();
        
        // Only own profile for preferences, or admin access
        if ($user->id !== $currentUser->id && !$currentUser->hasAdminAccess()) {
            abort(403, 'Unauthorized to update notification preferences');
        }

        try {
            $this->profileService->updateNotificationPreferences($user, $request->validated(), $currentUser);

            $isOwnProfile = $user->id === $currentUser->id;
            $redirectUrl = $isOwnProfile 
                ? route('profile.preferences')
                : route('admin.users.show', $user);

            $message = $isOwnProfile 
                ? 'Notification preferences updated successfully!'
                : 'User notification preferences updated successfully!';

            return redirect($redirectUrl)->with('success', $message);

        } catch (\Exception $e) {
            Log::error('Notification preferences update failed', [
                'user_id' => $user->id,
                'updated_by' => $currentUser->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('error', 'Failed to update preferences. Please try again.');
        }
    }

    /**
     * Show profile completion guide
     */
    public function completion()
    {
        $user = auth()->user();
        $completion = $this->profileService->getProfileCompletionStatus($user);
        $suggestions = $this->profileService->getProfileSuggestions($user);

        return view('profile.completion', compact('user', 'completion', 'suggestions'));
    }

    /**
     * Export user data (GDPR compliance)
     */
    public function export(?User $user = null)
    {
        $user = $user ?? auth()->user();
        $currentUser = auth()->user();

        try {
            $data = $this->profileService->exportUserData($user, $currentUser);
            
            $filename = 'profile_data_' . $user->id . '_' . now()->format('Y-m-d_H-i-s') . '.json';
            
            return response()->json($data, 200, [
                'Content-Type' => 'application/json',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]);

        } catch (\Exception $e) {
            Log::error('Data export failed', [
                'user_id' => $user->id,
                'requested_by' => $currentUser->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('error', 'Failed to export data. Please try again.');
        }
    }

    /**
     * Send profile completion reminder (admin only)
     */
    public function sendCompletionReminder(User $user)
    {
        $currentUser = auth()->user();

        if (!$currentUser->hasAdminAccess()) {
            abort(403, 'Unauthorized action');
        }

        try {
            $sent = $this->profileService->sendCompletionReminder($user);
            
            if ($sent) {
                return redirect()->back()
                    ->with('success', 'Profile completion reminder sent successfully!');
            } else {
                return redirect()->back()
                    ->with('info', 'Reminder was not sent (user profile is complete or reminder sent recently).');
            }

        } catch (\Exception $e) {
            Log::error('Failed to send completion reminder', [
                'user_id' => $user->id,
                'admin_id' => $currentUser->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('error', 'Failed to send reminder. Please try again.');
        }
    }

    /**
     * Show account deletion form
     */
    public function showDeleteForm()
    {
        $user = auth()->user();
        
        // Get user's data summary for deletion confirmation
        $dataSummary = [
            'projects_count' => $user->projects()->count(),
            'quotations_count' => $user->quotations()->count(),
            'messages_count' => $user->messages()->count(),
            'posts_count' => $user->posts()->count(),
        ];

        return view('profile.delete', compact('user', 'dataSummary'));
    }

    /**
     * Delete the user's account
     */
    public function destroy(Request $request, ?User $user = null)
    {
        $user = $user ?? auth()->user();
        $currentUser = auth()->user();
        $isOwnProfile = $user->id === $currentUser->id;

        // Check permissions
        if (!$isOwnProfile && !$currentUser->can('delete users')) {
            abort(403, 'Unauthorized to delete this account');
        }

        $request->validate([
            'password' => $isOwnProfile ? ['required', 'current_password'] : [],
            'confirmation' => ['required', 'accepted'],
        ]);

        try {
            Log::info('User account deletion initiated', [
                'user_id' => $user->id,
                'deleted_by' => $currentUser->id,
                'is_own_profile' => $isOwnProfile,
                'ip' => $request->ip(),
            ]);

            // Use the centralized service
            $this->profileService->deleteUser($user);

            if ($isOwnProfile) {
                Auth::logout();
                return redirect()->route('login')
                    ->with('success', 'Your account has been successfully deleted.');
            } else {
                return redirect()->route('admin.users.index')
                    ->with('success', 'User account has been successfully deleted.');
            }

        } catch (\Exception $e) {
            Log::error('Account deletion failed', [
                'user_id' => $user->id,
                'deleted_by' => $currentUser->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * API: Get completion status as JSON for AJAX requests
     */
    public function completionStatus(?User $user = null)
    {
        $user = $user ?? auth()->user();
        $currentUser = auth()->user();
        
        // Check permission
        if ($user->id !== $currentUser->id && !$currentUser->hasAdminAccess()) {
            abort(403, 'Unauthorized');
        }

        $completion = $this->profileService->getProfileCompletionStatus($user);
        $suggestions = $this->profileService->getProfileSuggestions($user);

        return response()->json([
            'completion' => $completion,
            'suggestions' => $suggestions,
        ]);
    }

    /**
     * API: Get profile activity summary as JSON
     */
    public function activitySummary(?User $user = null)
    {
        $user = $user ?? auth()->user();
        $currentUser = auth()->user();
        
        // Check permission
        if ($user->id !== $currentUser->id && !$currentUser->hasAdminAccess()) {
            abort(403, 'Unauthorized');
        }

        $summary = $this->profileService->getUserActivitySummary($user, $currentUser);

        return response()->json($summary);
    }

    /**
     * API: Test notification system
     */
    public function testNotification()
    {
        try {
            $user = auth()->user();
            
            // Send test notification using the centralized service
            $result = $this->profileService->sendTestNotification($user);
            
            if ($result) {
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
     * Determine the appropriate redirect after action based on user context
     */
    protected function getContextualRedirect(User $user, string $action = 'show'): string
    {
        return $this->profileService->getRedirectUrl($user, $action, auth()->user());
    }

    /**
     * Check if current user can perform action on target user
     */
    protected function canPerformAction(User $targetUser, string $action): bool
    {
        $currentUser = auth()->user();
        $isOwnProfile = $targetUser->id === $currentUser->id;

        return match($action) {
            'view' => $isOwnProfile || $currentUser->hasAdminAccess(),
            'edit' => $isOwnProfile || $currentUser->can('edit users'),
            'delete' => ($isOwnProfile && $targetUser->id !== 1) || $currentUser->can('delete users'),
            'change_password' => $isOwnProfile || $currentUser->can('edit users'),
            'export' => $isOwnProfile || $currentUser->can('export user data'),
            default => false,
        };
    }

    /**
     * Get success message based on action and context
     */
    protected function getSuccessMessage(string $action, bool $isOwnProfile): string
    {
        $messages = [
            'update' => $isOwnProfile ? 'Profile updated successfully!' : 'User profile updated successfully!',
            'password' => $isOwnProfile ? 'Password updated successfully!' : 'User password updated successfully!',
            'preferences' => $isOwnProfile ? 'Preferences updated successfully!' : 'User preferences updated successfully!',
            'delete' => $isOwnProfile ? 'Your account has been deleted.' : 'User account has been deleted.',
        ];

        return $messages[$action] ?? 'Action completed successfully!';
    }
}