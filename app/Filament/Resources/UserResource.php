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
            Section::make('Location')->schema([
                Forms\Components\TextInput::make('profile.city')
                    ->label('City')
                    ->maxLength(100)
                    ->helperText('User\'s city location'),
                Forms\Components\TextInput::make('profile.state')
                    ->label('State/Province')
                    ->maxLength(100)
                    ->helperText('State, province, or region'),
                Forms\Components\TextInput::make('profile.country')
                    ->label('Country')
                    ->maxLength(100)
                    ->helperText('Country name'),
                Forms\Components\TextInput::make('profile.latitude')
                    ->label('Latitude')
                    ->numeric()
                    ->step(0.0000001)
                    ->minValue(-90)
                    ->maxValue(90)
                    ->helperText('Geographic latitude (-90 to 90)'),
                Forms\Components\TextInput::make('profile.longitude')
                    ->label('Longitude')
                    ->numeric()
                    ->step(0.0000001)
                    ->minValue(-180)
                    ->maxValue(180)
                    ->helperText('Geographic longitude (-180 to 180)'),
                Forms\Components\Placeholder::make('location_info')
                    ->label('Location Info')
                    ->content(function ($record) {
                        if (!$record || !$record->profile) {
                            return 'No location set';
                        }
                        $profile = $record->profile;
                        $parts = array_filter([
                            $profile->city,
                            $profile->state,
                            $profile->country,
                        ]);
                        $location = !empty($parts) ? implode(', ', $parts) : 'Not set';
                        $coords = ($profile->latitude && $profile->longitude)
                            ? "Coordinates: {$profile->latitude}, {$profile->longitude}"
                            : 'No coordinates';
                        return "{$location}\n{$coords}";
                    })
                    ->columnSpanFull(),
            ])->columns(2)->collapsible(),
            Section::make('Roles')->schema([
                Forms\Components\Select::make('roles')
                    ->label('Assigned Roles')
                    ->multiple()
                    ->relationship('roles', 'name')
                    ->preload()
                    ->columnSpanFull()
                    ->helperText('admin · user · moderator · blogger · personal_assistant'),
            ])->columns(1),
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
            Section::make('Security & Tracking')->schema([
                Forms\Components\TextInput::make('registration_ip')
                    ->label('Registration IP')
                    ->disabled()
                    ->helperText('IP address used during account creation'),
                Forms\Components\TextInput::make('last_login_ip')
                    ->label('Last Login IP')
                    ->disabled()
                    ->helperText('Most recent login IP address'),
                Forms\Components\DateTimePicker::make('last_login_at')
                    ->label('Last Login')
                    ->disabled()
                    ->helperText('Last successful login timestamp'),
                Forms\Components\DateTimePicker::make('created_at')
                    ->label('Account Created')
                    ->disabled(),
            ])->columns(2)->collapsible(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('primaryPhoto.thumbnail_path')
                    ->label('Photo')
                    ->disk('public')
                    ->defaultImageUrl(fn () => null)
                    ->circular(),
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('email')->searchable(),
                Tables\Columns\TextColumn::make('username')->searchable(),
                Tables\Columns\TextColumn::make('roles.name')
                    ->label('Roles')
                    ->badge()
                    ->color(fn (string $state): string => match($state) {
                        'admin'              => 'danger',
                        'moderator'          => 'warning',
                        'blogger'            => 'info',
                        'personal_assistant' => 'purple',
                        default              => 'gray',
                    })
                    ->separator(','),
                Tables\Columns\IconColumn::make('is_premium')->label('Premium')->boolean(),
                Tables\Columns\IconColumn::make('is_banned')->label('Banned')->boolean(),
                Tables\Columns\IconColumn::make('likes_restricted')->label('Likes Off')->boolean(),
                Tables\Columns\IconColumn::make('profile_complete')->label('Profile Done')->boolean(),
                Tables\Columns\TextColumn::make('credit_balance')->label('Credits')->sortable()->alignRight(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->label('Joined')->sortable(),
                Tables\Columns\TextColumn::make('registration_ip')
                    ->label('Reg. IP')
                    ->searchable()
                    ->copyable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('last_login_ip')
                    ->label('Last IP')
                    ->searchable()
                    ->copyable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('last_login_at')
                    ->label('Last Login')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                        $amount     = (int) $data['amount'];
                        $oldBalance = (int) $record->credit_balance;
                        $record->increment('credit_balance', $amount);
                        \App\Models\WalletTransaction::create([
                            'user_id'       => $record->id,
                            'type'          => 'admin_credit',
                            'amount'        => $amount,
                            'balance_after' => $oldBalance + $amount,
                            'description'   => 'Admin credit: ' . ($data['reason'] ?: 'No reason given'),
                        ]);
                        try {
                            $record->notify(new \App\Notifications\WalletFundedNotification(
                                $amount,
                                'Admin adjustment' . ($data['reason'] ? ': ' . $data['reason'] : ''),
                            ));
                        } catch (\Throwable) {
                            // Mail server unavailable — notification skipped, credits already added
                        }
                        FilamentNotification::make()
                            ->title('+' . $amount . ' credits added to ' . $record->name . '. New balance: ' . $record->fresh()->credit_balance)
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
                        $subtract   = min((int) $data['amount'], $record->credit_balance);
                        $oldBalance = (int) $record->credit_balance;
                        $record->decrement('credit_balance', $subtract);
                        \App\Models\WalletTransaction::create([
                            'user_id'       => $record->id,
                            'type'          => 'admin_debit',
                            'amount'        => $subtract,
                            'balance_after' => max(0, $oldBalance - $subtract),
                            'description'   => 'Admin debit: ' . ($data['reason'] ?: 'No reason given'),
                        ]);
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





