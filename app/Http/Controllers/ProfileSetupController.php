<?php

namespace App\Http\Controllers;

use App\Helpers\CountryHelper;
use App\Models\Interest;
use App\Models\UserPreference;
use App\Notifications\ProfileCompleteNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileSetupController extends Controller
{
    public const TOTAL_STEPS = 5;

    public function index(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if ($user->profile_complete) {
            return redirect()->route('dashboard');
        }

        $step = max(1, (int) $user->onboarding_step);
        $step = min($step, self::TOTAL_STEPS);

        return redirect()->route('setup.step', ['step' => $step]);
    }

    public function show(Request $request, int $step): View|RedirectResponse
    {
        $user = $request->user();

        if ($user->profile_complete) {
            return redirect()->route('dashboard');
        }

        if ($step < 1 || $step > self::TOTAL_STEPS) {
            return redirect()->route('setup.step', ['step' => 1]);
        }

        $user->load('profile');

        $data = [
            'step'    => $step,
            'total'   => self::TOTAL_STEPS,
            'user'    => $user,
            'profile' => $user->profile,
        ];

        if ($step === 3) {
            $data['photos'] = $user->photos()->orderByDesc('is_primary')->orderBy('id')->get();
        }

        if ($step === 4) {
            $data['preference']         = UserPreference::firstOrNew(['user_id' => $user->id]);
            $data['is_premium']         = (bool) $user->is_premium;
            $data['location_updates']   = (int) ($user->profile?->location_updates_count ?? 0);
        }

        if ($step === 5) {
            $data['interests'] = Interest::orderBy('name')->get();
            $data['selected']  = $user->profile
                ? $user->profile->interests()->pluck('interests.id')->toArray()
                : [];
        }

        return view("onboarding.step{$step}", $data);
    }

    public function store(Request $request, int $step): RedirectResponse
    {
        $user = $request->user();

        match ($step) {
            1 => $this->saveStep1($request, $user),
            2 => $this->saveStep2($request, $user),
            3 => $this->saveStep3($request, $user),
            4 => $this->saveStep4($request, $user),
            5 => $this->saveStep5($request, $user),
        };

        $user->update(['onboarding_step' => max($user->onboarding_step, $step)]);

        if ($step >= self::TOTAL_STEPS) {
            $user->update(['profile_complete' => true]);
            
            // Send congratulatory notification for completing profile
            try {
                $user->notify(new ProfileCompleteNotification());
            } catch (\Throwable) {
                // Notification failed but profile is complete - continue
            }
            
            return redirect()->route('dashboard')->with('success', 'Welcome to HeartsConnect! ❤️');
        }

        return redirect()->route('setup.step', ['step' => $step + 1]);
    }

    private function saveStep1(Request $request, $user): void
    {
        $data = $request->validate([
            'gender'        => 'required|in:male,female,non_binary,other',
            'seeking'       => 'required|in:male,female,everyone',
            'date_of_birth' => 'required|date|before:-18 years',
        ]);
        $user->update($data);
    }

    private function saveStep2(Request $request, $user): void
    {
        $data = $request->validate([
            'headline'          => 'nullable|max:120',
            'bio'               => 'nullable|max:1000',
            'height_cm'         => 'nullable|integer|between:100,250',
            'body_type'         => 'nullable|in:slim,athletic,average,curvy,large,prefer_not_say',
            'ethnicity'         => 'nullable|max:60',
            'religion'          => 'nullable|max:60',
            'education'         => 'nullable|in:high_school,some_college,bachelors,masters,phd,trade_school,other',
            'occupation'        => 'nullable|max:100',
            'relationship_goal' => 'nullable|in:casual,serious,friendship,marriage,open',
            'smoking'           => 'nullable|in:never,occasionally,regularly,trying_to_quit',
            'drinking'          => 'nullable|in:never,socially,regularly',
            'has_children'      => 'nullable|boolean',
            'wants_children'    => 'nullable|in:yes,no,open,not_sure',
        ]);

        $user->profile()->updateOrCreate(['user_id' => $user->id], $data);
    }

    private function saveStep3(Request $request, $user): void
    {
        // Photo upload is handled separately via PhotoController
        // This step just advances after the user uploads at least one photo
    }

    private function saveStep4(Request $request, $user): void
    {
        $data = $request->validate([
            'city'            => 'nullable|max:100',
            'state'           => 'nullable|max:100',
            'country'         => 'nullable|max:100',
            'latitude'        => 'nullable|numeric|between:-90,90',
            'longitude'       => 'nullable|numeric|between:-180,180',
            'min_age'         => 'nullable|integer|between:18,100',
            'max_age'         => 'nullable|integer|between:18,100',
            'max_distance_km' => 'nullable|integer|between:1,500',
            'seeking_gender'  => 'nullable|in:male,female,everyone',
            'preferred_state' => 'nullable|max:100',
            'body_types'      => 'nullable|array',
            'body_types.*'    => 'in:slim,athletic,average,curvy,large',
            'show_online_only'=> 'nullable|boolean',
        ]);

        $isPremium = (bool) $user->is_premium;
        $profile   = $user->profile()->firstOrCreate(['user_id' => $user->id]);

        $locationChanged = ($data['city']      ?? null) !== $profile->city
            || ($data['state']     ?? null) !== $profile->state
            || ($data['country']   ?? null) !== $profile->country
            || ($data['latitude']  ?? null) != $profile->latitude
            || ($data['longitude'] ?? null) != $profile->longitude;

        // Free users may only update their location twice. Premium users: unlimited.
        if ($locationChanged && ! $isPremium && $profile->location_updates_count >= 2) {
            // Silently skip location update for free users who hit the cap
            abort(403, 'Free accounts can only update their location twice. Upgrade to Premium for unlimited location updates.');
        }

        $updates = [
            'city'      => $data['city']      ?? null,
            'state'     => $data['state']     ?? null,
            'country'   => CountryHelper::resolve($data['country'] ?? null),
            'latitude'  => $data['latitude']  ?? null,
            'longitude' => $data['longitude'] ?? null,
        ];

        if ($locationChanged) {
            $updates['location_updates_count'] = $isPremium
                ? $profile->location_updates_count   // don't increment for premium
                : $profile->location_updates_count + 1;
        }

        $profile->update($updates);

        UserPreference::updateOrCreate(['user_id' => $user->id], [
            'min_age'          => $data['min_age'] ?? 18,
            'max_age'          => $data['max_age'] ?? 50,
            'max_distance_km'  => isset($data['max_distance_km']) && $data['max_distance_km'] !== '' && $data['max_distance_km'] !== null
                ? (int) $data['max_distance_km']
                : null,
            'seeking_gender'   => $data['seeking_gender'] ?? 'everyone',
            'preferred_state'  => $data['preferred_state'] ?? null,
            'body_types'       => $data['body_types'] ?? [],
            'show_online_only' => ! empty($data['show_online_only']),
        ]);
    }

    private function saveStep5(Request $request, $user): void
    {
        $interestIds = $request->input('interests', []);
        $profile = $user->profile()->firstOrCreate(['user_id' => $user->id]);
        $profile->interests()->sync(array_slice($interestIds, 0, 20));
    }

    /** Show the preferences edit page for users who already completed onboarding. */
    public function editPreferences(Request $request): View
    {
        $user = $request->user();
        $user->load('profile');
        return view('onboarding.step4', [
            'step'             => 4,
            'total'            => self::TOTAL_STEPS,
            'user'             => $user,
            'profile'          => $user->profile,
            'preference'       => UserPreference::firstOrNew(['user_id' => $user->id]),
            'is_edit'          => true,
            'is_premium'       => (bool) $user->is_premium,
            'location_updates' => (int) ($user->profile?->location_updates_count ?? 0),
        ]);
    }

    /** Save preferences for users who already completed onboarding. */
    public function updatePreferences(Request $request): RedirectResponse
    {
        $user      = $request->user();
        $isPremium = (bool) $user->is_premium;
        $profile   = $user->profile()->firstOrCreate(['user_id' => $user->id]);

        // Pre-check: free users who used both location updates cannot change location
        $newCity    = $request->input('city');
        $newCountry = $request->input('country');
        $newLat     = $request->input('latitude');
        $newLng     = $request->input('longitude');

        $newState = $request->input('state');

        $locationChanged = $newCity    !== $profile->city
            || $newState   !== $profile->state
            || $newCountry !== $profile->country
            || $newLat     != $profile->latitude
            || $newLng     != $profile->longitude;

        if ($locationChanged && ! $isPremium && $profile->location_updates_count >= 2) {
            return redirect()->route('preferences.edit')
                ->withErrors(['location' => 'Free accounts can only update their location twice. Upgrade to Premium for unlimited updates.'])
                ->withInput();
        }

        $this->saveStep4($request, $user);
        return redirect()->route('preferences.edit')->with('success', 'Preferences updated! Your swipe deck will refresh.');
    }
}
