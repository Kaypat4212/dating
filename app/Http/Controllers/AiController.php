<?php

namespace App\Http\Controllers;

use App\Models\AiMessage;
use App\Models\Conversation;
use App\Models\User;
use App\Services\AiAssistantService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\View\View;

class AiController extends Controller
{
    // Words that are not appropriate for community usernames
    private const BANNED_WORDS = [
        'sex','porn','nude','naked','fuck','shit','bitch','ass','cock','dick',
        'pussy','cunt','whore','slut','rape','nigger','nigga','faggot','kike',
        'spic','racist','hate','kill','murder','suicide','drug','weed','420',
        'escort','hookup','nudes','onlyfans','admin','support','moderator',
        'official','staff','bot','system',
    ];

    // Free-tier hourly limit (suggestions + chat combined)
    private const FREE_HOURLY_LIMIT = 10;

    public function __construct(private AiAssistantService $ai) {}

    // ── Rate-limit helpers ──────────────────────────────────────────────────

    private function checkRateLimit(User $user): ?JsonResponse
    {
        if ($user->isPremiumActive()) return null;

        $key = 'ai_hourly:' . $user->id;
        if (RateLimiter::tooManyAttempts($key, self::FREE_HOURLY_LIMIT)) {
            $wait = RateLimiter::availableIn($key);
            return response()->json([
                'rate_limited' => true,
                'message'      => "You've reached your hourly AI limit. Upgrade to Premium for unlimited access.",
                'retry_after'  => $wait,
                'limit'        => self::FREE_HOURLY_LIMIT,
            ], 429);
        }
        RateLimiter::hit($key, 3600);
        return null;
    }

    private function aiRemaining(User $user): int
    {
        if ($user->isPremiumActive()) return -1; // -1 = unlimited
        $used = RateLimiter::attempts('ai_hourly:' . $user->id);
        return max(0, self::FREE_HOURLY_LIMIT - $used);
    }

    // ── Suggest (bio, reply, icebreaker, username…) ─────────────────────────

    public function suggest(Request $request): JsonResponse
    {
        $me = $request->user();
        if ($limited = $this->checkRateLimit($me)) return $limited;

        $request->validate([
            'type'            => 'required|in:reply,topics,icebreaker,bio,username,rephrase',
            'conversation_id' => 'nullable|integer',
            'partner_id'      => 'nullable|integer',
            'draft'           => 'nullable|string|max:2000',
        ]);

        $type = $request->input('type');
        $me   = $request->user();
        $ctx  = [];

        if ($type === 'rephrase') {
            $ctx['draft'] = $request->input('draft', '');
        }

        if (in_array($type, ['reply', 'topics', 'icebreaker'])) {
            $partnerId = (int) $request->input('partner_id', 0);
            $partner   = $partnerId ? User::with(['profile.interests'])->find($partnerId) : null;

            if ($partner) {
                $ctx['partner_name']      = $partner->name;
                $ctx['partner_bio']       = $partner->profile?->about ?? $partner->profile?->bio ?? '';
                $ctx['partner_interests'] = $partner->profile?->interests?->pluck('name')->join(', ') ?: 'not listed';
            }

            if ($type === 'reply' && $request->input('conversation_id')) {
                $conv = Conversation::find((int) $request->input('conversation_id'));
                if ($conv && ($conv->match->user1_id === $me->id || $conv->match->user2_id === $me->id)) {
                    $lastMsgs = $conv->messages()
                        ->where('type', 'text')
                        ->latest('id')
                        ->limit(6)
                        ->get()
                        ->reverse()
                        ->map(fn ($m) => ($m->sender_id === $me->id ? 'Me' : ($partner?->name ?? 'Them')) . ': ' . $m->body)
                        ->join("\n");
                    $ctx['last_messages'] = $lastMsgs;
                }
            }
        }

        if ($type === 'bio') {
            $profile = $me->load('profile.interests')->profile;
            $ctx['interests']  = $profile?->interests?->pluck('name')->join(', ') ?: 'not listed';
            $ctx['age']        = $me->date_of_birth ? now()->diffInYears($me->date_of_birth) : 'not set';
            $ctx['occupation'] = $profile?->occupation ?? 'not set';
        }

        if ($type === 'username') {
            $profile = $me->load('profile.interests')->profile;
            $ctx['name']      = $me->name;
            $ctx['interests'] = $profile?->interests?->pluck('name')->join(', ') ?: 'not specified';
        }

        $suggestions = $this->ai->suggest($type, $ctx);

        return response()->json([
            'suggestions' => $suggestions,
            'remaining'   => $this->aiRemaining($me),
        ]);
    }

    // ── AI status ───────────────────────────────────────────────────────────

    public function status(Request $request): JsonResponse
    {
        $me = $request->user();
        return response()->json([
            'is_premium' => $me->isPremiumActive(),
            'remaining'  => $this->aiRemaining($me),
            'limit'      => self::FREE_HOURLY_LIMIT,
        ]);
    }

    // ── AI Chat (dedicated assistant conversation) ──────────────────────────

    public function chatView(Request $request): View
    {
        $me        = $request->user();
        $history   = AiMessage::where('user_id', $me->id)
            ->orderBy('id', 'asc')
            ->limit(60)
            ->get();
        $isPremium = $me->isPremiumActive();
        $remaining = $this->aiRemaining($me);
        $limit     = self::FREE_HOURLY_LIMIT;

        return view('conversations.ai-chat', compact('history', 'isPremium', 'remaining', 'limit'));
    }

    public function chatReply(Request $request): JsonResponse
    {
        $me = $request->user();
        if ($limited = $this->checkRateLimit($me)) return $limited;

        $request->validate(['message' => 'required|string|max:1000']);
        $userMsg = trim($request->input('message'));

        AiMessage::create(['user_id' => $me->id, 'role' => 'user', 'body' => $userMsg]);

        // Build last ~10 exchanges for context
        $history = AiMessage::where('user_id', $me->id)
            ->latest('id')
            ->limit(21)
            ->get()
            ->reverse()
            ->values()
            ->map(fn ($m) => ['role' => $m->role, 'body' => $m->body])
            ->toArray();

        $contextHistory = array_slice($history, 0, -1); // strip the message we just saved

        $reply = $this->ai->chat($contextHistory, $userMsg);

        AiMessage::create(['user_id' => $me->id, 'role' => 'assistant', 'body' => $reply]);

        return response()->json([
            'reply'      => $reply,
            'remaining'  => $this->aiRemaining($me),
            'is_premium' => $me->isPremiumActive(),
        ]);
    }

    /**
     * Check a username for availability and community suitability,
     * and return AI-generated alternatives.
     * GET /ai/username-check?username=xxx
     */
    public function usernameCheck(Request $request): JsonResponse
    {
        $request->validate(['username' => 'required|string|max:30']);

        $username = strtolower(trim($request->input('username')));
        $me       = $request->user();

        $taken   = User::where('username', $username)
                       ->where('id', '!=', $me->id)
                       ->exists();

        $flagged = false;
        foreach (self::BANNED_WORDS as $word) {
            if (str_contains($username, $word)) {
                $flagged = true;
                break;
            }
        }

        // Build context for suggestions
        $profile = $me->load('profile.interests')->profile;
        $ctx = [
            'name'      => $me->name,
            'interests' => $profile?->interests?->pluck('name')->join(', ') ?: 'not specified',
        ];

        $suggestions = ($taken || $flagged)
            ? $this->ai->suggest('username', $ctx)
            : [];

        // If AI returned suggestions that contain banned words, filter them out
        $suggestions = array_values(array_filter($suggestions, function ($s) {
            $s = strtolower($s);
            foreach (self::BANNED_WORDS as $word) {
                if (str_contains($s, $word)) return false;
            }
            return true;
        }));

        return response()->json([
            'taken'       => $taken,
            'flagged'     => $flagged,
            'suggestions' => array_slice($suggestions, 0, 5),
        ]);
    }
}
