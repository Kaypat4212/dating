<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WalletTransactionResource\Pages;
use App\Models\WalletTransaction;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class WalletTransactionResource extends Resource
{
    protected static ?string $model = WalletTransaction::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-receipt-percent';
    protected static string|\UnitEnum|null $navigationGroup = 'Wallet';
    protected static ?string $label = 'Transaction Log';
    protected static ?int $navigationSort = 4;

    public static function canCreate(): bool { return false; }
    public static function canEdit($record): bool { return false; }
    public static function canDelete($record): bool { return false; }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('#')->sortable(),
                TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->sortable()
                    ->url(fn ($record) => $record->user_id ? url('/admin/users/' . $record->user_id . '/edit') : null),
                TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'deposit'      => '💳 Deposit',
                        'withdrawal'   => '🏧 Withdrawal',
                        'tip_sent'     => '💸 Gift Sent',
                        'tip_received' => '🎁 Gift Received',
                        'admin_credit' => '⬆ Admin Credit',
                        'admin_debit'  => '⬇ Admin Debit',
                        default        => ucfirst(str_replace('_', ' ', $state)),
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'deposit', 'tip_received', 'admin_credit' => 'success',
                        'withdrawal', 'tip_sent', 'admin_debit'   => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('amount')
                    ->label('Amount')
                    ->sortable()
                    ->formatStateUsing(fn ($state, $record): string =>
                        ($record->isCredit() ? '+' : '-') . number_format($state) . ' cr'
                    )
                    ->color(fn ($record): string => $record->isCredit() ? 'success' : 'danger'),
                TextColumn::make('balance_after')
                    ->label('Balance After')
                    ->sortable()
                    ->formatStateUsing(fn ($state): string => number_format($state) . ' cr')
                    ->color('gray'),
                TextColumn::make('description')
                    ->label('Description')
                    ->limit(55)
                    ->placeholder('—'),
                TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('M d, Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('type')
                    ->options([
                        'deposit'      => '💳 Deposit',
                        'withdrawal'   => '🏧 Withdrawal',
                        'tip_sent'     => '💸 Gift Sent',
                        'tip_received' => '🎁 Gift Received',
                        'admin_credit' => '⬆ Admin Credit',
                        'admin_debit'  => '⬇ Admin Debit',
                    ]),
                Filter::make('today')
                    ->label('Today')
                    ->query(fn (Builder $query): Builder => $query->whereDate('created_at', today())),
                Filter::make('this_month')
                    ->label('This Month')
                    ->query(fn (Builder $query): Builder => $query->whereMonth('created_at', now()->month)),
                Filter::make('credits_only')
                    ->label('Credits Only')
                    ->query(fn (Builder $query): Builder => $query->whereIn('type', ['deposit', 'admin_credit', 'tip_received'])),
                Filter::make('debits_only')
                    ->label('Debits Only')
                    ->query(fn (Builder $query): Builder => $query->whereIn('type', ['withdrawal', 'admin_debit', 'tip_sent'])),
            ])
            ->searchable();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWalletTransactions::route('/'),
        ];
    }
}
