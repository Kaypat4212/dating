<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Actions;
use Filament\Forms;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions\DeleteBulkAction;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Support\Facades\Auth;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    public static function getNavigationIcon(): ?string { return 'heroicon-o-users'; }
    public static function getNavigationGroup(): ?string { return 'Members'; }
    public static function getNavigationSort(): ?int { return 1; }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Account')->schema([
                Forms\Components\TextInput::make('name')->required(),
                Forms\Components\TextInput::make('email')->email()->required(),
                Forms\Components\TextInput::make('username'),
                Forms\Components\Select::make('gender')->options(['male' => 'Male', 'female' => 'Female', 'non_binary' => 'Non-binary', 'other' => 'Other']),
                Forms\Components\Select::make('seeking')->options(['male' => 'Male', 'female' => 'Female', 'everyone' => 'Everyone']),
                Forms\Components\DatePicker::make('date_of_birth')->label('Date of Birth'),
            ])->columns(2),
            Section::make('Moderation')->schema([
                Forms\Components\Toggle::make('is_premium')->label('Premium'),
                Forms\Components\DateTimePicker::make('premium_expires_at')->label('Premium Expires'),
                Forms\Components\Toggle::make('is_banned')->label('Banned'),
                Forms\Components\Textarea::make('banned_reason')->label('Ban Reason')->columnSpanFull(),
                Forms\Components\Toggle::make('likes_restricted')
                    ->label('Restrict Likes')
                    ->helperText('Prevent this user from sending likes/swipes right.'),
                Forms\Components\Toggle::make('swipes_restricted')
                    ->label('Restrict Swipe Deck')
                    ->helperText('Prevent this user from viewing the swipe deck.'),
                Forms\Components\Toggle::make('profile_complete')->label('Profile Complete'),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('primaryPhoto.thumbnail_path')
                    ->label('Photo')
                    ->defaultImageUrl(fn () => null)
                    ->circular(),
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('email')->searchable(),
                Tables\Columns\TextColumn::make('username')->searchable(),
                Tables\Columns\IconColumn::make('is_premium')->label('Premium')->boolean(),
                Tables\Columns\IconColumn::make('is_banned')->label('Banned')->boolean(),
                Tables\Columns\IconColumn::make('likes_restricted')->label('Likes Off')->boolean(),
                Tables\Columns\IconColumn::make('profile_complete')->label('Profile Done')->boolean(),
                Tables\Columns\TextColumn::make('credit_balance')->label('Credits')->sortable()->alignRight(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->label('Joined')->sortable(),
            ])
            ->filters([
                Tables\Filters\Filter::make('premium')->query(fn ($query) => $query->where('is_premium', true)),
                Tables\Filters\Filter::make('banned')->query(fn ($query) => $query->where('is_banned', true)),
                Tables\Filters\Filter::make('likes_restricted')->label('Likes Restricted')->query(fn ($query) => $query->where('likes_restricted', true)),
            ])
            ->recordActions([
                Actions\EditAction::make(),
                Actions\Action::make('restrictLikes')
                    ->label('Restrict Likes')
                    ->icon('heroicon-o-hand-raised')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->visible(fn (User $record): bool => ! $record->likes_restricted)
                    ->action(fn (User $record) => $record->update(['likes_restricted' => true])),
                Actions\Action::make('unrestrictLikes')
                    ->label('Restore Likes')
                    ->icon('heroicon-o-heart')
                    ->color('success')
                    ->visible(fn (User $record): bool => (bool) $record->likes_restricted)
                    ->action(fn (User $record) => $record->update(['likes_restricted' => false])),
                Actions\Action::make('ban')
                    ->label('Ban')
                    ->icon('heroicon-o-no-symbol')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (User $record): bool => ! $record->is_banned)
                    ->action(fn (User $record) => $record->update(['is_banned' => true])),
                Actions\Action::make('unban')
                    ->label('Unban')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (User $record): bool => (bool) $record->is_banned)
                    ->action(fn (User $record) => $record->update(['is_banned' => false, 'banned_reason' => null])),
                Actions\Action::make('loginAsUser')
                    ->label('Login as User')
                    ->icon('heroicon-o-arrow-right-on-rectangle')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Login as User')
                    ->modalDescription(fn (User $record) => 'You will be logged in as "' . $record->name . '". A banner will let you return to admin.')
                    ->modalSubmitActionLabel('Yes, Login as this User')
                    ->action(function (User $record) {
                        session(['impersonating_id' => Auth::id()]);
                        Auth::login($record);
                        return redirect()->route('dashboard');
                    }),
                Actions\Action::make('addCredits')
                    ->label('Add Credits')
                    ->icon('heroicon-o-plus-circle')
                    ->color('success')
                    ->modalHeading(fn (User $record) => 'Add Credits — ' . $record->name)
                    ->schema([
                        Forms\Components\TextInput::make('amount')
                            ->label('Credits to Add')
                            ->numeric()
                            ->minValue(1)
                            ->required()
                            ->helperText(fn (User $record) => 'Current balance: ' . $record->credit_balance . ' credits'),
                        Forms\Components\Textarea::make('reason')
                            ->label('Admin Note / Reason')
                            ->placeholder('e.g. Bonus, compensation, promotion…')
                            ->rows(2),
                    ])
                    ->action(function (User $record, array $data) {
                        $record->increment('credit_balance', (int) $data['amount']);
                        try {
                            $record->notify(new \App\Notifications\WalletFundedNotification(
                                (int) $data['amount'],
                                'Admin adjustment' . ($data['reason'] ? ': ' . $data['reason'] : ''),
                            ));
                        } catch (\Throwable) {
                            // Mail server unavailable — notification skipped, credits already added
                        }
                        FilamentNotification::make()
                            ->title('+' . $data['amount'] . ' credits added to ' . $record->name . '. New balance: ' . $record->fresh()->credit_balance)
                            ->success()
                            ->send();
                    }),
                Actions\Action::make('subtractCredits')
                    ->label('Subtract Credits')
                    ->icon('heroicon-o-minus-circle')
                    ->color('danger')
                    ->modalHeading(fn (User $record) => 'Subtract Credits — ' . $record->name)
                    ->schema([
                        Forms\Components\TextInput::make('amount')
                            ->label('Credits to Subtract')
                            ->numeric()
                            ->minValue(1)
                            ->required()
                            ->helperText(fn (User $record) => 'Current balance: ' . $record->credit_balance . ' credits'),
                        Forms\Components\Textarea::make('reason')
                            ->label('Admin Note / Reason')
                            ->placeholder('e.g. Chargeback, penalty, correction…')
                            ->rows(2),
                    ])
                    ->action(function (User $record, array $data) {
                        $subtract = min((int) $data['amount'], $record->credit_balance);
                        $record->decrement('credit_balance', $subtract);
                        FilamentNotification::make()
                            ->title('-' . $subtract . ' credits removed from ' . $record->name . '. New balance: ' . $record->fresh()->credit_balance)
                            ->warning()
                            ->send();
                    }),
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
            'index' => Pages\ListUsers::route('/'),
            'edit'  => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}





