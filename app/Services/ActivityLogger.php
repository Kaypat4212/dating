<?php

namespace App\Services;

use App\Models\UserActivityLog;
use App\Models\User;
use Illuminate\Http\Request;

class ActivityLogger
{
    /**
     * Log a user action and re-evaluate their spam score.
     *
     * @param  int|User  $user
     * @param  string    $action   e.g. 'login', 'message_sent', 'like_sent', 'report_sent', 'photo_upload'
     * @param  array     $meta     optional extra context
     * @param  Request|null $request  if provided, IP + UA are captured
     */
    public static function log(int|User $user, string $action, array $meta = [], ?Request $request = null): UserActivityLog
    {
        $userId = $user instanceof User ? $user->id : $user;

        $log = UserActivityLog::create([
            'user_id'    => $userId,
            'action'     => $action,
            'ip_address' => $request?->ip(),
            'user_agent' => $request?->userAgent(),
            'meta'       => $meta ?: null,
        ]);

        // Re-evaluate spam score asynchronously (lightweight, runs in same process)
        static::evaluateSpamScore($userId);

        return $log;
    }

    /**
     * Compute a simple spam score (0-100) based on recent activity patterns
     * and update the user record accordingly.
     */
    public static function evaluateSpamScore(int $userId): void
    {
        $since = now()->subHours(24);

        $counts = UserActivityLog::where('user_id', $userId)
            ->where('created_at', '>=', $since)
            ->selectRaw("action, COUNT(*) as cnt")
            ->groupBy('action')
            ->pluck('cnt', 'action');

        $score = 0;

        // Scoring rules (each rule adds points — max 100)
        // Too many messages to different users
        $msgCount = (int) ($counts['message_sent'] ?? 0);
        if ($msgCount > 50)  $score += 30;
        elseif ($msgCount > 20) $score += 15;
        elseif ($msgCount > 10) $score += 5;

        // Excessive likes in one day
        $likeCount = (int) ($counts['like_sent'] ?? 0);
        if ($likeCount > 100) $score += 25;
        elseif ($likeCount > 50) $score += 10;

        // Multiple logins from different IPs in a short window
        $loginIps = UserActivityLog::where('user_id', $userId)
            ->where('action', 'login')
            ->where('created_at', '>=', now()->subHours(6))
            ->distinct('ip_address')
            ->count('ip_address');
        if ($loginIps > 3) $score += 20;

        // Multiple reports received about this user
        $reportsReceived = (int) ($counts['report_received'] ?? 0);
        if ($reportsReceived >= 3) $score += 25;
        elseif ($reportsReceived >= 1) $score += 10;

        // Multiple photos uploaded fast (possible fake account)
        $photoUploads = (int) ($counts['photo_upload'] ?? 0);
        if ($photoUploads > 5) $score += 10;

        // Repeated profile views of the same user (stalking pattern)
        $viewCount = (int) ($counts['profile_viewed'] ?? 0);
        if ($viewCount > 20) $score += 10;

        $score = min(100, $score);

        $isSuspicious = $score >= 40;

        User::where('id', $userId)->update([
            'spam_score'      => $score,
            'is_suspicious'   => $isSuspicious,
            'last_flagged_at' => $isSuspicious ? now() : null,
        ]);

        // Flag individual log entries if score is high
        if ($isSuspicious) {
            UserActivityLog::where('user_id', $userId)
                ->whereNull('flag')
                ->where('created_at', '>=', $since)
                ->update(['flag' => 'suspicious']);
        }
    }
}
