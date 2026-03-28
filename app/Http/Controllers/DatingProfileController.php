<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\IcebreakerAnswer;
use App\Models\Interest;
use App\Models\ProfileView;
use App\Models\SiteSetting;
use App\Models\User;
use App\Models\UserMatch;
use App\Models\UserPreference;
use App\Models\VoicePrompt;
use App\Services\CompatibilityService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class DatingProfileController extends Controller
{
    public function show(Request $request, string $username): View
    {
        $viewer      = $request->user();
        $profileUser = User::with([
            'profile.interests',
            'photos' => fn($q) => $q->where('is_approved', true)->orderBy('sort_order'),
        ])->where('username', $username)->firstOrFail();

        $photos = $profileUser->photos;
        $userId = $profileUser->id;

        // Record profile view (throttle: once per 24h)
        $cacheKey = "pv_{$viewer->id}_{$userId}";
        if ($viewer->id !== $userId && !Cache::has($cacheKey)) {
            ProfileView::create(['viewer_id' => $viewer->id, 'viewed_id' => $userId]);
            $profileUser->profile?->increment('views_count');
            Cache::put($cacheKey, true, now()->addHours(24));

            // Notify the VIEWED user only (never the viewer), and only if they are premium
            if ($profileUser->isPremiumActive()) {
                try { $profileUser->notify(new \App\Notifications\ProfileViewedNotification($viewer)); } catch (\Throwable) {}
            }
        }

        $compat        = app(CompatibilityService::class)->score($viewer, $profileUser);
        $compatibility = $compat;
        $profile       = $profileUser->profile;
        $hasLiked      = $viewer->hasLiked($userId);
        $isMatched     = $viewer->isMatchedWith($userId);
        $iBlockedThem  = \App\Models\Block::where('blocker_id', $viewer->id)->where('blocked_id', $userId)->exists();
        $theyBlockedMe = \App\Models\Block::where('blocker_id', $userId)->where('blocked_id', $viewer->id)->exists();
        $isBlocked     = $iBlockedThem || $theyBlockedMe;

        $conversationId = null;
        if ($isMatched) {
            $match = UserMatch::where(function ($q) use ($viewer, $userId) {
                    $q->where('user1_id', $viewer->id)->where('user2_id', $userId);
                })->orWhere(function ($q) use ($viewer, $userId) {
                    $q->where('user1_id', $userId)->where('user2_id', $viewer->id);
                })->first();

            if ($match) {
                $conversation = Conversation::where('match_id', $match->id)->first();
                $conversationId = $conversation?->id;
            }
        }

        $voicePrompts = VoicePrompt::where('user_id', $userId)
            ->where('show_on_profile', true)
            ->with('question')
            ->get();

        $icebreakerAnswers = IcebreakerAnswer::where('user_id', $userId)
            ->where('show_on_profile', true)
            ->with('question')
            ->get();

        return view('profile.show', compact(
            'profileUser', 'profile', 'photos',
            'compatibility', 'hasLiked', 'isMatched', 'isBlocked', 'iBlockedThem', 'theyBlockedMe', 'conversationId',
            'voicePrompts', 'icebreakerAnswers'
        ));
    }

    public function editDating(Request $request): View
    {
        $user      = $request->user()->load(['profile.interests', 'preferences']);
        $profile   = $user->profile;
        $interests = Interest::orderBy('name')->get();
        return view('profile.edit', compact('user', 'profile', 'interests'));
    }

    public function updateDating(Request $request): RedirectResponse
    {
        $user = $request->user();

        // ── Validate user-level fields ────────────────────────────────────────
        $userData = $request->validate([
            'name'     => 'nullable|string|max:100',
            'username' => [
                'nullable', 'string', 'min:3', 'max:30',
                'regex:/^[a-zA-Z0-9_]+$/',
                \Illuminate\Validation\Rule::unique('users', 'username')->ignore($user->id),
            ],
        ]);

        // ── Validate profile-level fields (map form names → column names) ─────
        // The view sends: tagline, about, education_level, smoking_habit, drinking_habit
        $request->validate([
            'tagline'           => 'nullable|max:120',
            'about'             => 'nullable|max:2000',
            'mood_status'       => 'nullable|max:80',
            'height_cm'         => 'nullable|integer|between:100,250',
            'body_type'         => 'nullable|in:slim,athletic,average,curvy,plus_size,muscular,large,prefer_not_say',
            'ethnicity'         => 'nullable|max:80',
            'religion'          => 'nullable|max:80',
            'education_level'   => 'nullable|in:high_school,some_college,bachelors,masters,phd,trade_school,other',
            'occupation'        => 'nullable|max:100',
            'relationship_goal' => 'nullable|in:casual,long_term,marriage,friendship,unsure,serious,open',
            'smoking_habit'     => 'nullable|in:never,sometimes,occasionally,regularly,trying_to_quit',
            'drinking_habit'    => 'nullable|in:never,socially,regularly',
            'wants_children'    => 'nullable',
            'city'              => 'nullable|max:100',
            'state'             => 'nullable|max:100',
            'country'           => 'nullable|max:100',
            'latitude'          => 'nullable|numeric|between:-90,90',
            'longitude'         => 'nullable|numeric|between:-180,180',
        ]);

        // ── Save user name / username ──────────────────────────────────────────
        $userChanges = array_filter([
            'name'     => $request->input('name') ?: null,
            'username' => $request->input('username') ?: null,
        ]);
        if (!empty($userChanges)) {
            $user->update($userChanges);
        }

        // ── Map form field names to Profile model column names ────────────────
        $profileData = array_filter([
            'headline'          => $request->input('tagline'),
            'bio'               => $request->input('about'),
            'mood_status'       => $request->input('mood_status'),
            'height_cm'         => $request->input('height_cm'),
            'body_type'         => $request->input('body_type'),
            'ethnicity'         => $request->input('ethnicity'),
            'religion'          => $request->input('religion'),
            'education'         => $request->input('education_level'),
            'occupation'        => $request->input('occupation'),
            'relationship_goal' => $request->input('relationship_goal'),
            'smoking'           => $request->input('smoking_habit'),
            'drinking'          => $request->input('drinking_habit'),
            'wants_children'    => $request->has('wants_children') ? (bool) $request->input('wants_children') : false,
            'city'              => $request->input('city'),
            'state'             => $request->input('state'),
            'country'           => $request->input('country'),
            'latitude'          => $request->input('latitude') ?: null,
            'longitude'         => $request->input('longitude') ?: null,
        ], fn($v) => $v !== null && $v !== '');

        // Always persist wants_children (checkbox can be unchecked)
        $profileData['wants_children'] = $request->has('wants_children');

        $profile = $user->profile()->updateOrCreate(['user_id' => $user->id], $profileData);

        if ($request->has('interests')) {
            $profile->interests()->sync(array_slice((array) $request->input('interests', []), 0, 20));
        }

        UserPreference::updateOrCreate(['user_id' => $user->id], [
            'min_age'          => $request->input('min_age', 18),
            'max_age'          => $request->input('max_age', 50),
            'max_distance_km'  => $request->input('max_distance_km', 100),
            'seeking_gender'   => $request->input('seeking_gender', 'everyone'),
        ]);

        return back()->with('success', 'Dating profile updated!');
    }

    public function whoViewedMe(Request $request): View
    {
        $user  = $request->user();
        $views = ProfileView::where('viewed_id', $user->id)
            ->with(['viewer.primaryPhoto'])
            ->orderByDesc('viewed_at')
            ->paginate(24);

        return view('profile.who-viewed', compact('views'));
    }
}
