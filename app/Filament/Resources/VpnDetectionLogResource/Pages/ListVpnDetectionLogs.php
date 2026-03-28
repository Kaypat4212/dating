<?php

namespace App\Filament\Resources\VpnDetectionLogResource\Pages;

use App\Filament\Resources\VpnDetectionLogResource;
use App\Models\VpnDetectionLog;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListVpnDetectionLogs extends ListRecords
{
    protected static string $resource = VpnDetectionLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('clearOldLogs')
                ->label('Clear Old Logs')
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Clear Old VPN Detection Logs')
                ->modalDescription('This will delete all VPN detection logs older than 90 days. This action cannot be undone.')
                ->modalSubmitActionLabel('Delete Old Logs')
                ->action(function () {
                    $deleted = VpnDetectionLog::where('created_at', '<', now()->subDays(90))->delete();
                    
                    Notification::make()
                        ->title("Successfully deleted {$deleted} old log entries.")
                        ->success()
                        ->send();
                }),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All Detections')
                ->badge(VpnDetectionLog::count()),
            
            'vpn_detected' => Tab::make('VPN Detected')
                ->badge(VpnDetectionLog::where('is_vpn', true)->count())
                ->badgeColor('danger')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_vpn', true)),
            
            'blocked' => Tab::make('Blocked')
                ->badge(VpnDetectionLog::where('action_taken', 'blocked')->count())
                ->badgeColor('danger')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('action_taken', 'blocked')),
            
            'high_confidence' => Tab::make('High Confidence')
                ->badge(VpnDetectionLog::where('confidence', '>=', 80)->count())
                ->badgeColor('warning')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('confidence', '>=', 80)),
            
            'today' => Tab::make('Today')
                ->badge(VpnDetectionLog::whereDate('created_at', today())->count())
                ->badgeColor('info')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereDate('created_at', today())),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            VpnDetectionLogResource\Widgets\VpnStatsWidget::class,
        ];
    }
}
