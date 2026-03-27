<?php

namespace App\Filament\Pages;

use App\Models\VpnDetectionLog;
use App\Models\UserActivityLog;
use App\Models\User;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;

class SecurityDashboard extends Page
{
    protected string $view = 'filament.pages.security-dashboard';

    public static function getNavigationIcon(): ?string 
    { 
        return 'heroicon-o-shield-check'; 
    }

    public static function getNavigationLabel(): string 
    { 
        return 'Security Dashboard'; 
    }

    public static function getNavigationGroup(): ?string 
    { 
        return 'Security'; 
    }

    public static function getNavigationSort(): ?int 
    { 
        return 0; 
    }

    public function getStats(): array
    {
        $today = now()->startOfDay();
        $week = now()->subDays(7);
        $month = now()->subDays(30);

        return [
            // VPN Detection Stats
            'vpn_today' => VpnDetectionLog::where('is_vpn', true)
                ->whereDate('created_at', today())
                ->count(),
            
            'vpn_week' => VpnDetectionLog::where('is_vpn', true)
                ->where('created_at', '>=', $week)
                ->count(),
            
            'vpn_blocked_today' => VpnDetectionLog::where('action_taken', 'blocked')
                ->whereDate('created_at', today())
                ->count(),
            
            'vpn_high_confidence' => VpnDetectionLog::where('is_vpn', true)
                ->where('confidence', '>=', 80)
                ->where('created_at', '>=', $week)
                ->count(),
            
            // Activity Stats
            'suspicious_today' => UserActivityLog::where('flag', 'suspicious')
                ->whereDate('created_at', today())
                ->count(),
            
            'suspicious_week' => UserActivityLog::where('flag', 'suspicious')
                ->where('created_at', '>=', $week)
                ->count(),
            
            'failed_logins_today' => UserActivityLog::where('action', 'login_failed')
                ->whereDate('created_at', today())
                ->count(),
            
            'spam_reports_today' => UserActivityLog::where('flag', 'spam')
                ->whereDate('created_at', today())
                ->count(),
            
            // User Security Stats
            'banned_users' => User::where('is_banned', true)->count(),
            'flagged_users' => User::where('is_suspicious', true)->count(),
            
            // System Status
            'vpn_detection_enabled' => env('VPN_DETECTION_ENABLED', false),
            'telegram_enabled' => env('TELEGRAM_NOTIFICATIONS_ENABLED', false),
            'debug_mode' => env('APP_DEBUG', false),
        ];
    }

    public function getRecentVpnDetections(): array
    {
        return VpnDetectionLog::with('user')
            ->where('is_vpn', true)
            ->orderByDesc('created_at')
            ->limit(10)
            ->get()
            ->map(fn($log) => [
                'id' => $log->id,
                'ip' => $log->ip_address,
                'user' => $log->user?->name ?? 'Guest',
                'user_id' => $log->user_id,
                'confidence' => $log->confidence,
                'provider' => $log->provider,
                'action' => $log->action_taken,
                'time' => $log->created_at->diffForHumans(),
                'timestamp' => $log->created_at->format('Y-m-d H:i:s'),
            ])
            ->toArray();
    }

    public function getRecentSuspiciousActivity(): array
    {
        return UserActivityLog::with('user')
            ->where('flag', 'suspicious')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get()
            ->map(fn($log) => [
                'id' => $log->id,
                'user' => $log->user?->name ?? 'Deleted User',
                'user_id' => $log->user_id,
                'action' => $log->action,
                'ip' => $log->ip_address,
                'time' => $log->created_at->diffForHumans(),
                'timestamp' => $log->created_at->format('Y-m-d H:i:s'),
            ])
            ->toArray();
    }

    public function getTopVpnProviders(): array
    {
        return VpnDetectionLog::select('provider', DB::raw('count(*) as count'))
            ->where('is_vpn', true)
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('provider')
            ->orderByDesc('count')
            ->limit(10)
            ->get()
            ->map(fn($item) => [
                'provider' => $item->provider ?: 'Unknown',
                'count' => $item->count,
            ])
            ->toArray();
    }

    public function getTopSuspiciousIps(): array
    {
        return VpnDetectionLog::select('ip_address', DB::raw('count(*) as count'))
            ->where('is_vpn', true)
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('ip_address')
            ->orderByDesc('count')
            ->limit(10)
            ->get()
            ->map(fn($item) => [
                'ip' => $item->ip_address,
                'count' => $item->count,
            ])
            ->toArray();
    }

    public function getSecurityAlerts(): array
    {
        $alerts = [];

        // Check if debug mode is enabled
        if (env('APP_DEBUG', false)) {
            $alerts[] = [
                'type' => 'danger',
                'icon' => 'exclamation-triangle',
                'title' => 'Debug Mode Enabled',
                'message' => 'APP_DEBUG is set to true. This should NEVER be enabled in production!',
                'action' => 'Disable immediately in .env file',
            ];
        }

        // Check if VPN detection is disabled
        if (!env('VPN_DETECTION_ENABLED', true)) {
            $alerts[] = [
                'type' => 'warning',
                'icon' => 'shield-exclamation',
                'title' => 'VPN Detection Disabled',
                'message' => 'VPN detection system is currently disabled.',
                'action' => 'Enable in VPN Settings',
            ];
        }

        // Check for high failed login attempts
        $failedLogins = UserActivityLog::where('action', 'login_failed')
            ->whereDate('created_at', today())
            ->count();
        
        if ($failedLogins > 50) {
            $alerts[] = [
                'type' => 'warning',
                'icon' => 'key',
                'title' => 'High Failed Login Attempts',
                'message' => "{$failedLogins} failed login attempts today. Possible brute force attack.",
                'action' => 'Review Activity Log',
            ];
        }

        // Check for high VPN detections
        $vpnToday = VpnDetectionLog::where('is_vpn', true)
            ->whereDate('created_at', today())
            ->count();
        
        if ($vpnToday > 20) {
            $alerts[] = [
                'type' => 'info',
                'icon' => 'shield-check',
                'title' => 'High VPN Activity',
                'message' => "{$vpnToday} VPN connections detected today.",
                'action' => 'Review VPN Detections',
            ];
        }

        // Check for suspicious activity spike
        $suspicious = UserActivityLog::where('flag', 'suspicious')
            ->whereDate('created_at', today())
            ->count();
        
        if ($suspicious > 10) {
            $alerts[] = [
                'type' => 'warning',
                'icon' => 'flag',
                'title' => 'Suspicious Activity Spike',
                'message' => "{$suspicious} suspicious activities flagged today.",
                'action' => 'Investigate immediately',
            ];
        }

        return $alerts;
    }
}
