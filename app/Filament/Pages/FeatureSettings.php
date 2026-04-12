<?php

namespace App\Filament\Pages;

use App\Models\SiteSetting;
use Filament\Actions\Action;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Auth;

class FeatureSettings extends Page
{
    protected string $view = 'filament.pages.feature-settings';

    public static function getNavigationIcon(): ?string  { return 'heroicon-o-puzzle-piece'; }
    public static function getNavigationLabel(): string  { return 'Feature Toggles'; }
    public static function getNavigationGroup(): ?string { return 'Site Settings'; }
    public static function getNavigationSort(): ?int     { return 2; }

    public function getTitle(): string | Htmlable { return 'Feature Toggles'; }

    public static function canAccess(): bool
    {
        return Auth::check() && Auth::id() === 1;
    }

    public array $data = [];

    // All feature keys with their defaults
    public static array $features = [
        'feature_snaps_enabled'        => true,
        'feature_streaks_enabled'      => true,
        'feature_stories_enabled'      => true,
        'feature_speed_dating_enabled' => true,
        'feature_forums_enabled'       => true,
        'feature_safe_date_enabled'    => true,
        'feature_badges_enabled'       => true,
        'feature_boosts_enabled'       => true,
        'feature_icebreakers_enabled'  => true,
        'feature_waves_enabled'        => true,
        'feature_travel_enabled'       => true,
        'feature_second_chance_enabled'=> true,
        'feature_leaderboard_enabled'  => true,
        'feature_gifts_enabled'        => true,
    ];

    public function mount(): void
    {
        $saved   = SiteSetting::allAsArray();
        $merged  = array_merge(static::$features, array_intersect_key($saved, static::$features));

        foreach (array_keys(static::$features) as $key) {
            $merged[$key] = filter_var($merged[$key], FILTER_VALIDATE_BOOLEAN);
        }

        $this->form->fill($merged);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([

                Section::make('Communication & Dating')
                    ->description('These features appear in chat, profile pages, and the homepage.')
                    ->columns(2)
                    ->schema([
                        Toggle::make('feature_snaps_enabled')
                            ->label('Snaps (Disappearing Photos)')
                            ->helperText('Send disappearing photos and videos to matches. Hides snap buttons and the homepage mention when off.'),

                        Toggle::make('feature_streaks_enabled')
                            ->label('Daily Streaks')
                            ->helperText('Gamified daily interaction streaks. Hides streak badges, counters, and homepage card when off.'),

                        Toggle::make('feature_waves_enabled')
                            ->label('Waves')
                            ->helperText('Quick one-tap wave to show interest without matching first.'),

                        Toggle::make('feature_icebreakers_enabled')
                            ->label('Icebreaker Questions')
                            ->helperText('Daily conversation-starter questions shown on match profiles.'),

                        Toggle::make('feature_gifts_enabled')
                            ->label('Virtual Gifts')
                            ->helperText('Send virtual gifts to matches as a premium feature.'),
                    ]),

                Section::make('Social & Community')
                    ->description('These features appear in the navigation and community sections.')
                    ->columns(2)
                    ->schema([
                        Toggle::make('feature_forums_enabled')
                            ->label('Country / Community Forums')
                            ->helperText('Discussion forums grouped by country. Hides forum nav link and homepage card when off.'),

                        Toggle::make('feature_stories_enabled')
                            ->label('Stories')
                            ->helperText('24-hour disappearing stories visible on the discover page.'),

                        Toggle::make('feature_speed_dating_enabled')
                            ->label('Speed Dating Events')
                            ->helperText('Timed speed-dating sessions. Hides events page and homepage mention when off.'),

                        Toggle::make('feature_leaderboard_enabled')
                            ->label('Leaderboard')
                            ->helperText('Public leaderboard ranking most active/popular users.'),

                        Toggle::make('feature_badges_enabled')
                            ->label('Badges & XP')
                            ->helperText('Gamification system — badges earned for activity milestones. Hides the homepage card when off.'),
                    ]),

                Section::make('Safety & Discovery')
                    ->description('Core trust and discovery features.')
                    ->columns(2)
                    ->schema([
                        Toggle::make('feature_safe_date_enabled')
                            ->label('Safe Date Check-In')
                            ->helperText('Users can set a safe-date timer that alerts emergency contacts if not checked in. Hides the homepage card when off.'),

                        Toggle::make('feature_boosts_enabled')
                            ->label('Profile Boosts')
                            ->helperText('Paid/earned profile boosts that increase visibility. Hides boost button and premium plan mention when off.'),

                        Toggle::make('feature_travel_enabled')
                            ->label('Travel Mode')
                            ->helperText('Let users match in a target city before they travel.'),

                        Toggle::make('feature_second_chance_enabled')
                            ->label('Second Chance')
                            ->helperText('Recover accidentally swiped-left profiles.'),
                    ]),
            ]);
    }

    public function save(): void
    {
        $data = $this->form->getState();

        foreach (array_keys(static::$features) as $key) {
            SiteSetting::set($key, isset($data[$key]) && $data[$key] ? '1' : '0');
        }

        Notification::make()
            ->title('Feature settings saved!')
            ->body('Changes take effect immediately — no deployment needed.')
            ->success()
            ->send();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label('Save Changes')
                ->icon('heroicon-o-check-circle')
                ->action('save'),
        ];
    }
}
