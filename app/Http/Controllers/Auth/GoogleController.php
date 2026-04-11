<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    /**
     * Redirect to Google's OAuth consent screen.
     */
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle the OAuth callback from Google.
     * Creates the user if they don't exist, then logs them in.
     */
    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Throwable) {
            return redirect()->route('login')->withErrors(['email' => 'Google sign-in failed. Please try again.']);
        }

        // Find existing account by google_id or email
        $user = User::where('google_id', $googleUser->getId())
            ->orWhere('email', $googleUser->getEmail())
            ->first();

        if ($user) {
            // Update google_id if missing
            if (empty($user->google_id)) {
                $user->forceFill(['google_id' => $googleUser->getId()])->save();
            }

            if ($user->is_banned) {
                return redirect()->route('login')->withErrors(['email' => 'Your account has been suspended.']);
            }
        } else {
            // Create a new user
            $user = User::create([
                'name'              => $googleUser->getName() ?? 'Google User',
                'email'             => $googleUser->getEmail(),
                'google_id'         => $googleUser->getId(),
                'email_verified_at' => now(),
                'password'          => Hash::make(Str::random(32)),
                'registration_ip'   => request()->ip(),
                'referral_code'     => strtoupper(Str::random(8)),
            ]);

            $user->assignRole('user');

            // Handle referral from session
            $referralCode = session('referral_code');
            if ($referralCode) {
                $referrer = User::where('referral_code', $referralCode)->first();
                if ($referrer && $referrer->id !== $user->id) {
                    \App\Models\Referral::create([
                        'referrer_id' => $referrer->id,
                        'referred_id' => $user->id,
                    ]);
                }
                session()->forget('referral_code');
            }
        }

        Auth::login($user, true);

        // Redirect to onboarding if profile is incomplete
        if (! $user->profile_complete) {
            return redirect()->route('setup.step', ['step' => 1]);
        }

        return redirect()->intended(route('dashboard'));
    }
}
