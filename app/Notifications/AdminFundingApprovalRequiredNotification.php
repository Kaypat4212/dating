<?php

namespace App\Notifications;

use App\Models\PremiumPayment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminFundingApprovalRequiredNotification extends Notification
{
    use Queueable;

    public function __construct(
        public readonly PremiumPayment $payment,
        public readonly string $approveUrl,
        public readonly string $rejectUrl,
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Funding Approval Required: #' . $this->payment->id)
            ->markdown('emails.admin-funding-review', [
                'payment' => $this->payment,
                'approveUrl' => $this->approveUrl,
                'rejectUrl' => $this->rejectUrl,
                'appName' => config('app.name'),
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'admin_funding_review_required',
            'payment_id' => $this->payment->id,
            'user_id' => $this->payment->user_id,
            'user_email' => $this->payment->user?->email,
            'plan' => $this->payment->plan,
            'amount' => $this->payment->amount,
            'approve_url' => $this->approveUrl,
            'reject_url' => $this->rejectUrl,
            'message' => 'A new premium funding request requires review.',
        ];
    }
}
