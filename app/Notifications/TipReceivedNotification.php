<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class TipReceivedNotification extends Notification
{
    public function __construct(
        public readonly string  $senderName,
        public readonly int     $amount,
        public readonly ?string $message       = null,
        public readonly ?string $senderPhotoUrl = null,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $text = "{$this->senderName} sent you a {$this->amount}-credit gift!";
        if ($this->message) {
            $text .= " \"{$this->message}\"";
        }

        return [
            'type'       => 'tip_received',
            'amount'     => $this->amount,
            'sender'     => $this->senderName,
            'photo'      => $this->senderPhotoUrl,
            'message'    => $text,
            'url'        => route('wallet.index'),
        ];
    }
}
