<?php

namespace App\Filament\Pages;

use Filament\Actions\Action;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
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
            
            // Broadcasting / Pusher / Reverb
            'broadcast_connection'   => env('BROADCAST_CONNECTION', 'log'),
            'pusher_app_id'          => env('PUSHER_APP_ID', ''),
            'pusher_app_key'         => env('PUSHER_APP_KEY', ''),
            'pusher_app_secret'      => env('PUSHER_APP_SECRET', ''),
            'pusher_app_cluster'     => env('PUSHER_APP_CLUSTER', 'mt1'),
            'reverb_app_id'          => env('REVERB_APP_ID', ''),
            'reverb_app_key'         => env('REVERB_APP_KEY', ''),
            'reverb_app_secret'      => env('REVERB_APP_SECRET', ''),
            'reverb_host'            => env('REVERB_HOST', 'localhost'),
            'reverb_port'            => env('REVERB_PORT', '8080'),

            // Queue
            'queue_connection' => env('QUEUE_CONNECTION', 'database'),

            // Session
            'session_driver'   => env('SESSION_DRIVER', 'file'),
            'session_lifetime' => env('SESSION_LIFETIME', 120),

            // Stripe
            'stripe_key'        => env('STRIPE_KEY', ''),
            'stripe_secret'     => env('STRIPE_SECRET', ''),
            'stripe_webhook'    => env('STRIPE_WEBHOOK_SECRET', ''),

            // PayPal
            'paypal_client_id'     => env('PAYPAL_CLIENT_ID', ''),
            'paypal_client_secret' => env('PAYPAL_CLIENT_SECRET', ''),
            'paypal_mode'          => env('PAYPAL_MODE', 'sandbox'),

            // Google OAuth
            'google_client_id'     => env('GOOGLE_CLIENT_ID', ''),
            'google_client_secret' => env('GOOGLE_CLIENT_SECRET', ''),
            'google_redirect'      => env('GOOGLE_REDIRECT_URI', ''),

            // Facebook OAuth
            'facebook_client_id'     => env('FACEBOOK_CLIENT_ID', ''),
            'facebook_client_secret' => env('FACEBOOK_CLIENT_SECRET', ''),

            // Cloudinary (media storage)
            'cloudinary_url'            => env('CLOUDINARY_URL', ''),
            'cloudinary_cloud_name'     => env('CLOUDINARY_CLOUD_NAME', ''),
            'cloudinary_api_key'        => env('CLOUDINARY_API_KEY', ''),
            'cloudinary_api_secret'     => env('CLOUDINARY_API_SECRET', ''),

            // AWS S3 (file storage)
            'aws_access_key_id'     => env('AWS_ACCESS_KEY_ID', ''),
            'aws_secret_access_key' => env('AWS_SECRET_ACCESS_KEY', ''),
            'aws_default_region'    => env('AWS_DEFAULT_REGION', 'us-east-1'),
            'aws_bucket'            => env('AWS_BUCKET', ''),
            'aws_url'               => env('AWS_URL', ''),

            // Twilio (SMS / calls)
            'twilio_sid'          => env('TWILIO_SID', ''),
            'twilio_auth_token'   => env('TWILIO_AUTH_TOKEN', ''),
            'twilio_phone_number' => env('TWILIO_PHONE_NUMBER', ''),

            // Daily.co (video/voice calls)
            'daily_api_key'  => env('DAILY_API_KEY', ''),
            'daily_domain'   => env('DAILY_DOMAIN', ''),

            // Agora (alternative video)
            'agora_app_id'          => env('AGORA_APP_ID', ''),
            'agora_app_certificate' => env('AGORA_APP_CERTIFICATE', ''),

            // Firebase (push notifications)
            'firebase_api_key'      => env('FIREBASE_API_KEY', ''),
            'firebase_project_id'   => env('FIREBASE_PROJECT_ID', ''),
            'firebase_server_key'   => env('FIREBASE_SERVER_KEY', ''),
            'firebase_credentials'  => env('FIREBASE_CREDENTIALS', ''),

            // Google Maps / Places
            'google_maps_key' => env('GOOGLE_MAPS_API_KEY', ''),

            // OpenAI (alternative to Groq)
            'openai_api_key' => env('OPENAI_API_KEY', ''),
            'openai_org_id'  => env('OPENAI_ORGANIZATION', ''),

            // reCAPTCHA
            'recaptcha_site_key'   => env('RECAPTCHA_SITE_KEY', ''),
            'recaptcha_secret_key' => env('RECAPTCHA_SECRET_KEY', ''),
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
                            ->helperText('âš ï¸ Never enable in production!'),
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
                            ->visible(fn ($get) => $get('vpn_detection_enabled')),
                        
                        Forms\Components\TextInput::make('vpn_confidence_threshold')
                            ->label('Confidence Threshold (%)')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->visible(fn ($get) => $get('vpn_detection_enabled')),
                        
                        Forms\Components\TextInput::make('vpn_cache_duration')
                            ->label('Cache Duration (minutes)')
                            ->numeric()
                            ->visible(fn ($get) => $get('vpn_detection_enabled')),
                        
                        Forms\Components\TextInput::make('iphub_api_key')
                            ->label('IPHub API Key')
                            ->password()
                            ->revealable()
                            ->visible(fn ($get) => $get('vpn_detection_enabled')),
                        
                        Forms\Components\TextInput::make('proxycheck_api_key')
                            ->label('ProxyCheck API Key')
                            ->password()
                            ->revealable()
                            ->visible(fn ($get) => $get('vpn_detection_enabled')),
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
                            ->visible(fn ($get) => $get('telegram_enabled')),
                        
                        Forms\Components\TextInput::make('telegram_chat_id')
                            ->label('Chat ID')
                            ->helperText('Your chat ID or channel ID')
                            ->visible(fn ($get) => $get('telegram_enabled')),
                    ])->columns(2),
                
                Section::make('💳 Stripe Payments')
                    ->icon('heroicon-o-credit-card')
                    ->description('Stripe API keys — get them at dashboard.stripe.com → Developers → API Keys')
                    ->collapsible()->collapsed()
                    ->schema([
                        Forms\Components\TextInput::make('stripe_key')
                            ->label('Publishable Key (pk_...)')
                            ->placeholder('pk_live_...')
                            ->helperText('Safe to expose to clients'),
                        Forms\Components\TextInput::make('stripe_secret')
                            ->label('Secret Key (sk_...)')
                            ->password()->revealable()
                            ->placeholder('sk_live_...'),
                        Forms\Components\TextInput::make('stripe_webhook')
                            ->label('Webhook Secret (whsec_...)')
                            ->password()->revealable()
                            ->placeholder('whsec_...')
                            ->helperText('dashboard.stripe.com → Webhooks → signing secret'),
                    ])->columns(2),

                Section::make('💰 PayPal')
                    ->icon('heroicon-o-banknotes')
                    ->description('developer.paypal.com → Apps & Credentials')
                    ->collapsible()->collapsed()
                    ->schema([
                        Forms\Components\Select::make('paypal_mode')
                            ->label('Mode')
                            ->options(['sandbox' => 'Sandbox (testing)', 'live' => 'Live']),
                        Forms\Components\TextInput::make('paypal_client_id')
                            ->label('Client ID'),
                        Forms\Components\TextInput::make('paypal_client_secret')
                            ->label('Client Secret')
                            ->password()->revealable(),
                    ])->columns(2),

                Section::make('🔐 Google OAuth & Maps')
                    ->icon('heroicon-o-identification')
                    ->description('console.cloud.google.com → APIs & Services → Credentials')
                    ->collapsible()->collapsed()
                    ->schema([
                        Forms\Components\TextInput::make('google_client_id')
                            ->label('OAuth Client ID')
                            ->placeholder('XXXXXXXXX.apps.googleusercontent.com'),
                        Forms\Components\TextInput::make('google_client_secret')
                            ->label('OAuth Client Secret')
                            ->password()->revealable(),
                        Forms\Components\TextInput::make('google_redirect')
                            ->label('OAuth Redirect URI')
                            ->placeholder('https://heartsconnect.cc/auth/google/callback'),
                        Forms\Components\TextInput::make('google_maps_key')
                            ->label('Google Maps / Places API Key')
                            ->password()->revealable()
                            ->placeholder('AIza...'),
                    ])->columns(2),

                Section::make('📘 Facebook OAuth')
                    ->icon('heroicon-o-identification')
                    ->description('developers.facebook.com → Your App → Settings → Basic')
                    ->collapsible()->collapsed()
                    ->schema([
                        Forms\Components\TextInput::make('facebook_client_id')
                            ->label('App ID'),
                        Forms\Components\TextInput::make('facebook_client_secret')
                            ->label('App Secret')
                            ->password()->revealable(),
                    ])->columns(2),

                Section::make('🖼️ Cloudinary (Media Storage)')
                    ->icon('heroicon-o-photo')
                    ->description('cloudinary.com → Dashboard → API Keys')
                    ->collapsible()->collapsed()
                    ->schema([
                        Forms\Components\TextInput::make('cloudinary_cloud_name')
                            ->label('Cloud Name'),
                        Forms\Components\TextInput::make('cloudinary_api_key')
                            ->label('API Key'),
                        Forms\Components\TextInput::make('cloudinary_api_secret')
                            ->label('API Secret')
                            ->password()->revealable(),
                        Forms\Components\TextInput::make('cloudinary_url')
                            ->label('Cloudinary URL (optional)')
                            ->placeholder('cloudinary://API_KEY:API_SECRET@CLOUD_NAME')
                            ->columnSpanFull(),
                    ])->columns(2),

                Section::make('☁️ AWS S3 (File Storage)')
                    ->icon('heroicon-o-server')
                    ->description('aws.amazon.com → IAM → Users → Security Credentials')
                    ->collapsible()->collapsed()
                    ->schema([
                        Forms\Components\TextInput::make('aws_access_key_id')
                            ->label('Access Key ID'),
                        Forms\Components\TextInput::make('aws_secret_access_key')
                            ->label('Secret Access Key')
                            ->password()->revealable(),
                        Forms\Components\TextInput::make('aws_default_region')
                            ->label('Region')
                            ->placeholder('us-east-1'),
                        Forms\Components\TextInput::make('aws_bucket')
                            ->label('Bucket Name'),
                        Forms\Components\TextInput::make('aws_url')
                            ->label('Custom CDN URL (optional)')
                            ->placeholder('https://cdn.heartsconnect.cc'),
                    ])->columns(2),

                Section::make('📱 Twilio (SMS / Calls)')
                    ->icon('heroicon-o-phone-arrow-up-right')
                    ->description('console.twilio.com → Account Info')
                    ->collapsible()->collapsed()
                    ->schema([
                        Forms\Components\TextInput::make('twilio_sid')
                            ->label('Account SID')
                            ->placeholder('AC...'),
                        Forms\Components\TextInput::make('twilio_auth_token')
                            ->label('Auth Token')
                            ->password()->revealable(),
                        Forms\Components\TextInput::make('twilio_phone_number')
                            ->label('From Phone Number')
                            ->placeholder('+1234567890'),
                    ])->columns(2),

                Section::make('🎥 Daily.co (Video Calls)')
                    ->icon('heroicon-o-video-camera')
                    ->description('dashboard.daily.co → Developers → API keys')
                    ->collapsible()->collapsed()
                    ->schema([
                        Forms\Components\TextInput::make('daily_api_key')
                            ->label('API Key')
                            ->password()->revealable(),
                        Forms\Components\TextInput::make('daily_domain')
                            ->label('Daily Domain')
                            ->placeholder('yourapp.daily.co'),
                    ])->columns(2),

                Section::make('📡 Agora (Alternative Video)')
                    ->icon('heroicon-o-signal')
                    ->description('console.agora.io → Project Management')
                    ->collapsible()->collapsed()
                    ->schema([
                        Forms\Components\TextInput::make('agora_app_id')
                            ->label('App ID'),
                        Forms\Components\TextInput::make('agora_app_certificate')
                            ->label('App Certificate')
                            ->password()->revealable(),
                    ])->columns(2),

                Section::make('🔔 Firebase (Push Notifications)')
                    ->icon('heroicon-o-bell-alert')
                    ->description('console.firebase.google.com → Project Settings → Cloud Messaging')
                    ->collapsible()->collapsed()
                    ->schema([
                        Forms\Components\TextInput::make('firebase_api_key')
                            ->label('API Key (Cloud Messaging)')
                            ->password()->revealable()
                            ->helperText('From Firebase Console → Project Settings → General → Web API Key')
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('firebase_project_id')
                            ->label('Project ID'),
                        Forms\Components\TextInput::make('firebase_server_key')
                            ->label('Server Key (FCM Legacy - Optional)')
                            ->password()->revealable(),
                        Forms\Components\TextInput::make('firebase_credentials')
                            ->label('Service Account JSON path (or base64)')
                            ->helperText('Path to service account JSON file, e.g. storage/app/firebase.json')
                            ->columnSpanFull(),
                    ])->columns(2),

                Section::make('🤖 OpenAI (Alternative to Groq)')
                    ->icon('heroicon-o-cpu-chip')
                    ->description('platform.openai.com → API Keys')
                    ->collapsible()->collapsed()
                    ->schema([
                        Forms\Components\TextInput::make('openai_api_key')
                            ->label('API Key')
                            ->password()->revealable()
                            ->placeholder('sk-...'),
                        Forms\Components\TextInput::make('openai_org_id')
                            ->label('Organization ID (optional)')
                            ->placeholder('org-...'),
                    ])->columns(2),

                Section::make('🤖 reCAPTCHA')
                    ->icon('heroicon-o-shield-check')
                    ->description('google.com/recaptcha → Admin Console')
                    ->collapsible()->collapsed()
                    ->schema([
                        Forms\Components\TextInput::make('recaptcha_site_key')
                            ->label('Site Key'),
                        Forms\Components\TextInput::make('recaptcha_secret_key')
                            ->label('Secret Key')
                            ->password()->revealable(),
                    ])->columns(2),

                Section::make('📡 Pusher / Reverb (Real-time)')
                    ->icon('heroicon-o-bolt')
                    ->description('pusher.com → App Keys   OR   self-hosted Laravel Reverb')
                    ->collapsible()->collapsed()
                    ->schema([
                        Forms\Components\Select::make('broadcast_connection')
                            ->label('Broadcasting Driver')
                            ->options([
                                'log'    => 'Log (disabled)',
                                'reverb' => 'Reverb (self-hosted)',
                                'pusher' => 'Pusher',
                                'redis'  => 'Redis',
                            ]),
                        Forms\Components\TextInput::make('pusher_app_id')
                            ->label('Pusher App ID'),
                        Forms\Components\TextInput::make('pusher_app_key')
                            ->label('Pusher App Key'),
                        Forms\Components\TextInput::make('pusher_app_secret')
                            ->label('Pusher App Secret')
                            ->password()->revealable(),
                        Forms\Components\TextInput::make('pusher_app_cluster')
                            ->label('Pusher Cluster')
                            ->placeholder('mt1'),
                        Forms\Components\TextInput::make('reverb_app_id')
                            ->label('Reverb App ID'),
                        Forms\Components\TextInput::make('reverb_app_key')
                            ->label('Reverb App Key'),
                        Forms\Components\TextInput::make('reverb_app_secret')
                            ->label('Reverb App Secret')
                            ->password()->revealable(),
                        Forms\Components\TextInput::make('reverb_host')
                            ->label('Reverb Host')
                            ->placeholder('localhost'),
                        Forms\Components\TextInput::make('reverb_port')
                            ->label('Reverb Port')
                            ->numeric()
                            ->placeholder('8080'),
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
                
                Section::make('âš ï¸ Important Notes')
                    ->schema([
                        \Filament\Forms\Components\Textarea::make('_notes')
                            ->label(false)
                            ->default("Security Warning: Never share your .env file publicly. Passwords are not displayed for security. After saving, run: php artisan config:clear\n\nTelegram Setup: Create a bot via @BotFather → get Bot Token → send message → get Chat ID from api.telegram.org/bot<TOKEN>/getUpdates\n\nVPN APIs: IPHub (iphub.info) and ProxyCheck (proxycheck.io) — both offer 1,000 free requests/day.")
                            ->disabled()
                            ->rows(7)
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
                'QUEUE_CONNECTION'      => $data['queue_connection'],
                'SESSION_DRIVER'        => $data['session_driver'],
                'SESSION_LIFETIME'      => $data['session_lifetime'],

                // Stripe
                'STRIPE_KEY'            => $data['stripe_key'] ?? '',
                'STRIPE_SECRET'         => $data['stripe_secret'] ?? '',
                'STRIPE_WEBHOOK_SECRET' => $data['stripe_webhook'] ?? '',

                // PayPal
                'PAYPAL_CLIENT_ID'     => $data['paypal_client_id'] ?? '',
                'PAYPAL_CLIENT_SECRET' => $data['paypal_client_secret'] ?? '',
                'PAYPAL_MODE'          => $data['paypal_mode'] ?? 'sandbox',

                // Google
                'GOOGLE_CLIENT_ID'     => $data['google_client_id'] ?? '',
                'GOOGLE_CLIENT_SECRET' => $data['google_client_secret'] ?? '',
                'GOOGLE_REDIRECT_URI'  => $data['google_redirect'] ?? '',
                'GOOGLE_MAPS_API_KEY'  => $data['google_maps_key'] ?? '',

                // Facebook
                'FACEBOOK_CLIENT_ID'     => $data['facebook_client_id'] ?? '',
                'FACEBOOK_CLIENT_SECRET' => $data['facebook_client_secret'] ?? '',

                // Cloudinary
                'CLOUDINARY_URL'        => $data['cloudinary_url'] ?? '',
                'CLOUDINARY_CLOUD_NAME' => $data['cloudinary_cloud_name'] ?? '',
                'CLOUDINARY_API_KEY'    => $data['cloudinary_api_key'] ?? '',
                'CLOUDINARY_API_SECRET' => $data['cloudinary_api_secret'] ?? '',

                // AWS S3
                'AWS_ACCESS_KEY_ID'     => $data['aws_access_key_id'] ?? '',
                'AWS_SECRET_ACCESS_KEY' => $data['aws_secret_access_key'] ?? '',
                'AWS_DEFAULT_REGION'    => $data['aws_default_region'] ?? 'us-east-1',
                'AWS_BUCKET'            => $data['aws_bucket'] ?? '',
                'AWS_URL'               => $data['aws_url'] ?? '',

                // Twilio
                'TWILIO_SID'          => $data['twilio_sid'] ?? '',
                'TWILIO_AUTH_TOKEN'   => $data['twilio_auth_token'] ?? '',
                'TWILIO_PHONE_NUMBER' => $data['twilio_phone_number'] ?? '',

                // Daily.co
                'DAILY_API_KEY' => $data['daily_api_key'] ?? '',
                'DAILY_DOMAIN'  => $data['daily_domain'] ?? '',

                // Agora
                'AGORA_APP_ID'          => $data['agora_app_id'] ?? '',
                'AGORA_APP_CERTIFICATE' => $data['agora_app_certificate'] ?? '',

                // Firebase
                'FIREBASE_API_KEY'     => $data['firebase_api_key'] ?? '',
                'FIREBASE_PROJECT_ID'  => $data['firebase_project_id'] ?? '',
                'FIREBASE_SERVER_KEY'  => $data['firebase_server_key'] ?? '',
                'FIREBASE_CREDENTIALS' => $data['firebase_credentials'] ?? '',

                // OpenAI
                'OPENAI_API_KEY'      => $data['openai_api_key'] ?? '',
                'OPENAI_ORGANIZATION' => $data['openai_org_id'] ?? '',

                // reCAPTCHA
                'RECAPTCHA_SITE_KEY'   => $data['recaptcha_site_key'] ?? '',
                'RECAPTCHA_SECRET_KEY' => $data['recaptcha_secret_key'] ?? '',

                // Pusher / Reverb
                'PUSHER_APP_ID'      => $data['pusher_app_id'] ?? '',
                'PUSHER_APP_KEY'     => $data['pusher_app_key'] ?? '',
                'PUSHER_APP_SECRET'  => $data['pusher_app_secret'] ?? '',
                'PUSHER_APP_CLUSTER' => $data['pusher_app_cluster'] ?? 'mt1',
                'REVERB_APP_ID'      => $data['reverb_app_id'] ?? '',
                'REVERB_APP_KEY'     => $data['reverb_app_key'] ?? '',
                'REVERB_APP_SECRET'  => $data['reverb_app_secret'] ?? '',
                'REVERB_HOST'        => $data['reverb_host'] ?? 'localhost',
                'REVERB_PORT'        => $data['reverb_port'] ?? '8080',
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
