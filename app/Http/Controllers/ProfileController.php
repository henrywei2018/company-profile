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

class ProfileController extends Controller
{
    protected UserProfileService $profileService;
    protected UserService $userService;

    public function __construct(
        UserProfileService $profileService,
        UserService $userService
    ) {
        $this->profileService = $profileService;
        $this->userService = $userService;
    }

    /**
     * Display the user's profile.
     */
    public function show()
    {
        $user = auth()->user();
        $activitySummary = $this->profileService->getUserActivitySummary($user);
        $suggestions = $this->profileService->getProfileSuggestions($user);

        return view('profile.show', compact('user', 'activitySummary', 'suggestions'));
    }

    /**
     * Show the form for editing the user's profile.
     */
    public function edit()
    {
        $user = auth()->user();
        $completion = $this->profileService->getProfileCompletionStatus($user);

        return view('profile.edit', compact('user', 'completion'));
    }

    /**
     * Update the user's profile information.
     */
    public function update(UpdateProfileRequest $request)
    {
        $user = auth()->user();
        
        try {
            $updatedUser = $this->profileService->updateProfile(
                $user, 
                $request->validated(), 
                $request->file('avatar')
            );

            // Check if email changed and require verification
            if ($user->email !== $request->email && $request->has('email')) {
                $updatedUser->email_verified_at = null;
                $updatedUser->save();
                
                // Send verification email would be handled by observer or event
                
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
     * Show the form for changing password.
     */
    public function showChangePasswordForm()
    {
        return view('profile.change-password');
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        try {
            $user = auth()->user();
            $this->userService->changePassword($user, $request->password);

            return redirect()->route('profile.show')
                ->with('success', 'Password updated successfully!');

        } catch (\Exception $e) {
            Log::error('Password update failed', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('error', 'Failed to update password. Please try again.');
        }
    }

    /**
     * Show notification preferences.
     */
    public function preferences()
    {
        $user = auth()->user();
        $completion = $this->profileService->getProfileCompletionStatus($user);

        return view('profile.preferences', compact('user', 'completion'));
    }

    /**
     * Update notification preferences.
     */
    public function updatePreferences(UpdateNotificationPreferencesRequest $request)
    {
        try {
            $user = auth()->user();
            $this->profileService->updateNotificationPreferences($user, $request->validated());

            return redirect()->route('profile.preferences')
                ->with('success', 'Notification preferences updated successfully!');

        } catch (\Exception $e) {
            Log::error('Notification preferences update failed', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('error', 'Failed to update preferences. Please try again.');
        }
    }

    /**
     * Show profile completion guide.
     */
    public function completion()
    {
        $user = auth()->user();
        $completion = $this->profileService->getProfileCompletionStatus($user);
        $suggestions = $this->profileService->getProfileSuggestions($user);

        return view('profile.completion', compact('user', 'completion', 'suggestions'));
    }

    /**
     * Export user data (GDPR compliance).
     */
    public function export()
    {
        try {
            $user = auth()->user();
            $data = $this->profileService->exportUserData($user);
            
            $filename = 'profile_data_' . $user->id . '_' . now()->format('Y-m-d_H-i-s') . '.json';
            
            return response()->json($data, 200, [
                'Content-Type' => 'application/json',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]);

        } catch (\Exception $e) {
            Log::error('Data export failed', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('error', 'Failed to export data. Please try again.');
        }
    }

    /**
     * Show account deletion form.
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
     * Delete the user's account.
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'password' => ['required', 'current_password'],
            'confirmation' => ['required', 'accepted'],
        ]);

        try {
            $user = auth()->user();
            
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
                'user_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('error', 'Failed to delete account. Please contact support if this persists.');
        }
    }

    /**
     * Get completion status as JSON for AJAX requests.
     */
    public function completionStatus()
    {
        $user = auth()->user();
        $completion = $this->profileService->getProfileCompletionStatus($user);
        $suggestions = $this->profileService->getProfileSuggestions($user);

        return response()->json([
            'completion' => $completion,
            'suggestions' => $suggestions,
        ]);
    }

    /**
     * Get profile activity summary as JSON.
     */
    public function activitySummary()
    {
        $user = auth()->user();
        $summary = $this->profileService->getUserActivitySummary($user);

        return response()->json($summary);
    }
}