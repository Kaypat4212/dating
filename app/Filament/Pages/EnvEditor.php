<?php

namespace App\Filament\Pages;

use Filament\Actions\Action;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class EnvEditor extends Page
{
    protected string $view = 'filament.pages.env-editor';

    public static function getNavigationIcon(): ?string 
    { 
        return 'heroicon-o-cog-6-tooth'; 
    }

    public static function getNavigationLabel(): string 
    { 
        return 'Environment Settings'; 
    }

    public static function getNavigationGroup(): ?string 
    { 
        return 'System'; 
    }

    public static function getNavigationSort(): ?int 
    { 
        return 95; 
    }

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill($this->getEnvVariables());
    }

    protected function getEnvVariables(): array
    {
        return [
            // App Settings
            'app_name' => env('APP_NAME', 'Laravel'),
            'app_url' => env('APP_URL', ''),
            'app_trusted_urls' => env('APP_TRUSTED_URLS', ''),
            'app_debug' => env('APP_DEBUG', false),
            
            // Database
            'db_host' => env('DB_HOST', 'localhost'),
            'db_port' => env('DB_PORT', '3306'),
            'db_database' => env('DB_DATABASE', ''),
            'db_username' => env('DB_USERNAME', ''),
            
            // Mail
            'mail_mailer' => env('MAIL_MAILER', 'smtp'),
            'mail_host' => env('MAIL_HOST', ''),
            'mail_port' => env('MAIL_PORT', '587'),
            'mail_username' => env('MAIL_USERNAME', ''),
            'mail_from_address' => env('MAIL_FROM_ADDRESS', ''),
            'mail_from_name' => env('MAIL_FROM_NAME', ''),
            
            // VPN Detection
            'vpn_detection_enabled' => env('VPN_DETECTION_ENABLED', true),
            'vpn_detection_check_all' => env('VPN_DETECTION_CHECK_ALL', false),
            'vpn_confidence_threshold' => env('VPN_CONFIDENCE_THRESHOLD', 40),
            'vpn_cache_duration' => env('VPN_CACHE_DURATION', 1440),
            'iphub_api_key' => env('IPHUB_API_KEY', ''),
            'proxycheck_api_key' => env('PROXYCHECK_API_KEY', ''),
            
            // Telegram
            'telegram_enabled' => env('TELEGRAM_NOTIFICATIONS_ENABLED', false),
            'telegram_bot_token' => env('TELEGRAM_BOT_TOKEN', ''),
            'telegram_chat_id' => env('TELEGRAM_CHAT_ID', ''),
            
            // Broadcasting
            'broadcast_connection' => env('BROADCAST_CONNECTION', 'log'),
            
            // Queue
            'queue_connection' => env('QUEUE_CONNECTION', 'database'),
            
            // Session
            'session_driver' => env('SESSION_DRIVER', 'file'),
            'session_lifetime' => env('SESSION_LIFETIME', 120),
        ];
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Application Settings')
                    ->schema([
                        Forms\Components\TextInput::make('app_name')
                            ->label('Application Name')
                            ->required(),
                        
                        Forms\Components\TextInput::make('app_url')
                            ->label('Application URL')
                            ->url()
                            ->required(),

                        Forms\Components\Textarea::make('app_trusted_urls')
                            ->label('Additional Trusted URLs')
                            ->rows(2)
                            ->columnSpanFull()
                            ->helperText('Comma-separated list of additional trusted APP URLs (e.g. staging or CDN URLs). Used for CORS and host validation.')
                            ->placeholder('https://staging.example.com, https://www.example.com'),
                        
                        Forms\Components\Toggle::make('app_debug')
                            ->label('Debug Mode')
                            ->helperText('⚠️ Never enable in production!'),
                    ])->columns(2),
                
                Section::make('Database Configuration')
                    ->schema([
                        Forms\Components\TextInput::make('db_host')
                            ->label('Database Host')
                            ->required(),
                        
                        Forms\Components\TextInput::make('db_port')
                            ->label('Database Port')
                            ->numeric()
                            ->required(),
                        
                        Forms\Components\TextInput::make('db_database')
                            ->label('Database Name')
                            ->required(),
                        
                        Forms\Components\TextInput::make('db_username')
                            ->label('Database Username')
                            ->required(),
                    ])->columns(2),
                
                Section::make('Mail Configuration')
                    ->schema([
                        Forms\Components\Select::make('mail_mailer')
                            ->label('Mail Driver')
                            ->options([
                                'smtp'     => 'SMTP',
                                'sendmail' => 'Sendmail',
                                'cpanel'   => 'cPanel PHP Mail (/usr/sbin/sendmail -t -i)',
                                'mailgun'  => 'Mailgun',
                                'ses'      => 'Amazon SES',
                                'log'      => 'Log (Testing)',
                            ])
                            ->required(),
                        
                        Forms\Components\TextInput::make('mail_host')
                            ->label('Mail Host'),
                        
                        Forms\Components\TextInput::make('mail_port')
                            ->label('Mail Port')
                            ->numeric(),
                        
                        Forms\Components\TextInput::make('mail_username')
                            ->label('Mail Username'),
                        
                        Forms\Components\TextInput::make('mail_from_address')
                            ->label('From Email Address')
                            ->email(),
                        
                        Forms\Components\TextInput::make('mail_from_name')
                            ->label('From Name'),
                    ])->columns(2),
                
                Section::make('VPN Detection')
                    ->schema([
                        Forms\Components\Toggle::make('vpn_detection_enabled')
                            ->label('Enable VPN Detection')
                            ->live(),
                        
                        Forms\Components\Toggle::make('vpn_detection_check_all')
                            ->label('Check All Users')
                            ->visible(fn (Get $get) => $get('vpn_detection_enabled')),
                        
                        Forms\Components\TextInput::make('vpn_confidence_threshold')
                            ->label('Confidence Threshold (%)')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->visible(fn (Get $get) => $get('vpn_detection_enabled')),
                        
                        Forms\Components\TextInput::make('vpn_cache_duration')
                            ->label('Cache Duration (minutes)')
                            ->numeric()
                            ->visible(fn (Get $get) => $get('vpn_detection_enabled')),
                        
                        Forms\Components\TextInput::make('iphub_api_key')
                            ->label('IPHub API Key')
                            ->password()
                            ->revealable()
                            ->visible(fn (Get $get) => $get('vpn_detection_enabled')),
                        
                        Forms\Components\TextInput::make('proxycheck_api_key')
                            ->label('ProxyCheck API Key')
                            ->password()
                            ->revealable()
                            ->visible(fn (Get $get) => $get('vpn_detection_enabled')),
                    ])->columns(2),
                
                Section::make('Telegram Notifications')
                    ->schema([
                        Forms\Components\Toggle::make('telegram_enabled')
                            ->label('Enable Telegram Notifications')
                            ->live(),
                        
                        Forms\Components\TextInput::make('telegram_bot_token')
                            ->label('Bot Token')
                            ->helperText('Get from @BotFather on Telegram')
                            ->password()
                            ->revealable()
                            ->visible(fn (Get $get) => $get('telegram_enabled')),
                        
                        Forms\Components\TextInput::make('telegram_chat_id')
                            ->label('Chat ID')
                            ->helperText('Your chat ID or channel ID')
                            ->visible(fn (Get $get) => $get('telegram_enabled')),
                    ])->columns(2),
                
                Section::make('System Configuration')
                    ->schema([
                        Forms\Components\Select::make('broadcast_connection')
                            ->label('Broadcasting Driver')
                            ->options([
                                'log' => 'Log',
                                'reverb' => 'Reverb',
                                'pusher' => 'Pusher',
                                'redis' => 'Redis',
                            ]),
                        
                        Forms\Components\Select::make('queue_connection')
                            ->label('Queue Driver')
                            ->options([
                                'sync' => 'Sync (No Queue)',
                                'database' => 'Database',
                                'redis' => 'Redis',
                                'sqs' => 'Amazon SQS',
                            ]),
                        
                        Forms\Components\Select::make('session_driver')
                            ->label('Session Driver')
                            ->options([
                                'file' => 'File',
                                'cookie' => 'Cookie',
                                'database' => 'Database',
                                'redis' => 'Redis',
                            ]),
                        
                        Forms\Components\TextInput::make('session_lifetime')
                            ->label('Session Lifetime (minutes)')
                            ->numeric(),
                    ])->columns(2),
                
                Section::make('⚠️ Important Notes')
                    ->schema([
                        Forms\Components\Placeholder::make('warnings')
                            ->content('
                                **Security Warning:**
                                - Never share your .env file publicly
                                - Passwords are not displayed for security
                                - Database password cannot be changed here (edit .env manually)
                                - After saving, run `php artisan config:clear` to apply changes
                                
                                **Telegram Setup:**
                                1. Create a bot with @BotFather on Telegram
                                2. Get your Bot Token
                                3. Send a message to your bot
                                4. Get your Chat ID from https://api.telegram.org/bot<YOUR_TOKEN>/getUpdates
                                
                                **VPN Detection APIs:**
                                - IPHub: https://iphub.info (Free: 1,000 requests/day)
                                - ProxyCheck: https://proxycheck.io (Free: 1,000 requests/day)
                            ')
                            ->columnSpanFull(),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        
        $envFile = base_path('.env');
        
        if (!File::exists($envFile)) {
            Notification::make()
                ->danger()
                ->title('Error')
                ->body('.env file not found!')
                ->send();
            return;
        }

        try {
            $content = File::get($envFile);
            
            // Update each env variable
            $updates = [
                'APP_NAME' => $data['app_name'],
                'APP_URL' => $data['app_url'],
                'APP_TRUSTED_URLS' => $data['app_trusted_urls'] ?? '',
                'APP_DEBUG' => $data['app_debug'] ? 'true' : 'false',
                
                'DB_HOST' => $data['db_host'],
                'DB_PORT' => $data['db_port'],
                'DB_DATABASE' => $data['db_database'],
                'DB_USERNAME' => $data['db_username'],
                
                'MAIL_MAILER' => $data['mail_mailer'],
                'MAIL_HOST' => $data['mail_host'] ?? '',
                'MAIL_PORT' => $data['mail_port'] ?? '',
                'MAIL_USERNAME' => $data['mail_username'] ?? '',
                'MAIL_FROM_ADDRESS' => $data['mail_from_address'] ?? '',
                'MAIL_FROM_NAME' => $data['mail_from_name'] ?? '',
                
                'VPN_DETECTION_ENABLED' => $data['vpn_detection_enabled'] ? 'true' : 'false',
                'VPN_DETECTION_CHECK_ALL' => $data['vpn_detection_check_all'] ? 'true' : 'false',
                'VPN_CONFIDENCE_THRESHOLD' => $data['vpn_confidence_threshold'] ?? 40,
                'VPN_CACHE_DURATION' => $data['vpn_cache_duration'] ?? 1440,
                'IPHUB_API_KEY' => $data['iphub_api_key'] ?? '',
                'PROXYCHECK_API_KEY' => $data['proxycheck_api_key'] ?? '',
                
                'TELEGRAM_NOTIFICATIONS_ENABLED' => $data['telegram_enabled'] ? 'true' : 'false',
                'TELEGRAM_BOT_TOKEN' => $data['telegram_bot_token'] ?? '',
                'TELEGRAM_CHAT_ID' => $data['telegram_chat_id'] ?? '',
                
                'BROADCAST_CONNECTION' => $data['broadcast_connection'],
                'QUEUE_CONNECTION' => $data['queue_connection'],
                'SESSION_DRIVER' => $data['session_driver'],
                'SESSION_LIFETIME' => $data['session_lifetime'],
            ];
            
            foreach ($updates as $key => $value) {
                // Escape quotes in values
                $value = str_replace('"', '\\"', $value);
                
                // Check if key exists
                if (preg_match("/^{$key}=/m", $content)) {
                    // Update existing key
                    $content = preg_replace(
                        "/^{$key}=.*/m",
                        "{$key}=\"{$value}\"",
                        $content
                    );
                } else {
                    // Add new key at the end
                    $content .= "\n{$key}=\"{$value}\"";
                }
            }
            
            // Write back to file
            File::put($envFile, $content);
            
            // Clear config cache
            Artisan::call('config:clear');
            
            Notification::make()
                ->success()
                ->title('Environment Updated')
                ->body('Environment variables have been updated successfully. Config cache cleared.')
                ->send();
                
        } catch (\Exception $e) {
            Notification::make()
                ->danger()
                ->title('Error Updating Environment')
                ->body($e->getMessage())
                ->send();
        }
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Save Environment Settings')
                ->submit('save')
                ->color('primary'),
        ];
    }
}
