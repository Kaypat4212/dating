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
}
