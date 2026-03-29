<?php

namespace App\Notifications;

use App\Models\WalletFundingRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminWalletFundingApprovalRequiredNotification extends Notification
{
    use Queueable;

    public function __construct(
        public readonly WalletFundingRequest $fundingRequest,
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
            ->subject('Wallet Funding Approval Required: #' . $this->fundingRequest->id)
            ->markdown('emails.admin-wallet-funding-review', [
                'fundingRequest' => $this->fundingRequest,
                'approveUrl' => $this->approveUrl,
                'rejectUrl' => $this->rejectUrl,
                'appName' => config('app.name'),
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'admin_wallet_funding_review_required',
            'funding_request_id' => $this->fundingRequest->id,
            'user_id' => $this->fundingRequest->user_id,
            'user_email' => $this->fundingRequest->user?->email,
            'amount' => $this->fundingRequest->amount,
            'txid' => $this->fundingRequest->txid,
            'approve_url' => $this->approveUrl,
            'reject_url' => $this->rejectUrl,
            'message' => 'A wallet funding request requires review.',
        ];
    }
}
