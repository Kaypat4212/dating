<?php

namespace App\Http\Middleware;

use App\Models\SiteSetting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\Response;

/**
 * Reads the admin-configured allowed session domains and dynamically sets
 * the session cookie domain to match the current request host.
 *
 * This allows the platform to serve multiple domain names (e.g.
 * heartsconnect.site and www.heartsconnect.site, or a secondary domain)
 * while each one receives a properly scoped session cookie.
 */
class DynamicSessionDomain
{
    public function handle(Request $request, Closure $next): Response
    {
        // Only act when dynamic matching is enabled
        $dynamicEnabled = (bool) SiteSetting::get('session_dynamic_domain', true);

        if ($dynamicEnabled) {
            $requestHost = $request->getHost(); // e.g. "www.heartsconnect.site"

            $stored = SiteSetting::get('session_allowed_domains', '[]');
            $allowed = json_decode($stored, true);

            if (is_array($allowed) && in_array($requestHost, $allowed, true)) {
                // Scope the cookie exactly to the current host
                Config::set('session.domain', $requestHost);
            } else {
                // Fall back to the admin-configured primary domain
                $primary = SiteSetting::get('session_primary_domain', '');
                if ($primary) {
                    Config::set('session.domain', $primary);
                }
            }
        }

        // Apply lifetime and cookie settings from DB if present
        if ($lifetime = SiteSetting::get('session_lifetime')) {
            Config::set('session.lifetime', (int) $lifetime);
        }
        if (($secure = SiteSetting::get('session_secure_cookie')) !== null) {
            Config::set('session.secure', (bool) $secure);
        }
        if ($sameSite = SiteSetting::get('session_same_site')) {
            Config::set('session.same_site', $sameSite);
        }

        return $next($request);
    }
}
