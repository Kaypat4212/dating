<?php

use App\Jobs\ClearExpiredMoods;
use App\Jobs\ExpirePremiumAccounts;
use App\Jobs\PairSpeedDatingUsers;
use App\Jobs\PurgeExpiredMessages;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Daily: expire premium subscriptions that have passed their expiry date
Schedule::job(new ExpirePremiumAccounts)->dailyAt('00:05');

// Daily: send profile stats summary to all users (at 08:00)
Schedule::command('emails:daily-summary')->dailyAt('08:00');

// Hourly: clear mood statuses older than 24 hours
Schedule::job(new ClearExpiredMoods)->hourly();

// Every 2 minutes: pair users waiting in speed dating queue
Schedule::job(new PairSpeedDatingUsers)->everyTwoMinutes();

// Hourly: delete messages whose disappear timer has expired
Schedule::job(new PurgeExpiredMessages)->hourly();

// Every 5 minutes: delete viewed disappearing content (Snapchat-style)
Schedule::call(function () {
    \App\Models\DisappearingContent::deleteExpired();
})->everyFiveMinutes()->name('cleanup-disappearing-content');

// Every 5 minutes: check for overdue safe date check-ins and send alerts
Schedule::command('safe-date:alert')->everyFiveMinutes();

// Weekly Monday 08:00: send weekly digest emails (likes, matches, views)
Schedule::command('emails:weekly-digest')->weeklyOn(1, '08:00');
