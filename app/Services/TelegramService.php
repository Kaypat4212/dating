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
     * Send a message with inline keyboard buttons.
     */
    public static function sendWithButtons(string $message, array $inlineKeyboard): bool
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
                    'chat_id' => $chatId,
                    'text' => $message,
                    'parse_mode' => 'HTML',
                    'reply_markup' => json_encode([
                        'inline_keyboard' => $inlineKeyboard,
                    ], JSON_UNESCAPED_SLASHES),
                    'disable_web_page_preview' => true,
                ]
            );
        } catch (\Throwable) {
            // Never let a Telegram failure break the app
        }

        return true;
    }

    /**
     * Alert admins that a new premium funding request needs review.
     */
    public static function notifyAdminFundingReviewRequired(
        \App\Models\PremiumPayment $payment,
        string $approveUrl,
        string $rejectUrl,
    ): void {
        $payment->loadMissing('user');

        $message = "💰 <b>Funding Approval Required</b>\n\n"
            . "🧾 <b>Payment ID:</b> #{$payment->id}\n"
            . "👤 <b>User:</b> {$payment->user?->name} ({$payment->user?->email})\n"
            . "📦 <b>Plan:</b> {$payment->plan_label}\n"
            . "💵 <b>Amount:</b> $" . number_format((float) $payment->amount, 2) . "\n"
            . "🪙 <b>Crypto:</b> " . ($payment->crypto_currency ?: 'N/A') . "\n"
            . "🔗 <b>TX Hash:</b> <code>" . ($payment->tx_hash ?: 'N/A') . "</code>\n"
            . "⏰ <b>Submitted:</b> " . $payment->created_at?->format('Y-m-d H:i:s') . " UTC";

        static::sendWithButtons($message, [
            [
                ['text' => '✅ Approve', 'url' => $approveUrl],
                ['text' => '❌ Reject', 'url' => $rejectUrl],
            ],
        ]);
    }

    /**
     * Alert admins that a wallet funding request needs review.
     */
    public static function notifyAdminWalletFundingReviewRequired(
        \App\Models\WalletFundingRequest $fundingRequest,
        string $approveUrl,
        string $rejectUrl,
    ): void {
        $fundingRequest->loadMissing('user');

        $message = "💳 <b>Wallet Funding Approval Required</b>\n\n"
            . "🧾 <b>Request ID:</b> #{$fundingRequest->id}\n"
            . "👤 <b>User:</b> {$fundingRequest->user?->name} ({$fundingRequest->user?->email})\n"
            . "💰 <b>Credits:</b> " . number_format((int) $fundingRequest->amount) . "\n"
            . "🔗 <b>TXID:</b> <code>" . ($fundingRequest->txid ?: 'N/A') . "</code>\n"
            . "⏰ <b>Submitted:</b> " . $fundingRequest->created_at?->format('Y-m-d H:i:s') . " UTC";

        static::sendWithButtons($message, [
            [
                ['text' => '✅ Approve', 'url' => $approveUrl],
                ['text' => '❌ Reject', 'url' => $rejectUrl],
            ],
        ]);
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
