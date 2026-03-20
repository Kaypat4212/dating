<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmailTemplateResource\Pages;
use App\Models\EmailTemplate;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class EmailTemplateResource extends Resource
{
    protected static ?string $model = EmailTemplate::class;

    public static function getNavigationIcon(): ?string  { return 'heroicon-o-envelope-open'; }
    public static function getNavigationGroup(): ?string { return 'Site Settings'; }
    public static function getNavigationLabel(): string  { return 'Email Templates'; }
    public static function getNavigationSort(): ?int     { return 5; }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Template')->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Template Name')
                    ->disabled()
                    ->dehydrated(false),

                Forms\Components\TextInput::make('key')
                    ->label('Key')
                    ->disabled()
                    ->dehydrated(false)
                    ->helperText('Internal identifier — read-only.'),

                Forms\Components\TextInput::make('subject')
                    ->label('Email Subject')
                    ->required()
                    ->columnSpanFull()
                    ->helperText('Use placeholders like {user_name}, {app_name}. See "Available Variables" below.'),

                Forms\Components\Textarea::make('body')
                    ->label('Email Body (HTML)')
                    ->required()
                    ->rows(20)
                    ->columnSpanFull()
                    ->helperText('Write HTML. Use {placeholder} tokens for dynamic values — see the variable list below.'),

            ])->columns(2),

            Section::make('Available Variables')
                ->description('These placeholders will be replaced with real values when the email is sent. Copy and paste them into your subject or body.')
                ->collapsible()
                ->schema([
                    \Filament\Infolists\Components\TextEntry::make('variable_list')
                        ->label('')
                        ->html()
                        ->state(function (EmailTemplate $record): string {
                            $vars = $record->variables ?? [];
                            if (empty($vars)) {
                                return '<p class="text-sm text-gray-500">No variables defined for this template.</p>';
                            }
                            $badges = array_map(
                                fn ($v) => '<code style="background:#f3f4f6;border:1px solid #e5e7eb;border-radius:4px;padding:2px 8px;font-size:.85rem;margin:2px;display:inline-block">' . htmlspecialchars($v) . '</code>',
                                $vars
                            );
                            return '<div style="display:flex;flex-wrap:wrap;gap:4px">' . implode('', $badges) . '</div>';
                        }),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Template')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('subject')
                    ->label('Subject')
                    ->limit(60)
                    ->searchable(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Last Edited')
                    ->since()
                    ->sortable(),
            ])
            ->filters([])
            ->recordActions([
                Actions\EditAction::make()
                    ->successNotification(
                        Notification::make()->title('Template saved successfully!')->success()
                    ),
            ])
            ->toolbarActions([]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmailTemplates::route('/'),
            'edit'  => Pages\EditEmailTemplate::route('/{record}/edit'),
        ];
    }

    /** Prevent creating new templates from the UI — they are seeded. */
    public static function canCreate(): bool
    {
        return false;
    }

    /** Prevent deleting templates. */
    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return false;
    }
}
