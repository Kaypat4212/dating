<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AccountController extends Controller
{
    /**
     * Show account settings page.
     */
    public function show(Request $request): \Illuminate\View\View
    {
        return view('account.settings', ['user' => $request->user()]);
    }

    /**
     * GDPR data export — downloads a JSON file with all personal data.
     */
    public function export(Request $request): Response
    {
        $user = $request->user()->load([
            'profile',
            'photos',
            'interests',
            'likesGiven',
            'matches',
            'conversations',
            'premiumPayments',
            'blocks',
        ]);

        $data = [
            'account' => [
                'name'        => $user->name,
                'email'       => $user->email,
                'username'    => $user->username,
                'gender'      => $user->gender,
                'seeking'     => $user->seeking,
                'dob'         => $user->date_of_birth?->toDateString(),
                'created_at'  => $user->created_at->toIso8601String(),
                'is_premium'  => $user->isPremiumActive(),
            ],
            'profile'  => $user->profile?->toArray(),
            'photos'   => $user->photos->map(fn($p) => ['url' => $p->url, 'created_at' => $p->created_at])->toArray(),
            'interests' => $user->interests->pluck('name')->toArray(),
            'likes_given' => $user->likesGiven->map(fn($l) => ['liked_user_id' => $l->liked_user_id, 'created_at' => $l->created_at])->toArray(),
            'payments' => $user->premiumPayments->map(fn($p) => ['plan' => $p->plan, 'status' => $p->status, 'created_at' => $p->created_at])->toArray(),
        ];

        $filename = 'heartsconnect-data-' . $user->id . '-' . now()->format('Ymd') . '.json';

        return response(json_encode($data, JSON_PRETTY_PRINT), 200, [
            'Content-Type'        => 'application/json',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Delete the user's account and all associated data.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'string'],
        ]);

        $user = $request->user();

        if (! Hash::check($request->password, $user->password)) {
            return back()->withErrors(['password' => 'The password you entered is incorrect.']);
        }

        // Delete profile photos from storage
        foreach ($user->photos as $photo) {
            Storage::disk('public')->delete('photos/' . $photo->filename);
            Storage::disk('public')->delete('photos/thumbnails/' . $photo->filename);
        }

        Auth::logout();

        $user->delete(); // Cascade deletes handled by DB foreign keys with onDelete('cascade')

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'Your account has been permanently deleted.');
    }

    /**
     * Pause / hide profile from discovery.
     */
    public function pause(Request $request): RedirectResponse
    {
        $user = $request->user();
        $profile = $user->profile;

        $profile->update(['is_paused' => ! $profile->is_paused]);

        $msg = $profile->is_paused
            ? 'Your profile is now hidden from discovery.'
            : 'Your profile is visible in discovery again.';

        return back()->with('success', $msg);
    }

    /**
     * Toggle "hide last seen" — available to any active Premium plan.
     */
    public function toggleLastSeen(Request $request): RedirectResponse
    {
        $user = $request->user();

        if (! $user->isPremiumActive()) {
            return back()->withErrors(['last_seen' => 'This feature requires an active Premium plan.']);
        }

        $user->update(['hide_last_seen' => ! $user->hide_last_seen]);

        return back()->with(
            'success',
            $user->hide_last_seen
                ? 'Your last seen is now hidden from other users.'
                : 'Your last seen is now visible to matches.'
        );
    }

    /**
     * Toggle photo privacy — blur photos for users who aren't matched (Premium).
     */
    public function togglePrivatePhotos(Request $request): RedirectResponse
    {
        $user = $request->user();

        if (! $user->isPremiumActive()) {
            return back()->withErrors(['private_photos' => 'Photo privacy requires an active Premium plan.']);
        }

        $profile = $user->profile()->firstOrCreate(['user_id' => $user->id]);
        $profile->update(['private_photos' => ! $profile->private_photos]);

        return back()->with(
            'success',
            $profile->private_photos
                ? 'Photos are now private — only your matches can see them clearly.'
                : 'Your photos are now publicly visible.'
        );
    }

    /**
     * Toggle read receipts — Premium feature.
     * When disabled, the user's read_at is still stamped but never broadcast to senders.
     */
    public function toggleReadReceipts(Request $request): RedirectResponse
    {
        $user = $request->user();

        if (! $user->isPremiumActive()) {
            return back()->withErrors(['read_receipts' => 'Read receipts require an active Premium plan.']);
        }

        $user->update(['read_receipts_enabled' => ! ($user->read_receipts_enabled ?? true)]);

        return back()->with(
            'success',
            ($user->read_receipts_enabled ?? true)
                ? 'Read receipts are now enabled — your matches can see when you\'ve read their messages.'
                : 'Read receipts are now disabled — your matches will only see grey ticks.'
        );
    }

    /**
     * View the list of users blocked by the authenticated user.
     */
    public function blockedUsers(Request $request): \Illuminate\View\View
    {
        $blocks = $request->user()->blocks()->with('blocked.primaryPhoto')->latest('id')->paginate(24);
        return view('account.blocked', compact('blocks'));
    }

    /**
     * Save (or update) the user's personal secret word for password recovery.
     */
    public function saveSecretWord(Request $request): RedirectResponse
    {
        $request->validate([
            'secret_word' => ['required', 'string', 'min:3', 'max:100'],
        ]);

        $request->user()->update([
            'secret_word' => Hash::make($request->secret_word),
        ]);

        return back()->with('success', 'Secret word saved. You can now use it to reset your password.');
    }

    /**
     * Save the user's per-notification email preference toggles.
     */
    public function updateNotificationPreferences(Request $request): RedirectResponse
    {
        $user  = $request->user();
        $prefs = $user->preferences()->firstOrCreate(['user_id' => $user->id]);

        $prefs->update([
            'email_new_message'     => $request->boolean('email_new_message'),
            'email_new_match'       => $request->boolean('email_new_match'),
            'email_profile_liked'   => $request->boolean('email_profile_liked'),
            'email_wave_received'   => $request->boolean('email_wave_received'),
            'email_travel_interest' => $request->boolean('email_travel_interest'),
            'email_login_alert'     => $request->boolean('email_login_alert'),
        ]);

        return back()->with('success', 'Notification preferences saved.');
    }

    // ─────────────────────────────────────────────────────────
    // 2FA / TOTP
    // ─────────────────────────────────────────────────────────

    /**
     * Return a JSON object with the TOTP setup data (QR svg + secret).
     * Called via fetch() before the user has confirmed the code.
     */
    public function totpSetup(Request $request): \Illuminate\Http\JsonResponse
    {
        $user   = $request->user();
        $g2fa   = new \PragmaRX\Google2FA\Google2FA();
        $secret = $user->totp_secret ?? $g2fa->generateSecretKey();

        // Temporarily stash the generated secret in the session (not saved until confirmed)
        session(['totp_pending_secret' => $secret]);

        $qrUrl = $g2fa->getQRCodeUrl(
            config('app.name'),
            $user->email,
            $secret
        );

        // Use a simple backend QR renderer via Google2FAQRCode
        $renderer = new \PragmaRX\Google2FAQRCode\Google2FA();
        $qrSvg    = $renderer->getQRCodeInline(
            config('app.name'),
            $user->email,
            $secret,
            200
        );

        return response()->json([
            'secret' => $secret,
            'qr'     => $qrSvg,
        ]);
    }

    /**
     * Confirm and enable TOTP 2FA after the user has scanned the QR code
     * and entered a valid one-time code.
     */
    public function totpEnable(Request $request): RedirectResponse
    {
        $request->validate(['totp_code' => ['required', 'digits:6']]);

        $secret = session('totp_pending_secret');
        if (! $secret) {
            return back()->withErrors(['totp_code' => 'Session expired. Please start the 2FA setup again.']);
        }

        $g2fa = new \PragmaRX\Google2FA\Google2FA();
        if (! $g2fa->verifyKey($secret, $request->totp_code)) {
            return back()->withErrors(['totp_code' => 'Invalid code. Please try again.']);
        }

        $request->user()->forceFill(['totp_secret' => $secret])->save();
        session()->forget('totp_pending_secret');

        return back()->with('success', '2FA enabled! Your account is now protected by an authenticator app.');
    }

    /**
     * Disable TOTP 2FA after the user confirms with their current password.
     */
    public function totpDisable(Request $request): RedirectResponse
    {
        $request->validate(['password' => ['required', 'current_password']]);

        $request->user()->forceFill([
            'totp_secret'         => null,
            'totp_recovery_codes' => null,
        ])->save();

        return back()->with('success', '2FA has been disabled.');
    }
}

