<?php

namespace App\Filament\Pages;

use App\Models\SiteSetting;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;

class ManageHomePage extends Page
{
    protected string $view = 'filament.pages.manage-home-page';

    public static function getNavigationIcon(): ?string  { return 'heroicon-o-home'; }
    public static function getNavigationLabel(): string  { return 'Homepage'; }
    public static function getNavigationGroup(): ?string { return 'Site Settings'; }
    public static function getNavigationSort(): ?int     { return 1; }

    public function getTitle(): string | Htmlable { return 'Manage Homepage'; }

    /** @var array<string, mixed> */
    public array $data = [];

    public function mount(): void
    {
        $defaults = [
            // Hero
            'hero_heading'          => 'Find Love That Lasts',
            'hero_subtext'          => 'Join thousands of singles finding meaningful connections. Browse profiles, swipe, match, and start chatting — for free.',
            'hero_btn_primary'      => 'Join Free Today',
            'hero_btn_secondary'    => 'Sign In',
            'hero_badge_text'       => 'No credit card needed to get started',
            'hero_card1_image'      => null,
            'hero_card1_name'       => 'Emily, 26',
            'hero_card1_location'   => 'NYC',
            'hero_card2_image'      => null,
            'hero_card2_name'       => 'James, 29',
            'hero_card2_location'   => 'Chicago',
            // Stats
            'stat1_number'  => '50K+',
            'stat1_label'   => 'Active Members',
            'stat2_number'  => '12K+',
            'stat2_label'   => 'Matches Made',
            'stat3_number'  => '98%',
            'stat3_label'   => 'Safety Rating',
            // Features
            'features_heading' => 'Why choose us?',
            'feat1_icon'  => '🔥', 'feat1_title'  => 'Swipe & Browse',        'feat1_desc'  => 'Discover profiles with our fun swipe deck OR browse the full grid — your choice.',
            'feat2_icon'  => '🧬', 'feat2_title'  => 'Compatibility Score',   'feat2_desc'  => 'Our algorithm analyses shared interests, values, and goals to show you truly compatible matches.',
            'feat3_icon'  => '💬', 'feat3_title'  => 'Real-Time Chat',        'feat3_desc'  => 'Instant messaging with read receipts and typing indicators once you both match.',
            'feat4_icon'  => '🛡️', 'feat4_title'  => 'Safe & Verified',       'feat4_desc'  => 'Photo moderation, block, report, and a real human mod team keep you safe.',
            'feat5_icon'  => '⭐', 'feat5_title'  => 'Premium Features',      'feat5_desc'  => 'See who liked you, unlimited likes, boost your profile — pay with crypto for full privacy.',
            'feat6_icon'  => '📍', 'feat6_title'  => 'Location Discovery',    'feat6_desc'  => 'Find people near you or anywhere in the world — you control the distance range.',
            // CTA
            'cta_heading'  => 'Ready to Find Your Person?',
            'cta_subtext'  => 'It only takes 2 minutes to create your profile.',
            'cta_btn_text' => 'Create Free Account',
            // Footer
            'footer_contact_url'      => null,
            'site_name'               => config('app.name', 'HeartsConnect'),
            'footer_blurb'            => 'A safe, inclusive space to find meaningful connections and lasting love. Every heart deserves to be found.',
            'footer_facebook_url'     => null,
            'footer_instagram_url'    => null,
            'footer_twitter_url'      => null,
            'footer_tiktok_url'       => null,
            'footer_support_email'    => null,
            'footer_help_center_url'  => null,
            'footer_safety_tips_url'  => null,
            'footer_report_abuse_url' => null,
            'footer_cookie_settings_url' => null,
            'footer_app_store_url'    => null,
            'footer_google_play_url'  => null,
            'footer_apk_url'          => null,
            'footer_exe_url'          => null,
            'footer_webapp_url'       => null,
            'footer_app_badge_text'   => 'Coming Soon',
            'footer_copyright_text'   => 'All rights reserved. Made with love for love seekers worldwide.',
            'legal_support_email'     => 'support@heartsconnect.com',
            'legal_privacy_email'     => 'privacy@heartsconnect.com',
            'legal_security_email'    => 'security@heartsconnect.com',
            'legal_safety_email'      => 'safety@heartsconnect.com',
            'legal_billing_email'     => 'billing@heartsconnect.com',
            'legal_dmca_email'        => 'dmca@heartsconnect.com',
            'dev_quick_admin_email'   => 'admin@heartsconnect.com',
            'dev_quick_demo_email'    => 'demo@heartsconnect.com',
            // Branding
            'site_favicon'              => null,
            'site_apple_touch_icon'     => null,
            // SEO
            'seo_default_title'       => null,
            'seo_meta_description'    => 'Find meaningful connections and lasting love. Join, match, chat, and build real relationships.',
            'seo_meta_keywords'       => 'dating, relationships, singles, matchmaking, chat',
            'seo_robots'              => 'index,follow',
            'seo_twitter_handle'      => null,
            'seo_og_image'            => null,
            'seo_google_site_verification' => null,
            'seo_google_adsense_publisher_id' => null,
            'seo_google_adsense_auto_ads' => false,
        ];

        $saved = SiteSetting::allAsArray();
        $merged = array_merge($defaults, array_intersect_key($saved, $defaults));

        $this->form->fill($merged);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Tabs::make('Homepage Sections')
                    ->tabs([

                        Tab::make('Branding & Favicon')
                            ->icon('heroicon-o-photo')
                            ->schema([
                                Section::make('Favicon')->schema([
                                    FileUpload::make('site_favicon')
                                        ->label('Favicon (SVG or PNG)')
                                        ->image()
                                        ->disk('public')
                                        ->directory('site/branding')
                                        ->maxSize(512)
                                        ->helperText('Shown in browser tabs & bookmarks. Best results with a square PNG (64×64) or SVG. Leave blank to use the default heart icon.')
                                        ->columnSpanFull(),
                                    FileUpload::make('site_apple_touch_icon')
                                        ->label('Apple Touch Icon (PNG)')
                                        ->image()
                                        ->disk('public')
                                        ->directory('site/branding')
                                        ->maxSize(512)
                                        ->helperText('Shown when users add the site to their home screen on iOS/Android. Recommended: 180×180 PNG. Falls back to the favicon above if not set.')
                                        ->columnSpanFull(),
                                ])->columns(1),
                            ]),

                        Tab::make('Hero Section')
                            ->icon('heroicon-o-star')
                            ->schema([
                                Section::make('Hero Text')->schema([
                                    TextInput::make('hero_heading')
                                        ->label('Main Heading')
                                        ->required()
                                        ->columnSpanFull(),
                                    Textarea::make('hero_subtext')
                                        ->label('Sub-text')
                                        ->rows(3)
                                        ->columnSpanFull(),
                                    TextInput::make('hero_btn_primary')
                                        ->label('Primary Button Text'),
                                    TextInput::make('hero_btn_secondary')
                                        ->label('Secondary Button Text'),
                                    TextInput::make('hero_badge_text')
                                        ->label('Badge / Trust Text')
                                        ->columnSpanFull(),
                                ])->columns(2),

                                Section::make('Profile Card 1 (Left)')->schema([
                                    FileUpload::make('hero_card1_image')
                                        ->label('Card 1 Photo')
                                        ->image()
                                        ->disk('public')
                                        ->directory('site/hero')
                                        ->maxSize(2048)
                                        ->columnSpanFull(),
                                    TextInput::make('hero_card1_name')
                                        ->label('Name & Age (e.g. Emily, 26)'),
                                    TextInput::make('hero_card1_location')
                                        ->label('Location (e.g. NYC)'),
                                ])->columns(2),

                                Section::make('Profile Card 2 (Right)')->schema([
                                    FileUpload::make('hero_card2_image')
                                        ->label('Card 2 Photo')
                                        ->image()
                                        ->disk('public')
                                        ->directory('site/hero')
                                        ->maxSize(2048)
                                        ->columnSpanFull(),
                                    TextInput::make('hero_card2_name')
                                        ->label('Name & Age (e.g. James, 29)'),
                                    TextInput::make('hero_card2_location')
                                        ->label('Location (e.g. Chicago)'),
                                ])->columns(2),
                            ]),

                        Tab::make('Stats Bar')
                            ->icon('heroicon-o-chart-bar')
                            ->schema([
                                Section::make('Stat 1')->schema([
                                    TextInput::make('stat1_number')->label('Number (e.g. 50K+)')->required(),
                                    TextInput::make('stat1_label')->label('Label (e.g. Active Members)')->required(),
                                ])->columns(2),
                                Section::make('Stat 2')->schema([
                                    TextInput::make('stat2_number')->label('Number')->required(),
                                    TextInput::make('stat2_label')->label('Label')->required(),
                                ])->columns(2),
                                Section::make('Stat 3')->schema([
                                    TextInput::make('stat3_number')->label('Number')->required(),
                                    TextInput::make('stat3_label')->label('Label')->required(),
                                ])->columns(2),
                            ]),

                        Tab::make('Features Section')
                            ->icon('heroicon-o-squares-2x2')
                            ->schema([
                                TextInput::make('features_heading')
                                    ->label('Section Heading')
                                    ->required()
                                    ->columnSpanFull(),
                                Section::make('Feature 1')->schema([
                                    TextInput::make('feat1_icon')->label('Emoji Icon'),
                                    TextInput::make('feat1_title')->label('Title'),
                                    Textarea::make('feat1_desc')->label('Description')->rows(2)->columnSpanFull(),
                                ])->columns(2),
                                Section::make('Feature 2')->schema([
                                    TextInput::make('feat2_icon')->label('Emoji Icon'),
                                    TextInput::make('feat2_title')->label('Title'),
                                    Textarea::make('feat2_desc')->label('Description')->rows(2)->columnSpanFull(),
                                ])->columns(2),
                                Section::make('Feature 3')->schema([
                                    TextInput::make('feat3_icon')->label('Emoji Icon'),
                                    TextInput::make('feat3_title')->label('Title'),
                                    Textarea::make('feat3_desc')->label('Description')->rows(2)->columnSpanFull(),
                                ])->columns(2),
                                Section::make('Feature 4')->schema([
                                    TextInput::make('feat4_icon')->label('Emoji Icon'),
                                    TextInput::make('feat4_title')->label('Title'),
                                    Textarea::make('feat4_desc')->label('Description')->rows(2)->columnSpanFull(),
                                ])->columns(2),
                                Section::make('Feature 5')->schema([
                                    TextInput::make('feat5_icon')->label('Emoji Icon'),
                                    TextInput::make('feat5_title')->label('Title'),
                                    Textarea::make('feat5_desc')->label('Description')->rows(2)->columnSpanFull(),
                                ])->columns(2),
                                Section::make('Feature 6')->schema([
                                    TextInput::make('feat6_icon')->label('Emoji Icon'),
                                    TextInput::make('feat6_title')->label('Title'),
                                    Textarea::make('feat6_desc')->label('Description')->rows(2)->columnSpanFull(),
                                ])->columns(2),
                            ]),

                        Tab::make('Call-to-Action')
                            ->icon('heroicon-o-megaphone')
                            ->schema([
                                Section::make('CTA Banner')->schema([
                                    TextInput::make('cta_heading')
                                        ->label('Heading')
                                        ->required()
                                        ->columnSpanFull(),
                                    TextInput::make('cta_subtext')
                                        ->label('Sub-text')
                                        ->columnSpanFull(),
                                    TextInput::make('cta_btn_text')
                                        ->label('Button Text'),
                                ])->columns(2),
                            ]),

                        Tab::make('Footer')
                            ->icon('heroicon-o-bars-3-bottom-left')
                            ->schema([
                                Section::make('Footer Content')->schema([
                                    TextInput::make('site_name')
                                        ->label('Site Name (Logo Text)')
                                        ->required()
                                        ->columnSpanFull(),
                                    Textarea::make('footer_blurb')
                                        ->label('Brand Description')
                                        ->rows(3)
                                        ->columnSpanFull(),
                                    TextInput::make('footer_support_email')
                                        ->label('Support Email')
                                        ->email(),
                                    TextInput::make('footer_copyright_text')
                                        ->label('Copyright Line')
                                        ->columnSpanFull(),
                                ])->columns(2),

                                Section::make('Social Links')->schema([
                                    TextInput::make('footer_facebook_url')
                                        ->label('Facebook URL')
                                        ->url(),
                                    TextInput::make('footer_instagram_url')
                                        ->label('Instagram URL')
                                        ->url(),
                                    TextInput::make('footer_twitter_url')
                                        ->label('Twitter / X URL')
                                        ->url(),
                                    TextInput::make('footer_tiktok_url')
                                        ->label('TikTok URL')
                                        ->url(),
                                ])->columns(2),

                                Section::make('Support Links')->schema([
                                    TextInput::make('footer_contact_url')
                                        ->label('Contact Us URL')
                                        ->url()
                                        ->helperText('Leave blank to use the built-in Contact page.'),
                                    TextInput::make('footer_help_center_url')
                                        ->label('Help Center URL')
                                        ->url(),
                                    TextInput::make('footer_safety_tips_url')
                                        ->label('Safety Tips URL')
                                        ->url(),
                                    TextInput::make('footer_report_abuse_url')
                                        ->label('Report Abuse URL')
                                        ->url(),
                                    TextInput::make('footer_cookie_settings_url')
                                        ->label('Cookie Settings URL')
                                        ->url(),
                                ])->columns(2),

                                Section::make('App Links')->schema([
                                    TextInput::make('footer_app_store_url')
                                        ->label('iOS App Store URL')
                                        ->url()
                                        ->placeholder('https://apps.apple.com/app/...')
                                        ->helperText('Leave blank to hide the App Store button.'),
                                    TextInput::make('footer_google_play_url')
                                        ->label('Google Play URL')
                                        ->url()
                                        ->placeholder('https://play.google.com/store/apps/...')
                                        ->helperText('Leave blank to hide the Google Play button.'),
                                    TextInput::make('footer_apk_url')
                                        ->label('Android APK Direct Download URL')
                                        ->url()
                                        ->placeholder('https://yoursite.com/app.apk')
                                        ->helperText('Direct download link for the APK file. Shown when Google Play is not used.'),
                                    TextInput::make('footer_exe_url')
                                        ->label('Windows EXE Download URL')
                                        ->url()
                                        ->placeholder('https://yoursite.com/setup.exe')
                                        ->helperText('Download link for the Windows desktop app installer.'),
                                    TextInput::make('footer_webapp_url')
                                        ->label('Web App / PWA URL')
                                        ->url()
                                        ->placeholder('https://app.yoursite.com')
                                        ->helperText('Link to open the progressive web app in a browser.'),
                                ])->columns(2),

                                Section::make('Legal Contact Emails')->schema([
                                    TextInput::make('legal_support_email')
                                        ->label('Legal: General Support Email')
                                        ->email(),
                                    TextInput::make('legal_privacy_email')
                                        ->label('Legal: Privacy Email')
                                        ->email(),
                                    TextInput::make('legal_security_email')
                                        ->label('Legal: Security Email')
                                        ->email(),
                                    TextInput::make('legal_safety_email')
                                        ->label('Legal: Safety Email')
                                        ->email(),
                                    TextInput::make('legal_billing_email')
                                        ->label('Legal: Billing Email')
                                        ->email(),
                                    TextInput::make('legal_dmca_email')
                                        ->label('Legal: DMCA Email')
                                        ->email(),
                                ])->columns(2),

                                Section::make('Developer Quick Login (Local only)')->schema([
                                    TextInput::make('dev_quick_admin_email')
                                        ->label('Dev Quick Login: Admin Email')
                                        ->email()
                                        ->helperText('Used by local quick-login helper. Password remains admin123.'),
                                    TextInput::make('dev_quick_demo_email')
                                        ->label('Dev Quick Login: Demo User Email')
                                        ->email()
                                        ->helperText('Used by local quick-login helper. Password remains password.'),
                                ])->columns(2),
                            ]),

                        Tab::make('SEO')
                            ->icon('heroicon-o-magnifying-glass-circle')
                            ->schema([
                                Section::make('Search Engine Optimization')->schema([
                                    TextInput::make('seo_default_title')
                                        ->label('Default SEO Title')
                                        ->helperText('Used when a page does not provide its own title.')
                                        ->maxLength(70)
                                        ->columnSpanFull(),
                                    Textarea::make('seo_meta_description')
                                        ->label('Meta Description')
                                        ->rows(3)
                                        ->maxLength(170)
                                        ->columnSpanFull(),
                                    TextInput::make('seo_meta_keywords')
                                        ->label('Meta Keywords')
                                        ->helperText('Comma separated keywords.'),
                                    TextInput::make('seo_robots')
                                        ->label('Robots Directive')
                                        ->helperText('Example: index,follow or noindex,nofollow'),
                                    TextInput::make('seo_twitter_handle')
                                        ->label('Twitter/X Handle')
                                        ->placeholder('@yourbrand'),
                                    TextInput::make('seo_google_site_verification')
                                        ->label('Google Site Verification Token')
                                        ->helperText('Value from Google Search Console, used for <meta name="google-site-verification">')
                                        ->columnSpanFull(),
                                    TextInput::make('seo_google_adsense_publisher_id')
                                        ->label('Google AdSense Publisher ID')
                                        ->placeholder('ca-pub-XXXXXXXXXXXXXXXX')
                                        ->helperText('Adds AdSense script when set.')
                                        ->columnSpanFull(),
                                    Toggle::make('seo_google_adsense_auto_ads')
                                        ->label('Enable Google Auto Ads')
                                        ->inline(false),
                                    FileUpload::make('seo_og_image')
                                        ->label('Open Graph Image')
                                        ->image()
                                        ->disk('public')
                                        ->directory('site/seo')
                                        ->maxSize(4096)
                                        ->helperText('Recommended: 1200x630 PNG/JPG')
                                        ->columnSpanFull(),
                                ])->columns(2),
                            ]),

                    ])
                    ->columnSpanFull(),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label('Save Changes')
                ->icon('heroicon-o-check')
                ->color('primary')
                ->action('save'),
        ];
    }

    public function save(): void
    {
        $data = $this->form->getState();

        foreach ($data as $key => $value) {
            // FileUpload returns an array for single files — unwrap it
            if (is_array($value) && array_is_list($value) && count($value) === 1) {
                $value = $value[0];
            }
            SiteSetting::set($key, $value);
        }

        Notification::make()
            ->title('Homepage settings saved!')
            ->success()
            ->send();
    }
}
