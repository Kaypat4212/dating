<?php

namespace App\Filament\Pages;

use App\Services\AgoraTokenService;
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

    // â”€â”€ Per-service result state â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€

    /** @var array<string, array{status: string, message: string, detail: string, ms: int|null}> */
    public array $results = [];
    public bool $isRunning = false;

    // â”€â”€ Run ALL tests â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€

    public function testAll(): void
    {
        $this->isRunning = true;
        $this->results   = [];

        // Calls
        $this->runDailyCo();
        $this->runAgora();
        // AI
        $this->runGroq();
        $this->runOpenAI();
        // Auth & Social
        $this->runGoogle();
        // Payments
        $this->runStripe();
        // Communications
        $this->runTwilio();
        $this->runTelegram();
        $this->runMailSmtp();
        // Storage
        $this->runCloudinary();
        $this->runAwsS3();
        // Push Notifications
        $this->runFirebase();
        // Security / VPN
        $this->runIpHub();
        $this->runProxyCheck();
        // Infrastructure
        $this->runPusher();
        $this->runReverb();
        $this->runDatabase();

        $this->isRunning = false;
    }

    // â”€â”€ Individual test methods (callable from card buttons) â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€

    public function testDailyCo(): void    { $this->runDailyCo(); }
    public function testAgora(): void      { $this->runAgora(); }
    public function testGroq(): void       { $this->runGroq(); }
    public function testOpenAI(): void     { $this->runOpenAI(); }
    public function testGoogle(): void     { $this->runGoogle(); }
    public function testStripe(): void     { $this->runStripe(); }
    public function testTwilio(): void     { $this->runTwilio(); }
    public function testTelegram(): void   { $this->runTelegram(); }
    public function testMailSmtp(): void   { $this->runMailSmtp(); }
    public function testCloudinary(): void { $this->runCloudinary(); }
    public function testAwsS3(): void      { $this->runAwsS3(); }
    public function testFirebase(): void   { $this->runFirebase(); }
    public function testIpHub(): void      { $this->runIpHub(); }
    public function testProxyCheck(): void { $this->runProxyCheck(); }
    public function testPusher(): void     { $this->runPusher(); }
    public function testReverb(): void     { $this->runReverb(); }
    public function testDatabase(): void   { $this->runDatabase(); }

    // â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    // Result helpers
    // â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€

    private function pass(string $svc, string $msg, string $detail = '', ?int $ms = null): void
    {
        $this->results[$svc] = ['status' => 'pass', 'message' => $msg, 'detail' => $detail, 'ms' => $ms];
    }

    private function fail(string $svc, string $msg, string $detail = '', ?int $ms = null): void
    {
        $this->results[$svc] = ['status' => 'fail', 'message' => $msg, 'detail' => $detail, 'ms' => $ms];
    }

    private function warn(string $svc, string $msg, string $detail = '', ?int $ms = null): void
    {
        $this->results[$svc] = ['status' => 'warn', 'message' => $msg, 'detail' => $detail, 'ms' => $ms];
    }

    private function timed(callable $fn): array
    {
        $start  = hrtime(true);
        $result = $fn();
        $ms     = (int) round((hrtime(true) - $start) / 1_000_000);
        return [$result, $ms];
    }

    // â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    // â”€â”€ CALLS â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    // â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€

    private function runDailyCo(): void
    {
        // Use env() directly to avoid config caching issues - ensures we always test current .env values
        $apiKey = env('DAILY_CO_API_KEY', '');
        $domain = env('DAILY_CO_DOMAIN', '');

        if (empty($apiKey)) {
            $this->fail('dailyco', '❌ REQUIRED: Daily.co API key not set',
                'DAILY_CO_API_KEY is empty. Voice/video calls will NOT work. Sign up free at https://dashboard.daily.co and add key to .env file.');
            return;
        }

        [$response, $ms] = $this->timed(fn() =>
            Http::timeout(8)->withToken($apiKey)->get('https://api.daily.co/v1/rooms', ['limit' => 1])
        );

        if ($response->successful()) {
            $total  = data_get($response->json(), 'total_count', '?');
            $detail = "Rooms: {$total} | Domain: " . ($domain ?: '(not set)');
            empty($domain)
                ? $this->warn('dailyco', 'Key valid but DAILY_CO_DOMAIN not set', $detail . ' | Set DAILY_CO_DOMAIN in .env for branded rooms', $ms)
                : $this->pass('dailyco', 'API key valid & reachable', $detail, $ms);
        } elseif ($response->status() === 401) {
            $this->fail('dailyco', 'Invalid API key (401)', 'Check DAILY_CO_API_KEY in .env', $ms);
        } elseif ($response->status() === 429) {
            $this->warn('dailyco', 'Rate-limited (429) â€” key is valid', 'Too many requests to Daily.co API', $ms);
        } else {
            $this->fail('dailyco', "HTTP {$response->status()}", substr($response->body(), 0, 200), $ms);
        }
    }

    private function runAgora(): void
    {
        $appId   = config('services.agora.app_id', '');
        $appCert = config('services.agora.app_certificate', '');

        if (empty($appId) || empty($appCert)) {
            $this->warn('agora', 'Not configured',
                'AGORA_APP_ID or AGORA_APP_CERTIFICATE is empty. App currently uses Daily.co; Agora is kept as legacy fallback.');
            return;
        }

        // Step 1: generate token locally
        try {
            $svc = app(AgoraTokenService::class);
            [$token, $ms] = $this->timed(fn() => $svc->generateRtcToken('test-channel', 0, 60));
            if (empty($token) || !str_starts_with($token, '007')) {
                $this->fail('agora', 'Token has unexpected format', "Got: " . substr((string)$token, 0, 20) . 'â€¦');
                return;
            }
        } catch (\Throwable $e) {
            $this->fail('agora', 'Token generation failed', $e->getMessage());
            return;
        }

        // Step 2: verify App ID against Agora API
        [$response, $httpMs] = $this->timed(fn() =>
            Http::timeout(6)->withHeaders(['Content-Type' => 'application/json'])
                ->post("https://api.agora.io/v1/apps/{$appId}/cloud_recording/acquire", [
                    'cname' => 'test', 'uid' => '0', 'clientRequest' => [],
                ])
        );

        if ($response->status() === 404) {
            $this->fail('agora', 'App ID not found on Agora', "HTTP 404 â€” double-check AGORA_APP_ID", $httpMs + $ms);
        } elseif ($response->status() >= 500) {
            $this->warn('agora', 'Agora API unreachable (server error)', "HTTP {$response->status()}", $httpMs + $ms);
        } else {
            $this->pass('agora', 'App ID verified & token generation OK',
                "App ID: " . substr($appId, 0, 8) . "â€¦ | Token: " . substr($token, 0, 12) . "â€¦ | HTTP {$response->status()}",
                $ms + $httpMs);
        }
    }

    // â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    // â”€â”€ AI â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    // â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€

    private function runGroq(): void
    {
        $key = config('services.groq.api_key', '') ?: \App\Models\SiteSetting::get('ai_groq_api_key', '');

        if (empty($key)) {
            $this->warn('groq', 'Not configured', 'No GROQ_API_KEY set â€” AI features use built-in templates only');
            return;
        }

        [$response, $ms] = $this->timed(fn() =>
            Http::timeout(10)->withToken($key)
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
            $this->fail('groq', 'Invalid API key (401)', $response->body(), $ms);
        } elseif ($response->status() === 429) {
            $this->warn('groq', 'Key valid but rate-limited (429)', 'Quota may be exhausted â€” check console.groq.com', $ms);
        } else {
            $this->fail('groq', "HTTP {$response->status()}", $response->body(), $ms);
        }
    }

    private function runOpenAI(): void
    {
        $key = env('OPENAI_API_KEY', '');

        if (empty($key)) {
            $this->warn('openai', 'Not configured', 'OPENAI_API_KEY is empty â€” app uses Groq AI by default. Add this as a premium AI fallback.');
            return;
        }

        [$response, $ms] = $this->timed(fn() =>
            Http::timeout(10)->withToken($key)->get('https://api.openai.com/v1/models')
        );

        if ($response->successful()) {
            $count = count(data_get($response->json(), 'data', []));
            $this->pass('openai', 'API key valid', "Models available: {$count} | Key: " . substr($key, 0, 10) . 'â€¦', $ms);
        } elseif ($response->status() === 401) {
            $this->fail('openai', 'Invalid API key (401)', 'Check OPENAI_API_KEY in .env', $ms);
        } elseif ($response->status() === 429) {
            $this->warn('openai', 'Key valid but quota exceeded (429)', 'Check usage at platform.openai.com/usage', $ms);
        } else {
            $this->fail('openai', "HTTP {$response->status()}", substr($response->body(), 0, 200), $ms);
        }
    }

    // â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    // â”€â”€ AUTH & SOCIAL â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    // â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€

    private function runGoogle(): void
    {
        $clientId     = config('services.google.client_id', '');
        $clientSecret = config('services.google.client_secret', '');
        $redirect     = config('services.google.redirect', '');

        if (empty($clientId)) {
            $this->warn('google', 'Not configured', 'GOOGLE_CLIENT_ID is empty â€” "Sign in with Google" is disabled.');
            return;
        }

        if (!str_contains($clientId, '.apps.googleusercontent.com')) {
            $this->warn('google', 'Client ID format looks incorrect',
                'Google OAuth Client IDs must end in .apps.googleusercontent.com â€” check GOOGLE_CLIENT_ID');
            return;
        }

        if (empty($clientSecret)) {
            $this->fail('google', 'GOOGLE_CLIENT_SECRET is empty', 'Both Client ID and Client Secret are required.');
            return;
        }

        // Connectivity check against Google's OIDC discovery document (public endpoint)
        [$response, $ms] = $this->timed(fn() =>
            Http::timeout(6)->get('https://accounts.google.com/.well-known/openid-configuration')
        );

        if ($response->successful()) {
            $this->pass('google', 'Credentials set & Google reachable',
                "Client ID: â€¦" . substr($clientId, -30) . " | Redirect: {$redirect}", $ms);
        } else {
            $this->warn('google', 'Credentials set but Google unreachable',
                "HTTP {$response->status()} â€” may be a server-side network restriction", $ms);
        }
    }

    // â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    // â”€â”€ PAYMENTS â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    // â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€

    private function runStripe(): void
    {
        $key            = config('services.stripe.secret', '') ?: env('STRIPE_SECRET', '');
        $publishableKey = config('services.stripe.key', '') ?: env('STRIPE_KEY', '');
        $webhook        = env('STRIPE_WEBHOOK_SECRET', '');

        if (empty($key)) {
            $this->warn('stripe', 'Not configured', 'STRIPE_SECRET is empty â€” premium subscriptions/payments will not work.');
            return;
        }

        [$response, $ms] = $this->timed(fn() =>
            Http::timeout(8)->withBasicAuth($key, '')->get('https://api.stripe.com/v1/balance')
        );

        if ($response->successful()) {
            $avail    = data_get($response->json(), 'available.0.amount', 0);
            $currency = strtoupper(data_get($response->json(), 'available.0.currency', 'usd'));
            $mode     = str_starts_with($key, 'sk_live') ? 'LIVE âš ' : 'TEST';
            $missing  = empty($webhook) ? ' | âš  STRIPE_WEBHOOK_SECRET not set' : '';
            $this->pass('stripe', "Secret key valid ({$mode} mode)",
                "Available balance: {$avail} {$currency}{$missing}", $ms);
        } elseif ($response->status() === 401) {
            $this->fail('stripe', 'Invalid secret key (401)', 'Check STRIPE_SECRET in .env', $ms);
        } else {
            $this->fail('stripe', "HTTP {$response->status()}", substr($response->body(), 0, 200), $ms);
        }
    }

    // â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    // â”€â”€ COMMUNICATIONS â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    // â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€

    private function runTwilio(): void
    {
        $sid   = env('TWILIO_ACCOUNT_SID', '');
        $token = env('TWILIO_AUTH_TOKEN', '');
        $from  = env('TWILIO_FROM', '');

        if (empty($sid) || empty($token)) {
            $this->warn('twilio', 'Not configured',
                'TWILIO_ACCOUNT_SID or TWILIO_AUTH_TOKEN is empty â€” SMS phone verification is disabled.');
            return;
        }

        [$response, $ms] = $this->timed(fn() =>
            Http::timeout(8)->withBasicAuth($sid, $token)
                ->get("https://api.twilio.com/2010-04-01/Accounts/{$sid}.json")
        );

        if ($response->successful()) {
            $status  = data_get($response->json(), 'status', '?');
            $name    = data_get($response->json(), 'friendly_name', '?');
            $detail  = "Account: {$name} | Status: {$status}";
            $detail .= empty($from) ? ' | âš  TWILIO_FROM not set' : " | From: {$from}";
            $this->pass('twilio', 'Credentials valid', $detail, $ms);
        } elseif ($response->status() === 401) {
            $this->fail('twilio', 'Invalid credentials (401)', 'Check TWILIO_ACCOUNT_SID and TWILIO_AUTH_TOKEN', $ms);
        } else {
            $this->fail('twilio', "HTTP {$response->status()}", substr($response->body(), 0, 200), $ms);
        }
    }

    private function runTelegram(): void
    {
        $botToken = config('services.telegram.bot_token', '');
        $chatId   = config('services.telegram.chat_id', '');

        if (empty($botToken)) {
            $this->warn('telegram', 'Not configured', 'TELEGRAM_BOT_TOKEN is empty â€” admin alert notifications disabled.');
            return;
        }

        [$response, $ms] = $this->timed(fn() =>
            Http::timeout(8)->get("https://api.telegram.org/bot{$botToken}/getMe")
        );

        if ($response->successful() && data_get($response->json(), 'ok')) {
            $botName = data_get($response->json(), 'result.username', '?');
            $detail  = "Bot: @{$botName}";
            $detail .= empty($chatId) ? ' | âš  TELEGRAM_CHAT_ID not set â€” alerts will not send' : " | Chat ID: {$chatId}";
            empty($chatId)
                ? $this->warn('telegram', 'Bot token valid, chat ID missing', $detail, $ms)
                : $this->pass('telegram', 'Bot token valid', $detail, $ms);
        } elseif ($response->status() === 401) {
            $this->fail('telegram', 'Invalid bot token (401)', 'Check TELEGRAM_BOT_TOKEN in .env', $ms);
        } else {
            $this->fail('telegram', "HTTP {$response->status()}", substr($response->body(), 0, 200), $ms);
        }
    }

    private function runMailSmtp(): void
    {
        $mailer = config('mail.default', 'log');
        $host   = config('mail.mailers.smtp.host', '');
        $port   = (int) config('mail.mailers.smtp.port', 587);

        if ($mailer === 'log') {
            $this->warn('mail', 'Mail driver is "log"', 'No real emails are sent. Change MAIL_MAILER in .env to smtp, mailgun, postmark, etc.');
            return;
        }

        if ($mailer === 'smtp' && empty($host)) {
            $this->fail('mail', 'SMTP configured but MAIL_HOST is empty', 'Set MAIL_HOST in .env');
            return;
        }

        if ($mailer === 'smtp') {
            $start = hrtime(true);
            $sock  = @fsockopen($host, $port, $errno, $errstr, 5);
            $ms    = (int) round((hrtime(true) - $start) / 1_000_000);
            if ($sock) {
                fclose($sock);
                $from = config('mail.from.address', 'unknown');
                $this->pass('mail', "SMTP reachable ({$host}:{$port})", "From: {$from} | Driver: {$mailer}", $ms);
            } else {
                $this->fail('mail', "SMTP unreachable â€” {$errstr}", "{$host}:{$port} (errno {$errno})", $ms);
            }
        } else {
            $this->pass('mail', "Driver: {$mailer}", "Non-SMTP drivers (Mailgun, Postmark, etc.) don't need a socket test.");
        }
    }

    // â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    // â”€â”€ STORAGE â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    // â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€

    private function runCloudinary(): void
    {
        $cloudName = env('CLOUDINARY_CLOUD_NAME', '');
        $apiKey    = env('CLOUDINARY_API_KEY', '');
        $apiSecret = env('CLOUDINARY_API_SECRET', '');

        if (empty($cloudName) || empty($apiKey) || empty($apiSecret)) {
            $this->warn('cloudinary', 'Not configured',
                'CLOUDINARY_CLOUD_NAME, CLOUDINARY_API_KEY, or CLOUDINARY_API_SECRET is empty â€” image CDN/transformations disabled.');
            return;
        }

        [$response, $ms] = $this->timed(fn() =>
            Http::timeout(8)->withBasicAuth($apiKey, $apiSecret)
                ->get("https://api.cloudinary.com/v1_1/{$cloudName}/usage")
        );

        if ($response->successful()) {
            $plan    = data_get($response->json(), 'plan', '?');
            $storage = data_get($response->json(), 'storage.usage', 0);
            $this->pass('cloudinary', 'Credentials valid',
                "Cloud: {$cloudName} | Plan: {$plan} | Storage used: " . round($storage / 1048576, 1) . ' MB', $ms);
        } elseif ($response->status() === 401) {
            $this->fail('cloudinary', 'Invalid credentials (401)', 'Check CLOUDINARY_API_KEY and CLOUDINARY_API_SECRET', $ms);
        } else {
            $this->fail('cloudinary', "HTTP {$response->status()}", substr($response->body(), 0, 200), $ms);
        }
    }

    private function runAwsS3(): void
    {
        $key    = env('AWS_ACCESS_KEY_ID', '');
        $secret = env('AWS_SECRET_ACCESS_KEY', '');
        $bucket = env('AWS_BUCKET', '');
        $region = env('AWS_DEFAULT_REGION', 'us-east-1');

        if (empty($key) || empty($secret)) {
            $this->warn('awss3', 'Not configured',
                'AWS_ACCESS_KEY_ID or AWS_SECRET_ACCESS_KEY is empty â€” S3 file storage disabled. App uses local disk storage.');
            return;
        }

        if (empty($bucket)) {
            $this->warn('awss3', 'AWS credentials set but AWS_BUCKET is empty',
                'Set AWS_BUCKET in .env to point uploads to your S3 bucket.');
            return;
        }

        // HEAD the bucket endpoint â€” S3 returns 403 (auth required) for a valid bucket
        [$response, $ms] = $this->timed(fn() =>
            Http::timeout(8)->head("https://{$bucket}.s3.{$region}.amazonaws.com/")
        );

        if ($response->status() === 403 || $response->successful()) {
            $this->pass('awss3', 'Bucket reachable',
                "Bucket: {$bucket} | Region: {$region} | Key: " . substr($key, 0, 8) . 'â€¦', $ms);
        } elseif ($response->status() === 404) {
            $this->fail('awss3', "Bucket '{$bucket}' not found (404)",
                "Check AWS_BUCKET name and AWS_DEFAULT_REGION in .env", $ms);
        } else {
            $this->warn('awss3', "HTTP {$response->status()} â€” bucket may still be valid",
                "Bucket: {$bucket} | Region: {$region}", $ms);
        }
    }

    // â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    // â”€â”€ PUSH NOTIFICATIONS â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    // â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€

    private function runFirebase(): void
    {
        $apiKey = config('services.firebase.api_key', '') ?: env('FIREBASE_API_KEY', '');
        $projectId = config('services.firebase.project_id', '') ?: env('FIREBASE_PROJECT_ID', '');
        $credentialsPath = config('services.firebase.credentials', '');

        if (empty($apiKey)) {
            $this->warn('firebase', 'Not configured',
                'FIREBASE_API_KEY is empty – push notifications via Firebase Cloud Messaging are disabled.');
            return;
        }

        // Dry-run send (no actual notification delivered)
        [$response, $ms] = $this->timed(fn() =>
            Http::timeout(8)
                ->withHeaders(['Authorization' => "key={$apiKey}"])
                ->post('https://fcm.googleapis.com/fcm/send', [
                    'dry_run'      => true,
                    'to'           => 'faketoken_akt_validation',
                    'notification' => ['title' => 'test'],
                ])
        );

        if ($response->status() === 401) {
            $this->fail('firebase', 'Invalid API key (401)', 'Check FIREBASE_API_KEY in .env', $ms);
        } elseif ($response->successful()) {
            $detail = 'API key accepted by FCM';
            if (!empty($projectId)) $detail .= " | Project: {$projectId}";
            $this->pass('firebase', 'Firebase Cloud Messaging API key valid', $detail, $ms);
        } else {
            $this->warn('firebase', "HTTP {$response->status()} â€” key may still be valid",
                substr($response->body(), 0, 200), $ms);
        }
    }

    // â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    // â”€â”€ SECURITY / VPN â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    // â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€

    private function runIpHub(): void
    {
        $key = config('services.iphub.api_key', '');

        if (empty($key)) {
            $this->warn('iphub', 'Not configured', 'IPHUB_API_KEY is empty â€” VPN detection via IPHub is disabled.');
            return;
        }

        [$response, $ms] = $this->timed(fn() =>
            Http::timeout(8)->withHeaders(['X-Key' => $key])->get('https://v2.api.iphub.info/ip/8.8.8.8')
        );

        if ($response->successful()) {
            $block = data_get($response->json(), 'block', '?');
            $isp   = data_get($response->json(), 'isp', '?');
            $this->pass('iphub', 'API key valid', "8.8.8.8 â†’ ISP: {$isp} | block: {$block}", $ms);
        } elseif ($response->status() === 401 || $response->status() === 403) {
            $this->fail('iphub', 'Invalid API key', "HTTP {$response->status()}", $ms);
        } elseif ($response->status() === 429) {
            $this->warn('iphub', 'Key valid but rate-limited (429)', 'Daily quota may be exhausted', $ms);
        } else {
            $this->fail('iphub', "HTTP {$response->status()}", substr($response->body(), 0, 200), $ms);
        }
    }

    private function runProxyCheck(): void
    {
        $key = config('services.proxycheck.api_key', '');

        if (empty($key)) {
            $this->warn('proxycheck', 'Not configured', 'PROXYCHECK_API_KEY is empty â€” ProxyCheck VPN detection disabled.');
            return;
        }

        [$response, $ms] = $this->timed(fn() =>
            Http::timeout(8)->get('https://proxycheck.io/v2/8.8.8.8', ['key' => $key, 'vpn' => 1, 'risk' => 1])
        );

        if ($response->successful()) {
            $status = data_get($response->json(), 'status', '');
            if ($status === 'ok' || $status === 'warning') {
                $proxy = data_get($response->json(), '8.8.8.8.proxy', '?');
                $this->pass('proxycheck', 'API key valid', "8.8.8.8 â†’ proxy: {$proxy}", $ms);
            } elseif ($status === 'denied') {
                $this->fail('proxycheck', 'API key denied', data_get($response->json(), 'message', ''), $ms);
            } else {
                $this->warn('proxycheck', "Unexpected status: {$status}", $response->body(), $ms);
            }
        } else {
            $this->fail('proxycheck', "HTTP {$response->status()}", substr($response->body(), 0, 200), $ms);
        }
    }

    // â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    // â”€â”€ INFRASTRUCTURE â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    // â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€

    private function runPusher(): void
    {
        $appId   = env('PUSHER_APP_ID', '');
        $key     = env('PUSHER_APP_KEY', '');
        $secret  = env('PUSHER_APP_SECRET', '');
        $cluster = env('PUSHER_APP_CLUSTER', 'mt1');

        if (empty($appId) || empty($key) || empty($secret)) {
            $this->warn('pusher', 'Not configured',
                'PUSHER_APP_ID / PUSHER_APP_KEY / PUSHER_APP_SECRET missing. App uses Reverb by default â€” configure Pusher as an alternative broadcast driver.');
            return;
        }

        // Build Pusher-signed request to GET /apps/{id}/channels
        $ts       = time();
        $bodyMd5  = md5('');
        $path     = "/apps/{$appId}/channels";
        $strToSign = "GET\n{$path}\nauth_key={$key}&auth_timestamp={$ts}&auth_version=1.0&body_md5={$bodyMd5}";
        $sig      = hash_hmac('sha256', $strToSign, $secret);

        [$response, $ms] = $this->timed(fn() => Http::timeout(8)->get(
            "https://api-{$cluster}.pusher.com/apps/{$appId}/channels",
            ['auth_key' => $key, 'auth_timestamp' => $ts, 'auth_version' => '1.0', 'body_md5' => $bodyMd5, 'auth_signature' => $sig]
        ));

        if ($response->successful()) {
            $channels = count(data_get($response->json(), 'channels', []));
            $this->pass('pusher', 'Credentials valid', "App ID: {$appId} | Cluster: {$cluster} | Active channels: {$channels}", $ms);
        } elseif ($response->status() === 401 || $response->status() === 403) {
            $this->fail('pusher', 'Invalid credentials', "HTTP {$response->status()} â€” check PUSHER_APP_ID, KEY, SECRET, CLUSTER", $ms);
        } else {
            $this->fail('pusher', "HTTP {$response->status()}", substr($response->body(), 0, 200), $ms);
        }
    }

    private function runReverb(): void
    {
        $host   = env('REVERB_HOST', '0.0.0.0');
        $port   = (int) env('REVERB_PORT', 8080);
        $appKey = env('REVERB_APP_KEY', '');
        $appId  = env('REVERB_APP_ID', '');

        $testHost = ($host === '0.0.0.0' || empty($host)) ? '127.0.0.1' : $host;
        $start    = hrtime(true);
        $sock     = @fsockopen($testHost, $port, $errno, $errstr, 3);
        $ms       = (int) round((hrtime(true) - $start) / 1_000_000);

        if (!$sock) {
            $this->fail('reverb', "NOT running on {$testHost}:{$port}",
                "Error: {$errstr} ({$errno}) â€” start via Admin â†’ Artisan Runner â†’ Start Reverb Server", $ms);
            return;
        }

        fclose($sock);

        if (empty($appKey) || empty($appId)) {
            $this->warn('reverb', "Port {$port} reachable but REVERB_APP_KEY/ID missing",
                'Set REVERB_APP_KEY and REVERB_APP_ID in .env', $ms);
            return;
        }

        $this->pass('reverb', "Running & reachable on :{$port}",
            "App ID: {$appId} | App Key: " . substr($appKey, 0, 8) . 'â€¦', $ms);
    }

    private function runDatabase(): void
    {
        $start = hrtime(true);
        try {
            \Illuminate\Support\Facades\DB::connection()->getPdo();
            $driver    = config('database.default', '?');
            $db        = config("database.connections.{$driver}.database", '?');
            $ms        = (int) round((hrtime(true) - $start) / 1_000_000);
            $userCount = \Illuminate\Support\Facades\DB::table('users')->count();
            $this->pass('database', 'Connection OK', "Driver: {$driver} | DB: {$db} | Users: {$userCount}", $ms);
        } catch (\Throwable $e) {
            $ms = (int) round((hrtime(true) - $start) / 1_000_000);
            $this->fail('database', 'Connection failed', $e->getMessage(), $ms);
        }
    }
}
