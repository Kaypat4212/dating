<?php

namespace App\Services;

use App\Models\SiteSetting;
use Illuminate\Support\Facades\Http;

class TelegramService
{
    /**
     * Send a plain-text message to the configured Telegram chat.
     * Returns true on success, false if Telegram is not configured/disabled.
     */
    public static function send(string $message): bool
    {
        $token  = SiteSetting::get('telegram_bot_token');
        $chatId = SiteSetting::get('telegram_chat_id');

        if (! $token || ! $chatId) {
            return false;
        }

        try {
            Http::timeout(5)->post(
                "https://api.telegram.org/bot{$token}/sendMessage",
                [
                    'chat_id'    => $chatId,
                    'text'       => $message,
                    'parse_mode' => 'HTML',
                ]
            );
        } catch (\Throwable) {
            // Never let a Telegram failure break the app
        }

        return true;
    }

    /**
     * Fire an admin-portal login alert if the admin has that toggle enabled.
     */
    public static function notifyAdminLoginAttempt(
        string  $email,
        bool    $success,
        string  $reason = '',
        string  $ip     = '',
    ): void {
        $successAlerts = (bool) SiteSetting::get('telegram_admin_login_alert', false);
        $failedAlerts  = (bool) SiteSetting::get('telegram_admin_login_failed_alert', false);

        if ($success && ! $successAlerts) {
            return;
        }
        if (! $success && ! $failedAlerts) {
            return;
        }

        $app   = config('app.name', 'HeartsConnect');
        $time  = now()->format('d M Y H:i:s') . ' UTC';
        $ipStr = $ip ? "\n🌐 IP: <code>{$ip}</code>" : '';

        if ($success) {
            $msg = "✅ <b>{$app} Admin Login</b>\n"
                 . "👤 <code>{$email}</code>\n"
                 . "🕐 {$time}"
                 . $ipStr;
        } else {
            $reasonStr = $reason ? "\n⚠️ {$reason}" : '';
            $msg = "🚨 <b>{$app} Admin Login FAILED</b>\n"
                 . "👤 <code>{$email}</code>\n"
                 . "🕐 {$time}"
                 . $ipStr
                 . $reasonStr;
        }

        static::send($msg);
    }

    /**
     * Test the current Telegram config by sending a test message.
     * Returns an error string, or null on success.
     */
    public static function test(): ?string
    {
        $token  = SiteSetting::get('telegram_bot_token');
        $chatId = SiteSetting::get('telegram_chat_id');

        if (! $token || ! $chatId) {
            return 'Telegram Bot Token and Chat ID must both be saved before testing.';
        }

        try {
            $response = Http::timeout(8)->post(
                "https://api.telegram.org/bot{$token}/sendMessage",
                [
                    'chat_id'    => $chatId,
                    'text'       => "✅ <b>" . config('app.name') . " Admin</b> — Telegram connection test successful!\n🕐 " . now()->format('d M Y H:i:s') . ' UTC',
                    'parse_mode' => 'HTML',
                ]
            );

            if (! $response->successful()) {
                $err = $response->json('description') ?? $response->body();
                return "Telegram API error: {$err}";
            }
        } catch (\Throwable $e) {
            return "Connection failed: {$e->getMessage()}";
        }

        return null;
    }
}
