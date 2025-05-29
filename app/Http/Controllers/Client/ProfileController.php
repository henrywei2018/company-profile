<?php
// File: app/Http/Controllers/Client/ProfileController.php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use App\Services\ClientAccessService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    protected DashboardService $dashboardService;
    protected ClientAccessService $clientAccessService;

    public function __construct(
        DashboardService $dashboardService,
        ClientAccessService $clientAccessService
    ) {
        $this->dashboardService = $dashboardService;
        $this->clientAccessService = $clientAccessService;
    }

    /**
     * Display the client's profile.
     */
    public function show()
    {
        $user = auth()->user();
        
        // Get profile completion status
        $profileCompletion = $this->getProfileCompletionStatus($user);
        
        // Get client statistics
        $statistics = $this->clientAccessService->getClientStatistics($user);
        
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
        
        // Check if email changed
        if ($user->email !== $validated['email']) {
            $validated['email_verified_at'] = null;
        }
        
        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            
            // Store new avatar
            $path = $request->file('avatar')->store('avatars', 'public');
            $validated['avatar'] = $path;
        }
        
        // Update user
        $user->update($validated);
        
        // Clear dashboard cache
        $this->dashboardService->clearCache($user);
        
        // If email changed, send verification notification
        if ($user->email_verified_at === null) {
            $user->sendEmailVerificationNotification();
            
            return redirect()->route('client.profile.show')
                ->with('success', 'Profile updated successfully! Please verify your new email address.');
        }
        
        return redirect()->route('client.profile.show')
            ->with('success', 'Profile updated successfully!');
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
        
        // Update password
        $user->update([
            'password' => Hash::make($validated['password']),
        ]);
        
        // Clear dashboard cache
        $this->dashboardService->clearCache($user);
        
        return redirect()->route('client.profile.show')
            ->with('success', 'Password changed successfully!');
    }

    /**
     * Show client preferences form.
     */
    public function preferences()
    {
        $user = auth()->user();
        
        return view('client.profile.preferences', compact('user'));
    }

    /**
     * Update client preferences.
     */
    public function updatePreferences(Request $request)
    {
        $user = auth()->user();
        
        $validated = $request->validate([
            'email_notifications' => 'boolean',
            'project_update_notifications' => 'boolean',
            'quotation_update_notifications' => 'boolean',
            'message_reply_notifications' => 'boolean',
            'deadline_alert_notifications' => 'boolean',
            'marketing_notifications' => 'boolean',
            'sms_notifications' => 'boolean',
            'newsletter_subscription' => 'boolean',
        ]);
        
        // Update preferences
        $user->update([
            'email_notifications' => $validated['email_notifications'] ?? false,
            'project_update_notifications' => $validated['project_update_notifications'] ?? false,
            'quotation_update_notifications' => $validated['quotation_update_notifications'] ?? false,
            'message_reply_notifications' => $validated['message_reply_notifications'] ?? false,
            'deadline_alert_notifications' => $validated['deadline_alert_notifications'] ?? false,
            'marketing_notifications' => $validated['marketing_notifications'] ?? false,
            'sms_notifications' => $validated['sms_notifications'] ?? false,
            'newsletter_subscription' => $validated['newsletter_subscription'] ?? false,
        ]);
        
        return redirect()->route('client.profile.preferences')
            ->with('success', 'Preferences updated successfully!');
    }

    /**
     * Show privacy settings form.
     */
    public function privacy()
    {
        $user = auth()->user();
        
        return view('client.profile.privacy', compact('user'));
    }

    /**
     * Update privacy settings.
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
        ]);
        
        // Update privacy settings
        $user->update([
            'profile_visibility' => $validated['profile_visibility'],
            'show_email' => $validated['show_email'] ?? false,
            'show_phone' => $validated['show_phone'] ?? false,
            'show_company' => $validated['show_company'] ?? false,
            'allow_testimonial_display' => $validated['allow_testimonial_display'] ?? true,
            'allow_project_showcase' => $validated['allow_project_showcase'] ?? true,
        ]);
        
        return redirect()->route('client.profile.privacy')
            ->with('success', 'Privacy settings updated successfully!');
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
        ]);
        
        // Clear dashboard cache
        $this->dashboardService->clearCache($user);
        
        // Delete avatar if exists
        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }
        
        // Logout user
        Auth::logout();
        
        // Delete user (this will cascade delete related data based on your model relationships)
        $user->delete();
        
        return redirect()->route('login')
            ->with('success', 'Your account has been successfully deleted.');
    }

    /**
     * Export user data.
     */
    public function exportData()
    {
        $user = auth()->user();
        
        // Get all user data
        $userData = [
            'profile' => $user->toArray(),
            'projects' => $this->clientAccessService->getClientProjects($user)->get()->toArray(),
            'quotations' => $this->clientAccessService->getClientQuotations($user)->get()->toArray(),
            'messages' => $this->clientAccessService->getClientMessages($user)->get()->toArray(),
            'testimonials' => \App\Models\Testimonial::whereHas('project', function($query) use ($user) {
                $query->where('client_id', $user->id);
            })->get()->toArray(),
        ];
        
        $filename = 'user_data_' . $user->id . '_' . now()->format('Y-m-d_H-i-s') . '.json';
        
        return response()->json($userData)
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->header('Content-Type', 'application/json');
    }

    /**
     * Get activity log.
     */
    public function activity(Request $request)
    {
        $user = auth()->user();
        
        // Get recent activities
        $dashboardData = $this->dashboardService->getDashboardData($user);
        $activities = $dashboardData['recent_activities'] ?? [];
        
        // Filter by type if specified
        if ($request->filled('type')) {
            $activities = array_filter($activities, function($activity) use ($request) {
                return $activity['type'] === $request->type;
            });
        }
        
        // Paginate activities (manual pagination for array)
        $page = $request->get('page', 1);
        $perPage = 15;
        $total = count($activities);
        $activities = array_slice($activities, ($page - 1) * $perPage, $perPage);
        
        $pagination = [
            'current_page' => $page,
            'per_page' => $perPage,
            'total' => $total,
            'last_page' => ceil($total / $perPage),
        ];
        
        return view('client.profile.activity', compact('activities', 'pagination'));
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
}