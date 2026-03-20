<?php

namespace App\Filament\Pages;

use App\Models\SiteSetting;
use App\Services\MailSettingsService;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;

class ManageEmailSettings extends Page
{
    protected string $view = 'filament.pages.manage-email-settings';

    public static function getNavigationIcon(): ?string  { return 'heroicon-o-envelope'; }
    public static function getNavigationLabel(): string  { return 'Email Settings'; }
    public static function getNavigationGroup(): ?string { return 'Site Settings'; }
    public static function getNavigationSort(): ?int     { return 3; }

    public function getTitle(): string | Htmlable { return 'Email & Mail Settings'; }

    /** @var array<string, mixed> */
    public array $data = [];

    public function mount(): void
    {
        $defaults = [
            // General
            'mail_driver'          => 'log',
            'mail_from_address'    => 'noreply@heartsconnect.com',
            'mail_from_name'       => config('app.name', 'HeartsConnect'),

            // SMTP
            'mail_smtp_host'       => '127.0.0.1',
            'mail_smtp_port'       => '587',
            'mail_smtp_username'   => null,
            'mail_smtp_password'   => null,
            'mail_smtp_encryption' => 'tls',

            // Mailhog
            'mail_mailhog_host'    => '127.0.0.1',
            'mail_mailhog_port'    => '1025',

            // Sendmail / cPanel
            'mail_sendmail_path'   => '/usr/sbin/sendmail -bs -i',

            // Mailgun
            'mail_mailgun_domain'   => null,
            'mail_mailgun_secret'   => null,
            'mail_mailgun_endpoint' => 'api.mailgun.net',

            // Amazon SES
            'mail_ses_key'    => null,
            'mail_ses_secret' => null,
            'mail_ses_region' => 'us-east-1',

            // Postmark
            'mail_postmark_token' => null,

            // Resend
            'mail_resend_key' => null,

            // Queue
            'queue_connection' => 'sync',

            // Notification toggles
            'email_login_alert_enabled'   => true,
            'email_feature_usage_enabled' => true,
            'email_daily_summary_enabled' => true,

            // Test email helper (not persisted)
            '_test_email_address' => null,
        ];

        $saved  = SiteSetting::allAsArray();
        $merged = array_merge($defaults, array_intersect_key($saved, $defaults));

        $this->form->fill($merged);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Tabs::make('Email Settings')
                    ->tabs([

                        // ── Tab 1: Mail Transport ───────────────────────────────────────
                        Tab::make('Mail Transport')
                            ->icon('heroicon-o-paper-airplane')
                            ->schema([

                                Section::make('Sending Method')->schema([
                                    Select::make('mail_driver')
                                        ->label('Mail Driver / Transport')
                                        ->options([
                                            'log'      => '🪵  Log (development — writes to laravel.log)',
                                            'smtp'     => '📨  SMTP (custom mail server)',
                                            'mailhog'  => '🐷  Mailhog (local dev mail catcher)',
                                            'sendmail' => '📬  PHP Mail / Sendmail (cPanel hosting)',
                                            'mailgun'  => '✈️  Mailgun (transactional email API)',
                                            'ses'      => '🌩️  Amazon SES (AWS email)',
                                            'postmark' => '📮  Postmark (transactional email API)',
                                            'resend'   => '🚀  Resend (modern email API)',
                                        ])
                                        ->default('log')
                                        ->required()
                                        ->live()
                                        ->helperText('Choose how the platform delivers all outgoing emails.')
                                        ->columnSpanFull(),

                                    TextInput::make('mail_from_address')
                                        ->label('From Email Address')
                                        ->email()
                                        ->required()
                                        ->placeholder('noreply@yourdomain.com'),

                                    TextInput::make('mail_from_name')
                                        ->label('From Name')
                                        ->required()
                                        ->placeholder('HeartsConnect'),
                                ])->columns(2),

                                // ── SMTP ────────────────────────────────────────────────────
                                Section::make('SMTP Configuration')
                                    ->icon('heroicon-o-server')
                                    ->hidden(fn ($get) => $get('mail_driver') !== 'smtp')
                                    ->schema([
                                        TextInput::make('mail_smtp_host')
                                            ->label('SMTP Host')
                                            ->placeholder('smtp.gmail.com')
                                            ->required(),
                                        TextInput::make('mail_smtp_port')
                                            ->label('SMTP Port')
                                            ->placeholder('587')
                                            ->numeric()
                                            ->required(),
                                        TextInput::make('mail_smtp_username')
                                            ->label('Username')
                                            ->placeholder('you@gmail.com'),
                                        TextInput::make('mail_smtp_password')
                                            ->label('Password')
                                            ->password()
                                            ->revealable()
                                            ->placeholder('••••••••'),
                                        Select::make('mail_smtp_encryption')
                                            ->label('Encryption')
                                            ->options([
                                                'tls'  => 'TLS (recommended — port 587)',
                                                'ssl'  => 'SSL (port 465)',
                                                ''     => 'None (not recommended)',
                                            ])
                                            ->default('tls')
                                            ->required(),
                                    ])->columns(2)
                                    ->description('Fill in your SMTP server credentials. Works with Gmail, Outlook, Zoho, and any standard SMTP relay.'),

                                // ── Mailhog ─────────────────────────────────────────────────
                                Section::make('Mailhog Configuration')
                                    ->icon('heroicon-o-beaker')
                                    ->hidden(fn ($get) => $get('mail_driver') !== 'mailhog')
                                    ->schema([
                                        TextInput::make('mail_mailhog_host')
                                            ->label('Mailhog Host')
                                            ->default('127.0.0.1')
                                            ->required(),
                                        TextInput::make('mail_mailhog_port')
                                            ->label('Mailhog SMTP Port')
                                            ->default('1025')
                                            ->numeric()
                                            ->required(),
                                    ])->columns(2)
                                    ->description('Mailhog catches all outgoing emails locally. Open http://localhost:8025 to view caught mail.'),

                                // ── Sendmail / cPanel ────────────────────────────────────────
                                Section::make('PHP Mail / Sendmail (cPanel)')
                                    ->icon('heroicon-o-command-line')
                                    ->hidden(fn ($get) => $get('mail_driver') !== 'sendmail')
                                    ->schema([
                                        TextInput::make('mail_sendmail_path')
                                            ->label('Sendmail Binary Path')
                                            ->default('/usr/sbin/sendmail -bs -i')
                                            ->required()
                                            ->helperText('On cPanel servers this is usually /usr/sbin/sendmail -bs -i. Check with your host if unsure.')
                                            ->columnSpanFull(),
                                    ])->columns(1)
                                    ->description('Uses your server\'s built-in sendmail binary. Ideal for cPanel shared hosting.'),

                                // ── Mailgun ──────────────────────────────────────────────────
                                Section::make('Mailgun Configuration')
                                    ->icon('heroicon-o-cloud')
                                    ->hidden(fn ($get) => $get('mail_driver') !== 'mailgun')
                                    ->schema([
                                        TextInput::make('mail_mailgun_domain')
                                            ->label('Mailgun Domain')
                                            ->placeholder('mg.yourdomain.com')
                                            ->required(),
                                        TextInput::make('mail_mailgun_secret')
                                            ->label('Mailgun API Secret Key')
                                            ->password()
                                            ->revealable()
                                            ->required(),
                                        Select::make('mail_mailgun_endpoint')
                                            ->label('API Endpoint Region')
                                            ->options([
                                                'api.mailgun.net'    => 'US (api.mailgun.net)',
                                                'api.eu.mailgun.net' => 'EU (api.eu.mailgun.net)',
                                            ])
                                            ->default('api.mailgun.net')
                                            ->required(),
                                    ])->columns(2)
                                    ->description('Requires the symfony/mailgun-mailer package. Run: composer require symfony/mailgun-mailer'),

                                // ── Amazon SES ───────────────────────────────────────────────
                                Section::make('Amazon SES Configuration')
                                    ->icon('heroicon-o-cloud-arrow-up')
                                    ->hidden(fn ($get) => $get('mail_driver') !== 'ses')
                                    ->schema([
                                        TextInput::make('mail_ses_key')
                                            ->label('AWS Access Key ID')
                                            ->required(),
                                        TextInput::make('mail_ses_secret')
                                            ->label('AWS Secret Access Key')
                                            ->password()
                                            ->revealable()
                                            ->required(),
                                        Select::make('mail_ses_region')
                                            ->label('AWS Region')
                                            ->options([
                                                'us-east-1'      => 'US East (N. Virginia)',
                                                'us-west-2'      => 'US West (Oregon)',
                                                'eu-west-1'      => 'EU (Ireland)',
                                                'eu-central-1'   => 'EU (Frankfurt)',
                                                'ap-southeast-1' => 'Asia Pacific (Singapore)',
                                                'ap-southeast-2' => 'Asia Pacific (Sydney)',
                                            ])
                                            ->default('us-east-1')
                                            ->required(),
                                    ])->columns(2)
                                    ->description('Requires the aws/aws-sdk-php package. Run: composer require aws/aws-sdk-php'),

                                // ── Postmark ────────────────────────────────────────────────
                                Section::make('Postmark Configuration')
                                    ->icon('heroicon-o-envelope-open')
                                    ->hidden(fn ($get) => $get('mail_driver') !== 'postmark')
                                    ->schema([
                                        TextInput::make('mail_postmark_token')
                                            ->label('Postmark Server API Token')
                                            ->password()
                                            ->revealable()
                                            ->required()
                                            ->helperText('Find this in your Postmark account → Servers → your server → API Tokens.')
                                            ->columnSpanFull(),
                                    ])->columns(1)
                                    ->description('Requires the symfony/postmark-mailer package. Run: composer require symfony/postmark-mailer'),

                                // ── Resend ───────────────────────────────────────────────────
                                Section::make('Resend Configuration')
                                    ->icon('heroicon-o-bolt')
                                    ->hidden(fn ($get) => $get('mail_driver') !== 'resend')
                                    ->schema([
                                        TextInput::make('mail_resend_key')
                                            ->label('Resend API Key')
                                            ->password()
                                            ->revealable()
                                            ->required()
                                            ->placeholder('re_xxxxxxxxxxxx')
                                            ->helperText('Create an API key at resend.com/api-keys.')
                                            ->columnSpanFull(),
                                    ])->columns(1)
                                    ->description('Requires the resend/resend-php package. Run: composer require resend/resend-laravel'),

                                // ── Queue Delivery Mode ──────────────────────────────────
                                Section::make('Email Delivery Mode')
                                    ->icon('heroicon-o-queue-list')
                                    ->description('Choose how emails are dispatched. On shared/cPanel hosting with no queue worker, use Sync.')
                                    ->schema([
                                        Select::make('queue_connection')
                                            ->label('Queue / Delivery Mode')
                                            ->options([
                                                'sync'     => '⚡ Sync — Send immediately (recommended for cPanel/shared hosting)',
                                                'database' => '🗄️ Database Queue — Requires a running queue:work process',
                                            ])
                                            ->default('sync')
                                            ->required()
                                            ->helperText('If you switch to Database Queue, emails will only be sent once "php artisan queue:work" is running on the server. For cPanel, use Sync.')
                                            ->columnSpanFull(),
                                    ])->columns(1),

                            ]),

                        // ── Tab 2: Email Notifications ─────────────────────────────────
                        Tab::make('Notification Emails')
                            ->icon('heroicon-o-bell')
                            ->schema([
                                Section::make('Login & Security Emails')->schema([
                                    Toggle::make('email_login_alert_enabled')
                                        ->label('Send login alert email on every sign-in')
                                        ->helperText('Notifies users with the IP address, browser, and time whenever their account is logged into.')
                                        ->inline(false)
                                        ->columnSpanFull(),
                                ])->columns(1),

                                Section::make('Activity Emails')->schema([
                                    Toggle::make('email_feature_usage_enabled')
                                        ->label('Send emails when users use key features (matches, likes, waves)')
                                        ->inline(false)
                                        ->columnSpanFull(),
                                    Toggle::make('email_daily_summary_enabled')
                                        ->label('Send daily digest email (profile views, new likes, new matches summary)')
                                        ->inline(false)
                                        ->columnSpanFull(),
                                ])->columns(1),
                            ]),

                        // ── Tab 3: Test Email ───────────────────────────────────────────
                        Tab::make('Test Email')
                            ->icon('heroicon-o-paper-airplane')
                            ->schema([
                                Section::make('Send a Test Email')
                                    ->description('Save your settings first, then use this tool to verify emails are being delivered correctly.')
                                    ->schema([
                                        TextInput::make('_test_email_address')
                                            ->label('Send Test Email To')
                                            ->email()
                                            ->placeholder('you@example.com')
                                            ->helperText('Enter any email address to receive a test message using the currently saved configuration.')
                                            ->columnSpanFull(),
                                    ])->columns(1),
                            ]),

                        // ── Tab 4: Diagnostics ──────────────────────────────────────────
                        Tab::make('Diagnostics')
                            ->icon('heroicon-o-bug-ant')
                            ->schema([
                                \Filament\Schemas\Components\View::make('filament.pages.email-diagnostics'),
                            ]),

                    ])
                    ->columnSpanFull(),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('processQueuedEmails')
                ->label('Process Queued Emails Now')
                ->icon('heroicon-o-play-circle')
                ->color('success')
                ->requiresConfirmation()
                ->modalDescription('This will immediately process all pending email jobs in the queue (runs queue:work --stop-when-empty). Use this if you\'re on shared hosting and emails are stuck.')
                ->action('processQueuedEmails'),

            Action::make('clearFailedJobs')
                ->label('Clear Failed Jobs')
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->requiresConfirmation()
                ->modalDescription('This will delete all failed queue jobs. Emails in failed jobs will not be retried.')
                ->action('clearFailedJobs'),

            Action::make('sendTestEmail')
                ->label('Send Test Email')
                ->icon('heroicon-o-paper-airplane')
                ->color('gray')
                ->action('sendTestEmail'),

            Action::make('save')
                ->label('Save Settings')
                ->icon('heroicon-o-check')
                ->color('primary')
                ->action('save'),
        ];
    }

    public function processQueuedEmails(): void
    {
        try {
            Artisan::call('queue:work', [
                '--stop-when-empty' => true,
                '--tries'           => 3,
                '--timeout'         => 60,
                '--queue'           => 'default',
            ]);
            $output = trim(Artisan::output());
            Notification::make()
                ->title('Queue processed')
                ->body($output ?: 'All pending jobs processed.')
                ->success()
                ->send();
        } catch (\Throwable $e) {
            Notification::make()
                ->title('Queue processing failed')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function clearFailedJobs(): void
    {
        try {
            Artisan::call('queue:flush');
            Notification::make()
                ->title('Failed jobs cleared')
                ->success()
                ->send();
        } catch (\Throwable $e) {
            Notification::make()
                ->title('Failed to clear jobs')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function save(): void
    {
        $data = $this->form->getState();

        // Don't persist the test email helper field.
        unset($data['_test_email_address']);

        foreach ($data as $key => $value) {
            SiteSetting::set($key, $value);
        }

        // Re-apply the updated settings immediately to the running process.
        try {
            MailSettingsService::applyFromSettings();
            MailSettingsService::applyQueueSettings();
        } catch (\Throwable) {}

        // Clear any stale config cache so the new driver/port takes effect
        // on the next request without a manual artisan call.
        Artisan::call('config:clear');

        Notification::make()
            ->title('Email settings saved')
            ->success()
            ->send();
    }

    public function sendTestEmail(): void
    {
        $data = $this->form->getState();
        $to   = trim($data['_test_email_address'] ?? '');

        if (empty($to)) {
            Notification::make()
                ->title('Enter a recipient email address in the "Test Email" tab first.')
                ->warning()
                ->send();
            return;
        }

        try {
            Mail::raw(
                'This is a test email from ' . config('app.name') . '. If you received this, your mail configuration is working correctly!',
                function ($message) use ($to, $data) {
                    $message->to($to)
                        ->subject('Test Email from ' . ($data['mail_from_name'] ?? config('app.name')));

                    if (!empty($data['mail_from_address'])) {
                        $message->from($data['mail_from_address'], $data['mail_from_name'] ?? config('app.name'));
                    }
                }
            );

            Notification::make()
                ->title("Test email sent to {$to}")
                ->body('Check your inbox (or mail catcher) for the message.')
                ->success()
                ->send();

        } catch (\Throwable $e) {
            Notification::make()
                ->title('Failed to send test email')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }
}
