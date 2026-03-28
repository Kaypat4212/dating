<?php

namespace App\Filament\Pages;

use App\Models\SiteSetting;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Artisan;

class VpnSettings extends Page
{
    protected string $view = 'filament.pages.vpn-settings';

    public static function getNavigationIcon(): ?string 
    { 
        return 'heroicon-o-shield-check'; 
    }

    public static function getNavigationLabel(): string 
    { 
        return 'VPN Settings'; 
    }

    public static function getNavigationGroup(): ?string 
    { 
        return 'Security'; 
    }

    public static function getNavigationSort(): ?int 
    { 
        return 2; 
    }

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'vpn_detection_enabled' => SiteSetting::get('vpn_detection_enabled', true),
            'vpn_detection_check_all' => SiteSetting::get('vpn_detection_check_all', false),
            'vpn_confidence_threshold' => SiteSetting::get('vpn_confidence_threshold', 40),
            'vpn_enable_iphub' => SiteSetting::get('vpn_enable_iphub', true),
            'vpn_enable_proxycheck' => SiteSetting::get('vpn_enable_proxycheck', true),
            'vpn_enable_dns_check' => SiteSetting::get('vpn_enable_dns_check', true),
            'vpn_enable_ip_range_check' => SiteSetting::get('vpn_enable_ip_range_check', true),
            'vpn_cache_duration' => SiteSetting::get('vpn_cache_duration', 1440),
            'vpn_iphub_api_key' => config('services.iphub.api_key', ''),
            'vpn_proxycheck_api_key' => config('services.proxycheck.api_key', ''),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('VPN Detection Settings')
                    ->description('Configure how VPN detection works on your platform')
                    ->schema([
                        Forms\Components\Toggle::make('vpn_detection_enabled')
                            ->label('Enable VPN Detection')
                            ->helperText('Turn on/off VPN detection globally')
                            ->default(true)
                            ->live(),
                        
                        Forms\Components\Toggle::make('vpn_detection_check_all')
                            ->label('Check All Users')
                            ->helperText('When enabled, checks all users (not just new registrations). When disabled, only checks unauthenticated users.')
                            ->default(false)
                            ->visible(fn (Get $get) => $get('vpn_detection_enabled')),
                        
                        Forms\Components\TextInput::make('vpn_confidence_threshold')
                            ->label('Confidence Threshold (%)')
                            ->helperText('Block users when VPN confidence is above this percentage')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->default(40)
                            ->suffix('%')
                            ->visible(fn (Get $get) => $get('vpn_detection_enabled')),
                        
                        Forms\Components\TextInput::make('vpn_cache_duration')
                            ->label('Cache Duration (minutes)')
                            ->helperText('How long to cache VPN detection results for each IP')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(10080)
                            ->default(1440)
                            ->suffix('min')
                            ->visible(fn (Get $get) => $get('vpn_detection_enabled')),
                    ])
                    ->columns(2),
                
                Section::make('Detection Methods')
                    ->description('Enable or disable specific VPN detection methods')
                    ->schema([
                        Forms\Components\Toggle::make('vpn_enable_ip_range_check')
                            ->label('Known VPN IP Ranges')
                            ->helperText('Check against known VPN provider IP ranges (NordVPN, ExpressVPN, etc.)')
                            ->default(true),
                        
                        Forms\Components\Toggle::make('vpn_enable_dns_check')
                            ->label('DNS Pattern Matching')
                            ->helperText('Analyze reverse DNS records for VPN-related patterns')
                            ->default(true),
                        
                        Forms\Components\Toggle::make('vpn_enable_iphub')
                            ->label('IPHub API')
                            ->helperText('Use IPHub.info API for VPN detection (requires API key)')
                            ->default(true)
                            ->live(),
                        
                        Forms\Components\Toggle::make('vpn_enable_proxycheck')
                            ->label('ProxyCheck API')
                            ->helperText('Use ProxyCheck.io API for VPN detection (requires API key)')
                            ->default(true)
                            ->live(),
                    ])
                    ->columns(2)
                    ->visible(fn (Get $get) => $get('vpn_detection_enabled')),
                
                Section::make('API Configuration')
                    ->description('Configure third-party API keys for VPN detection services')
                    ->schema([
                        Forms\Components\TextInput::make('vpn_iphub_api_key')
                            ->label('IPHub API Key')
                            ->helperText('Get your free API key from https://iphub.info')
                            ->password()
                            ->revealable()
                            ->placeholder('Enter IPHub API key')
                            ->visible(fn (Get $get) => $get('vpn_enable_iphub')),
                        
                        Forms\Components\TextInput::make('vpn_proxycheck_api_key')
                            ->label('ProxyCheck API Key')
                            ->helperText('Get your free API key from https://proxycheck.io')
                            ->password()
                            ->revealable()
                            ->placeholder('Enter ProxyCheck API key')
                            ->visible(fn (Get $get) => $get('vpn_enable_proxycheck')),
                    ])
                    ->columns(1)
                    ->visible(fn (Get $get) => 
                        $get('vpn_detection_enabled') && 
                        ($get('vpn_enable_iphub') || $get('vpn_enable_proxycheck'))
                    ),
                
                Section::make('Information')
                    ->description('How VPN detection works')
                    ->schema([
                        Forms\Components\Placeholder::make('info')
                            ->content('
                                **Detection Process:**
                                
                                1. **IP Range Check**: Compares against known VPN provider IP ranges
                                2. **DNS Analysis**: Checks reverse DNS for VPN-related patterns
                                3. **IPHub API**: Queries IPHub.info database (requires API key)
                                4. **ProxyCheck API**: Queries ProxyCheck.io database (requires API key)
                                5. **IP Quality Heuristics**: Analyzes IP characteristics and patterns
                                
                                **How It Works:**
                                - Multiple detection methods are combined for accuracy
                                - Each method contributes to overall confidence score
                                - Users are blocked if confidence exceeds your threshold
                                - Detection results are cached to improve performance
                                - All detections are logged for review
                                
                                **Recommended Settings:**
                                - Threshold: 40-60% for strict blocking, 70-80% for moderate
                                - Cache: 1440 minutes (24 hours) for optimal performance
                                - Enable all methods for best accuracy
                            ')
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        // Save settings to database
        SiteSetting::set('vpn_detection_enabled', $data['vpn_detection_enabled']);
        SiteSetting::set('vpn_detection_check_all', $data['vpn_detection_check_all']);
        SiteSetting::set('vpn_confidence_threshold', $data['vpn_confidence_threshold']);
        SiteSetting::set('vpn_enable_iphub', $data['vpn_enable_iphub']);
        SiteSetting::set('vpn_enable_proxycheck', $data['vpn_enable_proxycheck']);
        SiteSetting::set('vpn_enable_dns_check', $data['vpn_enable_dns_check']);
        SiteSetting::set('vpn_enable_ip_range_check', $data['vpn_enable_ip_range_check']);
        SiteSetting::set('vpn_cache_duration', $data['vpn_cache_duration']);

        // Update config/services.php settings if API keys provided
        if (!empty($data['vpn_iphub_api_key'])) {
            $this->updateEnvFile('IPHUB_API_KEY', $data['vpn_iphub_api_key']);
        }
        
        if (!empty($data['vpn_proxycheck_api_key'])) {
            $this->updateEnvFile('PROXYCHECK_API_KEY', $data['vpn_proxycheck_api_key']);
        }

        // Clear config cache to apply changes
        Artisan::call('config:clear');

        Notification::make()
            ->success()
            ->title('VPN Settings Saved')
            ->body('Your VPN detection settings have been updated successfully.')
            ->send();
    }

    protected function updateEnvFile(string $key, string $value): void
    {
        $envFile = base_path('.env');
        
        if (!file_exists($envFile)) {
            return;
        }

        $content = file_get_contents($envFile);
        
        // Check if key exists
        if (preg_match("/^{$key}=/m", $content)) {
            // Update existing key
            $content = preg_replace(
                "/^{$key}=.*/m",
                "{$key}={$value}",
                $content
            );
        } else {
            // Add new key
            $content .= "\n{$key}={$value}\n";
        }
        
        file_put_contents($envFile, $content);
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Save Settings')
                ->submit('save')
                ->color('primary'),
        ];
    }
}
