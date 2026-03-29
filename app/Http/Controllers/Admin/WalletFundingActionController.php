<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\WalletFundingRequest;
use App\Notifications\DepositRejectedNotification;
use App\Notifications\WalletFundedNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class WalletFundingActionController extends Controller
{
    public function handle(WalletFundingRequest $fundingRequest, string $action, int $admin): View
    {
        $adminUser = User::find($admin);

        if (! $adminUser || ! ($adminUser->id === 1 || $adminUser->hasRole('admin'))) {
            abort(403, 'This approval link is not valid for an admin account.');
        }

        if ($fundingRequest->status !== 'pending') {
            return view('admin.funding-action-result', [
                'status' => 'info',
                'title' => 'Already Processed',
                'message' => "Funding request #{$fundingRequest->id} has already been {$fundingRequest->status}.",
            ]);
        }

        if ($action === 'approve') {
            DB::transaction(function () use ($fundingRequest) {
                $oldBalance = (int) $fundingRequest->user->credit_balance;
                $amount = (int) $fundingRequest->amount;

                $fundingRequest->update(['status' => 'approved']);
                $fundingRequest->user()->increment('credit_balance', $amount);

                \App\Models\WalletTransaction::create([
                    'user_id' => $fundingRequest->user_id,
                    'type' => 'deposit',
                    'amount' => $amount,
                    'balance_after' => $oldBalance + $amount,
                    'reference_id' => $fundingRequest->id,
                    'reference_type' => 'funding_request',
                    'description' => 'Crypto deposit approved (TXID: ' . ($fundingRequest->txid ?? '—') . ')',
                ]);
            });

            $fresh = $fundingRequest->fresh();
            try {
                $fresh->user->notify(new WalletFundedNotification((int) $fresh->amount, $fresh->txid ?? ''));
            } catch (\Throwable) {
                // Keep action flow resilient if email fails
            }

            return view('admin.funding-action-result', [
                'status' => 'success',
                'title' => 'Funding Approved',
                'message' => "Funding request #{$fundingRequest->id} approved and credits added to user wallet.",
            ]);
        }

        $fundingRequest->update([
            'status' => 'rejected',
            'admin_note' => 'Rejected via secure one-click admin action.',
        ]);

        $fresh = $fundingRequest->fresh();
        try {
            $fresh->user->notify(new DepositRejectedNotification(
                (int) $fresh->amount,
                $fresh->txid ?? '',
                (string) $fresh->admin_note,
            ));
        } catch (\Throwable) {
            // Keep action flow resilient if email fails
        }

        return view('admin.funding-action-result', [
            'status' => 'danger',
            'title' => 'Funding Rejected',
            'message' => "Funding request #{$fundingRequest->id} has been rejected.",
        ]);
    }
}
