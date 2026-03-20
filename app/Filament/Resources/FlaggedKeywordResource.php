<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FlaggedKeywordResource\Pages;
use App\Models\FlaggedKeyword;
use Filament\Actions;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\DeleteBulkAction;

class FlaggedKeywordResource extends Resource
{
    protected static ?string $model = FlaggedKeyword::class;

    public static function getNavigationIcon(): ?string  { return 'heroicon-o-chat-bubble-left-ellipsis'; }
    public static function getNavigationGroup(): ?string { return 'Moderation'; }
    public static function getNavigationSort(): ?int     { return 4; }
    public static function getNavigationLabel(): string  { return 'Flagged Keywords'; }
    public static function getModelLabel(): string       { return 'Keyword'; }
    public static function getPluralModelLabel(): string { return 'Flagged Keywords'; }

    public static function getNavigationBadge(): ?string
    {
        $count = static::getEloquentQuery()->where('is_active', true)->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Forms\Components\TextInput::make('word')
                ->label('Keyword / Phrase')
                ->required()
                ->maxLength(100)
                ->placeholder('e.g. whatsapp, telegram, send money')
                ->helperText('Case-insensitive. Matches any message containing this word or phrase.'),

            Forms\Components\Select::make('severity')
                ->options([
                    'low'    => 'Low',
                    'medium' => 'Medium',
                    'high'   => 'High',
                ])
                ->default('medium')
                ->required(),

            Forms\Components\Toggle::make('is_active')
                ->label('Active')
                ->default(true)
                ->helperText('Disable to pause monitoring without deleting the keyword.'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('word')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('severity')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'low'  => 'success',
                        'high' => 'danger',
                        default => 'warning',
                    }),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),

                Tables\Columns\TextColumn::make('flags_count')
                    ->label('Times Triggered')
                    ->counts('flags')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('severity')
                    ->options(['low' => 'Low', 'medium' => 'Medium', 'high' => 'High']),
                Tables\Filters\TernaryFilter::make('is_active')->label('Active'),
            ])
            ->recordActions([
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ])
            ->headerActions([
                Actions\CreateAction::make(),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListFlaggedKeywords::route('/'),
            'create' => Pages\CreateFlaggedKeyword::route('/create'),
            'edit'   => Pages\EditFlaggedKeyword::route('/{record}/edit'),
        ];
    }
}
