<?php

namespace App\Filament\Pages;

use App\Helpers\CountryHelper;
use App\Models\SiteSetting;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
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
                    ->description(fn (Get $get) => match ($get('country_restriction_mode')) {
                        'blocklist' => 'Users from these countries will be blocked.',
                        'allowlist' => 'Only users from these countries will be allowed.',
                        default     => 'Select a mode above to configure countries.',
                    })
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
                    ->visible(fn (Get $get) => $get('country_restriction_mode') !== 'none'),

                Section::make('How It Works')
                    ->icon('heroicon-o-information-circle')
                    ->description('Details about how country detection and enforcement work.')
                    ->schema([
                        Forms\Components\Placeholder::make('how_it_works')
                            ->label('')
                            ->content(new \Illuminate\Support\HtmlString('
                                <ul class="list-disc pl-5 space-y-1 text-sm text-gray-600 dark:text-gray-400">
                                    <li><strong>Detection:</strong> The visitor\'s IP address is looked up via <code>ip-api.com</code> (free, no key needed).</li>
                                    <li><strong>Caching:</strong> Results are cached for 24 hours per IP to avoid API rate limits.</li>
                                    <li><strong>Admin bypass:</strong> The <code>/admin</code> panel is always accessible regardless of restrictions.</li>
                                    <li><strong>Private IPs:</strong> Local / development IPs are never blocked.</li>
                                    <li><strong>Detection failure:</strong> If geolocation cannot be determined, the visitor is allowed through to prevent false-blocking.</li>
                                    <li><strong>Rate limit:</strong> ip-api.com allows 45 requests / minute on the free tier. For high-traffic sites consider a paid plan.</li>
                                </ul>
                            '))
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
