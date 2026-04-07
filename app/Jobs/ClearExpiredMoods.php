<?php

namespace App\Jobs;

use App\Models\Profile;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ClearExpiredMoods implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        // Clear mood_status for any profile where mood was set > 24 h ago
        // We piggy-back on updated_at only when mood_status is non-null.
        // If a dedicated mood_updated_at column exists, use that instead.
        Profile::whereNotNull('mood_status')
            ->where('updated_at', '<=', now()->subHours(24))
            ->update(['mood_status' => null]);
    }
}
