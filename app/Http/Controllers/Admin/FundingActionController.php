<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PremiumPayment;
use App\Models\User;
use App\Notifications\PremiumPurchasedNotification;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FundingActionController extends Controller
{
    public function handle(Request $request, PremiumPayment $payment, string $action, int $admin): View
    {
        $adminUser = User::find($admin);

        if (! $adminUser || ! ($adminUser->id === 1 || $adminUser->hasRole('admin'))) {
            abort(403, 'This approval link is not valid for an admin account.');
        }

        if ($payment->status !== 'pending') {
            return view('admin.funding-action-result', [
                'status' => 'info',
                'title' => 'Already Processed',
                'message' => "Payment #{$payment->id} has already been {$payment->status}.",
            ]);
        }

        if ($action === 'approve') {
            $payment->update([
                'status' => 'approved',
                'approved_by' => $adminUser->id,
                'approved_at' => now(),
            ]);

            $payment->user->setPremium($payment->plan);
            $payment->user->notify(new PremiumPurchasedNotification(
                plan: $payment->plan_label,
                expiresAt: $payment->user->premium_expires_at->format('F j, Y'),
            ));

            return view('admin.funding-action-result', [
                'status' => 'success',
                'title' => 'Payment Approved',
                'message' => "Payment #{$payment->id} has been approved and premium was activated.",
            ]);
        }

        $payment->update([
            'status' => 'rejected',
            'approved_by' => $adminUser->id,
            'approved_at' => now(),
        ]);

        return view('admin.funding-action-result', [
            'status' => 'danger',
            'title' => 'Payment Rejected',
            'message' => "Payment #{$payment->id} has been rejected.",
        ]);
    }
}
