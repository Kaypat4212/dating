<?php

namespace App\Http\Middleware;

use App\Models\HomepageVisit;
use App\Models\SiteSetting;
use App\Services\TelegramNotificationService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
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
     * Track homepage visit: store in DB and (conditionally) send Telegram notification.
     */
    protected function trackVisit(Request $request): void
    {
        $ip       = $request->ip();
        $ua       = $request->userAgent() ?? '';
        $userId   = Auth::id();
        $isBot    = $this->isBot($ua);

        // Per-IP/user deduplication cooldown (admin-configurable, default 5 min)
        $cooldownMinutes = max(1, (int) SiteSetting::get('telegram_visit_per_ip_cooldown', 5));
        $cacheKey = "homepage_visit_{$ip}_" . ($userId ?? 'guest');

        if (Cache::has($cacheKey)) {
            return;
        }
        Cache::put($cacheKey, true, now()->addMinutes($cooldownMinutes));

        try {
            /** @var TelegramNotificationService $telegram */
            $telegram = app(TelegramNotificationService::class);

            // Resolve geo info once and reuse for both DB record and Telegram
            $geo     = $telegram->lookupGeoIp($ip);
            $browser = $ua ? $this->parseBrowser($ua) : null;

            // ── Store visit in database ─────────────────────────────────
            HomepageVisit::create([
                'ip_address'   => $ip,
                'user_id'      => $userId,
                'user_agent'   => $ua ?: null,
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

            // ── Telegram notification guards ────────────────────────────

            // 1. Master switch — admin can disable visit notifications entirely
            if (! filter_var(SiteSetting::get('telegram_visit_notifications_enabled', '1'), FILTER_VALIDATE_BOOLEAN)) {
                return;
            }

            // 2. Bot filter — skip if UA matches a known bot/crawler pattern
            if ($isBot && filter_var(SiteSetting::get('telegram_visit_filter_bots', '1'), FILTER_VALIDATE_BOOLEAN)) {
                Log::info('TrackHomepageVisits: bot visit skipped', ['ip' => $ip, 'ua' => substr($ua, 0, 120)]);
                return;
            }

            // 3. Hosting / datacenter IP filter — many bots use cloud/datacenter ranges
            if (filter_var(SiteSetting::get('telegram_visit_filter_datacenter', '1'), FILTER_VALIDATE_BOOLEAN)) {
                $isHosting = $geo['hosting'] ?? false;
                if ($isHosting) {
                    Log::info('TrackHomepageVisits: datacenter IP skipped', ['ip' => $ip]);
                    return;
                }
            }

            // 4. Global hourly rate limit — prevents floods from unknown bots or scrapers
            $hourlyLimit = (int) SiteSetting::get('telegram_visit_hourly_limit', 30);
            if ($hourlyLimit > 0) {
                $rateLimitKey = 'telegram_visit_notif_hourly';
                if (RateLimiter::tooManyAttempts($rateLimitKey, $hourlyLimit)) {
                    Log::info('TrackHomepageVisits: hourly rate limit reached, notification suppressed', [
                        'limit' => $hourlyLimit,
                        'ip'    => $ip,
                    ]);
                    return;
                }
                RateLimiter::hit($rateLimitKey, 3600); // count in 1-hour window
            }

            // ── All guards passed — send notification ───────────────────
            $telegram->notifyHomepageVisit(
                ip:        $ip,
                userAgent: $ua ?: null,
                userId:    $userId,
                geo:       $geo,
            );
        } catch (\Throwable $e) {
            Log::error('TrackHomepageVisits error: ' . $e->getMessage(), ['exception' => $e]);
        }
    }

    /**
     * Detect known bots, crawlers, and HTTP libraries from the user-agent string.
     * Returns true if the request looks like an automated, non-human visitor.
     */
    protected function isBot(string $ua): bool
    {
        if (empty($ua)) {
            return true; // No user-agent = almost certainly a bot or script
        }

        $lower = strtolower($ua);

        // Known search engine & social bots
        $botKeywords = [
            // Search engines
            'googlebot', 'google-inspectiontool', 'adsbot-google', 'mediapartners-google',
            'bingbot', 'msnbot', 'slurp', 'duckduckbot', 'duckduckgo',
            'baiduspider', 'yandexbot', 'yandex.com/bots',
            'sogou', 'exabot', 'seznambot',
            // Social / content bots
            'facebookexternalhit', 'facebot', 'twitterbot', 'linkedinbot',
            'whatsapp', 'telegrambot', 'discordbot', 'slackbot',
            'applebot', 'ia_archiver', 'archive.org_bot',
            // SEO & analytics crawlers
            'semrushbot', 'ahrefsbot', 'mj12bot', 'dotbot', 'rogerbot',
            'majestic', 'seznambot', 'blexbot', 'seokicks', 'uptimerobot',
            'pingdom', 'statuscake', 'zabbix', 'newrelic',
            // AI training bots
            'gptbot', 'chatgpt-user', 'anthropic', 'claude-web', 'cohere-ai',
            'ccbot', 'pimgptbot', 'omgilibot',
            // Generic crawlers, scrapers, libraries
            'crawl', 'spider', 'scrape', 'bot/', '/bot',
            'python-requests', 'python-urllib', 'python/',
            'curl/', 'wget/', 'libwww', 'lwp-',
            'java/', 'go-http-client', 'okhttp/',
            'apache-httpclient', 'reactor-netty',
            'postman', 'insomnia', 'httpie',
            'scrapy', 'mechanize', 'httpclient',
            'axios/', 'node-fetch', 'got/',
        ];

        foreach ($botKeywords as $keyword) {
            if (str_contains($lower, $keyword)) {
                return true;
            }
        }

        // Generic heuristic: real browsers always include Gecko/AppleWebKit/Trident
        // A UA with none of these and no "Mozilla" is almost certainly a script
        if (
            ! str_contains($lower, 'mozilla') &&
            ! str_contains($lower, 'webkit') &&
            ! str_contains($lower, 'gecko') &&
            ! str_contains($lower, 'trident') &&
            ! str_contains($lower, 'presto')
        ) {
            return true;
        }

        return false;
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

