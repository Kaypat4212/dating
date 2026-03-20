<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WalletFundedNotification extends Notification
{

    public function __construct(
        public readonly int $credits,
        public readonly string $txid,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('💳 Your wallet has been credited — ' . number_format($this->credits) . ' credits added!')
            ->markdown('emails.wallet-funded', [
                'user'      => $notifiable,
                'credits'   => $this->credits,
                'txid'      => $this->txid,
                'walletUrl' => route('wallet.index'),
                'appName'   => config('app.name'),
                'appUrl'    => config('app.url'),
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'    => 'wallet_funded',
            'credits' => $this->credits,
            'txid'    => $this->txid,
            'message' => "Your wallet has been credited with {$this->credits} credits.",
            'url'     => route('wallet.index'),
        ];
    }
}
