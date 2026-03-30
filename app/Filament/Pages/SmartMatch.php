<?php

namespace App\Filament\Pages;

use App\Models\Conversation;
use App\Models\Like;
use App\Models\User;
use App\Models\UserMatch;
use App\Services\CompatibilityService;
use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class SmartMatch extends Page
{
    protected string $view = 'filament.pages.smart-match';

    public static function getNavigationIcon(): ?string  { return 'heroicon-o-sparkles'; }
    public static function getNavigationGroup(): ?string { return 'Members'; }
    public static function getNavigationSort(): ?int     { return 3; }
    public static function getNavigationLabel(): string  { return 'Smart Match'; }
    public function getTitle(): string|Htmlable { return 'Smart Match — New User Suggestions'; }

    /** Selected new-user ID from query string */
    public ?int $focusUserId = null;

    /** Cached suggestion rows for the focused user */
    public Collection $suggestions;

    /** Error message shown in the UI (null = no error) */
    public ?string $pageError = null;

    // ── Same-sex match confirmation state ────────────────────────────────
    public bool    $showSameSexModal  = false;
    public ?int    $pendingMatchA     = null;
    public ?int    $pendingMatchB     = null;
    public string  $pendingMatchNames = '';
    public string  $pendingGender     = '';

    public function mount(): void
    {
        $this->suggestions = collect();
        $this->pageError   = null;

        try {
            $this->focusUserId = (int) request()->query('user_id') ?: null;

            if ($this->focusUserId) {
                $this->loadSuggestions($this->focusUserId);
            }
        } catch (\Throwable $e) {
            Log::error('SmartMatch mount error: ' . $e->getMessage(), ['exception' => $e]);
            $this->pageError = 'Failed to initialise Smart Match: ' . $e->getMessage();
        }
    }

    /**
     * Return the 30 newest complete users for the sidebar list.
     */
    public function getNewUsers(): Collection
    {
        try {
            return User::where('profile_complete', true)
                ->whereNotNull('email_verified_at')
                ->where('is_banned', false)
                ->with('primaryPhoto')
                ->orderByDesc('created_at')
                ->limit(30)
                ->get();
        } catch (\Throwable $e) {
            Log::error('SmartMatch getNewUsers error: ' . $e->getMessage(), ['exception' => $e]);
            return collect();
        }
    }

    /**
     * Compute top-10 compatible candidates for the given user.
     */
    protected function loadSuggestions(int $userId): void
    {
        $user = User::with(['profile.interests', 'primaryPhoto'])->find($userId);
        if (! $user) {
            $this->suggestions = collect();
            return;
        }

        $this->suggestions = $this->buildSuggestions($user);
    }

    /**
     * Query and rank candidate users for the given user.
     *
     * @return Collection<int, array{user: User, score: int}>
     */
    private function buildSuggestions(User $user): Collection
    {
        $existingMatchIds = $this->getExistingMatchIds($user->id);

        $compat = app(CompatibilityService::class);

        return User::where('users.id', '!=', $user->id)
            ->where('profile_complete', true)
            ->whereNotNull('email_verified_at')
            ->where('is_banned', false)
            ->whereNotIn('users.id', $existingMatchIds)
            ->when(
                $user->seeking && $user->seeking !== 'everyone',
                fn ($q) => $q->where('gender', $user->seeking)
            )
            ->with(['profile.interests', 'primaryPhoto'])
            ->limit(100)
            ->get()
            ->map(fn (User $candidate): array => [
                'user'  => $candidate,
                'score' => $compat->score($user, $candidate),
            ])
            ->sortByDesc('score')
            ->take(10)
            ->values();
    }

    /**
     * Return IDs of users already matched with the given user.
     *
     * @return int[]
     */
    private function getExistingMatchIds(int $userId): array
    {
        return UserMatch::where(function ($q) use ($userId) {
            $q->where('user1_id', $userId)->orWhere('user2_id', $userId);
        })
            ->get()
            ->flatMap(fn (UserMatch $m): array => [$m->user1_id, $m->user2_id])
            ->unique()
            ->reject(fn ($id) => $id === $userId)
            ->toArray();
    }

    /**
     * Livewire action: select a new user to inspect.
     */
    public function selectUser(int $userId): void
    {
        $this->pageError = null;
        try {
            $this->focusUserId = $userId;
            $this->loadSuggestions($userId);
        } catch (\Throwable $e) {
            Log::error('SmartMatch selectUser error: ' . $e->getMessage(), ['exception' => $e]);
            $this->pageError = 'Failed to load suggestions: ' . $e->getMessage();
        }
    }

    /**
     * Livewire action: manually create a match between two users.
     * Intercepts same-sex pairs and asks for confirmation before proceeding.
     */
    public function forceMatch(int $userAId, int $userBId): void
    {
        $userA = User::find($userAId);
        $userB = User::find($userBId);

        if (! $userA || ! $userB) {
            Notification::make()->title('User not found.')->danger()->send();
            return;
        }

        // ── Same-sex guard ────────────────────────────────────────────────
        $gA = strtolower(trim($userA->gender ?? ''));
        $gB = strtolower(trim($userB->gender ?? ''));
        $bothGenderKnown = $gA !== '' && $gB !== '';

        if ($bothGenderKnown && $gA === $gB) {
            // Show modal instead of proceeding
            $this->pendingMatchA     = $userAId;
            $this->pendingMatchB     = $userBId;
            $this->pendingGender     = ucfirst($gA);
            $this->pendingMatchNames = "{$userA->name} & {$userB->name}";
            $this->showSameSexModal  = true;
            return;
        }

        $this->executeMatch($userAId, $userBId, $userA, $userB);
    }

    /**
     * Confirmed by admin: proceed with the same-sex match.
     */
    public function confirmSameSexMatch(): void
    {
        $uAId = $this->pendingMatchA;
        $uBId = $this->pendingMatchB;
        $this->showSameSexModal  = false;
        $this->pendingMatchA     = null;
        $this->pendingMatchB     = null;
        $this->pendingMatchNames = '';
        $this->pendingGender     = '';

        if (! $uAId || ! $uBId) return;

        $userA = User::find($uAId);
        $userB = User::find($uBId);
        if (! $userA || ! $userB) {
            Notification::make()->title('User not found.')->danger()->send();
            return;
        }
        $this->executeMatch($uAId, $uBId, $userA, $userB);
    }

    /**
     * Admin cancelled the same-sex match confirmation.
     */
    public function cancelSameSexMatch(): void
    {
        $this->showSameSexModal  = false;
        $this->pendingMatchA     = null;
        $this->pendingMatchB     = null;
        $this->pendingMatchNames = '';
        $this->pendingGender     = '';
    }

    /**
     * Internal: actually create the match record, likes, conversation, and notifications.
     */
    private function executeMatch(int $userAId, int $userBId, User $userA, User $userB): void
    {
        [$u1, $u2] = $userAId < $userBId
            ? [$userAId, $userBId]
            : [$userBId, $userAId];

        $match = UserMatch::firstOrCreate(
            ['user1_id' => $u1, 'user2_id' => $u2],
            ['matched_at' => now(), 'is_active' => true]
        );

        if ($match->wasRecentlyCreated) {
            // Ensure a mutual like exists for integrity
            foreach ([[$userAId, $userBId], [$userBId, $userAId]] as [$sender, $receiver]) {
                Like::firstOrCreate(
                    ['sender_id' => $sender, 'receiver_id' => $receiver],
                    ['is_super_like' => false]
                );
            }

            $match->conversation()->firstOrCreate([]);

            // Notify both users
            try { $userA->notify(new \App\Notifications\NewMatchNotification($match, $userB)); } catch (\Throwable) {}
            try { $userB->notify(new \App\Notifications\NewMatchNotification($match, $userA)); } catch (\Throwable) {}

            Notification::make()
                ->title("✅ Matched {$userA->name} ↔ {$userB->name}!")
                ->success()
                ->send();
        } else {
            Notification::make()
                ->title('These users are already matched.')
                ->warning()
                ->send();
        }

        // Reload suggestions after matching
        $this->loadSuggestions($this->focusUserId ?? $userAId);
    }
}
