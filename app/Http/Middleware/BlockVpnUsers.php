<?php

namespace App\Http\Middleware;

use App\Models\SiteSetting;
use App\Services\VpnDetectionService;
use App\Services\TelegramNotificationService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class BlockVpnUsers
{
    public function __construct(
        private VpnDetectionService $vpnDetector,
        private TelegramNotificationService $telegram
    ) {}

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if VPN blocking is enabled — DB setting takes precedence, fallback to .env
        $dbEnabled = SiteSetting::get('vpn_detection_enabled', null);
        $enabled   = $dbEnabled !== null
            ? filter_var($dbEnabled, FILTER_VALIDATE_BOOLEAN)
            : filter_var(env('VPN_DETECTION_ENABLED', false), FILTER_VALIDATE_BOOLEAN);

        if (!$enabled) {
            return $next($request);
        }

        // Skip for already authenticated users unless check-all is active
        $dbCheckAll = SiteSetting::get('vpn_detection_check_all', null);
        $checkAll   = $dbCheckAll !== null
            ? filter_var($dbCheckAll, FILTER_VALIDATE_BOOLEAN)
            : filter_var(env('VPN_DETECTION_CHECK_ALL', false), FILTER_VALIDATE_BOOLEAN);

        if (Auth::check() && !$checkAll) {
            return $next($request);
        }

        // Get client IP
        $ip = $request->ip();

        // Perform VPN detection
        $result = $this->vpnDetector->detect($ip);

        // Log the detection
        $this->vpnDetector->logDetection(
            $ip,
            Auth::id(),
            $result,
            $result['is_vpn'] ? 'blocked' : 'allowed'
        );

        // If VPN is detected, block the request
        if ($result['is_vpn']) {
            Log::warning('VPN detected and blocked', [
                'ip' => $ip,
                'confidence' => $result['confidence'],
                'provider' => $result['provider'],
                'user_id' => Auth::id(),
                'route' => $request->route()?->getName(),
            ]);

            // Send Telegram notification
            $this->telegram->notifyVpnDetection(
                $ip,
                $result['confidence'],
                $result['provider'],
                Auth::id()
            );

            // Return custom error view
            return response()->view('errors.vpn-blocked', [
                'confidence' => $result['confidence'],
                'provider' => $result['provider'],
                'support_email' => config('mail.from.address'),
            ], 403);
        }

        return $next($request);
    }
}
