<?php

namespace App\Services;

use App\Models\User;
use App\Models\WalletFundingRequest;
use App\Notifications\AdminWalletFundingApprovalRequiredNotification;
use Illuminate\Support\Facades\URL;

class AdminWalletFundingAlertService
{
    public function notifyNewPendingFundingRequest(WalletFundingRequest $fundingRequest): void
    {
        if ($fundingRequest->status !== 'pending') {
            return;
        }

        $fundingRequest->loadMissing('user');

        $admins = User::query()
            ->where(function ($query) {
                $query->where('id', 1)
                    ->orWhereHas('roles', fn ($roleQ) => $roleQ->where('name', 'admin'));
            })
            ->whereNotNull('email')
            ->get();

        foreach ($admins as $admin) {
            if (! $admin instanceof User) {
                continue;
            }

            $approveUrl = URL::temporarySignedRoute(
                'admin.wallet-funding.action',
                now()->addDays(7),
                ['fundingRequest' => $fundingRequest->id, 'action' => 'approve', 'admin' => $admin->id]
            );

            $rejectUrl = URL::temporarySignedRoute(
                'admin.wallet-funding.action',
                now()->addDays(7),
                ['fundingRequest' => $fundingRequest->id, 'action' => 'reject', 'admin' => $admin->id]
            );

            if (MailSettingsService::emailEnabled('email_feature_usage_enabled')) {
                $admin->notify(new AdminWalletFundingApprovalRequiredNotification($fundingRequest, $approveUrl, $rejectUrl));
            }

            if ($admin->id === $admins->first()->id) {
                TelegramService::notifyAdminWalletFundingReviewRequired($fundingRequest, $approveUrl, $rejectUrl);
            }
        }
    }
}
