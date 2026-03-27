<?php

namespace App\Http\Middleware;

use App\Services\TelegramNotificationService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
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
     * Track homepage visit and send Telegram notification
     */
    protected function trackVisit(Request $request): void
    {
        $ip = $request->ip();
        $userId = Auth::id();
        $cacheKey = "homepage_visit_{$ip}_" . ($userId ?? 'guest');

        // Prevent duplicate notifications within 5 minutes
        if (Cache::has($cacheKey)) {
            return;
        }

        // Cache for 5 minutes to prevent spam
        Cache::put($cacheKey, true, now()->addMinutes(5));

        // Send Telegram notification
        $telegram = app(TelegramNotificationService::class);
        $telegram->notifyHomepageVisit(
            ip: $ip,
            userAgent: $request->userAgent(),
            userId: $userId
        );
    }
}
