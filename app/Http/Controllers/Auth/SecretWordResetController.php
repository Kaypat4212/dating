<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class SecretWordResetController extends Controller
{
    /** Show the secret-word reset form. */
    public function create(): View
    {
        return view('auth.secret-reset');
    }

    /** Handle the secret-word reset submission. */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email'                 => ['required', 'email'],
            'secret_word'           => ['required', 'string'],
            'password'              => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Find the user first
        $user = User::where('email', $request->email)->first();

        if (! $user) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'No account found with that email address.']);
        }

        // User must have set a personal secret word
        if (! $user->secret_word) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['secret_word' => 'No secret word has been set for this account. Please use the email reset link instead.']);
        }

        // Check secret word against the user's own hashed secret word
        if (! Hash::check($request->secret_word, $user->secret_word)) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['secret_word' => 'The secret word is incorrect.']);
        }

        // Update password
        $user->forceFill([
            'password'       => Hash::make($request->password),
            'remember_token' => \Illuminate\Support\Str::random(60),
        ])->save();

        return redirect()->route('login')
            ->with('status', 'Password reset successfully. You can now sign in with your new password.');
    }
}
