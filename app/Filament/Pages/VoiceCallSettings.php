<?php

namespace App\Filament\Pages;

use App\Models\SiteSetting;
use App\Models\VoiceCall;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Auth;

class VoiceCallSettings extends Page
{
    protected string $view = 'filament.pages.voice-call-settings';

    public static function getNavigationIcon(): ?string  { return 'heroicon-o-phone'; }
    public static function getNavigationLabel(): string  { return 'Voice Calls'; }
    public static function getNavigationGroup(): ?string { return 'Site Settings'; }
    public static function getNavigationSort(): ?int     { return 6; }

    public function getTitle(): string | Htmlable { return 'Voice Call Settings'; }

    public static function canAccess(): bool
    {
        return Auth::check() && Auth::id() === 1;
    }

    // ── Form state ────────────────────────────────────────────────────────

    public array $data = [];
    public string $agoraTestResult = '';
    public string $agoraTestStatus = ''; // 'success', 'error', 'testing'

    public function mount(): void
    {
        $defaults = [
            'voice_calls_enabled'       => true,
            'voice_call_timeout'        => 30,
            'voice_call_max_duration'   => 0,
            'voice_call_daily_limit'    => 0,
            'voice_call_require_match'  => true,
            'voice_call_token_expire'   => 3600,
            'agora_app_id'              => '',
            'agora_app_certificate'     => '',
        ];

        $saved  = SiteSetting::allAsArray();
        $merged = array_merge($defaults, array_intersect_key($saved, $defaults));
        
        // Get Agora credentials from config (env)
        $merged['agora_app_id'] = config('services.agora.app_id', '');
        $merged['agora_app_certificate'] = config('services.agora.app_certificate', '');

        // Cast booleans
        $merged['voice_calls_enabled']      = filter_var($merged['voice_calls_enabled'],      FILTER_VALIDATE_BOOLEAN);
        $merged['voice_call_require_match'] = filter_var($merged['voice_call_require_match'], FILTER_VALIDATE_BOOLEAN);

        // Cast integers
        $merged['voice_call_timeout']      = (int) $merged['voice_call_timeout'];
        $merged['voice_call_max_duration'] = (int) $merged['voice_call_max_duration'];
        $merged['voice_call_daily_limit']  = (int) $merged['voice_call_daily_limit'];
        $merged['voice_call_token_expire'] = (int) $merged['voice_call_token_expire'];

        $this->form->fill($merged);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([

                // ── Agora API Credentials ──────────────────────────────
                Section::make('Agora API Credentials')
                    ->description('Configure your Agora.io project credentials for voice calls. Sign up at console.agora.io to get your App ID and Certificate.')
                    ->schema([
                        TextInput::make('agora_app_id')
                            ->label('Agora App ID')
                            ->placeholder('Enter your Agora App ID')
                            ->helperText('Found in Agora Console → Project Management → Your Project')
                            ->required(),
                        
                        TextInput::make('agora_app_certificate')
                            ->label('Agora App Certificate')
                            ->placeholder('Enter your Agora App Certificate')
                            ->helperText('Enable App Certificate in your project settings to get this key')
                            ->password()
                            ->revealable()
                            ->required(),
                    ]),

                // ── Master switch ──────────────────────────────────────
                Section::make('Master Switch')
                    ->description('Enable or disable the entire voice call feature across the platform. When disabled, the call button is hidden and all call endpoints return 503.')
                    ->schema([
                        Toggle::make('voice_calls_enabled')
                            ->label('Enable voice calls')
                            ->helperText('Disabling this immediately hides the call button from all users without requiring a deployment.'),
                    ]),

                // ── Call behaviour ─────────────────────────────────────
                Section::make('Call Behaviour')
                    ->schema([
                        TextInput::make('voice_call_timeout')
                            ->label('Ring timeout (seconds)')
                            ->numeric()
                            ->minValue(10)
                            ->maxValue(120)
                            ->step(5)
                            ->suffix('seconds')
                            ->helperText('How long the callee phone rings before the call is automatically marked as missed. Range: 10–120 seconds.'),

                        TextInput::make('voice_call_max_duration')
                            ->label('Maximum call duration (minutes)')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(480)
                            ->suffix('minutes')
                            ->helperText('0 = unlimited. Set a limit to curtail abuse or bandwidth costs. The call token will also expire after this time.'),

                        TextInput::make('voice_call_daily_limit')
                            ->label('Daily call limit per user')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(500)
                            ->suffix('calls / day')
                            ->helperText('0 = unlimited. Counts calls initiated by each user per calendar day (UTC).'),

                        Toggle::make('voice_call_require_match')
                            ->label('Only matched users can call each other')
                            ->helperText('When enabled, both users must have an active match to start a call. Disable only for testing.'),
                    ]),

                // ── Agora token ────────────────────────────────────────
                Section::make('Agora Token Expiry')
                    ->description('The Agora RTC token grants access to the call channel. Shorter tokens are more secure but require re-issue on reconnect.')
                    ->schema([
                        Select::make('voice_call_token_expire')
                            ->label('Token lifetime')
                            ->options([
                                900   => '15 minutes',
                                1800  => '30 minutes',
                                3600  => '1 hour (recommended)',
                                7200  => '2 hours',
                                21600 => '6 hours',
                                86400 => '24 hours',
                            ])
                            ->helperText('Also increase this if users regularly experience dropped calls due to token expiry.'),
                    ]),
            ]);
    }

    public function save(): void
    {
        $data = $this->form->getState();

        // Save Agora credentials to .env file
        $this->updateEnvFile('AGORA_APP_ID', $data['agora_app_id'] ?? '');
        $this->updateEnvFile('AGORA_APP_CERTIFICATE', $data['agora_app_certificate'] ?? '');

        SiteSetting::set('voice_calls_enabled',      $data['voice_calls_enabled']      ? '1' : '0');
        SiteSetting::set('voice_call_timeout',        (string) (int) ($data['voice_call_timeout']      ?? 30));
        SiteSetting::set('voice_call_max_duration',   (string) (int) ($data['voice_call_max_duration']  ?? 0));
        SiteSetting::set('voice_call_daily_limit',    (string) (int) ($data['voice_call_daily_limit']   ?? 0));
        SiteSetting::set('voice_call_require_match',  $data['voice_call_require_match'] ? '1' : '0');
        SiteSetting::set('voice_call_token_expire',   (string) (int) ($data['voice_call_token_expire']  ?? 3600));

        Notification::make()
            ->title('Voice call settings saved!')
            ->body('Agora credentials and call settings have been updated.')
            ->success()
            ->send();
    }

    private function updateEnvFile(string $key, string $value): void
    {
        $envPath = base_path('.env');
        if (!file_exists($envPath)) {
            return;
        }

        $envContent = file_get_contents($envPath);
        $pattern = "/^{$key}=.*$/m";
        $replacement = "{$key}={$value}";

        if (preg_match($pattern, $envContent)) {
            $envContent = preg_replace($pattern, $replacement, $envContent);
        } else {
            $envContent .= "\n{$replacement}";
        }

        file_put_contents($envPath, $envContent);
    }

    public function testAgoraConnection(): void
    {
        $data = $this->form->getState();
        $appId = $data['agora_app_id'] ?? '';
        $appCert = $data['agora_app_certificate'] ?? '';

        $this->agoraTestStatus = 'testing';
        $this->agoraTestResult = '';

        if (empty($appId) || empty($appCert)) {
            $this->agoraTestStatus = 'error';
            $this->agoraTestResult = 'Please enter both App ID and App Certificate before testing.';
            return;
        }

        try {
            // Temporarily set config for testing
            config(['services.agora.app_id' => $appId]);
            config(['services.agora.app_certificate' => $appCert]);
            
            // Test token generation
            $service = new \App\Services\AgoraTokenService();
            $testChannel = 'test_channel_' . time();
            $testUid = 12345;
            
            $token = $service->generateRtcToken($testChannel, $testUid);
            
            if (!empty($token)) {
                $this->agoraTestStatus = 'success';
                $this->agoraTestResult = '✓ Connected successfully! Agora credentials are valid and token generation works.';
                
                Notification::make()
                    ->title('Agora Connection Successful')
                    ->body('Your Agora credentials are valid and working properly.')
                    ->success()
                    ->send();
            } else {
                $this->agoraTestStatus = 'error';
                $this->agoraTestResult = 'Token generation failed. Please check your credentials.';
            }
        } catch (\Exception $e) {
            $this->agoraTestStatus = 'error';
            $this->agoraTestResult = 'Connection failed: ' . $e->getMessage();
            
            Notification::make()
                ->title('Agora Connection Failed')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('testConnection')
                ->label('Test Agora Connection')
                ->icon('heroicon-o-signal')
                ->color('info')
                ->action('testAgoraConnection'),
            Action::make('save')
                ->label('Save Settings')
                ->icon('heroicon-o-check')
                ->color('primary')
                ->action('save'),
        ];
    }

    /** Format seconds → "42s" or "1.5 min" — public so the blade can call it without a named function. */
    public function formatSeconds(float $sec): string
    {
        if ($sec < 60) return number_format($sec, 0) . 's';
        return number_format($sec / 60, 1) . ' min';
    }

    // ── Live stats (called from view via wire:click) ──────────────────────

    /** Stats snapshot for the live dashboard panel. */
    public function getStatsProperty(): array
    {
        if (! \Illuminate\Support\Facades\Schema::hasTable('voice_calls')) {
            return $this->emptyStats();
        }

        try {
            $today     = now()->startOfDay();
            $yesterday = now()->subDay()->startOfDay();

            $totalToday = VoiceCall::whereDate('created_at', today())->count();
            $activeCalls = VoiceCall::where('status', 'active')->count();
            $ringingCalls = VoiceCall::where('status', 'ringing')->count();
            $missedToday = VoiceCall::where('status', 'missed')->whereDate('created_at', today())->count();

            // Average duration for ended calls (in seconds)
            $avgDuration = VoiceCall::where('status', 'ended')
                ->whereDate('created_at', today())
                ->whereNotNull('started_at')
                ->whereNotNull('ended_at')
                ->get()
                ->avg(fn($c) => $c->durationSeconds()) ?? 0;

            $totalAll = VoiceCall::count();
            $totalEnded = VoiceCall::where('status', 'ended')->count();
            $totalMissed = VoiceCall::where('status', 'missed')->count();
            $totalRejected = VoiceCall::where('status', 'rejected')->count();

            $avgDurationAll = VoiceCall::where('status', 'ended')
                ->whereNotNull('started_at')
                ->whereNotNull('ended_at')
                ->limit(200)
                ->get()
                ->avg(fn($c) => $c->durationSeconds()) ?? 0;

            return [
                'total_today'      => $totalToday,
                'active_now'       => $activeCalls,
                'ringing_now'      => $ringingCalls,
                'missed_today'     => $missedToday,
                'avg_duration_today' => $avgDuration,
                'total_all'        => $totalAll,
                'total_ended'      => $totalEnded,
                'total_missed'     => $totalMissed,
                'total_rejected'   => $totalRejected,
                'avg_duration_all' => $avgDurationAll,
            ];
        } catch (\Throwable) {
            return $this->emptyStats();
        }
    }

    private function emptyStats(): array
    {
        return [
            'total_today' => 0, 'active_now' => 0, 'ringing_now' => 0,
            'missed_today' => 0, 'avg_duration_today' => 0, 'total_all' => 0,
            'total_ended' => 0, 'total_missed' => 0, 'total_rejected' => 0,
            'avg_duration_all' => 0,
        ];
    }

    /** Kill all currently active / ringing calls (emergency stop). */
    public function endAllActiveCalls(): void
    {
        if (! \Illuminate\Support\Facades\Schema::hasTable('voice_calls')) {
            Notification::make()->title('voice_calls table does not exist yet.')->warning()->send();
            return;
        }

        $count = VoiceCall::whereIn('status', ['active', 'ringing'])->count();

        VoiceCall::whereIn('status', ['active', 'ringing'])->update([
            'status'   => 'ended',
            'ended_at' => now(),
        ]);

        Notification::make()
            ->title("Ended {$count} active/ringing call(s).")
            ->success()
            ->send();
    }

    /** Flush all call records (dangerous — for dev/staging only). */
    public function clearCallHistory(): void
    {
        if (! \Illuminate\Support\Facades\Schema::hasTable('voice_calls')) {
            return;
        }
        $count = VoiceCall::count();
        VoiceCall::truncate();

        Notification::make()
            ->title("Deleted {$count} call record(s).")
            ->warning()
            ->send();
    }
}
