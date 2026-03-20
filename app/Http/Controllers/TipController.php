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
        $amount = $request->amount;

        // Assume users have a 'credit_balance' field
        if ($sender->credit_balance < $amount) {
            return response()->json(['error' => 'Insufficient balance.'], 422);
        }

        DB::transaction(function () use ($sender, $recipient, $amount, $request) {
            $sender->decrement('credit_balance', $amount);
            $recipient->increment('credit_balance', $amount);
            Tip::create([
                'sender_id' => $sender->id,
                'recipient_id' => $recipient->id,
                'amount' => $amount,
                'message' => $request->message,
            ]);
        });

        $recipient->notify(new TipReceivedNotification(
            $sender->name,
            $amount,
            $request->message,
        ));

        return response()->json(['success' => true]);
    }
}
