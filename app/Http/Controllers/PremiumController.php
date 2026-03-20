<?php

namespace App\Http\Controllers;

use App\Models\CryptoWallet;
use App\Models\PremiumPayment;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PremiumController extends Controller
{
    // Pricing in USD equivalent (shown on page, user pays in crypto)
    private const PLANS = [
        '30day'  => ['label' => '1 Month',  'price' => 9.99],
        '90day'  => ['label' => '3 Months', 'price' => 19.99],
        '365day' => ['label' => '1 Year',   'price' => 49.99],
    ];

    private const PLAN_DAYS = [
        '30day'  => 30,
        '90day'  => 90,
        '365day' => 365,
    ];

    private const PLAN_RANK = [
        '30day'  => 1,
        '90day'  => 2,
        '365day' => 3,
    ];

    public function show(Request $request): View
    {
        $user    = $request->user();
        $wallets = CryptoWallet::active()->get();
        $plans   = self::PLANS;

        $pending = PremiumPayment::where('user_id', $user->id)
            ->where('status', 'pending')
            ->latest()
            ->first();

        $upgradeOptions = null;
        if ($user->isPremiumActive() && ! $user->isHighestPremium() && ! $pending) {
            $upgradeOptions = $this->buildUpgradeOptions($user);
        }

        return view('premium.show', compact('user', 'wallets', 'plans', 'pending', 'upgradeOptions'));
    }

    public function submit(Request $request): RedirectResponse
    {
        $user = $request->user();

        // Only one pending payment at a time
        if (PremiumPayment::where('user_id', $user->id)->where('status', 'pending')->exists()) {
            return back()->withErrors(['tx_hash' => 'You already have a pending payment under review.']);
        }

        $data = $request->validate([
            'plan'            => 'required|in:30day,90day,365day',
            'crypto_currency' => 'required|max:20',
            'wallet_address'  => 'required|max:255',
            'tx_hash'         => 'nullable|max:255',
            'proof_image'     => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf|max:5120',
        ]);

        if (empty($data['tx_hash']) && ! $request->hasFile('proof_image')) {
            return back()->withErrors(['tx_hash' => 'Please provide either a transaction hash or upload a payment screenshot/receipt.'])->withInput();
        }

        // Find matching plan price
        $plan   = self::PLANS[$data['plan']];
        $wallet = CryptoWallet::where('currency', $data['crypto_currency'])->where('is_active', true)->first();

        $proofPath = null;
        if ($request->hasFile('proof_image')) {
            $proofPath = $request->file('proof_image')->store('premium-proofs', 'public');
        }

        PremiumPayment::create([
            'user_id'         => $user->id,
            'plan'            => $data['plan'],
            'amount'          => $plan['price'],
            'crypto_currency' => $data['crypto_currency'],
            'wallet_address'  => $wallet?->address ?? $data['wallet_address'],
            'tx_hash'         => $data['tx_hash'] ?? null,
            'proof_image'     => $proofPath,
            'status'          => 'pending',
        ]);

        return back()->with('success', 'Payment submitted! We will verify your transaction and activate your subscription within 24 hours.');
    }

    public function submitUpgrade(Request $request): RedirectResponse
    {
        $user = $request->user();

        if (! $user->isPremiumActive() || $user->isHighestPremium()) {
            return back()->withErrors(['plan' => 'You are not eligible to upgrade.']);
        }

        if (PremiumPayment::where('user_id', $user->id)->where('status', 'pending')->exists()) {
            return back()->withErrors(['tx_hash' => 'You already have a pending payment under review.']);
        }

        $data = $request->validate([
            'plan'            => 'required|in:30day,90day,365day',
            'crypto_currency' => 'required|max:20',
            'wallet_address'  => 'required|max:255',
            'tx_hash'         => 'nullable|max:255',
            'proof_image'     => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf|max:5120',
        ]);

        if (empty($data['tx_hash']) && ! $request->hasFile('proof_image')) {
            return back()->withErrors(['upgrade_tx_hash' => 'Please provide either a transaction hash or upload a payment screenshot/receipt.'])->withInput();
        }

        $currentPlan = $user->premium_plan;

        if ((self::PLAN_RANK[$data['plan']] ?? 0) <= (self::PLAN_RANK[$currentPlan] ?? 0)) {
            return back()->withErrors(['plan' => 'Please select a higher-tier plan to upgrade.']);
        }

        $upgradeData  = $this->buildUpgradeOptions($user);
        $credit       = $upgradeData['credit'];
        $newPlan      = self::PLANS[$data['plan']];
        $amountDue    = max(0, round($newPlan['price'] - $credit, 2));

        $wallet    = CryptoWallet::where('currency', $data['crypto_currency'])->where('is_active', true)->first();
        $proofPath = null;
        if ($request->hasFile('proof_image')) {
            $proofPath = $request->file('proof_image')->store('premium-proofs', 'public');
        }

        PremiumPayment::create([
            'user_id'           => $user->id,
            'plan'              => $data['plan'],
            'amount'            => $amountDue,
            'crypto_currency'   => $data['crypto_currency'],
            'wallet_address'    => $wallet?->address ?? $data['wallet_address'],
            'tx_hash'           => $data['tx_hash'] ?? null,
            'proof_image'       => $proofPath,
            'status'            => 'pending',
            'is_upgrade'        => true,
            'upgrade_from_plan' => $currentPlan,
            'upgrade_credit'    => $credit,
        ]);

        return back()->with('upgrade_success', 'Upgrade payment submitted! We will verify and upgrade your plan within 24 hours.');
    }

    public function invoice(Request $request, PremiumPayment $payment): View
    {
        if ($payment->user_id !== $request->user()->id) {
            abort(403);
        }

        $planFull = self::PLANS[$payment->plan] ?? ['label' => $payment->plan_label, 'price' => $payment->amount];

        return view('premium.invoice', compact('payment', 'planFull'));
    }

    // ── Private helpers ───────────────────────────────────────────────────────

    private function buildUpgradeOptions(User $user): array
    {
        $currentPlan  = $user->premium_plan;
        $currentDays  = self::PLAN_DAYS[$currentPlan] ?? 30;
        $currentPrice = self::PLANS[$currentPlan]['price'] ?? 0;
        $remainingDays = max(0, (int) now()->diffInDays($user->premium_expires_at, false));
        $dailyRate    = $currentDays > 0 ? $currentPrice / $currentDays : 0;
        $credit       = round($remainingDays * $dailyRate, 2);

        $options = [];
        foreach (self::PLANS as $key => $plan) {
            if ((self::PLAN_RANK[$key] ?? 0) > (self::PLAN_RANK[$currentPlan] ?? 0)) {
                $amountDue = max(0, round($plan['price'] - $credit, 2));
                $options[$key] = [
                    'label'      => $plan['label'],
                    'price'      => $plan['price'],
                    'amount_due' => $amountDue,
                    'credit'     => $credit,
                ];
            }
        }

        return [
            'current_plan'   => $currentPlan,
            'current_label'  => self::PLANS[$currentPlan]['label'] ?? $currentPlan,
            'remaining_days' => $remainingDays,
            'credit'         => $credit,
            'options'        => $options,
        ];
    }
}
