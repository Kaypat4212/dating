<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class LowBalanceNotification extends Notification
{
    public function __construct(
        public readonly int $balance,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'    => 'low_balance',
            'balance' => $this->balance,
            'message' => "Your wallet balance is low ({$this->balance} credits remaining). Top up to keep sending gifts! 💳",
            'url'     => route('wallet.index'),
        ];
    }
}
