<?php
// File: app/Http/Controllers/Client/ProfileController.php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Display the client's profile.
     */
    public function show()
    {
        $user = auth()->user();
        
        return view('client.profile.show', compact('user'));
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
        
        return redirect()->route('client.profile.show')
            ->with('success', 'Password changed successfully!');
    }
}