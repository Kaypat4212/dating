<?php

namespace App\Http\Controllers;

use App\Models\Referral;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class InviteController extends Controller
{
    /** Show the user's invite / referral dashboard. */
    public function index(): View
    {
        $user = auth()->user();
        $this->ensureReferralCode($user);

        $referrals = Referral::with('referred:id,name,created_at')
            ->where('referrer_id', $user->id)
            ->latest()
            ->get();

        $inviteLink = route('register') . '?ref=' . $user->referral_code;

        return view('invite.index', compact('user', 'referrals', 'inviteLink'));
    }

    /**
     * Public landing — store the referral code in session and
     * redirect to the registration page.
     */
    public function track(string $code): RedirectResponse
    {
        $referrer = User::where('referral_code', $code)->first();

        if ($referrer) {
            session(['referral_code' => $code]);
        }

        return redirect()->route('register')->with('ref_note', 'You were invited by a friend!');
    }

    /** Ensure the user always has a referral code set. */
    private function ensureReferralCode(User $user): void
    {
        if (empty($user->referral_code)) {
            $user->referral_code = strtoupper(Str::random(8));
            $user->save();
        }
    }
}
