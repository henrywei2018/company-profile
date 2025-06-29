<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\OtpVerificationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Authentication Routes with Simple Rate Limiting
|--------------------------------------------------------------------------
*/

// Guest routes with rate limiting for security
Route::middleware('guest')->group(function () {
    
    // Login routes - 5 attempts per minute
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store'])
        ->middleware('throttle:5,1');

    // Register routes - 3 attempts per minute (stricter)
    Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('register', [RegisteredUserController::class, 'store'])
        ->middleware('throttle:3,1');

    // Password reset - 3 attempts per minute
    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('password.request');
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('password.email')
        ->middleware('throttle:3,1');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset');
    Route::post('reset-password', [NewPasswordController::class, 'store'])
        ->name('password.store')
        ->middleware('throttle:3,1');
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    
    Route::get('verify-otp', [OtpVerificationController::class, 'show'])
                ->name('verification.otp');
    
    Route::post('verify-otp', [OtpVerificationController::class, 'verify'])
                ->name('verification.otp.verify');
    
    Route::post('resend-otp', [OtpVerificationController::class, 'resend'])
                ->name('verification.otp.resend');

    // Password confirmation
    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');
    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store'])
        ->middleware('throttle:5,1');

    // Password update
    Route::put('password', [PasswordController::class, 'update'])
        ->name('password.update')
        ->middleware('throttle:3,1');

    // Logout
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});