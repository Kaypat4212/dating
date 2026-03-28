<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Trust all proxies (ngrok, Cloudflare, etc.) and all forwarded hosts
        $middleware->trustProxies(at: '*', headers: \Symfony\Component\HttpFoundation\Request::HEADER_X_FORWARDED_FOR | \Symfony\Component\HttpFoundation\Request::HEADER_X_FORWARDED_HOST | \Symfony\Component\HttpFoundation\Request::HEADER_X_FORWARDED_PORT | \Symfony\Component\HttpFoundation\Request::HEADER_X_FORWARDED_PROTO | \Symfony\Component\HttpFoundation\Request::HEADER_X_FORWARDED_PREFIX);

        // Build trusted hosts: APP_URL + any comma-separated APP_TRUSTED_URLS
        $trustedHosts = ['.*']; // default: trust all (keeps existing behaviour)
        $appUrl = env('APP_URL', '');
        if ($appUrl) {
            $host = parse_url($appUrl, PHP_URL_HOST);
            $trustedHosts = $host ? [preg_quote($host, '#')] : ['.*'];
        }
        $extraUrls = array_filter(array_map('trim', explode(',', env('APP_TRUSTED_URLS', ''))));
        foreach ($extraUrls as $url) {
            $host = parse_url($url, PHP_URL_HOST);
            if ($host) {
                $trustedHosts[] = preg_quote($host, '#');
            }
        }
        $middleware->trustHosts(at: $trustedHosts);

        // Append to the 'web' middleware group
        $middleware->web(append: [
            \App\Http\Middleware\UpdateLastActive::class,
            \App\Http\Middleware\BlockVpnUsers::class,
            \App\Http\Middleware\TrackHomepageVisits::class,
        ]);

        // Alias for use in routes
        $middleware->alias([
            'profile.complete' => \App\Http\Middleware\EnsureProfileComplete::class,
            'vpn.block' => \App\Http\Middleware\BlockVpnUsers::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
