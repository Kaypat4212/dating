<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DepositRejectedNotification extends Notification
{
    public function __construct(
        public readonly int $credits,
        public readonly string $txid,
        public readonly ?string $adminNote = null,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('❌ Your deposit request was not approved')
            ->markdown('emails.wallet-deposit-rejected', [
                'user'      => $notifiable,
                'credits'   => $this->credits,
                'txid'      => $this->txid,
                'reason'    => $this->adminNote,
                'walletUrl' => route('wallet.index'),
                'appName'   => config('app.name'),
                'appUrl'    => config('app.url'),
            ]);
    }

    public function toArray(object $notifiable): array
    {
        $message = "Your deposit request of {$this->credits} credits was not approved."
            . ($this->adminNote ? " Reason: {$this->adminNote}" : '');

        return [
            'type'    => 'deposit_rejected',
            'credits' => $this->credits,
            'txid'    => $this->txid,
            'message' => $message,
            'url'     => route('wallet.index'),
        ];
    }
}
