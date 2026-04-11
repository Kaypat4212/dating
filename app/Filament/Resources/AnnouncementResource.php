<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AnnouncementResource\Pages;
use App\Models\Announcement;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Group;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Http;

class AnnouncementResource extends Resource
{
    protected static ?string $model = Announcement::class;

    public static function getNavigationIcon(): ?string  { return 'heroicon-o-megaphone'; }
    public static function getNavigationGroup(): ?string { return 'Community'; }
    public static function getNavigationLabel(): string  { return 'Announcements'; }
    public static function getNavigationSort(): ?int     { return 2; }

    public static function getNavigationBadge(): ?string
    {
        try {
            $count = static::getEloquentQuery()->where('is_published', false)->count();
            return $count > 0 ? (string) $count : null;
        } catch (\Throwable) {
            return null;
        }
    }

    public static function getNavigationBadgeColor(): ?string   { return 'warning'; }
    public static function getNavigationBadgeTooltip(): ?string { return 'Unpublished drafts'; }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Group::make([
                Section::make('Announcement Content')
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(140)
                            ->placeholder('e.g. "Speed Dating is here!"')
                            ->suffixAction(
                                \Filament\Actions\Action::make('generateTitle')
                                    ->icon('heroicon-o-sparkles')
                                    ->label('AI Generate')
                                    ->tooltip('Generate announcement title with AI')
                                    ->action(function ($set, $get) {
                                        $type = $get('type') ?? 'feature';
                                        $body = $get('body');
                                        $title = self::generateAiContent('title', $type, $body);
                                        if ($title) {
                                            $set('title', $title);
                                            \Filament\Notifications\Notification::make()
                                                ->title('Title generated!')
                                                ->success()
                                                ->send();
                                        }
                                    })
                            )
                            ->columnSpanFull(),

                        Forms\Components\RichEditor::make('body')
                            ->required()
                            ->toolbarButtons([
                                'bold', 'italic', 'underline', 'strike',
                                'bulletList', 'orderedList',
                                'h2', 'h3', 'blockquote',
                                'link', 'redo', 'undo',
                            ])
                            ->columnSpanFull()
                            ->helperText('Supports rich text formatting. Keep it concise for the best user experience.')
                            ->hintActions([
                                \Filament\Actions\Action::make('generateBody')
                                    ->icon('heroicon-o-sparkles')
                                    ->label('Generate with AI')
                                    ->tooltip('Create announcement content using AI')
                                    ->form([
                                        Forms\Components\Textarea::make('prompt')
                                            ->label('What is this announcement about?')
                                            ->placeholder('e.g. "New video calling feature launched"')
                                            ->required()
                                            ->rows(3),
                                    ])
                                    ->action(function (array $data, $set, $get) {
                                        $type = $get('type') ?? 'feature';
                                        $content = self::generateAiContent('body', $type, $data['prompt'] ?? '');
                                        if ($content) {
                                            $set('body', $content);
                                            \Filament\Notifications\Notification::make()
                                                ->title('Content generated!')
                                                ->success()
                                                ->send();
                                        }
                                    }),
                                \Filament\Actions\Action::make('improveBody')
                                    ->icon('heroicon-o-arrow-path')
                                    ->label('Improve')
                                    ->tooltip('Enhance existing content with AI')
                                    ->action(function ($set, $get) {
                                        $existing = strip_tags($get('body') ?? '');
                                        if (empty($existing)) {
                                            \Filament\Notifications\Notification::make()
                                                ->title('No content to improve')
                                                ->warning()
                                                ->send();
                                            return;
                                        }
                                        $improved = self::generateAiContent('improve', $get('type'), $existing);
                                        if ($improved) {
                                            $set('body', $improved);
                                            \Filament\Notifications\Notification::make()
                                                ->title('Content improved!')
                                                ->success()
                                                ->send();
                                        }
                                    }),
                            ]),
                    ]),
            ])->columnSpan(2),

            Group::make([
                Section::make('Metadata')
                    ->icon('heroicon-o-tag')
                    ->schema([
                        Forms\Components\Select::make('type')
                            ->options([
                                'feature'     => '✨ New Feature',
                                'update'      => '🔄 Update',
                                'maintenance' => '🔧 Maintenance',
                                'message'     => '💬 Message',
                                'promo'       => '🎁 Promotion',
                            ])
                            ->required()
                            ->default('feature'),

                        Forms\Components\TextInput::make('version')
                            ->placeholder('e.g. v2.4')
                            ->maxLength(20)
                            ->helperText('Optional version tag shown on the card.'),

                        Forms\Components\TextInput::make('badge_label')
                            ->placeholder('e.g. NEW, HOT, FIXED')
                            ->maxLength(40)
                            ->helperText('Short badge text on the announcement card.'),

                        Forms\Components\Select::make('badge_color')
                            ->options([
                                'primary'   => 'Blue (Primary)',
                                'success'   => 'Green (Success)',
                                'warning'   => 'Yellow (Warning)',
                                'danger'    => 'Red (Danger)',
                                'info'      => 'Teal (Info)',
                                'secondary' => 'Grey (Secondary)',
                            ])
                            ->default('primary'),
                    ]),

                Section::make('Targeting & Visibility')
                    ->icon('heroicon-o-eye')
                    ->schema([
                        Forms\Components\Toggle::make('is_published')
                            ->label('Published')
                            ->helperText('Only published announcements are visible to users.')
                            ->default(false),

                        Forms\Components\Toggle::make('show_popup')
                            ->label('Auto-open modal')
                            ->helperText('Automatically opens the "What\'s New" popup on users\' next visit.')
                            ->default(true),

                        Forms\Components\DateTimePicker::make('published_at')
                            ->label('Schedule publish at')
                            ->helperText('Leave empty to publish immediately when toggled on.')
                            ->nullable(),

                        Forms\Components\Select::make('target_user_id')
                            ->label('Target user (optional)')
                            ->placeholder('All users')
                            ->searchable()
                            ->getSearchResultsUsing(fn (string $search) =>
                                \App\Models\User::where('name', 'like', "%{$search}%")
                                    ->orWhere('email', 'like', "%{$search}%")
                                    ->limit(20)
                                    ->pluck('name', 'id')
                                    ->toArray()
                            )
                            ->getOptionLabelUsing(fn ($value) =>
                                \App\Models\User::find($value)?->name ?? 'Unknown'
                            )
                            ->nullable()
                            ->helperText('Send to a specific user. Leave blank for everyone.'),
                    ]),

                Section::make('Reach')
                    ->icon('heroicon-o-chart-bar')
                    ->schema([
                        Forms\Components\Placeholder::make('reads_count')
                            ->label('Times read')
                            ->content(fn (?Announcement $record) => $record
                                ? $record->reads()->count() . ' users'
                                : '—'
                            ),
                    ])
                    ->hiddenOn('create'),
            ])->columnSpan(1),
        ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('#')
                    ->sortable()
                    ->width(50),

                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match($state) {
                        'feature'     => '✨ Feature',
                        'update'      => '🔄 Update',
                        'maintenance' => '🔧 Maintenance',
                        'message'     => '💬 Message',
                        'promo'       => '🎁 Promo',
                        default       => $state,
                    })
                    ->color(fn ($state) => match($state) {
                        'feature'     => 'success',
                        'update'      => 'primary',
                        'maintenance' => 'warning',
                        'message'     => 'info',
                        'promo'       => 'danger',
                        default       => 'secondary',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->limit(60)
                    ->weight('semibold'),

                Tables\Columns\TextColumn::make('version')
                    ->badge()
                    ->color('gray')
                    ->placeholder('—'),

                Tables\Columns\IconColumn::make('is_published')
                    ->label('Live')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('gray'),

                Tables\Columns\IconColumn::make('show_popup')
                    ->label('Popup')
                    ->boolean()
                    ->trueColor('primary')
                    ->falseColor('gray'),

                Tables\Columns\TextColumn::make('reads_count')
                    ->label('Reads')
                    ->getStateUsing(fn (Announcement $record) => $record->reads()->count())
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('published_at')
                    ->label('Scheduled')
                    ->dateTime('M j, Y g:i A')
                    ->placeholder('Immediate')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->since()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'feature'     => '✨ Feature',
                        'update'      => '🔄 Update',
                        'maintenance' => '🔧 Maintenance',
                        'message'     => '💬 Message',
                        'promo'       => '🎁 Promo',
                    ]),
                Tables\Filters\TernaryFilter::make('is_published')
                    ->label('Status')
                    ->placeholder('All')
                    ->trueLabel('Published')
                    ->falseLabel('Draft'),
            ])
            ->recordActions([
                Actions\Action::make('publish')
                    ->label('Publish')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Announcement $record) => ! $record->is_published)
                    ->action(function (Announcement $record) {
                        $record->update(['is_published' => true, 'published_at' => $record->published_at ?? now()]);
                        Notification::make()->title('Announcement published!')->success()->send();
                    }),

                Actions\Action::make('unpublish')
                    ->label('Unpublish')
                    ->icon('heroicon-o-x-circle')
                    ->color('warning')
                    ->visible(fn (Announcement $record) => $record->is_published)
                    ->action(function (Announcement $record) {
                        $record->update(['is_published' => false]);
                        Notification::make()->title('Announcement unpublished.')->warning()->send();
                    }),

                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    BulkAction::make('publishSelected')
                        ->label('Publish selected')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(fn ($records) => $records->each(fn ($r) => $r->update([
                            'is_published' => true,
                            'published_at' => $r->published_at ?? now(),
                        ])))
                        ->deselectRecordsAfterCompletion(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    /**
     * Generate AI content for announcements
     */
    protected static function generateAiContent(string $mode, string $type, string $context = ''): ?string
    {
        $apiKey = \App\Models\SiteSetting::get('ai_groq_api_key', '');
        
        if (empty($apiKey)) {
            Notification::make()
                ->title('AI not configured')
                ->body('Please configure Groq API key in Site Settings → AI Assistant')
                ->warning()
                ->send();
            return null;
        }

        $prompts = [
            'title' => "Generate a catchy, short announcement title (max 60 chars) for a dating app $type announcement about: $context. Return ONLY the title, no quotes.",
            'body' => "Write a friendly, engaging announcement for a dating app about: $context. Type: $type. Use HTML formatting (<p>, <strong>, <ul>, <li>). Keep it under 200 words and exciting. Include emojis where appropriate.",
            'improve' => "Improve this dating app announcement. Make it more engaging, friendly, and exciting. Add emojis and better formatting. Use HTML tags: $context",
        ];

        try {
            $response = Http::timeout(15)
                ->withToken($apiKey)
                ->post('https://api.groq.com/openai/v1/chat/completions', [
                    'model' => \App\Models\SiteSetting::get('ai_groq_model', 'llama-3.1-8b-instant'),
                    'messages' => [
                        ['role' => 'system', 'content' => 'You are a friendly dating app community manager who writes engaging, warm announcements.'],
                        ['role' => 'user', 'content' => $prompts[$mode] ?? $prompts['body']],
                    ],
                    'max_tokens' => $mode === 'title' ? 100 : 500,
                    'temperature' => 0.7,
                ]);

            if (!$response->successful()) {
                throw new \Exception($response->body());
            }

            $content = data_get($response->json(), 'choices.0.message.content', '');
            
            // Clean up the response
            $content = trim($content, "\" \n\r\t");
            
            return $content ?: null;
            
        } catch (\Throwable $e) {
            Notification::make()
                ->title('AI generation failed')
                ->body($e->getMessage())
                ->danger()
                ->send();
            return null;
        }
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListAnnouncements::route('/'),
            'create' => Pages\CreateAnnouncement::route('/create'),
            'edit'   => Pages\EditAnnouncement::route('/{record}/edit'),
        ];
    }
}

