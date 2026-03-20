<?php

namespace App\Filament\Pages;

use App\Models\SiteSetting;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;

class ManageAiSettings extends Page
{
    protected string $view = 'filament.pages.manage-ai-settings';

    public static function getNavigationIcon(): ?string  { return 'heroicon-o-sparkles'; }
    public static function getNavigationLabel(): string  { return 'AI Assistant'; }
    public static function getNavigationGroup(): ?string { return 'Site Settings'; }
    public static function getNavigationSort(): ?int     { return 5; }

    public function getTitle(): string | Htmlable { return 'AI Writing Assistant'; }

    public array $data = [];

    public function mount(): void
    {
        $defaults = [
            'ai_enabled'      => false,
            'ai_groq_api_key' => '',
            'ai_groq_model'   => 'llama-3.1-8b-instant',
        ];

        $saved  = SiteSetting::allAsArray();
        $merged = array_merge($defaults, array_intersect_key($saved, $defaults));

        // Cast boolean
        $merged['ai_enabled'] = filter_var($merged['ai_enabled'], FILTER_VALIDATE_BOOLEAN);

        $this->form->fill($merged);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([

                Section::make('Groq Free API (0 cost)')
                    ->icon('heroicon-o-sparkles')
                    ->description(new HtmlString(
                        'Groq provides a <strong>completely free</strong> API with fast LLM inference (Llama 3). '
                        . 'Sign up at <strong>console.groq.com</strong> → API Keys → Create Key. '
                        . 'Free tier: 14,400 requests/day, no credit card required. '
                        . 'If no key is set, the feature still works using built-in suggested templates.'
                    ))
                    ->schema([
                        Toggle::make('ai_enabled')
                            ->label('Enable AI Writing Assistant for users')
                            ->helperText('When enabled, a ✨ button appears in chat and on the profile bio editor.'),

                        TextInput::make('ai_groq_api_key')
                            ->label('Groq API Key')
                            ->password()
                            ->revealable()
                            ->placeholder('gsk_...')
                            ->helperText('Get your free key at console.groq.com → API Keys')
                            ->maxLength(200),

                        Select::make('ai_groq_model')
                            ->label('Model')
                            ->options([
                                'llama-3.1-8b-instant'   => 'Llama 3.1 8B Instant (fastest, free)',
                                'llama-3.3-70b-versatile' => 'Llama 3.3 70B Versatile (smarter, free)',
                                'mixtral-8x7b-32768'      => 'Mixtral 8x7B (free)',
                            ])
                            ->default('llama-3.1-8b-instant')
                            ->helperText('All models listed are on Groq\'s free tier.'),
                    ]),
            ]);
    }

    public function save(): void
    {
        $data = $this->form->getState();

        SiteSetting::set('ai_enabled',      $data['ai_enabled'] ? '1' : '0');
        SiteSetting::set('ai_groq_api_key', $data['ai_groq_api_key'] ?? '');
        SiteSetting::set('ai_groq_model',   $data['ai_groq_model'] ?? 'llama-3.1-8b-instant');

        Notification::make()->title('AI settings saved!')->success()->send();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label('Save Settings')
                ->icon('heroicon-o-check')
                ->color('primary')
                ->action('save'),
        ];
    }
}
