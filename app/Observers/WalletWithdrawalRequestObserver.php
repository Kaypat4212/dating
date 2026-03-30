<?php

namespace App\Observers;

use App\Models\WalletWithdrawalRequest;
use App\Services\TelegramNotificationService;

class WalletWithdrawalRequestObserver
{
    public function created(WalletWithdrawalRequest $request): void
    {
        try {
            app(TelegramNotificationService::class)->notifyNewWithdrawalRequest(
                userId:      $request->user_id,
                amount:      (int) $request->amount,
                destination: $request->destination ?? null,
                currency:    $request->currency ?? null,
                network:     $request->network ?? null,
            );
        } catch (\Throwable) {
            // Telegram down — never block the user request
        }
    }
}
