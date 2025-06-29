<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Mail\OtpVerificationMail; // Add this import
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail; // Add this import
use Illuminate\View\View;

class OtpVerificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the OTP verification form
     */
    public function show(): View
    {
        $user = Auth::user();
        
        // If already verified, redirect to dashboard
        if ($user->hasVerifiedEmail()) {
            return redirect()->route('dashboard');
        }
        
        // If no OTP exists or expired, generate new one
        if (!$user->otp_code || ($user->otp_expires_at && $user->otp_expires_at->isPast())) {
            $user->generateOtp();
            Mail::to($user->email)->send(new OtpVerificationMail($user, $user->otp_code));
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

        if (!$user->verifyOtp($request->otp)) {
            return back()->withErrors([
                'otp' => 'Invalid or expired OTP code.'
            ])->withInput();
        }

        // Mark user as verified
        $user->markEmailAsVerified();
        $user->clearOtp();

        return redirect()->route('dashboard')->with('success', 'Email verified successfully!');
    }

    /**
     * Resend OTP code
     */
    public function resend(): RedirectResponse
    {
        $user = Auth::user();
        
        if ($user->hasVerifiedEmail()) {
            return redirect()->route('dashboard');
        }

        $user->generateOtp();
        
        // IMPORTANT: Make sure OTP is actually generated before sending email
        if ($user->otp_code) {
            Mail::to($user->email)->send(new OtpVerificationMail($user, $user->otp_code));
            return back()->with('status', 'A new verification code has been sent to your email address.');
        } else {
            return back()->withErrors(['otp' => 'Failed to generate OTP code. Please try again.']);
        }
    }
}