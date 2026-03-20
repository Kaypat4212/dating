<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KeywordFlagResource\Pages;
use App\Models\KeywordFlag;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\DeleteBulkAction;

class KeywordFlagResource extends Resource
{
    protected static ?string $model = KeywordFlag::class;

    public static function getNavigationIcon(): ?string  { return 'heroicon-o-exclamation-triangle'; }
    public static function getNavigationGroup(): ?string { return 'Moderation'; }
    public static function getNavigationSort(): ?int     { return 5; }
    public static function getNavigationLabel(): string  { return 'Chat Alerts'; }
    public static function getModelLabel(): string       { return 'Alert'; }
    public static function getPluralModelLabel(): string { return 'Chat Alerts'; }

    public static function getNavigationBadge(): ?string
    {
        $count = static::getEloquentQuery()->where('is_reviewed', false)->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    /** Read-only — no create form needed. */
    public static function form(Schema $schema): Schema
    {
        return $schema->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('When')
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('sender.name')
                    ->label('Sent By')
                    ->searchable()
                    ->url(fn (KeywordFlag $record) => $record->sender_id
                        ? route('filament.admin.resources.users.edit', $record->sender_id)
                        : null),

                Tables\Columns\TextColumn::make('matched_word')
                    ->label('Matched Keyword')
                    ->badge()
                    ->color(fn (KeywordFlag $record): string => match ($record->keyword?->severity ?? 'medium') {
                        'low'  => 'success',
                        'high' => 'danger',
                        default => 'warning',
                    }),

                Tables\Columns\TextColumn::make('message.body')
                    ->label('Message Preview')
                    ->limit(80)
                    ->wrap()
                    ->searchable(),

                Tables\Columns\IconColumn::make('is_reviewed')
                    ->label('Reviewed')
                    ->boolean()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_reviewed')->label('Reviewed'),
                Tables\Filters\SelectFilter::make('severity')
                    ->label('Severity')
                    ->relationship('keyword', 'severity')
                    ->options(['low' => 'Low', 'medium' => 'Medium', 'high' => 'High']),
            ])
            ->recordActions([
                Actions\Action::make('mark_reviewed')
                    ->label('Mark Reviewed')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->hidden(fn (KeywordFlag $record) => $record->is_reviewed)
                    ->action(function (KeywordFlag $record) {
                        $record->update(['is_reviewed' => true, 'reviewed_at' => now()]);
                        Notification::make()->title('Marked as reviewed')->success()->send();
                    }),

                Actions\Action::make('view_conversation')
                    ->label('View Conversation')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->color('info')
                    ->url(fn (KeywordFlag $record) => route('conversations.show', $record->conversation_id))
                    ->openUrlInNewTab(),
            ])
            ->headerActions([
                Actions\Action::make('mark_all_reviewed')
                    ->label('Mark All Reviewed')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(fn () => KeywordFlag::where('is_reviewed', false)
                        ->update(['is_reviewed' => true, 'reviewed_at' => now()])),
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
            'index' => Pages\ListKeywordFlags::route('/'),
        ];
    }
}
