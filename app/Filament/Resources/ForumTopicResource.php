<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ForumTopicResource\Pages\CreateForumTopic;
use App\Filament\Resources\ForumTopicResource\Pages\ListForumTopics;
use App\Filament\Resources\ForumTopicResource\Pages\EditForumTopic;
use App\Filament\Resources\ForumTopicResource\RelationManagers\RepliesRelationManager;
use App\Models\ForumTopic;
use Filament\Actions;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Illuminate\Support\Str;

class ForumTopicResource extends Resource
{
    protected static ?string $model = ForumTopic::class;

    public static function getNavigationIcon(): ?string  { return 'heroicon-o-chat-bubble-left-right'; }
    public static function getNavigationGroup(): ?string { return 'Community'; }
    public static function getNavigationLabel(): string  { return 'Forum Topics'; }
    public static function getNavigationSort(): ?int     { return 11; }

    public static function getNavigationBadge(): ?string
    {
        try {
            $count = static::getEloquentQuery()->where('is_flagged', true)->count();
            return $count > 0 ? (string) $count : null;
        } catch (\Throwable) {
            return null;
        }
    }

    public static function getNavigationBadgeColor(): ?string    { return 'danger'; }
    public static function getNavigationBadgeTooltip(): ?string  { return 'Flagged topics'; }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            // ── Main column ──────────────────────────────────────────────
            Group::make([
                Section::make('Topic Content')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Set $set, ?string $state) {
                                $set('slug', Str::slug($state ?? ''));
                            })
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->prefix('forum/')
                            ->helperText('Auto-generated from title. Edit to customise.')
                            ->columnSpanFull(),

                        Forms\Components\RichEditor::make('content')
                            ->required()
                            ->toolbarButtons([
                                'bold',
                                'italic',
                                'strike',
                                'underline',
                                'bulletList',
                                'orderedList',
                                'blockquote',
                                'codeBlock',
                                'h2',
                                'h3',
                                'link',
                                'undo',
                                'redo',
                            ])
                            ->columnSpanFull(),
                    ]),

                Section::make('Tags')
                    ->icon('heroicon-o-tag')
                    ->schema([
                        Forms\Components\TagsInput::make('tags')
                            ->label(false)
                            ->placeholder('Add a tag and press Enter…')
                            ->suggestions([
                                'dating',
                                'relationships',
                                'advice',
                                'question',
                                'long distance',
                                'breakup',
                                'first date',
                                'online dating',
                            ])
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),
            ])
            ->columnSpan(['lg' => 2]),

            // ── Sidebar column ───────────────────────────────────────────
            Group::make([
                Section::make('Author & Category')
                    ->icon('heroicon-o-user')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->relationship('author', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->label('Posted by'),

                        Forms\Components\Select::make('category_id')
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')->required()->maxLength(80)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state ?? ''))),
                                Forms\Components\TextInput::make('slug')->required()->maxLength(80)->unique(),
                                Forms\Components\Textarea::make('description')->rows(2),
                                Forms\Components\TextInput::make('color')->default('#6366f1'),
                                Forms\Components\Toggle::make('requires_verified')->label('Verified users only'),
                            ])
                            ->label('Category'),
                    ]),

                Section::make('Moderation')
                    ->icon('heroicon-o-shield-check')
                    ->schema([
                        Forms\Components\Toggle::make('is_pinned')
                            ->label('Pinned')
                            ->helperText('Keep at the top of the category.'),
                        Forms\Components\Toggle::make('is_locked')
                            ->label('Locked')
                            ->helperText('Prevent new replies.'),
                        Forms\Components\Toggle::make('is_answered')
                            ->label('Answered')
                            ->helperText('Mark as resolved.'),
                        Forms\Components\Toggle::make('is_flagged')
                            ->label('Flagged')
                            ->helperText('Flag for moderation review.'),
                    ]),
            ])
            ->columnSpan(['lg' => 1]),
        ])
        ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->limit(60)
                    ->description(fn (ForumTopic $r) => $r->category?->name),

                Tables\Columns\TextColumn::make('author.name')
                    ->searchable()
                    ->label('Author'),

                Tables\Columns\TextColumn::make('replies_count')
                    ->label('Replies')
                    ->sortable(),

                Tables\Columns\TextColumn::make('views_count')
                    ->label('Views')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\IconColumn::make('is_pinned')
                    ->boolean()
                    ->label('Pinned'),

                Tables\Columns\IconColumn::make('is_locked')
                    ->boolean()
                    ->label('Locked'),

                Tables\Columns\IconColumn::make('is_answered')
                    ->boolean()
                    ->label('Solved')
                    ->toggleable(),

                Tables\Columns\IconColumn::make('is_flagged')
                    ->boolean()
                    ->label('Flagged')
                    ->color(fn (bool $state) => $state ? 'danger' : 'gray'),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('M j, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_flagged')->label('Flagged'),
                Tables\Filters\TernaryFilter::make('is_locked')->label('Locked'),
                Tables\Filters\TernaryFilter::make('is_pinned')->label('Pinned'),
                Tables\Filters\TernaryFilter::make('is_answered')->label('Answered'),
                Tables\Filters\SelectFilter::make('category')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                Actions\Action::make('lock')
                    ->label(fn (ForumTopic $r) => $r->is_locked ? 'Unlock' : 'Lock')
                    ->icon(fn (ForumTopic $r) => $r->is_locked ? 'heroicon-o-lock-open' : 'heroicon-o-lock-closed')
                    ->color(fn (ForumTopic $r) => $r->is_locked ? 'success' : 'warning')
                    ->action(fn (ForumTopic $r) => $r->update(['is_locked' => !$r->is_locked])),
                Actions\Action::make('pin')
                    ->label(fn (ForumTopic $r) => $r->is_pinned ? 'Unpin' : 'Pin')
                    ->icon('heroicon-o-bookmark')
                    ->color(fn (ForumTopic $r) => $r->is_pinned ? 'gray' : 'info')
                    ->action(fn (ForumTopic $r) => $r->update(['is_pinned' => !$r->is_pinned])),
                Actions\Action::make('unflag')
                    ->label('Unflag')
                    ->icon('heroicon-o-flag')
                    ->color('danger')
                    ->visible(fn (ForumTopic $r) => $r->is_flagged)
                    ->action(fn (ForumTopic $r) => $r->update(['is_flagged' => false]))
                    ->requiresConfirmation(),
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    BulkAction::make('bulk_lock')
                        ->label('Lock selected')
                        ->icon('heroicon-o-lock-closed')
                        ->color('warning')
                        ->action(fn ($records) => $records->each(fn (ForumTopic $r) => $r->update(['is_locked' => true])))
                        ->requiresConfirmation(),
                    BulkAction::make('bulk_unlock')
                        ->label('Unlock selected')
                        ->icon('heroicon-o-lock-open')
                        ->color('success')
                        ->action(fn ($records) => $records->each(fn (ForumTopic $r) => $r->update(['is_locked' => false]))),
                    BulkAction::make('bulk_unflag')
                        ->label('Unflag selected')
                        ->icon('heroicon-o-flag')
                        ->color('danger')
                        ->action(fn ($records) => $records->each(fn (ForumTopic $r) => $r->update(['is_flagged' => false])))
                        ->requiresConfirmation(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelationManagers(): array
    {
        return [
            RepliesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListForumTopics::route('/'),
            'create' => CreateForumTopic::route('/create'),
            'edit'   => EditForumTopic::route('/{record}/edit'),
        ];
    }
}
