<?php

namespace App\Filament\Pages;

use App\Models\SiteSetting;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;

class FirebaseAnalyticsSettings extends Page
{
    protected string $view = 'filament.pages.firebase-analytics-settings';

    public static function getNavigationIcon(): ?string  { return 'heroicon-o-chart-bar-square'; }
    public static function getNavigationLabel(): string  { return 'Firebase & Analytics'; }
    public static function getNavigationGroup(): ?string { return 'Site Settings'; }
    public static function getNavigationSort(): ?int     { return 7; }

    public function getTitle(): string | Htmlable { return 'Firebase & Google Analytics'; }

    public static function canAccess(): bool
    {
        return Auth::check() && Auth::id() === 1;
    }

    public array $data = [];

    public function mount(): void
    {
        $defaults = [
            'firebase_enabled'           => false,
            'firebase_api_key'           => '',
            'firebase_auth_domain'       => '',
            'firebase_project_id'        => '',
            'firebase_storage_bucket'    => '',
            'firebase_messaging_sender_id' => '',
            'firebase_app_id'            => '',
            'firebase_measurement_id'    => '',
            'google_analytics_enabled'   => false,
            'google_analytics_id'        => '',
        ];

        $saved  = SiteSetting::allAsArray();
        $merged = array_merge($defaults, array_intersect_key($saved, $defaults));

        // Cast booleans
        $merged['firebase_enabled'] = filter_var($merged['firebase_enabled'], FILTER_VALIDATE_BOOLEAN);
        $merged['google_analytics_enabled'] = filter_var($merged['google_analytics_enabled'], FILTER_VALIDATE_BOOLEAN);

        $this->form->fill($merged);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([

                Section::make('Google Analytics 4')
                    ->icon('heroicon-o-chart-bar')
                    ->description(new HtmlString(
                        'Track user behavior, page views, events, and conversions with Google Analytics 4. '
                        . '<br><strong>How to get your Measurement ID:</strong><br>'
                        . '1. Go to <a href="https://analytics.google.com" target="_blank" class="text-primary">analytics.google.com</a><br>'
                        . '2. Admin → Data Streams → Select your web stream<br>'
                        . '3. Copy the Measurement ID (format: G-XXXXXXXXXX)'
                    ))
                    ->schema([
                        Toggle::make('google_analytics_enabled')
                            ->label('Enable Google Analytics')
                            ->helperText('Track page views, events, and user behavior across your site.'),

                        TextInput::make('google_analytics_id')
                            ->label('Measurement ID')
                            ->placeholder('G-XXXXXXXXXX')
                            ->helperText('Your Google Analytics 4 Measurement ID')
                            ->maxLength(50),
                    ])
                    ->collapsible(),

                Section::make('Firebase Configuration')
                    ->icon('heroicon-o-fire')
                    ->description(new HtmlString(
                        'Firebase provides real-time database, authentication, cloud messaging, and analytics. '
                        . '<br><strong>How to get your Firebase config:</strong><br>'
                        . '1. Go to <a href="https://console.firebase.google.com" target="_blank" class="text-primary">Firebase Console</a><br>'
                        . '2. Select your project → Project Settings (gear icon)<br>'
                        . '3. Scroll to "Your apps" → Web app → SDK setup and configuration<br>'
                        . '4. Copy the values from the firebaseConfig object'
                    ))
                    ->schema([
                        Toggle::make('firebase_enabled')
                            ->label('Enable Firebase Integration')
                            ->helperText('Required for push notifications, real-time features, and Firebase Analytics.'),

                        TextInput::make('firebase_api_key')
                            ->label('API Key')
                            ->placeholder('AIzaSy...')
                            ->helperText('Firebase Web API Key')
                            ->maxLength(200),

                        TextInput::make('firebase_auth_domain')
                            ->label('Auth Domain')
                            ->placeholder('your-project.firebaseapp.com')
                            ->helperText('Firebase Authentication Domain')
                            ->maxLength(200),

                        TextInput::make('firebase_project_id')
                            ->label('Project ID')
                            ->placeholder('your-project-id')
                            ->helperText('Firebase Project ID')
                            ->maxLength(100),

                        TextInput::make('firebase_storage_bucket')
                            ->label('Storage Bucket')
                            ->placeholder('your-project.appspot.com')
                            ->helperText('Firebase Storage Bucket')
                            ->maxLength(200),

                        TextInput::make('firebase_messaging_sender_id')
                            ->label('Messaging Sender ID')
                            ->placeholder('123456789012')
                            ->helperText('Firebase Cloud Messaging Sender ID')
                            ->maxLength(50),

                        TextInput::make('firebase_app_id')
                            ->label('App ID')
                            ->placeholder('1:123456789012:web:...')
                            ->helperText('Firebase App ID')
                            ->maxLength(200),

                        TextInput::make('firebase_measurement_id')
                            ->label('Measurement ID (Optional)')
                            ->placeholder('G-XXXXXXXXXX')
                            ->helperText('Firebase Analytics Measurement ID (same as Google Analytics if linked)')
                            ->maxLength(50),
                    ])
                    ->collapsible(),

                Section::make('Testing & Verification')
                    ->icon('heroicon-o-beaker')
                    ->description('Use these tools to verify your configuration is working correctly.')
                    ->schema([
                        Textarea::make('test_output')
                            ->label('Integration Status')
                            ->disabled()
                            ->rows(4)
                            ->default($this->getIntegrationStatus())
                            ->helperText('Current status of Firebase and Analytics integration.'),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }

    private function getIntegrationStatus(): string
    {
        $status = [];
        
        if (SiteSetting::get('google_analytics_enabled') === '1') {
            $gaId = SiteSetting::get('google_analytics_id');
            $status[] = $gaId 
                ? "✅ Google Analytics: ENABLED ($gaId)"
                : "⚠️ Google Analytics: ENABLED but no Measurement ID set";
        } else {
            $status[] = "❌ Google Analytics: DISABLED";
        }

        if (SiteSetting::get('firebase_enabled') === '1') {
            $projectId = SiteSetting::get('firebase_project_id');
            $status[] = $projectId 
                ? "✅ Firebase: ENABLED (Project: $projectId)"
                : "⚠️ Firebase: ENABLED but incomplete configuration";
        } else {
            $status[] = "❌ Firebase: DISABLED";
        }

        return implode("\n", $status);
    }

    public function save(): void
    {
        $data = $this->form->getState();

        // Save Google Analytics settings
        SiteSetting::set('google_analytics_enabled', $data['google_analytics_enabled'] ? '1' : '0');
        SiteSetting::set('google_analytics_id', $data['google_analytics_id'] ?? '');

        // Save Firebase settings
        SiteSetting::set('firebase_enabled', $data['firebase_enabled'] ? '1' : '0');
        SiteSetting::set('firebase_api_key', $data['firebase_api_key'] ?? '');
        SiteSetting::set('firebase_auth_domain', $data['firebase_auth_domain'] ?? '');
        SiteSetting::set('firebase_project_id', $data['firebase_project_id'] ?? '');
        SiteSetting::set('firebase_storage_bucket', $data['firebase_storage_bucket'] ?? '');
        SiteSetting::set('firebase_messaging_sender_id', $data['firebase_messaging_sender_id'] ?? '');
        SiteSetting::set('firebase_app_id', $data['firebase_app_id'] ?? '');
        SiteSetting::set('firebase_measurement_id', $data['firebase_measurement_id'] ?? '');

        // Clear all caches to ensure settings take effect
        \Artisan::call('config:clear');
        \Artisan::call('view:clear');

        Notification::make()
            ->title('Firebase & Analytics settings saved!')
            ->body('Changes will take effect on next page load.')
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
            
            Action::make('test')
                ->label('Test Configuration')
                ->icon('heroicon-o-beaker')
                ->color('info')
                ->action(function () {
                    $this->form->fill($this->form->getState());
                    Notification::make()
                        ->title('Configuration loaded successfully')
                        ->body('Check the Integration Status section below.')
                        ->info()
                        ->send();
                }),
        ];
    }
}
