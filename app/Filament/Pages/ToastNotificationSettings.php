<?php

namespace App\Filament\Pages;

use App\Models\SiteSetting;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class ToastNotificationSettings extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-bell-alert';
    protected static ?string $navigationLabel = 'Toast Notifications';
    protected static ?int $navigationSort = 30;
    protected static string $view = 'filament.pages.toast-notification-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'toast_position' => SiteSetting::get('toast_position', 'top-right'),
            'toast_duration' => SiteSetting::get('toast_duration', '3000'),
            'toast_animation' => SiteSetting::get('toast_animation', 'slide'),
            'toast_show_icon' => filter_var(SiteSetting::get('toast_show_icon', '1'), FILTER_VALIDATE_BOOLEAN),
            'toast_close_button' => filter_var(SiteSetting::get('toast_close_button', '1'), FILTER_VALIDATE_BOOLEAN),
            'toast_success_color' => SiteSetting::get('toast_success_color', '#198754'),
            'toast_error_color' => SiteSetting::get('toast_error_color', '#dc3545'),
            'toast_warning_color' => SiteSetting::get('toast_warning_color', '#ffc107'),
            'toast_info_color' => SiteSetting::get('toast_info_color', '#0dcaf0'),
            'toast_primary_color' => SiteSetting::get('toast_primary_color', '#0d6efd'),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Toast Notification Settings')
                    ->description('Customize how toast notifications appear across your platform')
                    ->schema([
                        Select::make('toast_position')
                            ->label('Position')
                            ->options([
                                'top-left' => 'Top Left',
                                'top-center' => 'Top Center',
                                'top-right' => 'Top Right',
                                'bottom-left' => 'Bottom Left',
                                'bottom-center' => 'Bottom Center',
                                'bottom-right' => 'Bottom Right',
                                'center' => 'Center',
                            ])
                            ->default('top-right')
                            ->required()
                            ->columnSpan(1),

                        TextInput::make('toast_duration')
                            ->label('Duration (milliseconds)')
                            ->numeric()
                            ->default(3000)
                            ->minValue(1000)
                            ->maxValue(10000)
                            ->suffix('ms')
                            ->helperText('How long the toast stays visible (1000ms = 1 second)')
                            ->required()
                            ->columnSpan(1),

                        Select::make('toast_animation')
                            ->label('Animation')
                            ->options([
                                'slide' => 'Slide',
                                'fade' => 'Fade',
                            ])
                            ->default('slide')
                            ->required()
                            ->columnSpan(1),

                        Toggle::make('toast_show_icon')
                            ->label('Show Icons')
                            ->helperText('Display icons in toast notifications')
                            ->default(true)
                            ->columnSpan(1),

                        Toggle::make('toast_close_button')
                            ->label('Show Close Button')
                            ->helperText('Allow users to manually close toasts')
                            ->default(true)
                            ->columnSpan(1),
                    ])
                    ->columns(2),

                Section::make('Toast Colors')
                    ->description('Customize colors for different toast types')
                    ->schema([
                        ColorPicker::make('toast_success_color')
                            ->label('Success Color')
                            ->default('#198754')
                            ->required(),

                        ColorPicker::make('toast_error_color')
                            ->label('Error Color')
                            ->default('#dc3545')
                            ->required(),

                        ColorPicker::make('toast_warning_color')
                            ->label('Warning Color')
                            ->default('#ffc107')
                            ->required(),

                        ColorPicker::make('toast_info_color')
                            ->label('Info Color')
                            ->default('#0dcaf0')
                            ->required(),

                        ColorPicker::make('toast_primary_color')
                            ->label('Primary Color')
                            ->default('#0d6efd')
                            ->required(),
                    ])
                    ->columns(3),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        foreach ($data as $key => $value) {
            SiteSetting::set($key, is_bool($value) ? ($value ? '1' : '0') : $value);
        }

        Notification::make()
            ->success()
            ->title('Settings saved successfully')
            ->body('Toast notification settings have been updated.')
            ->send();
    }

    protected function getFormActions(): array
    {
        return [
            \Filament\Actions\Action::make('save')
                ->label('Save Settings')
                ->submit('save')
                ->color('primary'),

            \Filament\Actions\Action::make('test')
                ->label('Test Toast')
                ->color('success')
                ->action(function () {
                    Notification::make()
                        ->success()
                        ->title('Test Successful')
                        ->body('This is a test toast notification with your current settings.')
                        ->send();
                }),
        ];
    }
}
