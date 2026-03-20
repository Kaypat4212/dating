<?php

namespace App\Filament\Pages;

use App\Models\SiteSetting;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;

class ManageGiftSettings extends Page
{
    protected string $view = 'filament.pages.manage-gift-settings';

    public static function getNavigationIcon(): ?string  { return 'heroicon-o-gift'; }
    public static function getNavigationLabel(): string  { return 'Virtual Gifts'; }
    public static function getNavigationGroup(): ?string { return 'Wallet'; }
    public static function getNavigationSort(): ?int     { return 10; }

    public function getTitle(): string | Htmlable { return 'Virtual Gift Prices'; }

    public array $data = [];

    /**
     * The list of gifts available in the chat (must match JS GIFTS array in show.blade.php).
     */
    public static array $giftKeys = [
        'gift_price_rose'      => 'Rose 🌹',
        'gift_price_heart'     => 'Heart 💖',
        'gift_price_gift_box'  => 'Gift Box 🎁',
        'gift_price_chocolate' => 'Chocolate 🍫',
        'gift_price_star'      => 'Star ⭐',
        'gift_price_diamond'   => 'Diamond 💎',
        'gift_price_flower'    => 'Flower 🌸',
        'gift_price_love'      => 'Love 💌',
    ];

    public function mount(): void
    {
        $defaults = array_fill_keys(array_keys(self::$giftKeys), 10);
        $saved    = SiteSetting::allAsArray();
        $merged   = array_merge($defaults, array_intersect_key($saved, $defaults));

        foreach ($merged as $k => $v) {
            $merged[$k] = (int) $v;
        }

        $this->form->fill($merged);
    }

    public function form(Schema $schema): Schema
    {
        $inputs = [];
        foreach (self::$giftKeys as $key => $label) {
            $inputs[] = TextInput::make($key)
                ->label($label)
                ->numeric()
                ->minValue(1)
                ->suffix('credits')
                ->required();
        }

        return $schema
            ->statePath('data')
            ->components([
                Section::make('Gift Prices (credits per gift)')
                    ->icon('heroicon-o-gift')
                    ->description('Set how many credits each virtual gift costs when sent in chat.')
                    ->columns(2)
                    ->schema($inputs),
            ]);
    }

    public function save(): void
    {
        $data = $this->form->getState();

        foreach ($data as $key => $value) {
            SiteSetting::set($key, (int) $value);
        }

        Notification::make()->title('Gift prices saved!')->success()->send();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label('Save Prices')
                ->icon('heroicon-o-check')
                ->color('primary')
                ->action('save'),
        ];
    }
}
