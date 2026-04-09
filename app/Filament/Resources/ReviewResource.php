<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReviewResource\Pages;
use App\Models\Review;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\DeleteBulkAction;

class ReviewResource extends Resource
{
    protected static ?string $model = Review::class;

    public static function getNavigationIcon(): ?string  { return 'heroicon-o-star'; }
    public static function getNavigationGroup(): ?string { return 'Moderation'; }
    public static function getNavigationLabel(): string  { return 'Reviews'; }
    public static function getNavigationSort(): ?int     { return 5; }

    public static function getNavigationBadge(): ?string
    {
        try {
            $count = static::getEloquentQuery()->where('status', 'pending')->count();
            return $count > 0 ? (string) $count : null;
        } catch (\Throwable) {
            return null;
        }
    }

    public static function getNavigationBadgeColor(): ?string { return 'warning'; }
    public static function getNavigationBadgeTooltip(): ?string { return 'Pending reviews awaiting approval'; }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Review Details')->schema([
                Forms\Components\TextInput::make('author_name')
                    ->label('Author')
                    ->disabled()
                    ->formatStateUsing(fn (Review $record) => $record->author_name),

                Forms\Components\TextInput::make('guest_email')
                    ->label('Guest Email')
                    ->disabled()
                    ->hidden(fn (Review $record) => $record->user_id !== null),

                Forms\Components\TextInput::make('rating')
                    ->label('Rating')
                    ->disabled()
                    ->suffix('/ 5'),

                Forms\Components\TextInput::make('title')
                    ->label('Title')
                    ->disabled(),

                Forms\Components\Textarea::make('body')
                    ->label('Review')
                    ->disabled()
                    ->rows(6)
                    ->columnSpanFull(),
            ])->columns(2),

            Section::make('Moderation')->schema([
                Forms\Components\Select::make('status')
                    ->options([
                        'pending'  => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ])
                    ->required()
                    ->native(false),

                Forms\Components\Textarea::make('admin_note')
                    ->label('Admin Note (private)')
                    ->rows(3)
                    ->columnSpanFull(),
            ])->columns(1),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('#')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('author_name')
                    ->label('Author')
                    ->getStateUsing(fn (Review $record) => $record->author_name)
                    ->searchable(query: function ($query, string $search) {
                        $query->where('guest_name', 'like', "%{$search}%")
                              ->orWhereHas('user', fn ($q) => $q->where('name', 'like', "%{$search}%"));
                    }),

                Tables\Columns\TextColumn::make('rating')
                    ->label('⭐')
                    ->formatStateUsing(fn (int $state) => str_repeat('★', $state) . str_repeat('☆', 5 - $state))
                    ->sortable(),

                Tables\Columns\TextColumn::make('title')
                    ->label('Title')
                    ->limit(40)
                    ->placeholder('(no title)')
                    ->searchable(),

                Tables\Columns\TextColumn::make('body')
                    ->label('Review')
                    ->limit(80)
                    ->wrap(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'approved' => 'success',
                        'pending'  => 'warning',
                        'rejected' => 'danger',
                        default    => 'gray',
                    }),

                Tables\Columns\TextColumn::make('helpful_count')
                    ->label('👍 Helpful')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Submitted')
                    ->since()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending'  => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ])
                    ->default('pending'),

                Tables\Filters\SelectFilter::make('rating')
                    ->options([5 => '★★★★★', 4 => '★★★★', 3 => '★★★', 2 => '★★', 1 => '★']),
            ])
            ->recordActions([
                Actions\Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (Review $record) => $record->status !== 'approved')
                    ->action(function (Review $record): void {
                        $record->update(['status' => 'approved']);
                        Notification::make()->title('Review approved and published.')->success()->send();
                    }),

                Actions\Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (Review $record) => $record->status !== 'rejected')
                    ->action(function (Review $record): void {
                        $record->update(['status' => 'rejected']);
                        Notification::make()->title('Review rejected.')->warning()->send();
                    }),

                Actions\EditAction::make()->label('Note'),
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
            'index' => Pages\ListReviews::route('/'),
            'edit'  => Pages\EditReview::route('/{record}/edit'),
        ];
    }
}
