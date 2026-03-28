<?php

namespace App\Filament\Pages;

use App\Helpers\CountryHelper;
use App\Models\SiteSetting;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;

class CountryRestrictions extends Page
{
    protected string $view = 'filament.pages.country-restrictions';

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-globe-alt';
    }

    public static function getNavigationLabel(): string
    {
        return 'Country Restrictions';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Security';
    }

    public static function getNavigationSort(): ?int
    {
        return 3;
    }

    public function getTitle(): string|Htmlable
    {
        return 'Country Access Restrictions';
    }

    /** @var array<string, mixed> */
    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'country_restriction_mode'    => SiteSetting::get('country_restriction_mode', 'none'),
            'country_restriction_countries' => json_decode(
                SiteSetting::get('country_restriction_countries', '[]'),
                true
            ) ?? [],
        ]);
    }

    public function form(Schema $schema): Schema
    {
        // Build [code => "Flag  Name"] options for the multi-select
        $countryOptions = collect(CountryHelper::list())
            ->mapWithKeys(fn ($code, $name) => [$code => $name])
            ->sortKeys()
            ->toArray();

        return $schema
            ->statePath('data')
            ->components([

                Section::make('Restriction Mode')
                    ->icon('heroicon-o-lock-closed')
                    ->description('Choose how country-based access control works. Changes take effect immediately after saving.')
                    ->schema([
                        Forms\Components\Select::make('country_restriction_mode')
                            ->label('Mode')
                            ->options([
                                'none'      => 'Disabled — allow everyone',
                                'blocklist' => 'Blocklist — block selected countries, allow all others',
                                'allowlist' => 'Allowlist — only allow selected countries, block everyone else',
                            ])
                            ->default('none')
                            ->live()
                            ->required()
                            ->helperText('Blocklist: good for banning specific countries. Allowlist: good for region-only platforms.'),
                    ])->columns(1),

                Section::make('Countries')
                    ->icon('heroicon-o-map')
                    ->description('Blocklist: visitors from selected countries are blocked. Allowlist: only visitors from selected countries are allowed.')
                    ->schema([
                        Forms\Components\Select::make('country_restriction_countries')
                            ->label('Countries')
                            ->options($countryOptions)
                            ->multiple()
                            ->searchable()
                            ->placeholder('Search and select countries...')
                            ->helperText('You can select multiple countries. Use the search box to find countries quickly.')
                            ->columnSpanFull(),
                    ])
                    ->columns(1)
                    ->visible(fn ($get) => $get('country_restriction_mode') !== 'none'),

                Section::make('How It Works')
                    ->icon('heroicon-o-information-circle')
                    ->description('Details about how country detection and enforcement work.')
                    ->schema([
                        Forms\Components\Textarea::make('_info')
                            ->label('How it works')
                            ->disabled()
                            ->rows(6)
                            ->default(implode("\n", [
                                '• Detection: visitor IP looked up via ip-api.com (free, no key needed).',
                                '• Caching: result cached 24 hours per IP to stay within rate limits.',
                                '• Admin bypass: the /admin panel is always accessible.',
                                '• Private IPs: local/dev IPs are never blocked.',
                                '• Detection failure: if geo-lookup fails the visitor is allowed through.',
                                '• Rate limit: ip-api.com allows 45 requests/minute on the free tier.',
                            ]))
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }

    public function save(): void
    {
        $data = $this->form->getState();

        SiteSetting::set('country_restriction_mode', $data['country_restriction_mode']);
        SiteSetting::set(
            'country_restriction_countries',
            json_encode($data['country_restriction_countries'] ?? [])
        );

        Notification::make()
            ->title('Country restrictions saved')
            ->success()
            ->send();
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Save Restrictions')
                ->submit('save')
                ->icon('heroicon-o-check')
                ->color('primary'),
        ];
    }
}
