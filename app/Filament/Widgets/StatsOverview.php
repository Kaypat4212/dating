<?php

namespace App\Filament\Widgets;

use App\Models\PremiumPayment;
use App\Models\Report;
use App\Models\User;
use App\Models\UserMatch;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        return [
            Stat::make('Total Members', User::count())
                ->description('All registered users')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('primary'),

            Stat::make('Premium Members', User::where('is_premium', true)->count())
                ->description('Active subscribers')
                ->descriptionIcon('heroicon-m-star')
                ->color('warning'),

            Stat::make('Total Matches', UserMatch::count())
                ->description('Mutual likes')
                ->descriptionIcon('heroicon-m-heart')
                ->color('danger'),

            Stat::make('Pending Payments', PremiumPayment::where('status', 'pending')->count())
                ->description('Awaiting review')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('warning'),

            Stat::make('Open Reports', Report::where('status', 'pending')->count())
                ->description('Needs attention')
                ->descriptionIcon('heroicon-m-flag')
                ->color('danger'),

            Stat::make('Active Today', User::where('last_active_at', '>=', now()->subDay())->count())
                ->description('Users active in last 24h')
                ->descriptionIcon('heroicon-m-bolt')
                ->color('success'),
        ];
    }
}
