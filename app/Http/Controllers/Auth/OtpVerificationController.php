<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Mail\OtpVerificationMail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class OtpVerificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function show(): View|RedirectResponse
    {
        $user = Auth::user();
        
        // If already verified, redirect to dashboard
        if ($user->hasVerifiedEmail()) {
            \Log::info('User already verified, redirecting to dashboard: ' . $user->email);
            return redirect()->route('dashboard');
        }
        
        $needsNewOtp = !$user->otp_code || 
                    !$user->otp_expires_at || 
                    $user->otp_expires_at->isPast();
        
        if ($needsNewOtp) {
            \Log::info('Generating new OTP for user: ' . $user->email);
            $user->generateAndSendOtp();
        } else {
            \Log::info('Valid OTP already exists for user: ' . $user->email . ' - Code: ' . $user->otp_code);
        }
        
        return view('auth.verify-otp');
    }

    /**
     * Handle OTP verification
     */
    public function verify(Request $request): RedirectResponse
    {
        $request->validate([
            'otp' => 'required|string|size:6',
        ]);

        $user = Auth::user();

        \Log::info('OTP verification attempt for: ' . $user->email . ' - Input: ' . $request->otp . ' - Database: ' . $user->otp_code);

        if (!$user->verifyOtp($request->otp)) {
            \Log::warning('OTP verification failed for: ' . $user->email);
            return back()->withErrors([
                'otp' => 'Invalid or expired OTP code.'
            ])->withInput();
        }

        // Mark user as verified and clear OTP
        $user->markEmailAsVerified();
        $user->clearOtp();

        \Log::info('User email verified successfully: ' . $user->email);

        return redirect()->route('dashboard')->with('success', 'Email verified successfully!');
    }

    public function resend(): RedirectResponse
    {
        $user = Auth::user();
        
        if ($user->hasVerifiedEmail()) {
            return redirect()->route('dashboard');
        }

        \Log::info('OTP resend requested for user: ' . $user->email);

        // Always generate new OTP for resend
        if ($user->generateAndSendOtp()) {
            return back()->with('status', 'A new verification code has been sent to your email address.');
        } else {
            return back()->withErrors(['otp' => 'Failed to send verification code. Please try again.']);
        }
    }
}