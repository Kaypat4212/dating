<?php

namespace App\Services;

use App\Models\PremiumPayment;
use App\Models\User;
use App\Notifications\AdminFundingApprovalRequiredNotification;
use Illuminate\Support\Facades\URL;

class AdminFundingAlertService
{
    public function notifyNewPendingFunding(PremiumPayment $payment): void
    {
        if ($payment->status !== 'pending') {
            return;
        }

        $payment->loadMissing('user');

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
                'admin.funding.action',
                now()->addDays(7),
                ['payment' => $payment->id, 'action' => 'approve', 'admin' => $admin->id]
            );

            $rejectUrl = URL::temporarySignedRoute(
                'admin.funding.action',
                now()->addDays(7),
                ['payment' => $payment->id, 'action' => 'reject', 'admin' => $admin->id]
            );

            if (MailSettingsService::emailEnabled('email_feature_usage_enabled')) {
                $admin->notify(new AdminFundingApprovalRequiredNotification($payment, $approveUrl, $rejectUrl));
            }

            // Telegram is shared chat-based, so one alert per payment is enough.
            if ($admin->id === $admins->first()->id) {
                TelegramService::notifyAdminFundingReviewRequired($payment, $approveUrl, $rejectUrl);
            }
        }
    }
}
