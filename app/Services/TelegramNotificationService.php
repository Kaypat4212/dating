<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramNotificationService
{
    protected string $botToken;
    protected string $chatId;
    protected bool $enabled;

    public function __construct()
    {
        $this->botToken = (string) config('services.telegram.bot_token', '');
        $this->chatId = (string) config('services.telegram.chat_id', '');
        $this->enabled = (bool) config('services.telegram.enabled', false);
    }

    /**
     * Send a message to the configured Telegram chat
     */
    public function send(string $message, string $parseMode = 'HTML'): bool
    {
        if (!$this->enabled || empty($this->botToken) || empty($this->chatId)) {
            Log::info('Telegram notification skipped: Not configured', [
                'enabled' => $this->enabled,
                'has_token' => !empty($this->botToken),
                'has_chat_id' => !empty($this->chatId),
            ]);
            return false;
        }

        try {
            $response = Http::timeout(5)->post("https://api.telegram.org/bot{$this->botToken}/sendMessage", [
                'chat_id' => $this->chatId,
                'text' => $message,
                'parse_mode' => $parseMode,
                'disable_web_page_preview' => true,
            ]);

            if ($response->successful()) {
                Log::info('Telegram notification sent successfully');
                return true;
            }

            Log::warning('Telegram API error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            return false;

        } catch (\Exception $e) {
            Log::error('Failed to send Telegram notification', [
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Send homepage visit notification
     */
    public function notifyHomepageVisit(string $ip, ?string $userAgent = null, ?int $userId = null, ?array $geo = null): bool
    {
        $user = $userId ? \App\Models\User::find($userId) : null;

        // Allow caller to pass pre-resolved geo data to avoid a double lookup
        if ($geo === null) {
            $geo = $this->lookupGeoIp($ip);
        }

        $message = "🏠 <b>Homepage Visit</b>\n\n";
        $message .= "⏰ <b>Time:</b> " . now()->format('Y-m-d H:i:s') . "\n";
        $message .= "🌐 <b>IP:</b> <code>{$ip}</code>\n";

        if ($geo) {
            $flag     = $this->countryFlag($geo['countryCode'] ?? '');
            $country  = $geo['country']  ?? 'Unknown';
            $city     = $geo['city']     ?? '';
            $region   = $geo['regionName'] ?? '';
            $isp      = $geo['isp']      ?? '';
            $org      = $geo['org']      ?? '';
            $isProxy  = $geo['proxy']    ?? false;

            $location = array_filter([$city, $region, $country]);
            $message .= "📍 <b>Location:</b> {$flag} " . implode(', ', $location) . "\n";

            if ($isp) {
                $message .= "📡 <b>Network:</b> {$isp}\n";
            }
            if ($org && $org !== $isp) {
                $message .= "🏢 <b>Org:</b> {$org}\n";
            }
            if ($isProxy) {
                $message .= "🔴 <b>Proxy/VPN Detected</b>\n";
            }
        }

        if ($user) {
            $message .= "👤 <b>User:</b> {$user->name} (#{$user->id})\n";
            $message .= "📧 <b>Email:</b> {$user->email}\n";
        } else {
            $message .= "👤 <b>User:</b> Guest (not logged in)\n";
        }

        if ($userAgent) {
            $browser = $this->parseBrowser($userAgent);
            $message .= "💻 <b>Browser:</b> {$browser}\n";
        }

        $message .= "\n🔗 <b>URL:</b> " . config('app.url');

        return $this->send($message);
    }

    /**
     * Lookup geo-IP info using ip-api.com (free, no key required).
     * Results are cached for 1 hour per IP to avoid repeated lookups.
     *
     * @return array|null  Keys: country, countryCode, regionName, city, isp, org, proxy
     */
    public function lookupGeoIp(string $ip): ?array
    {
        // Skip private/loopback IPs
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
            return null;
        }

        $cacheKey = 'geoip_' . md5($ip);

        return \Illuminate\Support\Facades\Cache::remember($cacheKey, now()->addHour(), function () use ($ip) {
            try {
                $response = Http::timeout(4)->get(
                    "http://ip-api.com/json/{$ip}",
                    ['fields' => 'status,country,countryCode,regionName,city,isp,org,proxy,hosting']
                );

                if ($response->successful()) {
                    $data = $response->json();
                    if (($data['status'] ?? '') === 'success') {
                        return $data;
                    }
                }
            } catch (\Throwable $e) {
                Log::warning('GeoIP lookup failed for ' . $ip . ': ' . $e->getMessage());
            }

            return null;
        });
    }

    /**
     * Convert a 2-letter country code to an emoji flag.
     */
    protected function countryFlag(string $code): string
    {
        if (strlen($code) !== 2) {
            return '🌍';
        }
        $code = strtoupper($code);
        return mb_convert_encoding(
            '&#' . (0x1F1E0 + (ord($code[0]) - ord('A'))) . ';'
            . '&#' . (0x1F1E0 + (ord($code[1]) - ord('A'))) . ';',
            'UTF-8', 'HTML-ENTITIES'
        );
    }

    /**
     * Send VPN detection alert
     */
    public function notifyVpnDetection(string $ip, int $confidence, string $provider, ?int $userId = null): bool
    {
        $user = $userId ? \App\Models\User::find($userId) : null;
        
        $message = "🛡️ <b>VPN Detected</b>\n\n";
        $message .= "⚠️ <b>Confidence:</b> {$confidence}%\n";
        $message .= "🌐 <b>IP:</b> <code>{$ip}</code>\n";
        $message .= "🔒 <b>Provider:</b> {$provider}\n";
        
        if ($user) {
            $message .= "👤 <b>User:</b> {$user->name} (#{$user->id})\n";
            $message .= "📧 <b>Email:</b> {$user->email}\n";
        }
        
        $message .= "\n⏰ <b>Time:</b> " . now()->format('Y-m-d H:i:s');

        return $this->send($message);
    }

    /**
     * Send suspicious activity alert
     */
    public function notifySuspiciousActivity(string $action, string $ip, ?int $userId = null, ?array $meta = null): bool
    {
        $user = $userId ? \App\Models\User::find($userId) : null;
        
        $message = "⚠️ <b>Suspicious Activity</b>\n\n";
        $message .= "🔴 <b>Action:</b> {$action}\n";
        $message .= "🌐 <b>IP:</b> <code>{$ip}</code>\n";
        
        if ($user) {
            $message .= "👤 <b>User:</b> {$user->name} (#{$user->id})\n";
            $message .= "📧 <b>Email:</b> {$user->email}\n";
        }
        
        if ($meta && !empty($meta)) {
            $message .= "\n📋 <b>Details:</b>\n";
            foreach (array_slice($meta, 0, 5) as $key => $value) {
                $message .= "  • " . ucfirst(str_replace('_', ' ', $key)) . ": {$value}\n";
            }
        }
        
        $message .= "\n⏰ <b>Time:</b> " . now()->format('Y-m-d H:i:s');

        return $this->send($message);
    }

    /**
     * Send new user registration alert
     */
    public function notifyNewRegistration(int $userId): bool
    {
        $user = \App\Models\User::find($userId);
        
        if (!$user) {
            return false;
        }
        
        $message = "✨ <b>New User Registration</b>\n\n";
        $message .= "👤 <b>Name:</b> {$user->name}\n";
        $message .= "📧 <b>Email:</b> {$user->email}\n";
        $message .= "🆔 <b>ID:</b> #{$user->id}\n";
        $message .= "🌐 <b>IP:</b> <code>" . request()->ip() . "</code>\n";
        $message .= "\n⏰ <b>Time:</b> " . now()->format('Y-m-d H:i:s');

        return $this->send($message);
    }

    /**
     * Send security alert
     */
    public function notifySecurityAlert(string $title, string $description, array $details = []): bool
    {
        $message = "🚨 <b>{$title}</b>\n\n";
        $message .= "{$description}\n";
        
        if (!empty($details)) {
            $message .= "\n📋 <b>Details:</b>\n";
            foreach ($details as $key => $value) {
                $message .= "  • " . ucfirst(str_replace('_', ' ', $key)) . ": {$value}\n";
            }
        }
        
        $message .= "\n⏰ <b>Time:</b> " . now()->format('Y-m-d H:i:s');

        return $this->send($message);
    }

    /**
     * Parse browser from user agent
     */
    protected function parseBrowser(string $userAgent): string
    {
        if (preg_match('/Chrome\/[\d.]+/i', $userAgent)) {
            return '🌐 Chrome';
        } elseif (preg_match('/Firefox\/[\d.]+/i', $userAgent)) {
            return '🦊 Firefox';
        } elseif (preg_match('/Safari\/[\d.]+/i', $userAgent) && !preg_match('/Chrome/i', $userAgent)) {
            return '🧭 Safari';
        } elseif (preg_match('/Edge\/[\d.]+/i', $userAgent) || preg_match('/Edg\/[\d.]+/i', $userAgent)) {
            return '🌊 Edge';
        } elseif (preg_match('/Opera\/[\d.]+/i', $userAgent) || preg_match('/OPR\/[\d.]+/i', $userAgent)) {
            return '🎭 Opera';
        }
        
        return '💻 Unknown Browser';
    }
}
