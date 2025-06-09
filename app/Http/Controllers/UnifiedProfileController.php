<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\UserProfileService;
use App\Services\UserService;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Requests\UpdateNotificationPreferencesRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use App\Models\User;

/**
 * UnifiedProfileController - SELF-ONLY Profile Management
 * 
 * This controller handles profile management for authenticated users.
 * Users can ONLY manage their OWN profiles - no admin overrides.
 * 
 * Key Principle: $user parameter is always ignored, we use auth()->user()
 */
class UnifiedProfileController extends Controller
{
    protected UserProfileService $profileService;
    protected UserService $userService;

    public function __construct(
        UserProfileService $profileService,
        UserService $userService
    ) {
        $this->profileService = $profileService;
        $this->userService = $userService;
        
        // Ensure only authenticated users can access
        $this->middleware('auth');
    }

    /**
     * Display the authenticated user's profile
     * Note: $user parameter is ignored - always shows current user's profile
     */
    public function show(?User $user = null)
    {
        $user = auth()->user(); // Always use authenticated user
        $activitySummary = $this->profileService->getUserActivitySummary($user);
        $suggestions = $this->profileService->getProfileSuggestions($user);
        $layout = $this->getLayout();

        return view('profile.show', compact('user', 'activitySummary', 'suggestions', 'layout'));
    }

    /**
     * Show the form for editing the authenticated user's profile
     * Note: $user parameter is ignored - always edits current user's profile
     */
    public function edit(?User $user = null)
    {
        $user = auth()->user(); // Always use authenticated user
        $completion = $this->profileService->getProfileCompletionStatus($user);
        $layout = $this->getLayout();
        return view('profile.edit', compact('user', 'completion', 'layout'));
    }

    /**
     * Update the authenticated user's profile information
     * Note: $user parameter is ignored - always updates current user's profile
     */
    public function update(Request $request, ?User $user = null)
    {
        $user = auth()->user(); // Always use authenticated user
        
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
            'position' => ['nullable', 'string', 'max:255'],
            'allow_testimonials' => ['boolean'],
            'allow_public_profile' => ['boolean'],
            'marketing_notifications' => ['boolean'],
        ]);
        
        try {
            $updatedUser = $this->profileService->updateProfile(
                $user, 
                $validated, 
                $request->file('avatar')
            );

            // Check if email changed and require verification
            if ($user->email !== $request->email && $request->has('email')) {
                $updatedUser->email_verified_at = null;
                $updatedUser->save();
                
                return redirect()->route('profile.show')
                    ->with('success', 'Profile updated successfully! Please verify your new email address.');
            }

            return redirect()->route('profile.show')
                ->with('success', 'Profile updated successfully!');

        } catch (\Exception $e) {
            Log::error('Profile update failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update profile. Please try again.');
        }
    }

    /**
     * Show the form for changing the authenticated user's password
     * Note: $user parameter is ignored - always for current user
     */
    public function showChangePasswordForm(?User $user = null)
    {
        $user = auth()->user();
        $layout = $this->getLayout();
        return view('profile.change-password', compact('user', 'layout'));
    }

    /**
     * Update the authenticated user's password
     * Note: $user parameter is ignored - always updates current user's password
     */
    public function updatePassword(Request $request, ?User $user = null)
    {
        $user = auth()->user(); // Always use authenticated user
        
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        try {
            $this->userService->changePassword($user, $request->password);

            return redirect()->route('profile.show')
                ->with('success', 'Password updated successfully!');

        } catch (\Exception $e) {
            Log::error('Password update failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('error', 'Failed to update password. Please try again.');
        }
    }

    /**
     * Show notification preferences for the authenticated user
     * Note: $user parameter is ignored - always for current user
     */
    public function preferences(?User $user = null)
    {
        $user = auth()->user(); // Always use authenticated user
        $completion = $this->profileService->getProfileCompletionStatus($user);
        $layout = $this->getLayout();
        return view('profile.preferences', compact('user', 'completion', 'layout'));
    }

    /**
     * Update notification preferences for the authenticated user
     * Note: $user parameter is ignored - always updates current user's preferences
     */
    public function updatePreferences(Request $request, ?User $user = null)
    {
        $user = auth()->user(); // Always use authenticated user
        
        $validated = $request->validate([
            // Notification preferences
            'email_notifications' => 'boolean',
            'project_update_notifications' => 'boolean',
            'quotation_update_notifications' => 'boolean',
            'message_reply_notifications' => 'boolean',
            'deadline_alert_notifications' => 'boolean',
            'chat_notifications' => 'boolean',
            'system_notifications' => 'boolean',
            'marketing_notifications' => 'boolean',
            'testimonial_notifications' => 'boolean',
            'urgent_notifications' => 'boolean',
            'security_alert_notifications' => 'boolean',
            'notification_frequency' => 'nullable|string|in:immediate,hourly,daily,weekly',
            'quiet_hours' => 'nullable|array',
            'quiet_hours.enabled' => 'boolean',
            'quiet_hours.start' => 'nullable|string',
            'quiet_hours.end' => 'nullable|string',
        ]);

        try {
            $this->profileService->updateNotificationPreferences($user, $validated);

            return redirect()->route('profile.preferences')
                ->with('success', 'Notification preferences updated successfully!');

        } catch (\Exception $e) {
            Log::error('Notification preferences update failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('error', 'Failed to update preferences. Please try again.');
        }
    }

    /**
     * Show profile completion guide for the authenticated user
     */
    public function completion()
    {
        $user = auth()->user(); // Always use authenticated user
        $completion = $this->profileService->getProfileCompletionStatus($user);
        $suggestions = $this->profileService->getProfileSuggestions($user);

        return view('profile.completion', compact('user', 'completion', 'suggestions'));
    }

    /**
     * Export authenticated user's data (GDPR compliance)
     */
    public function export()
    {
        $user = auth()->user(); // Always use authenticated user
        
        try {
            $data = $this->profileService->exportUserData($user);
            
            $filename = 'profile_data_' . $user->id . '_' . now()->format('Y-m-d_H-i-s') . '.json';
            
            return response()->json($data, 200, [
                'Content-Type' => 'application/json',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]);

        } catch (\Exception $e) {
            Log::error('Data export failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('error', 'Failed to export data. Please try again.');
        }
    }

    /**
     * Show account deletion form for the authenticated user
     */
    public function showDeleteForm()
    {
        $user = auth()->user(); // Always use authenticated user
        
        // Get user's data summary for deletion confirmation
        $dataSummary = [
            'projects_count' => $user->projects()->count(),
            'quotations_count' => $user->quotations()->count(),
            'messages_count' => $user->messages()->count(),
            'posts_count' => $user->posts()->count(),
        ];
        $layout = $this->getLayout();

        return view('profile.delete', compact('user', 'dataSummary', 'layout'));
    }

    /**
     * Delete the authenticated user's account
     */
    public function destroy(Request $request)
    {
        $user = auth()->user(); // Always use authenticated user
        
        $request->validate([
            'password' => ['required', 'current_password'],
            'confirmation' => ['required', 'accepted'],
        ]);

        try {
            Log::info('User account deletion initiated', [
                'user_id' => $user->id,
                'email' => $user->email,
                'ip' => $request->ip(),
            ]);

            // Use the UserService to handle deletion properly
            $this->userService->deleteUser($user);

            Auth::logout();

            return redirect()->route('login')
                ->with('success', 'Your account has been successfully deleted.');

        } catch (\Exception $e) {
            Log::error('Account deletion failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('error', 'Failed to delete account. Please contact support if this persists.');
        }
    }

    /**
     * Get completion status as JSON for AJAX requests
     */
    public function completionStatus()
    {
        $user = auth()->user(); // Always use authenticated user
        $completion = $this->profileService->getProfileCompletionStatus($user);
        $suggestions = $this->profileService->getProfileSuggestions($user);

        return response()->json([
            'completion' => $completion,
            'suggestions' => $suggestions,
        ]);
    }

    /**
     * Get profile activity summary as JSON
     */
    public function activitySummary()
    {
        $user = auth()->user(); // Always use authenticated user
        $summary = $this->profileService->getUserActivitySummary($user);

        return response()->json($summary);
    }
    private function getLayout()
    {
        $user = auth()->user();
        
        // Admin users get admin layout
        if ($user->hasAnyRole(['super-admin', 'admin', 'manager', 'editor'])) {
            return 'layouts.admin';
        }
        
        // Client users get client layout  
        if ($user->hasRole('client')) {
            return 'layouts.client';
        }
        
        // Default layout for other users
        return 'layouts.app';
    }
}