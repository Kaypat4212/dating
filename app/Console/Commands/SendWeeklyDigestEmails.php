<?php

namespace App\Console\Commands;

use App\Models\Like;
use App\Models\ProfileView;
use App\Models\SiteSetting;
use App\Models\User;
use App\Models\UserMatch;
use App\Notifications\WeeklyDigestNotification;
use Illuminate\Console\Command;

class SendWeeklyDigestEmails extends Command
{
    protected $signature = 'emails:weekly-digest';

    protected $description = 'Send weekly digest emails: likes, matches, profile views, and engagement tips.';

    public function handle(): int
    {
        if (! SiteSetting::get('email_weekly_digest_enabled', true)) {
            $this->info('Weekly digest emails are disabled in settings.');
            return self::SUCCESS;
        }

        $since = now()->subWeek();

        /** @var \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users */
        $users = User::query()
            ->where('is_banned', false)
            ->whereNotNull('email_verified_at')
            ->get();

        $sent = 0;

        foreach ($users as $user) {
            /** @var \App\Models\User $user */

            // Respect per-user opt-out preference
            if (! ($user->preferences?->wantsEmail('email_weekly_digest') ?? true)) {
                continue;
            }

            $likesThisWeek = Like::where('receiver_id', $user->id)
                ->where('created_at', '>=', $since)
                ->count();

            $matchesThisWeek = UserMatch::where(function ($q) use ($user) {
                $q->where('user1_id', $user->id)->orWhere('user2_id', $user->id);
            })->where('matched_at', '>=', $since)->count();

            $viewsThisWeek = $user->isPremiumActive()
                ? ProfileView::where('viewed_id', $user->id)
                    ->where('created_at', '>=', $since)
                    ->count()
                : null; // null = hidden behind premium paywall

            $totalLikes   = Like::where('receiver_id', $user->id)->count();
            $totalMatches = UserMatch::where(function ($q) use ($user) {
                $q->where('user1_id', $user->id)->orWhere('user2_id', $user->id);
            })->count();

            $stats = [
                'likes_this_week'   => $likesThisWeek,
                'matches_this_week' => $matchesThisWeek,
                'views_this_week'   => $viewsThisWeek,
                'total_likes'       => $totalLikes,
                'total_matches'     => $totalMatches,
                'is_premium'        => $user->isPremiumActive(),
                'profile_complete'  => $user->profile_complete ?? false,
                'week_start'        => $since->format('M j'),
                'week_end'          => now()->format('M j, Y'),
            ];

            // Skip if no meaningful activity and profile is incomplete
            if ($likesThisWeek === 0 && $matchesThisWeek === 0 && $totalMatches === 0) {
                continue;
            }

            $user->notify(new WeeklyDigestNotification($stats));
            $sent++;
        }

        $this->info("Weekly digests queued: {$sent}");

        return self::SUCCESS;
    }
}
