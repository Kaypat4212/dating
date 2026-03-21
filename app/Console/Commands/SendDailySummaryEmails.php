<?php

namespace App\Console\Commands;

use App\Models\Like;
use App\Models\Message;
use App\Models\ProfileView;
use App\Models\SiteSetting;
use App\Models\User;
use App\Models\UserMatch;
use App\Notifications\DailySummaryNotification;
use Illuminate\Console\Command;

class SendDailySummaryEmails extends Command
{
    protected $signature = 'emails:daily-summary';

    protected $description = 'Send daily summary emails with profile views, likes, and matches.';

    public function handle(): int
    {
        if (! SiteSetting::get('email_daily_summary_enabled', true)) {
            $this->info('Daily summary emails are disabled in settings.');
            return self::SUCCESS;
        }

        /** @var \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users */
        $users = User::query()
            ->where('is_banned', false)
            ->whereNotNull('email_verified_at')
            ->get();

        $sent = 0;

        foreach ($users as $user) {
            /** @var \App\Models\User $user */
            $stats = [
                // Profile view counts are only meaningful for premium users who can see who visited them
                'profile_views_today' => $user->isPremiumActive()
                    ? ProfileView::where('viewed_id', $user->id)
                        ->where('created_at', '>=', now()->subDay())
                        ->count()
                    : 0,
                'likes_today' => Like::where('receiver_id', $user->id)
                    ->where('created_at', '>=', now()->subDay())
                    ->count(),
                'matches_today' => UserMatch::where(function ($query) use ($user) {
                    $query->where('user1_id', $user->id)
                        ->orWhere('user2_id', $user->id);
                })->where('matched_at', '>=', now()->subDay())
                    ->count(),
                'unread_messages' => Message::whereHas('conversation.match', function ($query) use ($user) {
                    $query->where('user1_id', $user->id)
                        ->orWhere('user2_id', $user->id);
                })->whereNull('read_at')
                    ->where('sender_id', '!=', $user->id)
                    ->count(),
            ];

            $user->notify(new DailySummaryNotification($stats));
            $sent++;
        }

        $this->info("Daily summaries queued: {$sent}");

        return self::SUCCESS;
    }
}
