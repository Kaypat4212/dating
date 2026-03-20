<?php

namespace App\Filament\Pages;

use App\Models\SiteSetting;
use App\Services\TelegramService;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;

class ManageSecuritySettings extends Page
{
    protected string $view = 'filament.pages.manage-security-settings';

    public static function getNavigationIcon(): ?string  { return 'heroicon-o-shield-check'; }
    public static function getNavigationLabel(): string  { return 'Security & Alerts'; }
    public static function getNavigationGroup(): ?string { return 'Site Settings'; }
    public static function getNavigationSort(): ?int     { return 4; }

    public function getTitle(): string | Htmlable { return 'Security & Telegram Alerts'; }

    /** @var array<string, mixed> */
    public array $data = [];

    public function mount(): void
    {
        $defaults = [
            // Telegram bot
            'telegram_bot_token'              => null,
            'telegram_chat_id'                => null,

            // Alert toggles
            'telegram_admin_login_alert'        => false,   // successful logins
            'telegram_admin_login_failed_alert' => false,   // failed attempts
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

                // ── Telegram Bot Setup ──────────────────────────────────────────────
                Section::make('Telegram Bot Configuration')
                    ->icon('heroicon-o-chat-bubble-left-ellipsis')
                    ->description(new \Illuminate\Support\HtmlString(
                        'Connect a Telegram bot to receive real-time admin notifications.<br>'
                        . '<strong>3-step setup:</strong> '
                        . '(1) Search <strong>@BotFather</strong> on Telegram → send <code>/newbot</code> → copy the token. '
                        . '(2) Send your bot any message → forward it to <strong>@userinfobot</strong> to get your Chat ID. '
                        . '(3) Paste both fields below, save, then click <em>Send Test Message</em> to verify.'
                    ))
                    ->schema([
                        TextInput::make('telegram_bot_token')
                            ->label('Bot Token')
                            ->password()
                            ->revealable()
                            ->placeholder('123456789:ABCDefGhIJKlmNoPQRstuVWXyz')
                            ->helperText('From @BotFather → /newbot → copy the token shown.')
                            ->columnSpanFull(),

                        TextInput::make('telegram_chat_id')
                            ->label('Chat ID / Channel ID')
                            ->placeholder('-1001234567890  or  @yourchannel')
                            ->helperText('Your personal chat ID (use @userinfobot to find it), a group ID, or a channel username like @mychannel.')
                            ->columnSpanFull(),
                    ])->columns(1),

                // ── Admin Login Alerts ──────────────────────────────────────────────
                Section::make('Admin Portal Login Alerts')
                    ->icon('heroicon-o-key')
                    ->description('Choose which admin portal login events send a Telegram notification.')
                    ->schema([
                        Toggle::make('telegram_admin_login_alert')
                            ->label('Notify on successful admin login')
                            ->helperText('Sends a ✅ message each time an admin logs in successfully.')
                            ->onColor('success'),

                        Toggle::make('telegram_admin_login_failed_alert')
                            ->label('Notify on failed login attempt')
                            ->helperText('Sends a 🚨 alert when someone fails to log in to the admin panel — useful for detecting brute-force attempts.')
                            ->onColor('danger'),
                    ])->columns(1),

            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('test_telegram')
                ->label('Send Test Message')
                ->icon('heroicon-o-paper-airplane')
                ->color('gray')
                ->action(function (): void {
                    // Save current form data first so the test uses the latest values
                    $this->save(notify: false);

                    $error = TelegramService::test();

                    if ($error) {
                        Notification::make()
                            ->title('Telegram test failed')
                            ->body($error)
                            ->danger()
                            ->send();
                    } else {
                        Notification::make()
                            ->title('Test message sent!')
                            ->body('Check your Telegram — a test message should have arrived.')
                            ->success()
                            ->send();
                    }
                }),

            Action::make('save')
                ->label('Save Settings')
                ->icon('heroicon-o-check')
                ->color('primary')
                ->action(fn () => $this->save()),
        ];
    }

    public function save(bool $notify = true): void
    {
        $state = $this->form->getState();

        $keys = [
            'telegram_bot_token',
            'telegram_chat_id',
            'telegram_admin_login_alert',
            'telegram_admin_login_failed_alert',
        ];

        foreach ($keys as $key) {
            if (array_key_exists($key, $state)) {
                SiteSetting::set($key, $state[$key]);
            }
        }

        if ($notify) {
            Notification::make()
                ->title('Security settings saved.')
                ->success()
                ->send();
        }
    }
}
