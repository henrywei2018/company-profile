<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
|
| These routes handle user authentication for both admin and client users.
| The routes are organized by authentication state (guest vs authenticated).
|
*/

// Guest-only routes (users who are not logged in)
Route::middleware(['guest'])->group(function () {
    
    // Registration routes
    Route::get('register', [RegisteredUserController::class, 'create'])
        ->name('register');
    Route::post('register', [RegisteredUserController::class, 'store'])
        ->middleware('throttle:5,1'); // Rate limit registration attempts

    // Login routes  
    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store'])
        ->middleware('throttle:5,1'); // Rate limit login attempts

    // Password reset routes
    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('password.request');
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('password.email')
        ->middleware('throttle:3,1'); // Rate limit password reset requests

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset');
    Route::post('reset-password', [NewPasswordController::class, 'store'])
        ->name('password.store')
        ->middleware('throttle:3,1');
});

// Authenticated user routes
Route::middleware(['auth'])->group(function () {
    
    // Email verification routes
    Route::get('verify-email', EmailVerificationPromptController::class)
        ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    // Password confirmation routes
    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');
    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store'])
        ->middleware('throttle:5,1');

    // Password update route
    Route::put('password', [PasswordController::class, 'update'])
        ->name('password.update')
        ->middleware('throttle:3,1');

    // Logout route
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
        
    // Additional authentication routes for enhanced security
    Route::get('session/extend', function() {
        session(['last_activity_time' => time()]);
        return response()->json(['status' => 'extended']);
    })->name('session.extend');
    
    // Check authentication status (for AJAX)
    Route::get('auth/check', function() {
        return response()->json([
            'authenticated' => true,
            'user' => auth()->user()->only(['id', 'name', 'email']),
            'roles' => auth()->user()->roles->pluck('name'),
            'permissions' => auth()->user()->getAllPermissions()->pluck('name'),
        ]);
    })->name('auth.check');
});

// Role-based post-authentication redirects (used by AuthenticatedSessionController)
Route::middleware(['auth', 'verified'])->group(function () {
    
    // Admin dashboard redirect
    Route::get('auth/admin-redirect', function() {
        $user = auth()->user();
        
        if (!$user->hasAnyRole(['super-admin', 'admin', 'manager', 'editor'])) {
            abort(403, 'Admin access required.');
        }
        
        return redirect()->route('admin.dashboard');
    })->name('auth.admin-redirect');
    
    // Client dashboard redirect  
    Route::get('auth/client-redirect', function() {
        $user = auth()->user();
        
        if (!$user->hasRole('client') && !$user->hasAnyRole(['super-admin', 'admin'])) {
            abort(403, 'Client access required.');
        }
        
        return redirect()->route('client.dashboard');
    })->name('auth.client-redirect');
    
    // Smart redirect based on user role
    Route::get('auth/dashboard', function() {
        $user = auth()->user();
        
        // Check role hierarchy and redirect accordingly
        if ($user->hasRole('super-admin')) {
            return redirect()->route('admin.dashboard');
        }
        
        if ($user->hasRole('admin')) {  
            return redirect()->route('admin.dashboard');
        }
        
        if ($user->hasRole('manager')) {
            return redirect()->route('admin.dashboard');
        }
        
        if ($user->hasRole('editor')) {
            return redirect()->route('admin.dashboard');
        }
        
        if ($user->hasRole('client')) {
            return redirect()->route('client.dashboard');
        }
        
        // Fallback based on permissions
        if ($user->can('view dashboard') || $user->can('access admin')) {
            return redirect()->route('admin.dashboard');
        }
        
        // Default to client dashboard
        return redirect()->route('client.dashboard');
        
    })->name('dashboard');
});

// Account status routes
Route::middleware(['auth'])->group(function () {
    
    // Check if account needs verification (for clients)
    Route::get('account/verification-status', function() {
        $user = auth()->user();
        
        $status = [
            'email_verified' => !is_null($user->email_verified_at),
            'account_active' => $user->is_active,
            'requires_verification' => false,
        ];
        
        // Check if client needs admin verification
        if ($user->hasRole('client') && property_exists($user, 'is_verified')) {
            $status['requires_verification'] = !$user->is_verified;
        }
        
        return response()->json($status);
    })->name('account.verification-status');
    
    // Account lockout information
    Route::get('account/lockout-status', function() {
        $user = auth()->user();
        
        $status = [
            'is_locked' => !is_null($user->locked_at ?? null),
            'locked_until' => $user->locked_at ?? null,
            'failed_attempts' => $user->failed_login_attempts ?? 0,
            'max_attempts' => 5,
        ];
        
        return response()->json($status);
    })->name('account.lockout-status');
});

// Development/Testing routes (only in local environment)
if (app()->environment('local', 'testing')) {
    Route::middleware(['auth'])->group(function () {
        
        // Test role assignment (for development)
        Route::post('dev/assign-role/{role}', function($role) {
            $user = auth()->user();
            
            $validRoles = ['super-admin', 'admin', 'manager', 'editor', 'client'];
            if (!in_array($role, $validRoles)) {
                abort(400, 'Invalid role');
            }
            
            $user->syncRoles([$role]);
            
            return response()->json([
                'message' => "Role '{$role}' assigned successfully",
                'user_roles' => $user->roles->pluck('name'),
            ]);
        })->name('dev.assign-role');
        
        // Test authentication flow
        Route::get('dev/auth-test', function() {
            $user = auth()->user();
            
            return response()->json([
                'user' => $user->only(['id', 'name', 'email']),
                'roles' => $user->roles->pluck('name'),
                'permissions' => $user->getAllPermissions()->pluck('name'),
                'can_access_admin' => $user->hasAnyRole(['super-admin', 'admin', 'manager', 'editor']),
                'can_access_client' => $user->hasRole('client') || $user->hasAnyRole(['super-admin', 'admin']),
                'is_active' => $user->is_active,
                'email_verified' => !is_null($user->email_verified_at),
            ]);
        })->name('dev.auth-test');
    });
}