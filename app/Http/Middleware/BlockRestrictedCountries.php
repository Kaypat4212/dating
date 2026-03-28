<?php

namespace App\Http\Middleware;

use App\Models\SiteSetting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class BlockRestrictedCountries
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Read mode from DB (none | allowlist | blocklist)
        $mode = SiteSetting::get('country_restriction_mode', 'none');

        if ($mode === 'none') {
            return $next($request);
        }

        // Skip admin panel routes — admins must never be locked out
        if (str_starts_with($request->path(), 'admin')) {
            return $next($request);
        }

        // Skip local/private IPs (local dev)
        $ip = $request->ip();
        if ($this->isPrivateIp($ip)) {
            return $next($request);
        }

        // Detect country
        $countryCode = $this->detectCountry($ip);

        // If detection failed, allow through to avoid false-blocking
        if ($countryCode === null) {
            return $next($request);
        }

        // Read the restricted/allowed country list
        $raw  = SiteSetting::get('country_restriction_countries', '[]');
        $list = json_decode($raw, true) ?? [];

        $inList = in_array(strtoupper($countryCode), array_map('strtoupper', $list));

        $blocked = false;
        if ($mode === 'blocklist' && $inList) {
            $blocked = true;
        } elseif ($mode === 'allowlist' && !$inList) {
            $blocked = true;
        }

        if ($blocked) {
            Log::info('Country restriction blocked', [
                'ip'          => $ip,
                'country'     => $countryCode,
                'mode'        => $mode,
                'route'       => $request->path(),
            ]);

            return response()->view('errors.country-blocked', [
                'countryCode' => strtoupper($countryCode),
                'mode'        => $mode,
            ], 403);
        }

        return $next($request);
    }

    /**
     * Detect the 2-letter ISO country code for an IP via ip-api.com (free, no key).
     * Results are cached for 24 hours.
     */
    private function detectCountry(string $ip): ?string
    {
        $cacheKey = "country_detect_{$ip}";

        return Cache::remember($cacheKey, now()->addHours(24), function () use ($ip) {
            try {
                $response = Http::timeout(3)
                    ->get("http://ip-api.com/json/{$ip}", [
                        'fields' => 'status,countryCode',
                    ]);

                if ($response->successful()) {
                    $data = $response->json();
                    if (($data['status'] ?? '') === 'success' && !empty($data['countryCode'])) {
                        return strtoupper($data['countryCode']);
                    }
                }
            } catch (\Throwable $e) {
                Log::warning('Country detection failed', ['ip' => $ip, 'error' => $e->getMessage()]);
            }

            return null;
        });
    }

    /**
     * Returns true for RFC-1918 / loopback addresses.
     */
    private function isPrivateIp(string $ip): bool
    {
        return !filter_var(
            $ip,
            FILTER_VALIDATE_IP,
            FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
        );
    }
}
