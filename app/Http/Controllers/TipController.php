<?php

namespace App\Http\Controllers;

use App\Models\Tip;
use App\Models\User;
use App\Notifications\TipReceivedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;

class TipController extends Controller
{
    public function send(Request $request): JsonResponse
    {
        $request->validate([
            'recipient_id' => 'required|exists:users,id',
            'amount' => 'required|integer|min:1',
            'message' => 'nullable|string|max:255',
        ]);

        /** @var \App\Models\User $sender */
        $sender = Auth::user();
        $recipient = User::findOrFail($request->recipient_id);
        $amount = (int) $request->amount;

        if ($sender->id === $recipient->id) {
            return response()->json(['error' => 'You cannot tip yourself.'], 422);
        }

        try {
            DB::transaction(function () use ($sender, $recipient, $amount, $request) {
                // Re-fetch with a row-level lock inside the transaction to prevent
                // concurrent requests from bypassing the balance check.
                $lockedSender = User::lockForUpdate()->find($sender->id);

                if ((int) ($lockedSender->credit_balance ?? 0) < $amount) {
                    throw new \DomainException('Insufficient balance.');
                }

                $lockedSender->decrement('credit_balance', $amount);
                $recipient->increment('credit_balance', $amount);

                Tip::create([
                    'sender_id'    => $lockedSender->id,
                    'recipient_id' => $recipient->id,
                    'amount'       => $amount,
                    'message'      => $request->message,
                ]);
            });
        } catch (\DomainException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }

        $recipient->notify(new TipReceivedNotification(
            $sender->name,
            $amount,
            $request->message,
        ));

        return response()->json(['success' => true]);
    }
}
