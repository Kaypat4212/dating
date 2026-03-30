<?php

namespace App\Http\Middleware;

use App\Models\HomepageVisit;
use App\Services\TelegramNotificationService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class TrackHomepageVisits
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only track homepage visits
        if ($request->is('/') || $request->is('home')) {
            $this->trackVisit($request);
        }

        return $next($request);
    }

    /**
     * Track homepage visit: store in DB and send Telegram notification.
     */
    protected function trackVisit(Request $request): void
    {
        $ip     = $request->ip();
        $userId = Auth::id();
        $cacheKey = "homepage_visit_{$ip}_" . ($userId ?? 'guest');

        // Prevent duplicate notifications within 5 minutes
        if (Cache::has($cacheKey)) {
            return;
        }

        // Cache for 5 minutes to prevent spam
        Cache::put($cacheKey, true, now()->addMinutes(5));

        try {
            /** @var TelegramNotificationService $telegram */
            $telegram = app(TelegramNotificationService::class);

            // Resolve geo info once and reuse for both DB record and Telegram
            $geo     = $telegram->lookupGeoIp($ip);
            $browser = $request->userAgent() ? $this->parseBrowser($request->userAgent()) : null;

            // ── Store visit in database ─────────────────────────────────
            HomepageVisit::create([
                'ip_address'   => $ip,
                'user_id'      => $userId,
                'user_agent'   => $request->userAgent(),
                'country'      => $geo['country']     ?? null,
                'country_code' => $geo['countryCode'] ?? null,
                'city'         => $geo['city']         ?? null,
                'region'       => $geo['regionName']  ?? null,
                'isp'          => $geo['isp']          ?? null,
                'org'          => $geo['org']          ?? null,
                'is_proxy'     => $geo['proxy']        ?? false,
                'browser'      => $browser,
                'visited_at'   => now(),
            ]);

            // ── Send Telegram notification ──────────────────────────────
            $telegram->notifyHomepageVisit(
                ip:        $ip,
                userAgent: $request->userAgent(),
                userId:    $userId,
                geo:       $geo,
            );
        } catch (\Throwable $e) {
            Log::error('TrackHomepageVisits error: ' . $e->getMessage(), ['exception' => $e]);
        }
    }

    /**
     * Basic browser name resolver (mirrors TelegramNotificationService::parseBrowser).
     */
    protected function parseBrowser(string $ua): string
    {
        if (preg_match('/Edg\//i', $ua))                          return 'Edge';
        if (preg_match('/OPR\//i', $ua) || preg_match('/Opera/i', $ua)) return 'Opera';
        if (preg_match('/Chrome\//i', $ua))                       return 'Chrome';
        if (preg_match('/Firefox\//i', $ua))                      return 'Firefox';
        if (preg_match('/Safari\//i', $ua))                       return 'Safari';
        return 'Other';
    }
}

