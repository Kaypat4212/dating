<?php

namespace App\Providers;

use App\Models\Message;
use App\Observers\MessageObserver;
use App\Services\MailSettingsService;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * ⚠️  DO NOT run `php artisan route:cache` on this app.
     *     Filament + Livewire register components via closures/callbacks that
     *     are silenced by the route cache, causing:
     *       ComponentNotFoundException: Unable to find component: [filament.auth.pages.login]
     *     on every POST /livewire/update request.
     *
     *     To cache Filament components use:  php artisan filament:cache-components
     *     To clear Filament component cache: php artisan filament:clear-cached-components
     */
    public function boot(): void
    {
        // Register model observers
        Message::observe(MessageObserver::class);

        // Apply admin-configured mail settings from the database (cache-backed).
        try {
            MailSettingsService::applyFromSettings();
            MailSettingsService::applyQueueSettings();
        } catch (\Throwable) {
            // DB not yet available (e.g. fresh install) — fall back to .env values.
        }

        // ── SMTP SSL peer-name bypass for cPanel shared hosting ───────────────
        // Laravel 12 uses Symfony Mailer.  MailManager::configureSmtpTransport()
        // only handles 'source_ip' and 'timeout' — it never applies the legacy
        // 'stream.ssl' config key.  On cPanel the server presents a wildcard cert
        // (e.g. *.black.host) instead of the configured MAIL_HOST cert, causing:
        //   Peer certificate CN=`*.black.host' did not match expected CN=`…'
        // Fix: register a custom SMTP factory that calls setStreamOptions().
        \Illuminate\Support\Facades\Mail::extend('smtp', function (array $config) {
            $scheme = $config['scheme'] ?? (($config['port'] ?? 0) == 465 ? 'smtps' : 'smtp');

            $factory = new \Symfony\Component\Mailer\Transport\Smtp\EsmtpTransportFactory;
            $transport = $factory->create(new \Symfony\Component\Mailer\Transport\Dsn(
                $scheme,
                $config['host'] ?? '127.0.0.1',
                $config['username'] ?? null,
                $config['password'] ?? null,
                $config['port'] ?? null,
                $config,
            ));

            if ($transport instanceof \Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport) {
                $stream = $transport->getStream();
                if ($stream instanceof \Symfony\Component\Mailer\Transport\Smtp\Stream\SocketStream) {
                    if (isset($config['timeout'])) {
                        $stream->setTimeout($config['timeout']);
                    }
                    $stream->setStreamOptions([
                        'ssl' => [
                            'verify_peer'       => false,
                            'verify_peer_name'  => false,
                            'allow_self_signed' => true,
                        ],
                    ]);
                }
            }

            return $transport;
        });

        // ── Dynamic URL root — works for XAMPP, ngrok, production, CLI server ──
        //
        // Strategy:
        //  1. Always read the ACTUAL incoming scheme+host from the request,
        //     which respects X-Forwarded-Host/Proto headers (ngrok, Cloudflare…)
        //  2. Append the subfolder path from APP_URL *only* when the request is
        //     coming from the same host that APP_URL is configured for (i.e. the
        //     XAMPP localhost case).  Any other host (ngrok, live domain) means
        //     the app is served at the domain root — no path prefix needed.
        $isCliServer     = PHP_SAPI === 'cli-server';
        $appUrl          = config('app.url');                               // http://localhost/dating/public
        $configuredHost  = parse_url($appUrl, PHP_URL_HOST) ?? 'localhost'; // localhost
        $appPath         = rtrim(parse_url($appUrl, PHP_URL_PATH) ?? '', '/'); // /dating/public

        // getSchemeAndHttpHost() respects X-Forwarded-Proto / X-Forwarded-Host
        // because trustProxies(at:'*') is set in bootstrap/app.php.
        $requestSchemeHost = request()->getSchemeAndHttpHost(); // e.g. https://xxxx.ngrok.io
        $requestHost       = request()->getHost();              // e.g. xxxx.ngrok.io

        if ($isCliServer) {
            // PHP built-in server: public/ IS the docroot — no subfolder prefix.
            $forcedRoot = $requestSchemeHost;
            $basePath   = '';
            app('config')->set('app.asset_url', $requestSchemeHost);
        } elseif ($requestHost === $configuredHost && $appPath !== '') {
            // XAMPP / Apache subfolder: same host as APP_URL, keep the path.
            $forcedRoot = $requestSchemeHost . $appPath;
            $basePath   = $appPath;
        } else {
            // ngrok, live domain, any other host: app is at the domain root.
            $forcedRoot = $requestSchemeHost;
            $basePath   = '';
        }

        \Illuminate\Support\Facades\URL::forceRootUrl($forcedRoot);

        if ($basePath !== '') {
            Livewire::setUpdateRoute(function ($handle) use ($basePath) {
                return Route::post("{$basePath}/livewire/update", $handle)
                    ->name('livewire.update');
            });

            // Fix Livewire JavaScript assets for subfolder deployments
            Livewire::setScriptRoute(function ($handle) use ($basePath) {
                return Route::get("{$basePath}/livewire/livewire.js", $handle)
                    ->name('livewire.js');
            });
        }
    }
}
