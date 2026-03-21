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

    public function mount(): void
    {
        $this->suggestions = collect();
        $this->focusUserId = (int) request()->query('user_id') ?: null;

        if ($this->focusUserId) {
            $this->loadSuggestions($this->focusUserId);
        }
    }

    /**
     * Return the 10 newest users (past 7 days, profile complete) for the sidebar list.
     */
    public function getNewUsers(): Collection
    {
        return User::where('profile_complete', true)
            ->whereNotNull('email_verified_at')
            ->where('is_banned', false)
            ->where('created_at', '>=', now()->subDays(7))
            ->with('primaryPhoto')
            ->orderByDesc('created_at')
            ->limit(20)
            ->get();
    }

    /**
     * Compute top-10 compatible candidates for the given user.
     */
    protected function loadSuggestions(int $userId): void
    {
        $user = User::with(['profile.interests', 'primaryPhoto'])->find($userId);
        if (! $user) { $this->suggestions = collect(); return; }

        $compat = app(CompatibilityService::class);

        // Exclude already-matched users
        $existingMatchIds = UserMatch::where('user1_id', $userId)
            ->orWhere('user2_id', $userId)
            ->get()
            ->flatMap(fn ($m) => [$m->user1_id, $m->user2_id])
            ->unique()
            ->toArray();

        $candidates = User::where('users.id', '!=', $userId)
            ->where('profile_complete', true)
            ->whereNotNull('email_verified_at')
            ->where('is_banned', false)
            ->whereNotIn('users.id', $existingMatchIds)
            ->when($user->seeking !== 'everyone', fn ($q) => $q->where('gender', $user->seeking))
            ->with(['profile.interests', 'primaryPhoto'])
            ->limit(100) // score top 100 then take best 10
            ->get()
            ->map(fn ($candidate) => [
                'user'  => $candidate,
                'score' => $compat->score($user, $candidate),
            ])
            ->sortByDesc('score')
            ->take(10)
            ->values();

        $this->suggestions = $candidates;
    }

    /**
     * Livewire action: select a new user to inspect.
     */
    public function selectUser(int $userId): void
    {
        $this->focusUserId = $userId;
        $this->loadSuggestions($userId);
    }

    /**
     * Livewire action: manually create a match between two users.
     */
    public function forceMatch(int $userAId, int $userBId): void
    {
        $userA = User::find($userAId);
        $userB = User::find($userBId);

        if (! $userA || ! $userB) {
            Notification::make()->title('User not found.')->danger()->send();
            return;
        }

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
