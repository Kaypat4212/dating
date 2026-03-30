<?php

namespace App\Filament\Resources\ForumTopicResource\RelationManagers;

use App\Models\ForumReply;
use Filament\Actions;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class RepliesRelationManager extends RelationManager
{
    protected static string $relationship = 'replies';
    protected static ?string $title       = 'Replies';

    public function form(Schema $schema): Schema
    {
        return $schema->schema([
            Forms\Components\Select::make('user_id')
                ->relationship('author', 'name')
                ->searchable()
                ->preload()
                ->required()
                ->label('Author'),

            Forms\Components\RichEditor::make('content')
                ->required()
                ->toolbarButtons(['bold', 'italic', 'bulletList', 'orderedList', 'link', 'blockquote', 'undo', 'redo'])
                ->columnSpanFull(),

            Forms\Components\Toggle::make('is_best_answer')
                ->label('Best answer')
                ->helperText('Mark this reply as the best / accepted answer.'),

            Forms\Components\Toggle::make('is_flagged')
                ->label('Flagged')
                ->helperText('Flag for moderation review.'),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'asc')
            ->columns([
                Tables\Columns\TextColumn::make('author.name')
                    ->searchable()
                    ->label('Author'),

                Tables\Columns\TextColumn::make('content')
                    ->html()
                    ->limit(120)
                    ->label('Reply'),

                Tables\Columns\IconColumn::make('is_best_answer')
                    ->boolean()
                    ->label('Best'),

                Tables\Columns\IconColumn::make('is_flagged')
                    ->boolean()
                    ->label('Flagged')
                    ->color(fn (bool $state) => $state ? 'danger' : 'gray'),

                Tables\Columns\TextColumn::make('likes_count')
                    ->label('Likes')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('M j, Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_flagged')->label('Flagged'),
                Tables\Filters\TernaryFilter::make('is_best_answer')->label('Best answer'),
            ])
            ->headerActions([
                Actions\CreateAction::make(),
            ])
            ->recordActions([
                Actions\Action::make('best_answer')
                    ->label(fn (ForumReply $r) => $r->is_best_answer ? 'Unmark best' : 'Best answer')
                    ->icon('heroicon-o-check-badge')
                    ->color(fn (ForumReply $r) => $r->is_best_answer ? 'gray' : 'success')
                    ->action(fn (ForumReply $r) => $r->update(['is_best_answer' => !$r->is_best_answer])),
                Actions\Action::make('unflag')
                    ->label('Unflag')
                    ->icon('heroicon-o-flag')
                    ->color('warning')
                    ->visible(fn (ForumReply $r) => $r->is_flagged)
                    ->action(fn (ForumReply $r) => $r->update(['is_flagged' => false]))
                    ->requiresConfirmation(),
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('unflag_all')
                        ->label('Unflag selected')
                        ->icon('heroicon-o-flag')
                        ->color('warning')
                        ->action(fn ($records) => $records->each(fn (ForumReply $r) => $r->update(['is_flagged' => false])))
                        ->requiresConfirmation(),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
