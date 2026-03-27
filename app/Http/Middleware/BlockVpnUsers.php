<?php

namespace App\Http\Middleware;

use App\Models\SiteSetting;
use App\Services\VpnDetectionService;
use App\Services\TelegramNotificationService;
use Closure;
use Illuminate\Http\Request;
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
        // Check if VPN blocking is enabled
        if (!SiteSetting::get('vpn_detection_enabled', false)) {
            return $next($request);
        }

        // Skip for already authenticated users (only check on registration)
        if (auth()->check() && !SiteSetting::get('vpn_detection_check_all', false)) {
            return $next($request);
        }

        // Get client IP
        $ip = $request->ip();

        // Perform VPN detection
        $result = $this->vpnDetector->detect($ip);

        // Log the detection
        $this->vpnDetector->logDetection(
            $ip,
            auth()->id(),
            $result,
            $result['is_vpn'] ? 'blocked' : 'allowed'
        );

        // If VPN is detected, block the request
        if ($result['is_vpn']) {
            Log::warning('VPN detected and blocked', [
                'ip' => $ip,
                'confidence' => $result['confidence'],
                'provider' => $result['provider'],
                'user_id' => auth()->id(),
                'route' => $request->route()?->getName(),
            ]);

            // Send Telegram notification
            $this->telegram->notifyVpnDetection(
                $ip,
                $result['confidence'],
                $result['provider'],
                auth()->id()
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
