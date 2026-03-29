<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ImpersonateController extends Controller
{
    /**
     * Admin logs in as the given user.
     * Stores the original admin ID in session so we can return later.
     */
    public function login(Request $request, User $user)
    {
        /** @var \App\Models\User $admin */
        $admin = $request->user();

        // Only admins may impersonate
        abort_unless($admin && $admin->hasRole('admin'), 403, 'Admin access required to impersonate users.');

        // Prevent admin from impersonating themselves
        if ($admin->id === $user->id) {
            return back()->with('error', 'You cannot impersonate yourself.');
        }

        // Store the real admin ID
        session(['impersonating_id' => $admin->id]);

        Auth::login($user);

        return redirect()->route('dashboard')
            ->with('success', 'You are now logged in as ' . $user->name . '. Use the banner to return to admin.');
    }

    /**
     * Return to the original admin account.
     */
    public function leave(Request $request)
    {
        $adminId = session()->pull('impersonating_id');

        if (! $adminId) {
            return redirect()->route('dashboard');
        }

        Auth::loginUsingId($adminId);

        return redirect(config('app.url') . '/admin/users')->with('success', 'Returned to admin account.');
    }
}
