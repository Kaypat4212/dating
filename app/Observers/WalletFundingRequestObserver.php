<?php

namespace App\Observers;

use App\Models\WalletFundingRequest;
use App\Services\TelegramNotificationService;

class WalletFundingRequestObserver
{
    public function created(WalletFundingRequest $request): void
    {
        try {
            app(TelegramNotificationService::class)->notifyNewDepositRequest(
                userId: $request->user_id,
                amount: (int) $request->amount,
                txid:   $request->txid ?? null,
            );
        } catch (\Throwable) {
            // Telegram down — never block the user request
        }
    }
}
