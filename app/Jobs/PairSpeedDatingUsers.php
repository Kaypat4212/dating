<?php

namespace App\Jobs;

use App\Models\SpeedDatingQueue;
use App\Models\SpeedDatingRoom;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class PairSpeedDatingUsers implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        // Expire rooms whose duration has elapsed
        SpeedDatingRoom::where('status', 'active')
            ->where(DB::raw('TIMESTAMPDIFF(MINUTE, started_at, NOW())'), '>=', DB::raw('duration_minutes'))
            ->update(['status' => 'ended', 'ended_at' => now()]);

        // Pull waiting users in FIFO order
        $waiting = DB::table('speed_dating_queue')
            ->where('status', 'waiting')
            ->orderBy('created_at')
            ->get();

        // Pair them in groups of 2
        $chunks = $waiting->chunk(2);
        foreach ($chunks as $pair) {
            if ($pair->count() < 2) break;

            [$qA, $qB] = [$pair->first(), $pair->last()];

            DB::transaction(function () use ($qA, $qB) {
                $room = SpeedDatingRoom::create([
                    'user1_id'        => $qA->user_id,
                    'user2_id'        => $qB->user_id,
                    'duration_minutes'=> 5,
                    'started_at'      => now(),
                    'status'          => 'active',
                ]);

                DB::table('speed_dating_queue')
                    ->whereIn('id', [$qA->id, $qB->id])
                    ->update(['status' => 'matched', 'room_id' => $room->id]);
            });
        }
    }
}
