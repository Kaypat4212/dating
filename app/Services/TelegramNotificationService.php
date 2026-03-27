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
    public function notifyHomepageVisit(string $ip, ?string $userAgent = null, ?int $userId = null): bool
    {
        $user = $userId ? \App\Models\User::find($userId) : null;
        
        $message = "🏠 <b>Homepage Visit</b>\n\n";
        $message .= "⏰ <b>Time:</b> " . now()->format('Y-m-d H:i:s') . "\n";
        $message .= "🌐 <b>IP:</b> <code>{$ip}</code>\n";
        
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
