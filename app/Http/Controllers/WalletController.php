<?php

namespace App\Http\Controllers;

use App\Models\Tip;
use App\Models\User;
use App\Models\SiteSetting;
use App\Models\WalletFundingRequest;
use App\Models\WalletWithdrawalRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;

class WalletController extends Controller
{
    public function show()
    {
        $wallets = \App\Models\CryptoWallet::active()->get();
        $creditsPerUsd = (float) SiteSetting::get('credits_per_usd', 10);
        return view('wallet.index', compact('wallets', 'creditsPerUsd'));
    }
    public function balance(): JsonResponse
    {
        $user = Auth::user();
        $received = Tip::where('recipient_id', $user->id)->sum('amount');
        $sent = Tip::where('sender_id', $user->id)->sum('amount');
        return response()->json([
            'balance' => $user->credit_balance,
            'received_total' => $received,
            'sent_total' => $sent,
        ]);
    }

    public function fund(Request $request): JsonResponse
    {
        $request->validate([
            'amount' => 'required|integer|min:1',
            'txid'   => 'required|string',
            'proof'  => 'required|image|max:4096',
        ]);
        $user = Auth::user();
        $proofPath = $request->file('proof')->store('wallet_proofs', 'public');
        WalletFundingRequest::create([
            'user_id'    => $user->id,
            'amount'     => $request->amount,
            'txid'       => $request->txid,
            'proof_path' => $proofPath,
            'status'     => 'pending',
        ]);
        return response()->json(['success' => true, 'message' => 'Funding request submitted for review.']);
    }

    public function withdraw(Request $request): JsonResponse
    {
        $request->validate([
            'amount'      => 'required|integer|min:1',
            'destination' => 'required|string|max:200',
            'currency'    => 'nullable|string|max:20',
            'network'     => 'nullable|string|max:60',
        ]);
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if ($user->credit_balance < $request->amount) {
            return response()->json(['error' => 'Insufficient balance. You only have '.$user->credit_balance.' credits.'], 422);
        }
        if ($request->amount < 10) {
            return response()->json(['error' => 'Minimum withdrawal is 10 credits.'], 422);
        }
        WalletWithdrawalRequest::create([
            'user_id'     => $user->id,
            'amount'      => $request->amount,
            'destination' => $request->destination,
            'currency'    => $request->currency,
            'network'     => $request->network,
            'status'      => 'pending',
        ]);
        $user->decrement('credit_balance', $request->amount);
        return response()->json(['success' => true, 'message' => 'Withdrawal request submitted! An admin will process it shortly.']);
    }

    public function receivedTips(): JsonResponse
    {
        $user = Auth::user();
        $tips = Tip::where('recipient_id', $user->id)->with('sender')->latest()->get();
        return response()->json(['tips' => $tips]);
    }

    public function sentTips(): JsonResponse
    {
        $user = Auth::user();
        $tips = Tip::where('sender_id', $user->id)->with('recipient')->latest()->get();
        return response()->json(['tips' => $tips]);
    }

    public function fundingHistory(): JsonResponse
    {
        $user = Auth::user();
        $requests = WalletFundingRequest::where('user_id', $user->id)->latest()->get();
        return response()->json(['requests' => $requests]);
    }

    public function withdrawalHistory(): JsonResponse
    {
        $user = Auth::user();
        $requests = WalletWithdrawalRequest::where('user_id', $user->id)->latest()->get();
        return response()->json(['requests' => $requests]);
    }
}
