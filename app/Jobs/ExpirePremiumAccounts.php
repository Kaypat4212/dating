<?php

namespace App\Jobs;

use App\Models\User;
use App\Notifications\PremiumExpiredNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ExpirePremiumAccounts implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        User::where('is_premium', true)
            ->whereNotNull('premium_expires_at')
            ->where('premium_expires_at', '<=', now())
            ->each(function (User $user) {
                $user->update([
                    'is_premium'         => false,
                    'premium_expires_at' => null,
                    'premium_plan'       => null,
                    'hide_last_seen'     => false,
                ]);
                $user->notify(new PremiumExpiredNotification());
            });
    }
}
