<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VerificationRejectedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly ?string $reason = null) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject('Update on your HeartsConnect verification request')
            ->greeting("Hi {$notifiable->name},")
            ->line("Unfortunately we could not verify your identity at this time.");

        if ($this->reason) {
            $mail->line("Reason: {$this->reason}");
        }

        return $mail
            ->line('Please re-submit with clearer photos and ensure your document is fully visible.')
            ->action('Resubmit Verification', route('verify.show'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'    => 'verification_rejected',
            'message' => '⚠️ Your verification was not approved. You can resubmit with clearer photos.',
            'url'     => route('verify.show'),
        ];
    }
}
