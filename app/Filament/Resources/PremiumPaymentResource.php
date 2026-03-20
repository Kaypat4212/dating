<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PremiumPaymentResource\Pages;
use App\Models\PremiumPayment;
use App\Notifications\PremiumPurchasedNotification;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class PremiumPaymentResource extends Resource
{
    protected static ?string $model = PremiumPayment::class;
    public static function getNavigationIcon(): ?string { return 'heroicon-o-currency-dollar'; }
    public static function getNavigationGroup(): ?string { return 'Members'; }
    public static function getNavigationSort(): ?int { return 2; }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getEloquentQuery()->where('status', 'pending')->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Forms\Components\Select::make('user_id')->relationship('user', 'email')->searchable()->required(),
            Forms\Components\Select::make('plan')->options(['30day' => '1 Month', '90day' => '3 Months', '365day' => '1 Year'])->required(),
            Forms\Components\TextInput::make('amount')->numeric()->prefix('$'),
            Forms\Components\TextInput::make('crypto_currency')->label('Cryptocurrency'),
            Forms\Components\TextInput::make('wallet_address')->label('Wallet Address'),
            Forms\Components\TextInput::make('tx_hash')->label('TX Hash'),
            Forms\Components\FileUpload::make('proof_image')
                ->label('Payment Proof (screenshot / receipt)')
                ->image()
                ->disk('public')
                ->directory('premium-proofs')
                ->visibility('public')
                ->openable()
                ->downloadable()
                ->columnSpanFull(),
            Forms\Components\Select::make('status')
                ->options(['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected'])
                ->required(),
            Forms\Components\Toggle::make('is_upgrade')->label('Upgrade Payment')->disabled()->columnSpanFull(),
            Forms\Components\Select::make('upgrade_from_plan')
                ->label('Upgraded From')
                ->options(['30day' => '1 Month', '90day' => '3 Months', '365day' => '1 Year'])
                ->disabled()
                ->visible(fn ($record) => $record?->is_upgrade),
            Forms\Components\TextInput::make('upgrade_credit')->label('Credit Applied ($)')->numeric()->disabled(),
            Forms\Components\TextInput::make('invoice_number')->label('Invoice #')->disabled(),
            Forms\Components\Textarea::make('admin_note')->label('Admin Note')->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('user.email')->searchable()->label('User'),
                Tables\Columns\TextColumn::make('plan')->formatStateUsing(fn ($state) => match($state) {
                    '30day' => '1 Month', '90day' => '3 Months', '365day' => '1 Year', default => $state,
                }),
                Tables\Columns\IconColumn::make('is_upgrade')
                    ->label('Upgrade')
                    ->boolean()
                    ->trueIcon('heroicon-o-arrow-trending-up')
                    ->falseIcon('heroicon-o-minus')
                    ->trueColor('warning')
                    ->falseColor('gray'),
                Tables\Columns\TextColumn::make('upgrade_from_plan')
                    ->label('From Plan')
                    ->formatStateUsing(fn ($state) => match($state) {
                        '30day' => '1 Month', '90day' => '3 Months', '365day' => '1 Year', null => '—', default => $state,
                    })
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('amount')->money('USD'),
                Tables\Columns\TextColumn::make('upgrade_credit')
                    ->label('Credit')
                    ->formatStateUsing(fn ($state) => $state ? '-$' . number_format($state, 2) : '—')
                    ->color('success'),
                Tables\Columns\TextColumn::make('invoice_number')->label('Invoice #')->searchable()->placeholder('—'),
                Tables\Columns\TextColumn::make('crypto_currency')->label('Crypto'),
                Tables\Columns\TextColumn::make('tx_hash')->limit(20)->label('TX Hash')->tooltip(fn ($record) => $record->tx_hash),
                Tables\Columns\ImageColumn::make('proof_image')
                    ->label('Proof')
                    ->disk('public')
                    ->height(40)
                    ->url(fn ($record) => $record->proof_image ? asset('storage/' . $record->proof_image) : null)
                    ->openUrlInNewTab(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match($state) {
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default    => 'warning',
                    }),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable()->label('Submitted'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options(['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected']),
                Tables\Filters\TernaryFilter::make('is_upgrade')->label('Upgrade Payments'),
            ])
            ->recordActions([
                Actions\Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (PremiumPayment $record): bool => $record->status === 'pending')
                    ->action(function (PremiumPayment $record): void {
                        $record->update(['status' => 'approved', 'approved_by' => Auth::user()?->id, 'approved_at' => now()]);
                        $record->user->setPremium($record->plan);
                        $record->user->notify(new PremiumPurchasedNotification(
                            plan: $record->plan_label,
                            expiresAt: $record->user->premium_expires_at->format('F j, Y'),
                        ));
                        $msg = $record->is_upgrade
                            ? 'Upgrade approved — plan upgraded to ' . $record->plan_label . '.'
                            : 'Payment approved and premium activated.';
                        Notification::make()->title($msg)->success()->send();
                    }),
                Actions\Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (PremiumPayment $record): bool => $record->status === 'pending')
                    ->action(function (PremiumPayment $record): void {
                        $record->update(['status' => 'rejected']);
                        Notification::make()->title('Payment rejected.')->danger()->send();
                    }),
                Actions\EditAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPremiumPayments::route('/'),
            'edit'  => Pages\EditPremiumPayment::route('/{record}/edit'),
        ];
    }
}





