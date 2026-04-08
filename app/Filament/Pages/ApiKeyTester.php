<?php

namespace App\Filament\Pages;

use App\Services\DailyCoService;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

class ApiKeyTester extends Page
{
    protected static ?string $slug = 'api-key-tester';

    protected string $view = 'filament.pages.api-key-tester';

    public static function getNavigationIcon(): ?string  { return 'heroicon-o-key'; }
    public static function getNavigationLabel(): string  { return 'API Key Tester'; }
    public static function getNavigationGroup(): ?string { return 'System'; }
    public static function getNavigationSort(): ?int     { return 98; }

    public function getTitle(): string | Htmlable { return 'API Key Tester'; }

    /** Only superadmin may run this. */
    public static function canAccess(): bool
    {
        return Auth::check() && Auth::id() === 1;
    }

    // ── Per-service result state ──────────────────────────────────────────

    /** @var array<string, array{status: string, message: string, detail: string, ms: int|null}> */
    public array $results = [];
    public bool $isRunning = false;

    // ── Test runners ──────────────────────────────────────────────────────

    /**
     * Run ALL tests at once.
     */
    public function testAll(): void
    {
        $this->isRunning = true;
        $this->results   = [];

        $this->runDailyCo();
        $this->runGroq();
        $this->runIpHub();
        $this->runProxyCheck();
        $this->runTelegram();
        $this->runMailSmtp();
        $this->runReverb();
        $this->runDatabase();

        $this->isRunning = false;
    }

    // ─────────────────────────────────────────────────────────────────────
    // Individual tests — each one can also be called in isolation from the UI
    // ─────────────────────────────────────────────────────────────────────

    public function testDailyCo(): void  { $this->runDailyCo(); }
    public function testGroq(): void     { $this->runGroq(); }
    public function testIpHub(): void    { $this->runIpHub(); }
    public function testProxyCheck(): void { $this->runProxyCheck(); }
    public function testTelegram(): void { $this->runTelegram(); }
    public function testMailSmtp(): void { $this->runMailSmtp(); }
    public function testReverb(): void   { $this->runReverb(); }
    public function testDatabase(): void { $this->runDatabase(); }

    // ─────────────────────────────────────────────────────────────────────
    // Internal implementation
    // ─────────────────────────────────────────────────────────────────────

    private function pass(string $service, string $message, string $detail = '', ?int $ms = null): void
    {
        $this->results[$service] = [
            'status'  => 'pass',
            'message' => $message,
            'detail'  => $detail,
            'ms'      => $ms,
        ];
    }

    private function fail(string $service, string $message, string $detail = '', ?int $ms = null): void
    {
        $this->results[$service] = [
            'status'  => 'fail',
            'message' => $message,
            'detail'  => $detail,
            'ms'      => $ms,
        ];
    }

    private function warn(string $service, string $message, string $detail = '', ?int $ms = null): void
    {
        $this->results[$service] = [
            'status'  => 'warn',
            'message' => $message,
            'detail'  => $detail,
            'ms'      => $ms,
        ];
    }

    /** Measure execution time of a closure in milliseconds. */
    private function timed(callable $fn): array
    {
        $start  = hrtime(true);
        $result = $fn();
        $ms     = (int) round((hrtime(true) - $start) / 1_000_000);
        return [$result, $ms];
    }

    // ── Daily.co ──────────────────────────────────────────────────────────

    private function runDailyCo(): void
    {
        $apiKey = config('services.dailyco.api_key', '');
        $domain = config('services.dailyco.domain', '');

        if (empty($apiKey)) {
            $this->warn(
                'dailyco',
                'Not configured — using Jitsi fallback',
                'DAILY_CO_API_KEY is empty. Voice/video calls fall back to free Jitsi Meet (meet.jit.si). Add DAILY_CO_API_KEY to .env to use Daily.co.'
            );
            return;
        }

        // Test the API key by listing rooms (GET /v1/rooms)
        [$response, $ms] = $this->timed(fn() =>
            Http::timeout(8)
                ->withToken($apiKey)
                ->get('https://api.daily.co/v1/rooms', ['limit' => 1])
        );

        if ($response->successful()) {
            $roomCount = data_get($response->json(), 'total_count', '?');
            $detail    = "API key valid | Domain: " . ($domain ?: '(not set)') . " | Rooms: {$roomCount}";
            if (empty($domain)) {
                $this->warn('dailyco', 'API key valid but DAILY_CO_DOMAIN not set', $detail . ' | Set DAILY_CO_DOMAIN in .env for custom subdomain calls', $ms);
            } else {
                $this->pass('dailyco', 'Daily.co API key valid & reachable', $detail, $ms);
            }
        } elseif ($response->status() === 401) {
            $this->fail('dailyco', 'Invalid API key (401 Unauthorized)', 'Check DAILY_CO_API_KEY in .env — get your key at daily.co/dashboard', $ms);
        } elseif ($response->status() === 429) {
            $this->warn('dailyco', 'Key valid but rate-limited (429)', 'Too many requests to Daily.co API', $ms);
        } else {
            $this->fail('dailyco', "Unexpected HTTP {$response->status()}", substr($response->body(), 0, 200), $ms);
        }
    }

    // ── Groq (AI) ─────────────────────────────────────────────────────────

    private function runGroq(): void
    {
        $key = config('services.groq.api_key', '') ?: \App\Models\SiteSetting::get('ai_groq_api_key', '');

        if (empty($key)) {
            $this->warn('groq', 'Not configured', 'No GROQ_API_KEY set — AI features use built-in templates only');
            return;
        }

        [$response, $ms] = $this->timed(fn() =>
            Http::timeout(10)
                ->withToken($key)
                ->post('https://api.groq.com/openai/v1/chat/completions', [
                    'model'      => 'llama-3.1-8b-instant',
                    'messages'   => [['role' => 'user', 'content' => 'Say "ok" in one word']],
                    'max_tokens' => 5,
                ])
        );

        if ($response->successful()) {
            $reply = data_get($response->json(), 'choices.0.message.content', '');
            $this->pass('groq', 'API key valid & responding', "Model reply: \"{$reply}\"", $ms);
        } elseif ($response->status() === 401) {
            $this->fail('groq', 'Invalid API key (401 Unauthorized)', $response->body(), $ms);
        } elseif ($response->status() === 429) {
            // Rate limited = key is valid but quota hit
            $this->warn('groq', 'Key valid but rate-limited (429)', 'Quota may be exhausted — check console.groq.com', $ms);
        } else {
            $this->fail('groq', "Unexpected response HTTP {$response->status()}", $response->body(), $ms);
        }
    }

    // ── IPHub ─────────────────────────────────────────────────────────────

    private function runIpHub(): void
    {
        $key = config('services.iphub.api_key', '');

        if (empty($key)) {
            $this->warn('iphub', 'Not configured', 'IPHUB_API_KEY is empty — VPN detection via IPHub disabled');
            return;
        }

        [$response, $ms] = $this->timed(fn() =>
            Http::timeout(8)
                ->withHeaders(['X-Key' => $key])
                ->get('https://v2.api.iphub.info/ip/8.8.8.8')
        );

        if ($response->successful()) {
            $block = data_get($response->json(), 'block', '?');
            $isp   = data_get($response->json(), 'isp', '?');
            $this->pass('iphub', 'API key valid', "8.8.8.8 → ISP: {$isp}, block: {$block}", $ms);
        } elseif ($response->status() === 401 || $response->status() === 403) {
            $this->fail('iphub', 'Invalid API key', "HTTP {$response->status()}", $ms);
        } elseif ($response->status() === 429) {
            $this->warn('iphub', 'Key valid but rate-limited (429)', 'Daily quota may be exhausted', $ms);
        } else {
            $this->fail('iphub', "Unexpected HTTP {$response->status()}", substr($response->body(), 0, 200), $ms);
        }
    }

    // ── ProxyCheck ────────────────────────────────────────────────────────

    private function runProxyCheck(): void
    {
        $key = config('services.proxycheck.api_key', '');

        if (empty($key)) {
            $this->warn('proxycheck', 'Not configured', 'PROXYCHECK_API_KEY is empty — VPN detection via ProxyCheck disabled');
            return;
        }

        [$response, $ms] = $this->timed(fn() =>
            Http::timeout(8)->get("https://proxycheck.io/v2/8.8.8.8", [
                'key'  => $key,
                'vpn'  => 1,
                'risk' => 1,
            ])
        );

        if ($response->successful()) {
            $status = data_get($response->json(), 'status', '');
            if ($status === 'ok' || $status === 'warning') {
                $proxy = data_get($response->json(), '8.8.8.8.proxy', '?');
                $this->pass('proxycheck', 'API key valid', "8.8.8.8 → proxy: {$proxy}", $ms);
            } elseif ($status === 'denied') {
                $this->fail('proxycheck', 'API key denied', data_get($response->json(), 'message', ''), $ms);
            } else {
                $this->warn('proxycheck', "Unexpected status: {$status}", $response->body(), $ms);
            }
        } else {
            $this->fail('proxycheck', "HTTP {$response->status()}", substr($response->body(), 0, 200), $ms);
        }
    }

    // ── Telegram ──────────────────────────────────────────────────────────

    private function runTelegram(): void
    {
        $token  = config('services.telegram.bot_token', '');
        $chatId = config('services.telegram.chat_id', '');

        if (empty($token)) {
            $this->warn('telegram', 'Not configured', 'TELEGRAM_BOT_TOKEN is empty');
            return;
        }

        [$response, $ms] = $this->timed(fn() =>
            Http::timeout(8)->get("https://api.telegram.org/bot{$token}/getMe")
        );

        if ($response->successful() && data_get($response->json(), 'ok')) {
            $botName = data_get($response->json(), 'result.username', '?');
            $detail  = "Bot: @{$botName}";
            if (empty($chatId)) {
                $detail .= ' | ⚠ TELEGRAM_CHAT_ID not set — notifications won\'t send';
                $this->warn('telegram', 'Bot token valid, chat ID missing', $detail, $ms);
            } else {
                $detail .= " | Chat ID: {$chatId}";
                $this->pass('telegram', 'Bot token valid', $detail, $ms);
            }
        } elseif ($response->status() === 401) {
            $this->fail('telegram', 'Invalid bot token', 'HTTP 401 — check TELEGRAM_BOT_TOKEN', $ms);
        } else {
            $this->fail('telegram', "HTTP {$response->status()}", substr($response->body(), 0, 200), $ms);
        }
    }

    // ── Mail / SMTP ───────────────────────────────────────────────────────

    private function runMailSmtp(): void
    {
        $mailer = config('mail.default', 'log');
        $host   = config('mail.mailers.smtp.host', '');
        $port   = config('mail.mailers.smtp.port', '');

        if ($mailer === 'log') {
            $this->warn('mail', 'Mail driver is "log"', 'No real emails are being sent. Change MAIL_MAILER in .env');
            return;
        }

        if ($mailer === 'smtp' && empty($host)) {
            $this->fail('mail', 'SMTP configured but host is empty', 'Set MAIL_HOST in .env');
            return;
        }

        // Try a real TCP socket connection to SMTP host
        if ($mailer === 'smtp') {
            $start = hrtime(true);
            $sock  = @fsockopen($host, (int) $port, $errno, $errstr, 5);
            $ms    = (int) round((hrtime(true) - $start) / 1_000_000);

            if ($sock) {
                fclose($sock);
                $from = config('mail.from.address', 'unknown');
                $this->pass('mail', "SMTP reachable ({$host}:{$port})", "From: {$from} | Driver: {$mailer}", $ms);
            } else {
                $this->fail('mail', "SMTP unreachable — {$errstr}", "{$host}:{$port} (errno {$errno})", $ms);
            }
        } else {
            $this->pass('mail', "Driver: {$mailer}", "Non-SMTP drivers don't require connection testing");
        }
    }

    // ── Reverb WebSocket ──────────────────────────────────────────────────

    private function runReverb(): void
    {
        $host    = env('REVERB_HOST', '0.0.0.0');
        $port    = (int) env('REVERB_PORT', 8080);
        $appKey  = env('REVERB_APP_KEY', '');
        $appId   = env('REVERB_APP_ID', '');

        $testHost = ($host === '0.0.0.0' || empty($host)) ? '127.0.0.1' : $host;

        $start = hrtime(true);
        $sock  = @fsockopen($testHost, $port, $errno, $errstr, 3);
        $ms    = (int) round((hrtime(true) - $start) / 1_000_000);

        if (!$sock) {
            $this->fail('reverb', "Reverb NOT running on {$testHost}:{$port}", "Error: {$errstr} ({$errno}) — start it via Artisan Runner → Start Reverb Server", $ms);
            return;
        }

        fclose($sock);

        if (empty($appKey) || empty($appId)) {
            $this->warn('reverb', "Port {$port} reachable but REVERB_APP_KEY/ID missing", 'Set REVERB_APP_KEY and REVERB_APP_ID in .env', $ms);
            return;
        }

        $this->pass('reverb', "Reverb running & reachable on :{$port}", "App ID: {$appId} | App Key: " . substr($appKey, 0, 8) . '…', $ms);
    }

    // ── Database ──────────────────────────────────────────────────────────

    private function runDatabase(): void
    {
        $start = hrtime(true);
        try {
            \Illuminate\Support\Facades\DB::connection()->getPdo();
            $db     = config('database.connections.' . config('database.default') . '.database', '?');
            $driver = config('database.default', '?');
            $ms     = (int) round((hrtime(true) - $start) / 1_000_000);

            // Quick sanity: count users table
            $userCount = \Illuminate\Support\Facades\DB::table('users')->count();
            $this->pass('database', 'Connection OK', "Driver: {$driver} | DB: {$db} | Users: {$userCount}", $ms);
        } catch (\Throwable $e) {
            $ms = (int) round((hrtime(true) - $start) / 1_000_000);
            $this->fail('database', 'Database connection failed', $e->getMessage(), $ms);
        }
    }
}
