<?php

use App\Jobs\ExpirePremiumAccounts;
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
