<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ForumTopicResource\Pages\ListForumTopics;
use App\Filament\Resources\ForumTopicResource\Pages\EditForumTopic;
use App\Models\ForumTopic;
use Filament\Actions;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ForumTopicResource extends Resource
{
    protected static ?string $model = ForumTopic::class;

    public static function getNavigationIcon(): ?string { return 'heroicon-o-chat-bubble-left-right'; }
    public static function getNavigationGroup(): ?string { return 'Community'; }
    public static function getNavigationSort(): ?int { return 11; }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getEloquentQuery()->where('is_flagged', true)->count() ?: null;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Forms\Components\TextInput::make('title')->required()->columnSpanFull(),
            Forms\Components\Select::make('category_id')->relationship('category', 'name')->required(),
            Forms\Components\Select::make('user_id')->relationship('author', 'name')->searchable()->required(),
            Forms\Components\Textarea::make('content')->rows(8)->columnSpanFull(),
            Forms\Components\Toggle::make('is_pinned'),
            Forms\Components\Toggle::make('is_locked'),
            Forms\Components\Toggle::make('is_answered'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('title')->searchable()->limit(60),
                Tables\Columns\TextColumn::make('author.name')->searchable(),
                Tables\Columns\TextColumn::make('category.name'),
                Tables\Columns\TextColumn::make('replies_count')->label('Replies')->sortable(),
                Tables\Columns\TextColumn::make('views_count')->label('Views')->sortable(),
                Tables\Columns\IconColumn::make('is_pinned')->boolean()->label('Pinned'),
                Tables\Columns\IconColumn::make('is_locked')->boolean()->label('Locked'),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_locked'),
                Tables\Filters\TernaryFilter::make('is_pinned'),
            ])
            ->recordActions([
                Actions\Action::make('lock')
                    ->label('Lock/Unlock')
                    ->icon('heroicon-o-lock-closed')
                    ->action(fn (ForumTopic $record) => $record->update(['is_locked' => !$record->is_locked])),
                Actions\Action::make('pin')
                    ->label('Pin/Unpin')
                    ->icon('heroicon-o-bookmark')
                    ->action(fn (ForumTopic $record) => $record->update(['is_pinned' => !$record->is_pinned])),
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListForumTopics::route('/'),
            'edit'  => EditForumTopic::route('/{record}/edit'),
        ];
    }
}
