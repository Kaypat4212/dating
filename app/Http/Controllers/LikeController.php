<?php

namespace App\Http\Controllers;

use App\Models\Like;
use App\Models\SiteSetting;
use App\Models\User;
use App\Models\UserMatch;
use App\Notifications\FeatureUsageNotification;
use App\Services\ActivityLogger;
use App\Services\EloService;
use App\Notifications\LikeResetNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;

class LikeController extends Controller
{
    public function __construct(private readonly EloService $elo) {}

    public function store(Request $request, User $user): JsonResponse|RedirectResponse
    {
        $sender     = $request->user();
        $receiverId = $user->id;

        // Rate limit: free users 15 likes/day, premium unlimited
        if (!$sender->isPremiumActive()) {
            // Admin restriction check
            if ($sender->likes_restricted) {
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'error'      => 'Your ability to send likes has been restricted. Please contact support.',
                        'restricted' => true,
                    ], 403);
                }
                return redirect()->back()->with('error', 'Your ability to send likes has been restricted. Please contact support.');
            }

            $key   = "likes:{$sender->id}";
            $limit = 15;

            if (RateLimiter::tooManyAttempts($key, $limit)) {
                $resetIn = RateLimiter::availableIn($key);
                $resetAt = now()->addSeconds($resetIn)->toISOString();

                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'error'    => 'You\'ve used all 15 free likes today. Upgrade to Premium for unlimited likes!',
                        'premium'  => true,
                        'reset_at' => $resetAt,
                        'reset_in' => $resetIn,
                    ], 429);
                }
                return redirect()->back()->with('error', 'You\'ve used all 15 free likes today. Upgrade to Premium for unlimited likes!');
            }

            RateLimiter::hit($key, 86400); // 24h decay

            // When user exhausts their last like, queue a reset email (once per cycle)
            if (RateLimiter::attempts($key) >= $limit) {
                $notifyKey = "likes_limit_notified:{$sender->id}";
                if (!Cache::has($notifyKey)) {
                    Cache::put($notifyKey, true, 86400);
                    $resetIn = RateLimiter::availableIn($key);
                    $sender->notify(
                        (new LikeResetNotification)->delay(now()->addSeconds($resetIn))
                    );
                }
            }
        }

        // Guard: can't like yourself
        if ($sender->id === $receiverId) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['error' => 'Cannot like yourself.'], 422);
            }
            return redirect()->back()->with('error', 'You cannot like yourself.');
        }

        // Guard: blocked
        if ($sender->hasBlockedOrIsBlocked($receiverId)) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['error' => 'Action not allowed.'], 403);
            }
            return redirect()->back()->with('error', 'Action not allowed.');
        }

        // Pass action (swipe left) — acknowledge but do record a ProfilePass
        if ($request->input('action') === 'pass') {
            try { $this->elo->onPass($user); } catch (\Throwable) {}
            // Record the pass for Second Chance Queue (silently ignore duplicates)
            try {
                \App\Models\ProfilePass::firstOrCreate([
                    'passer_id' => $sender->id,
                    'passed_id' => $user->id,
                ], [
                    'passed_at'   => now(),
                    'resurfaced'  => false,
                ]);
            } catch (\Throwable) {}
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['passed' => true]);
            }
            return redirect()->back();
        }

        // Create like (ignore if duplicate)
        $likeMessage = $request->input('like_message');
        if ($likeMessage) {
            $likeMessage = mb_substr(strip_tags($likeMessage), 0, 200);
        }

        // Send Note rate-limit: free users get 3 notes/day; premium is unlimited
        if ($likeMessage && !$sender->isPremiumActive()) {
            $noteKey   = "notes:{$sender->id}";
            $noteLimit = 3;
            if (RateLimiter::tooManyAttempts($noteKey, $noteLimit)) {
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'error'      => "You've used all {$noteLimit} free notes today. Upgrade to Premium for unlimited notes!",
                        'note_limit' => true,
                    ], 429);
                }
                return redirect()->back()->with('error', "You've used all {$noteLimit} free notes today. Upgrade to Premium for unlimited notes!");
            }
            RateLimiter::hit($noteKey, 86400);
        }
        Like::firstOrCreate([
            'sender_id'   => $sender->id,
            'receiver_id' => $receiverId,
        ], [
            'is_super_like' => $request->boolean('super_like'),
            'message'       => $likeMessage ?: null,
        ]);

        ActivityLogger::log($sender, 'like_sent', ['target_user_id' => $receiverId], $request);

        // Elo boost: target gains rating when liked
        try { $this->elo->onLike($sender, $user); } catch (\Throwable) {}

        // Notify receiver — swallow mail exceptions so a bad email never breaks the like
        try {
            $user->notify(new \App\Notifications\ProfileLikedNotification($sender));
        } catch (\Throwable) {}

        // Award like-received badges to the receiver
        try {
            $likesReceived = \App\Models\Like::where('receiver_id', $user->id)->count();
            \App\Services\BadgeService::checkLikeBadges($user, $likesReceived);
        } catch (\Throwable) {}

        if (SiteSetting::get('email_feature_usage_enabled', true)) {
            try {
                $sender->notify(new FeatureUsageNotification(
                    feature: 'Like Sent',
                    summary: "You liked {$user->name}'s profile.",
                    url: route('profile.show', $user->username),
                ));
            } catch (\Throwable) {}
        }

        // Check for mutual like → create match
        $mutualLike = Like::where('sender_id', $receiverId)
            ->where('receiver_id', $sender->id)
            ->exists();

        if ($mutualLike) {
            [$u1, $u2] = $sender->id < $receiverId
                ? [$sender->id, $receiverId]
                : [$receiverId, $sender->id];

            $match = UserMatch::firstOrCreate(
                ['user1_id' => $u1, 'user2_id' => $u2],
                ['matched_at' => now(), 'is_active' => true]
            );

            if ($match->wasRecentlyCreated) {
                $match->conversation()->create();
                $match->load('conversation');
                // Match created - notify both users
                try { $user->notify(new \App\Notifications\NewMatchNotification($match, $sender)); } catch (\Throwable) {}
                try { $sender->notify(new \App\Notifications\NewMatchNotification($match, $user)); } catch (\Throwable) {}

                // Award match badges to both parties
                try {
                    $senderMatchCount = \App\Models\UserMatch::where('user1_id', $sender->id)->orWhere('user2_id', $sender->id)->count();
                    $receiverMatchCount = \App\Models\UserMatch::where('user1_id', $user->id)->orWhere('user2_id', $user->id)->count();
                    \App\Services\BadgeService::checkMatchBadges($sender, $senderMatchCount);
                    \App\Services\BadgeService::checkMatchBadges($user, $receiverMatchCount);
                } catch (\Throwable) {}

                // Elo boost for mutual match
                try { $this->elo->onMatch($sender, $user); } catch (\Throwable) {}

                // Reveal any secret messages between these two users
                try {
                    SecretMessageController::revealBetween(
                        $sender->id, $user->id, $match->conversation->id
                    );
                } catch (\Throwable) {}

                if (SiteSetting::get('email_feature_usage_enabled', true)) {
                    try {
                        $sender->notify(new FeatureUsageNotification(
                            feature: 'New Match',
                            summary: "You matched with {$user->name}.",
                            url: route('matches.index'),
                        ));
                    } catch (\Throwable) {}
                }
            }

            if ($request->expectsJson() || $request->ajax()) {
                $convId = $match->conversation->id ?? null;
                return response()->json([
                    'liked'            => true,
                    'matched'          => true,
                    'note_sent'        => !empty($likeMessage),
                    'match_name'       => $user->name,
                    'match_photo'      => $user->profile?->photo_url,
                    'match'            => ['id' => $match->id],
                    'conversation_url' => $convId
                        ? route('conversations.show', $convId)
                        : route('conversations.index'),
                ]);
            }

            return redirect()->route('profile.show', $user->username)
                ->with('success', '🎉 It\'s a match with ' . $user->name . '! Start chatting now.')
                ->with('like_matched', true);
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'liked'     => true,
                'matched'   => false,
                'note_sent' => !empty($likeMessage),
            ]);
        }

        return redirect()->route('profile.show', $user->username)
            ->with('success', '❤️ You liked ' . $user->name . '!');
    }

    public function destroy(Request $request, User $user): JsonResponse|RedirectResponse
    {
        Like::where('sender_id', $request->user()->id)
            ->where('receiver_id', $user->id)
            ->delete();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['unliked' => true]);
        }

        return redirect()->route('profile.show', $user->username)
            ->with('success', 'Like removed.');
    }

    public function whoLikedMe(Request $request): \Illuminate\View\View
    {
        $user      = $request->user();
        $isPremium = $user->isPremiumActive();

        $likers = Like::where('receiver_id', $user->id)
            ->with(['sender.primaryPhoto'])
            ->orderByDesc('created_at')
            ->paginate(24);

        return view('like.who-liked-me', compact('likers', 'isPremium'));
    }
}
