<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\SiteSetting;
use App\Notifications\LoginAlertNotification;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Send login alert email if the feature is enabled.
        if (SiteSetting::get('email_login_alert_enabled', true)) {
            try {
                $ip     = $request->ip();
                $agent  = $request->userAgent() ?? 'Unknown browser';
                // Shorten the user-agent to a readable label
                $device = static::parseDevice($agent);
                $time   = now()->format('D, d M Y H:i:s T');

                $user->notify(new LoginAlertNotification($ip, $device, 'N/A', $time));
            } catch (\Throwable) {
                // Non-critical — never block login because of an email failure.
            }
        }

        // Log login activity for spam/suspicious scoring
        ActivityLogger::log($user, 'login', [], $request);

        return redirect()->intended(route('dashboard'));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }

    /**
     * Parse a User-Agent string into a concise human-readable label.
     */
    private static function parseDevice(string $ua): string
    {
        $browser = 'Unknown browser';
        $os      = 'Unknown OS';

        // Browser detection (order matters — most specific first)
        if (str_contains($ua, 'Edg/'))          $browser = 'Microsoft Edge';
        elseif (str_contains($ua, 'OPR/') || str_contains($ua, 'Opera')) $browser = 'Opera';
        elseif (str_contains($ua, 'Chrome/'))   $browser = 'Chrome';
        elseif (str_contains($ua, 'Firefox/'))  $browser = 'Firefox';
        elseif (str_contains($ua, 'Safari/') && str_contains($ua, 'Version/')) $browser = 'Safari';

        // OS detection
        if (str_contains($ua, 'Windows NT'))    $os = 'Windows';
        elseif (str_contains($ua, 'Macintosh')) $os = 'macOS';
        elseif (str_contains($ua, 'iPhone'))    $os = 'iPhone';
        elseif (str_contains($ua, 'iPad'))      $os = 'iPad';
        elseif (str_contains($ua, 'Android'))   $os = 'Android';
        elseif (str_contains($ua, 'Linux'))     $os = 'Linux';

        return "{$browser} on {$os}";
    }
}
