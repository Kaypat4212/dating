<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Referral;
use App\Models\User;
use App\Notifications\WelcomeNotification;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        // Pre-fill referral code from query string and persist in session
        if (request()->filled('ref') && !session('referral_code')) {
            session(['referral_code' => request('ref')]);
        }

        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Resolve referrer from session (set by /ref/{code} or ?ref= query param)
        $referralCode = session('referral_code') ?? $request->input('ref');
        $referrer = $referralCode
            ? User::where('referral_code', $referralCode)->first()
            : null;

        $user = User::create([
            'name'            => $request->name,
            'email'           => $request->email,
            'password'        => Hash::make($request->password),
            'registration_ip' => $request->ip(),
            'referral_code'   => strtoupper(Str::random(8)),
            'referred_by'     => $referrer?->id,
        ]);

        // Record the referral relationship
        if ($referrer) {
            Referral::create([
                'referrer_id' => $referrer->id,
                'referred_id' => $user->id,
            ]);
            session()->forget('referral_code');
        }

        // Auto-verify email so users can access the app immediately.
        $user->markEmailAsVerified();

        event(new Registered($user));

        Auth::login($user);

        // Send a welcome email — swallow failures so registration always succeeds.
        try { $user->notify(new WelcomeNotification()); } catch (\Throwable) {}

        // Notify admin via Telegram about the new registration.
        try { app(\App\Services\TelegramNotificationService::class)->notifyNewRegistration($user->id); } catch (\Throwable) {}

        return redirect(route('setup.step', ['step' => 1]))->with('just_registered', true);
    }
}
