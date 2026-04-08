<?php

namespace App\Console\Commands;

use App\Models\SafeDateCheckin;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendSafeDateAlerts extends Command
{
    protected $signature   = 'safe-date:alert';
    protected $description = 'Send alerts for overdue safe date check-ins';

    public function handle(): int
    {
        $overdue = SafeDateCheckin::where('status', 'active')
            ->whereNull('alert_sent_at')
            ->whereNull('checked_in_at')
            ->with('user')
            ->get()
            ->filter(fn($c) => $c->isExpired());

        foreach ($overdue as $checkin) {
            $user = $checkin->user;

            Mail::send([], [], function ($message) use ($checkin, $user) {
                $message
                    ->to($checkin->emergency_contact_email, $checkin->emergency_contact_name)
                    ->subject("⚠️ Safe Date Alert — {$user->name} hasn't checked in")
                    ->html(
                        "<h2>⚠️ Safe Date Alert</h2>" .
                        "<p>Hi <strong>{$checkin->emergency_contact_name}</strong>,</p>" .
                        "<p><strong>{$user->name}</strong> set up a safe date check-in for " .
                        "<strong>{$checkin->date_at->format('F j, Y g:i A')}</strong> at " .
                        "<strong>{$checkin->date_location}</strong>.</p>" .
                        "<p>They were supposed to check in <strong>{$checkin->checkin_minutes} minutes</strong> after the date started, " .
                        "but have not done so yet.</p>" .
                        "<p>Please try contacting them at your earliest convenience.</p>" .
                        "<p style='color:#999;font-size:12px'>This message was sent automatically by HeartsConnect Safe Date feature.</p>"
                    );
            });

            $checkin->update([
                'alert_sent_at' => now(),
                'status'        => 'alert_sent',
            ]);

            $this->info("Alert sent for check-in #{$checkin->id} ({$user->name})");
        }

        $this->info("Done. {$overdue->count()} alerts sent.");
        return 0;
    }
}
