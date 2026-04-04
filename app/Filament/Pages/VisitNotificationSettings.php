<?php

namespace App\Filament\Pages;

use App\Models\SiteSetting;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;

class VisitNotificationSettings extends Page
{
    protected string $view = 'filament.pages.visit-notification-settings';

    public static function getNavigationIcon(): ?string  { return 'heroicon-o-bell-alert'; }
    public static function getNavigationLabel(): string  { return 'Visit Notifications'; }
    public static function getNavigationGroup(): ?string { return 'Security'; }
    public static function getNavigationSort(): ?int     { return 5; }

    public function getTitle(): string | Htmlable { return 'Visit Notification Settings'; }

    public static function canAccess(): bool
    {
        return Auth::check() && Auth::id() === 1;
    }

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'telegram_visit_notifications_enabled' => filter_var(
                SiteSetting::get('telegram_visit_notifications_enabled', '1'), FILTER_VALIDATE_BOOLEAN
            ),
            'telegram_visit_filter_bots' => filter_var(
                SiteSetting::get('telegram_visit_filter_bots', '1'), FILTER_VALIDATE_BOOLEAN
            ),
            'telegram_visit_filter_datacenter' => filter_var(
                SiteSetting::get('telegram_visit_filter_datacenter', '1'), FILTER_VALIDATE_BOOLEAN
            ),
            'telegram_visit_hourly_limit'    => (int) SiteSetting::get('telegram_visit_hourly_limit', 30),
            'telegram_visit_per_ip_cooldown' => (int) SiteSetting::get('telegram_visit_per_ip_cooldown', 5),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([

                Section::make('Visit Notification Master Switch')
                    ->icon('heroicon-o-bell')
                    ->description('Control whether homepage visits are ever reported to Telegram.')
                    ->schema([
                        Forms\Components\Toggle::make('telegram_visit_notifications_enabled')
                            ->label('Enable visit notifications to Telegram')
                            ->helperText('When off, visits are still recorded in the database but nothing is sent to Telegram.'),
                    ]),

                Section::make('Bot & Crawler Filtering')
                    ->icon('heroicon-o-no-symbol')
                    ->description('Automatically suppress Telegram notifications for automated visitors so real human visits stand out.')
                    ->schema([
                        Forms\Components\Toggle::make('telegram_visit_filter_bots')
                            ->label('Filter known bots & crawlers')
                            ->helperText('Skips notifications for Googlebot, Bingbot, SEO crawlers, AI training bots (GPTBot, CCBot), HTTP libraries (curl, Python-requests, etc.), and any user-agent that lacks a real browser fingerprint.'),

                        Forms\Components\Toggle::make('telegram_visit_filter_datacenter')
                            ->label('Filter datacenter / hosting IPs')
                            ->helperText('Uses the geo-IP "hosting" flag to suppress cloud server, VPS, and datacenter IPs — the most common source of automated traffic.'),
                    ]),

                Section::make('Rate Limiting')
                    ->icon('heroicon-o-clock')
                    ->description('Cap the maximum number of Telegram visit notifications within a time window to prevent flooding.')
                    ->schema([
                        Forms\Components\TextInput::make('telegram_visit_hourly_limit')
                            ->label('Max Telegram visit notifications per hour')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(1000)
                            ->suffix('/ hour')
                            ->helperText('0 = unlimited. Recommended: 20–50. Once the limit is hit, additional visits are silently skipped until the next hour begins.'),

                        Forms\Components\TextInput::make('telegram_visit_per_ip_cooldown')
                            ->label('Per-IP cooldown')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(1440)
                            ->suffix('minutes')
                            ->helperText('Ignore repeat visits from the same IP within this window. Default: 5 min. Increase to 60 min to further reduce noise.'),
                    ]),
            ]);
    }

    public function save(): void
    {
        $data = $this->form->getState();

        SiteSetting::set('telegram_visit_notifications_enabled', $data['telegram_visit_notifications_enabled'] ? '1' : '0');
        SiteSetting::set('telegram_visit_filter_bots',           $data['telegram_visit_filter_bots']          ? '1' : '0');
        SiteSetting::set('telegram_visit_filter_datacenter',     $data['telegram_visit_filter_datacenter']    ? '1' : '0');
        SiteSetting::set('telegram_visit_hourly_limit',          (string) (int) ($data['telegram_visit_hourly_limit']    ?? 30));
        SiteSetting::set('telegram_visit_per_ip_cooldown',       (string) (int) ($data['telegram_visit_per_ip_cooldown'] ?? 5));

        Notification::make()
            ->title('Visit notification settings saved!')
            ->success()
            ->send();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label('Save Settings')
                ->icon('heroicon-o-check')
                ->color('primary')
                ->action('save'),

            Action::make('reset_rate_limit')
                ->label('Reset Hourly Counter')
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Reset the hourly notification counter?')
                ->modalDescription('This clears the in-memory counter so notifications can be sent again before the next hour window resets.')
                ->action(function () {
                    RateLimiter::clear('telegram_visit_notif_hourly');
                    Notification::make()->title('Hourly counter reset.')->success()->send();
                }),
        ];
    }

    /** Live stats for the view. */
    public function getRateLimitStatusProperty(): array
    {
        $limit        = (int) SiteSetting::get('telegram_visit_hourly_limit', 30);
        $used         = RateLimiter::attempts('telegram_visit_notif_hourly');
        $remaining    = $limit > 0 ? max(0, $limit - $used) : null;
        $retryAfter   = RateLimiter::availableIn('telegram_visit_notif_hourly');
        $isThrottled  = $limit > 0 && RateLimiter::tooManyAttempts('telegram_visit_notif_hourly', $limit);

        return compact('limit', 'used', 'remaining', 'retryAfter', 'isThrottled');
    }
}
