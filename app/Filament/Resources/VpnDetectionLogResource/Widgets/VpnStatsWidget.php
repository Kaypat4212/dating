<?php

namespace App\Filament\Resources\VpnDetectionLogResource\Widgets;

use App\Models\VpnDetectionLog;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class VpnStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $totalDetections = VpnDetectionLog::count();
        $vpnDetected = VpnDetectionLog::where('is_vpn', true)->count();
        $blocked = VpnDetectionLog::where('action_taken', 'blocked')->count();
        $todayDetections = VpnDetectionLog::whereDate('created_at', today())->count();
        $weekDetections = VpnDetectionLog::where('created_at', '>=', now()->subDays(7))->count();
        
        $vpnPercentage = $totalDetections > 0 ? round(($vpnDetected / $totalDetections) * 100, 1) : 0;
        $blockPercentage = $vpnDetected > 0 ? round(($blocked / $vpnDetected) * 100, 1) : 0;

        return [
            Stat::make('Total Detections', $totalDetections)
                ->description('All detection attempts')
                ->descriptionIcon('heroicon-o-eye')
                ->color('primary'),
            
            Stat::make('VPN Detected', $vpnDetected)
                ->description("{$vpnPercentage}% detection rate")
                ->descriptionIcon('heroicon-o-shield-exclamation')
                ->color('danger'),
            
            Stat::make('Blocked', $blocked)
                ->description("{$blockPercentage}% of VPN users blocked")
                ->descriptionIcon('heroicon-o-no-symbol')
                ->color('warning'),
            
            Stat::make('Today', $todayDetections)
                ->description('Detections today')
                ->descriptionIcon('heroicon-o-calendar')
                ->color('info'),
            
            Stat::make('Last 7 Days', $weekDetections)
                ->description('Recent activity')
                ->descriptionIcon('heroicon-o-chart-bar')
                ->color('success'),
            
            Stat::make('Avg Confidence', VpnDetectionLog::where('is_vpn', true)->avg('confidence') ?? 0)
                ->description('Average for detected VPNs')
                ->descriptionIcon('heroicon-o-calculator')
                ->color('gray')
                ->formatStateUsing(fn ($state) => round($state, 1) . '%'),
        ];
    }

    protected function getColumns(): int
    {
        return 3;
    }
}
