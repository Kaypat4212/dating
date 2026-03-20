<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WalletWithdrawalProcessedNotification extends Notification
{

    public function __construct(
        public readonly int $credits,
        public readonly string $status, // 'approved' | 'rejected'
        public readonly ?string $adminNote = null,
        public readonly ?string $destination = null,
        public readonly ?string $currency = null,
        public readonly ?string $network = null,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $subject = $this->status === 'approved'
            ? '✅ Your withdrawal request has been approved'
            : '❌ Your withdrawal request was rejected — credits refunded';

        return (new MailMessage)
            ->subject($subject)
            ->markdown('emails.wallet-withdrawal-processed', [
                'user'        => $notifiable,
                'credits'     => $this->credits,
                'approved'    => $this->status === 'approved',
                'reason'      => $this->adminNote,
                'destination' => $this->destination ?? '—',
                'currency'    => $this->currency,
                'network'     => $this->network,
                'walletUrl'   => route('wallet.index'),
                'appName'     => config('app.name'),
                'appUrl'      => config('app.url'),
            ]);
    }

    public function toArray(object $notifiable): array
    {
        $message = $this->status === 'approved'
            ? "Your withdrawal of {$this->credits} credits has been approved and is being processed."
            : "Your withdrawal of {$this->credits} credits was rejected." . ($this->adminNote ? " Note: {$this->adminNote}" : '');

        return [
            'type'    => 'wallet_withdrawal_processed',
            'credits' => $this->credits,
            'status'  => $this->status,
            'message' => $message,
            'url'     => route('wallet.index'),
        ];
    }
}
