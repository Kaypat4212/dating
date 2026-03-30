<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WalletWithdrawalRequestResource\Pages;
use App\Models\SiteSetting;
use App\Models\WalletWithdrawalRequest;
use App\Notifications\WalletWithdrawalProcessedNotification;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Actions\Action;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class WalletWithdrawalRequestResource extends Resource
{
    protected static ?string $model = WalletWithdrawalRequest::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-arrow-up-circle';
    protected static string|\UnitEnum|null $navigationGroup = 'Wallet';
    protected static ?string $label = 'Withdrawal Requests';
    protected static ?int $navigationSort = 3;

    public static function getNavigationBadge(): ?string
    {
        $count = WalletWithdrawalRequest::where('status', 'pending')->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    public static function table(Table $table): Table
    {
        $rate = max(1, (float) SiteSetting::get('credits_per_usd', 10));

        return $table
            ->columns([
                TextColumn::make('id')->label('#')->sortable(),
                TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => $record->user?->email ?? ''),
                TextColumn::make('amount')
                    ->label('Credits')
                    ->sortable()
                    ->formatStateUsing(fn ($state) => number_format($state) . ' cr')
                    ->color('warning'),
                TextColumn::make('usd_equivalent')
                    ->label('USD Equiv.')
                    ->state(fn ($record) => '$' . number_format($record->amount / $rate, 2))
                    ->color('success'),
                TextColumn::make('currency')
                    ->label('Crypto')
                    ->badge()
                    ->placeholder('—'),
                TextColumn::make('network')
                    ->label('Network')
                    ->placeholder('—'),
                TextColumn::make('destination')
                    ->label('Destination Address')
                    ->copyable()
                    ->limit(28)
                    ->placeholder('—'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default    => 'warning',
                    }),
                TextColumn::make('admin_note')
                    ->label('Admin Note')
                    ->limit(40)
                    ->placeholder('—'),
                TextColumn::make('created_at')
                    ->label('Submitted')
                    ->dateTime('M d, Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending'  => '⏳ Pending',
                        'approved' => '✅ Approved',
                        'rejected' => '❌ Rejected',
                    ]),
                SelectFilter::make('currency')
                    ->options(fn () => WalletWithdrawalRequest::distinct()->pluck('currency', 'currency')->filter()->toArray()),
                Filter::make('today')
                    ->label('Today')
                    ->query(fn (Builder $q) => $q->whereDate('created_at', today())),
                Filter::make('this_week')
                    ->label('This Week')
                    ->query(fn (Builder $q) => $q->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])),
            ])
            ->recordActions([
                Action::make('approve')
                    ->label('Approve & Pay')
                    ->color('success')
                    ->icon('heroicon-o-check-circle')
                    ->requiresConfirmation()
                    ->modalHeading('Approve Withdrawal')
                    ->modalDescription(function ($record) use ($rate): string {
                        $usd = number_format($record->amount / $rate, 2);
                        return "Approve {$record->user->name}'s withdrawal of {$record->amount} credits (≈ \${$usd} USD).\n\nSend \${$usd} to: {$record->destination}" .
                               ($record->currency ? " ({$record->currency}" . ($record->network ? " / {$record->network}" : '') . ')' : '') . '.';
                    })
                    ->visible(fn ($record) => $record->status === 'pending')
                    ->schema([
                        \Filament\Forms\Components\TextInput::make('txid')
                            ->label('Payment TX Hash / Reference (optional)')
                            ->placeholder('Transaction ID after you send payment'),
                    ])
                    ->action(function ($record, array $data) {
                        DB::transaction(function () use ($record, $data) {
                            $record->update([
                                'status' => 'approved',
                                'admin_note' => isset($data['txid']) && $data['txid'] ? 'TX: ' . $data['txid'] : null,
                            ]);
                        });
                        $fresh = $record->fresh();
                        try {
                            $fresh->user->notify(new WalletWithdrawalProcessedNotification(
                                (int) $fresh->amount, 'approved', null, $fresh->destination, $fresh->currency, $fresh->network,
                            ));
                        } catch (\Throwable) {}
                        static::sendTelegramWithdrawal($fresh, 'approved');
                        FilamentNotification::make()
                            ->title("✅ Approved — {$fresh->user->name}'s withdrawal is marked as paid")
                            ->success()
                            ->send();
                    }),

                Action::make('reject')
                    ->label('Reject & Refund')
                    ->color('danger')
                    ->icon('heroicon-o-x-circle')
                    ->requiresConfirmation()
                    ->modalHeading('Reject & Refund Credits')
                    ->schema([
                        \Filament\Forms\Components\Textarea::make('admin_note')
                            ->label('Reason for rejection (shown to user)')
                            ->placeholder('e.g. Invalid address, insufficient confirmations…')
                            ->rows(3)
                            ->required(),
                    ])
                    ->visible(fn ($record) => $record->status === 'pending')
                    ->action(function ($record, array $data) {
                        DB::transaction(function () use ($record, $data) {
                            $record->user()->increment('credit_balance', (int) $record->amount);
                            $record->update(['status' => 'rejected', 'admin_note' => $data['admin_note']]);
                            \App\Models\WalletTransaction::create([
                                'user_id'       => $record->user_id,
                                'type'          => 'admin_credit',
                                'amount'        => (int) $record->amount,
                                'balance_after' => (int) $record->user->fresh()->credit_balance,
                                'reference_id'   => $record->id,
                                'reference_type' => 'withdrawal_request',
                                'description'   => 'Withdrawal rejected — credits refunded: ' . $data['admin_note'],
                            ]);
                        });
                        $fresh = $record->fresh();
                        try {
                            $fresh->user->notify(new WalletWithdrawalProcessedNotification(
                                (int) $fresh->amount, 'rejected', $data['admin_note'], $fresh->destination, $fresh->currency, $fresh->network,
                            ));
                        } catch (\Throwable) {}
                        FilamentNotification::make()
                            ->title("❌ Rejected — {$fresh->amount} credits refunded to {$fresh->user->name}")
                            ->warning()
                            ->send();
                    }),
            ])
            ->toolbarActions([
                BulkAction::make('bulk_approve')
                    ->label('Approve Selected')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Approve Selected Withdrawals')
                    ->modalDescription('This will mark all selected pending withdrawals as approved. You must then send the actual payments manually.')
                    ->action(function (Collection $records) {
                        $count = 0;
                        foreach ($records->where('status', 'pending') as $record) {
                            $record->update(['status' => 'approved']);
                            try {
                                $record->user->notify(new WalletWithdrawalProcessedNotification(
                                    (int)$record->amount, 'approved', null, $record->destination, $record->currency, $record->network
                                ));
                            } catch (\Throwable) {}
                            $count++;
                        }
                        FilamentNotification::make()
                            ->title("✅ Bulk approved {$count} withdrawal(s) — remember to send payments!")
                            ->success()
                            ->send();
                    }),

                BulkAction::make('bulk_reject')
                    ->label('Reject & Refund Selected')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Reject & Refund Selected Withdrawals')
                    ->schema([
                        \Filament\Forms\Components\Textarea::make('admin_note')
                            ->label('Reason (applied to all)')
                            ->rows(2)
                            ->required(),
                    ])
                    ->action(function (Collection $records, array $data) {
                        foreach ($records->where('status', 'pending') as $record) {
                            DB::transaction(function () use ($record, $data) {
                                $record->user()->increment('credit_balance', (int) $record->amount);
                                $record->update(['status' => 'rejected', 'admin_note' => $data['admin_note']]);
                            });
                            try {
                                $record->user->notify(new WalletWithdrawalProcessedNotification(
                                    (int)$record->amount, 'rejected', $data['admin_note'], $record->destination, $record->currency, $record->network
                                ));
                            } catch (\Throwable) {}
                        }
                        FilamentNotification::make()
                            ->title('❌ Bulk rejected and credits refunded')
                            ->warning()
                            ->send();
                    }),
            ]);
    }

    protected static function sendTelegramWithdrawal(WalletWithdrawalRequest $record, string $status): void
    {
        try {
            $tg = app(\App\Services\TelegramNotificationService::class);
            $rate = max(1, (float) SiteSetting::get('credits_per_usd', 10));
            $usd  = number_format($record->amount / $rate, 2);
            $emoji = $status === 'approved' ? '✅' : '❌';
            $tg->send(
                "{$emoji} <b>Withdrawal {$status}</b>\n" .
                "👤 {$record->user->name} ({$record->user->email})\n" .
                "💸 <b>Amount:</b> {$record->amount} credits (≈ \${$usd} USD)\n" .
                "📬 <b>To:</b> " . ($record->destination ?? '—') .
                ($record->currency ? " ({$record->currency}" . ($record->network ? " / {$record->network}" : '') . ')' : '') . "\n" .
                "⏰ " . now()->format('Y-m-d H:i')
            );
        } catch (\Throwable) {}
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Forms\Components\TextInput::make('amount')->required()->numeric()->disabled(),
            Forms\Components\TextInput::make('destination')->required()->disabled(),
            Forms\Components\TextInput::make('currency')->disabled(),
            Forms\Components\TextInput::make('network')->disabled(),
            Forms\Components\Select::make('status')
                ->options(['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected'])
                ->disabled()
                ->helperText('Use the Approve / Reject actions on the list page.'),
            Forms\Components\Textarea::make('admin_note')->disabled(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWalletWithdrawalRequests::route('/'),
            'edit'  => Pages\EditWalletWithdrawalRequest::route('/{record}/edit'),
        ];
    }
}
