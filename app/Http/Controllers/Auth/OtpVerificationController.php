<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Notifications\EmailOtpNotification;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OtpVerificationController extends Controller
{
    /** Show the OTP entry page (or redirect if already verified). */
    public function show(Request $request): RedirectResponse|View
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(route('dashboard'));
        }

        // Auto-send an OTP if one hasn't been sent yet or it's expired
        $user = $request->user();
        if (!$user->email_otp || !$user->email_otp_expires_at || now()->gt($user->email_otp_expires_at)) {
            $this->sendOtp($user);
        }

        return view('auth.verify-otp');
    }

    /** Resend a fresh OTP. */
    public function resend(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return redirect()->intended(route('dashboard'));
        }

        $this->sendOtp($user);

        return back()->with('status', 'otp-sent');
    }

    /** Verify the submitted OTP. */
    public function verify(Request $request): RedirectResponse
    {
        $request->validate([
            'otp' => ['required', 'string', 'size:6'],
        ]);

        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return redirect()->intended(route('dashboard'));
        }

        // Wrong OTP
        if ($user->email_otp !== $request->input('otp')) {
            return back()->withErrors(['otp' => 'The code you entered is incorrect.']);
        }

        // Expired OTP
        if (!$user->email_otp_expires_at || now()->gt($user->email_otp_expires_at)) {
            return back()->withErrors(['otp' => 'This code has expired. Please request a new one.']);
        }

        // Mark verified and clear OTP fields
        $user->forceFill([
            'email_verified_at' => now(),
            'email_otp'         => null,
            'email_otp_expires_at' => null,
        ])->save();

        event(new Verified($user));

        return redirect()->intended(route('dashboard') . '?verified=1');
    }

    /** Generate and send a 6-digit OTP to the user. */
    private function sendOtp(\App\Models\User $user): void
    {
        $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $user->forceFill([
            'email_otp'            => $otp,
            'email_otp_expires_at' => now()->addMinutes(15),
        ])->save();

        $user->notify(new EmailOtpNotification($otp));
    }
}
