<?php

namespace App\Http\Controllers;

use App\Models\Block;
use App\Models\Tip;
use App\Models\User;
use App\Models\WalletTransaction;
use App\Notifications\LowBalanceNotification;
use App\Notifications\TipReceivedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;

class TipController extends Controller
{
    /** Credits below this threshold trigger a low-balance notification to the sender. */
    private const LOW_BALANCE_THRESHOLD = 10;

    public function send(Request $request): JsonResponse
    {
        $request->validate([
            'recipient_id' => 'required|exists:users,id',
            'amount'       => 'required|integer|min:1|max:10000',
            'message'      => 'nullable|string|max:255',
        ]);

        /** @var \App\Models\User $sender */
        $sender    = Auth::user();
        $recipient = User::findOrFail($request->recipient_id);
        $amount    = (int) $request->amount;

        // Guard: cannot tip yourself
        if ($sender->id === $recipient->id) {
            return response()->json(['error' => 'You cannot send a gift to yourself.'], 422);
        }

        // Guard: cannot tip banned users
        if ($recipient->is_banned) {
            return response()->json(['error' => 'This user account is not available.'], 422);
        }

        // Guard: cannot tip blocked/blocking users
        if (Block::where(function ($q) use ($sender, $recipient) {
            $q->where('blocker_id', $sender->id)->where('blocked_id', $recipient->id);
        })->orWhere(function ($q) use ($sender, $recipient) {
            $q->where('blocker_id', $recipient->id)->where('blocked_id', $sender->id);
        })->exists()) {
            return response()->json(['error' => 'You cannot send a gift to this user.'], 422);
        }

        $newSenderBalance = 0;

        try {
            DB::transaction(function () use ($sender, $recipient, $amount, $request, &$newSenderBalance) {
                // Re-fetch both users with a write lock to prevent concurrent balance exploits.
                $lockedSender    = User::lockForUpdate()->findOrFail($sender->id);
                $lockedRecipient = User::lockForUpdate()->findOrFail($recipient->id);

                if ((int) ($lockedSender->credit_balance ?? 0) < $amount) {
                    throw new \DomainException('Insufficient balance.');
                }

                $senderOldBalance    = (int) $lockedSender->credit_balance;
                $recipientOldBalance = (int) $lockedRecipient->credit_balance;

                $lockedSender->decrement('credit_balance', $amount);
                $lockedRecipient->increment('credit_balance', $amount);

                $newSenderBalance    = $senderOldBalance - $amount;
                $newRecipientBalance = $recipientOldBalance + $amount;

                $tip = Tip::create([
                    'sender_id'    => $lockedSender->id,
                    'recipient_id' => $lockedRecipient->id,
                    'amount'       => $amount,
                    'message'      => $request->message,
                ]);

                WalletTransaction::create([
                    'user_id'        => $lockedSender->id,
                    'type'           => 'tip_sent',
                    'amount'         => $amount,
                    'balance_after'  => $newSenderBalance,
                    'reference_id'   => $tip->id,
                    'reference_type' => 'tip',
                    'description'    => 'Gift sent to ' . $recipient->name
                        . ($request->message ? ': "' . $request->message . '"' : ''),
                ]);

                WalletTransaction::create([
                    'user_id'        => $lockedRecipient->id,
                    'type'           => 'tip_received',
                    'amount'         => $amount,
                    'balance_after'  => $newRecipientBalance,
                    'reference_id'   => $tip->id,
                    'reference_type' => 'tip',
                    'description'    => 'Gift received from ' . $sender->name
                        . ($request->message ? ': "' . $request->message . '"' : ''),
                ]);
            });
        } catch (\DomainException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }

        // Notify recipient (queued, so failure doesn't break the response)
        try {
            $recipient->notify(new TipReceivedNotification(
                senderName:    $sender->name,
                amount:        $amount,
                message:       $request->message,
                senderPhotoUrl: $sender->primaryPhoto?->thumbnail_url,
            ));
        } catch (\Throwable) {
            // Notification failure must not roll back the completed transaction
        }

        // Notify sender if balance is now low
        if ($newSenderBalance < self::LOW_BALANCE_THRESHOLD) {
            try {
                $sender->notify(new LowBalanceNotification($newSenderBalance));
            } catch (\Throwable) { }
        }

        return response()->json([
            'success'      => true,
            'new_balance'  => $newSenderBalance,
        ]);
    }
}
