<?php

namespace App\Filament\Pages;

use App\Models\SiteSetting;
use Filament\Actions\Action;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Artisan;

class ManageDomainSettings extends Page
{
    protected string $view = 'filament.pages.manage-domain-settings';

    public static function getNavigationIcon(): ?string  { return 'heroicon-o-globe-alt'; }
    public static function getNavigationLabel(): string  { return 'Domain & Session'; }
    public static function getNavigationGroup(): ?string { return 'Site Settings'; }
    public static function getNavigationSort(): ?int     { return 5; }

    public function getTitle(): string|Htmlable { return 'Domain & Session Settings'; }

    /** @var array<string, mixed> */
    public array $data = [];

    public function mount(): void
    {
        $saved = SiteSetting::allAsArray();

        // Stored as JSON array; fall back to APP_URL host if never set
        $storedDomains = $saved['session_allowed_domains'] ?? null;
        $domains = [];
        if ($storedDomains) {
            $decoded = json_decode($storedDomains, true);
            $domains = is_array($decoded) ? $decoded : [];
        }
        if (empty($domains)) {
            $host = parse_url(config('app.url', ''), PHP_URL_HOST);
            if ($host) $domains = [$host];
        }

        $this->data = [
            'session_allowed_domains'      => $domains,
            'session_dynamic_domain'       => (bool) ($saved['session_dynamic_domain'] ?? true),
            'session_primary_domain'       => $saved['session_primary_domain'] ?? (parse_url(config('app.url', ''), PHP_URL_HOST) ?: ''),
            'session_lifetime'             => $saved['session_lifetime'] ?? (int) env('SESSION_LIFETIME', 120),
            'session_secure_cookie'        => (bool) ($saved['session_secure_cookie'] ?? false),
            'session_same_site'            => $saved['session_same_site'] ?? 'lax',
        ];

        $this->form->fill($this->data);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->schema([
                Section::make('Allowed Session Domains')
                    ->description('Add every domain (and subdomain) that should be allowed to share the session cookie. The middleware automatically selects the correct domain per request.')
                    ->icon('heroicon-o-server')
                    ->schema([
                        TagsInput::make('session_allowed_domains')
                            ->label('Allowed Domains')
                            ->placeholder('e.g. heartsconnect.site')
                            ->helperText('Press Enter or Tab after each domain. Do NOT include https:// — just the bare hostname, e.g. heartsconnect.site or www.heartsconnect.site')
                            ->splitKeys(['Tab', 'Enter', ','])
                            ->columnSpanFull(),

                        TextInput::make('session_primary_domain')
                            ->label('Primary Domain (fallback)')
                            ->placeholder('heartsconnect.site')
                            ->helperText('Used when the request host does not match any allowed domain (e.g. direct IP access). Leave blank to use null.')
                            ->columnSpanFull(),

                        Toggle::make('session_dynamic_domain')
                            ->label('Enable dynamic domain matching')
                            ->helperText('When ON, the middleware sets the session domain to the current request host if it appears in the allowed list above. Turn OFF to always use the primary domain.')
                            ->columnSpanFull(),
                    ])->columns(1),

                Section::make('Cookie & Session Behaviour')
                    ->icon('heroicon-o-shield-check')
                    ->schema([
                        TextInput::make('session_lifetime')
                            ->label('Session Lifetime (minutes)')
                            ->numeric()
                            ->minValue(5)
                            ->maxValue(43200)
                            ->helperText('How long (in minutes) the session cookie stays alive. Default: 120.'),

                        \Filament\Forms\Components\Select::make('session_same_site')
                            ->label('SameSite Cookie Policy')
                            ->options([
                                'lax'    => 'Lax (recommended — protects against CSRF, allows top-level navigation)',
                                'strict' => 'Strict (maximum security — may break OAuth redirects)',
                                'none'   => 'None (required for cross-site iframes — must use Secure)',
                            ])
                            ->helperText('Controls when the session cookie is sent with cross-site requests.'),

                        Toggle::make('session_secure_cookie')
                            ->label('Secure cookies (HTTPS only)')
                            ->helperText('Only enable on HTTPS. If enabled on HTTP, the session cookie will never be sent and you will get 419 errors.'),
                    ])->columns(2),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label('Save Settings')
                ->icon('heroicon-o-check')
                ->color('primary')
                ->action('save'),
        ];
    }

    public function save(): void
    {
        $data = $this->form->getState();

        SiteSetting::set('session_allowed_domains', json_encode($data['session_allowed_domains'] ?? []));
        SiteSetting::set('session_dynamic_domain',  $data['session_dynamic_domain'] ? '1' : '0');
        SiteSetting::set('session_primary_domain',  $data['session_primary_domain'] ?? '');
        SiteSetting::set('session_lifetime',        (string) ($data['session_lifetime'] ?? 120));
        SiteSetting::set('session_secure_cookie',   $data['session_secure_cookie'] ? '1' : '0');
        SiteSetting::set('session_same_site',       $data['session_same_site'] ?? 'lax');

        // Clear config cache so the new values propagate
        Artisan::call('config:clear');

        Notification::make()
            ->title('Domain & session settings saved')
            ->success()
            ->send();
    }
}
