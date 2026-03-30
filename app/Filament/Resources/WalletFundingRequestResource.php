<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WalletFundingRequestResource\Pages;
use App\Models\WalletFundingRequest;
use App\Notifications\WalletFundedNotification;
use App\Notifications\DepositRejectedNotification;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Actions\Action;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class WalletFundingRequestResource extends Resource
{
    protected static ?string $model = WalletFundingRequest::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-arrow-down-circle';
    protected static string|\UnitEnum|null $navigationGroup = 'Wallet';
    protected static ?string $label = 'Deposit Requests';
    protected static ?int $navigationSort = 2;

    public static function getNavigationBadge(): ?string
    {
        $count = WalletFundingRequest::where('status', 'pending')->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function table(Table $table): Table
    {
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
                    ->color('success'),
                TextColumn::make('txid')
                    ->label('TXID / Reference')
                    ->copyable()
                    ->limit(28)
                    ->placeholder('—'),
                ImageColumn::make('proof_path')
                    ->label('Proof')
                    ->disk('public')
                    ->imageHeight(56)
                    ->url(fn ($record) => $record->proof_path ? asset('storage/' . $record->proof_path) : null, shouldOpenInNewTab: true),
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
                Filter::make('today')
                    ->label('Today')
                    ->query(fn (Builder $q) => $q->whereDate('created_at', today())),
                Filter::make('this_week')
                    ->label('This Week')
                    ->query(fn (Builder $q) => $q->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])),
            ])
            ->recordActions([
                Action::make('view_proof')
                    ->label('View Proof')
                    ->icon('heroicon-o-photo')
                    ->color('gray')
                    ->url(fn ($record) => $record->proof_path ? asset('storage/' . $record->proof_path) : '#', shouldOpenInNewTab: true)
                    ->visible(fn ($record) => !empty($record->proof_path)),

                Action::make('approve')
                    ->label('Approve')
                    ->color('success')
                    ->icon('heroicon-o-check-circle')
                    ->requiresConfirmation()
                    ->modalHeading('Approve Deposit')
                    ->modalDescription(fn ($record) => "Credit {$record->amount} credits to {$record->user->name}?")
                    ->visible(fn ($record) => $record->status === 'pending')
                    ->action(function ($record) {
                        DB::transaction(function () use ($record) {
                            $oldBalance = (int) $record->user->credit_balance;
                            $amount     = (int) $record->amount;
                            $record->update(['status' => 'approved']);
                            $record->user()->increment('credit_balance', $amount);
                            \App\Models\WalletTransaction::create([
                                'user_id'        => $record->user_id,
                                'type'           => 'deposit',
                                'amount'         => $amount,
                                'balance_after'  => $oldBalance + $amount,
                                'reference_id'   => $record->id,
                                'reference_type' => 'funding_request',
                                'description'    => 'Crypto deposit approved (TXID: ' . ($record->txid ?? '—') . ')',
                            ]);
                        });
                        $fresh = $record->fresh();
                        try {
                            $fresh->user->notify(new WalletFundedNotification(
                                (int) $fresh->amount,
                                $fresh->txid ?? '',
                            ));
                        } catch (\Throwable) {}
                        static::sendTelegramApproval($fresh, 'approved');
                        FilamentNotification::make()
                            ->title("✅ Approved — {$fresh->amount} credits added to {$fresh->user->name}")
                            ->success()
                            ->send();
                    }),

                Action::make('reject')
                    ->label('Reject')
                    ->color('danger')
                    ->icon('heroicon-o-x-circle')
                    ->requiresConfirmation()
                    ->modalHeading('Reject Deposit')
                    ->schema([
                        \Filament\Forms\Components\Textarea::make('admin_note')
                            ->label('Reason for rejection (shown to user)')
                            ->placeholder('e.g. Transaction not confirmed, invalid TXID…')
                            ->rows(3)
                            ->required(),
                    ])
                    ->visible(fn ($record) => $record->status === 'pending')
                    ->action(function ($record, array $data) {
                        $record->update([
                            'status'     => 'rejected',
                            'admin_note' => $data['admin_note'],
                        ]);
                        $fresh = $record->fresh();
                        try {
                            $fresh->user->notify(new DepositRejectedNotification(
                                (int) $fresh->amount,
                                $fresh->txid ?? '',
                                $data['admin_note'],
                            ));
                        } catch (\Throwable) {}
                        FilamentNotification::make()
                            ->title("❌ Rejected — {$fresh->user->name} has been notified")
                            ->warning()
                            ->send();
                    }),
            ])
            ->toolbarActions([
                // Bulk approve
                BulkAction::make('bulk_approve')
                    ->label('Approve Selected')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Approve Selected Deposits')
                    ->action(function (Collection $records) {
                        $count = 0;
                        foreach ($records->where('status', 'pending') as $record) {
                            DB::transaction(function () use ($record) {
                                $oldBalance = (int) $record->user->credit_balance;
                                $amount     = (int) $record->amount;
                                $record->update(['status' => 'approved']);
                                $record->user()->increment('credit_balance', $amount);
                                \App\Models\WalletTransaction::create([
                                    'user_id'        => $record->user_id,
                                    'type'           => 'deposit',
                                    'amount'         => $amount,
                                    'balance_after'  => $oldBalance + $amount,
                                    'reference_id'   => $record->id,
                                    'reference_type' => 'funding_request',
                                    'description'    => 'Crypto deposit approved (bulk)',
                                ]);
                            });
                            try { $record->user->notify(new WalletFundedNotification((int)$record->amount, $record->txid ?? '')); } catch (\Throwable) {}
                            $count++;
                        }
                        FilamentNotification::make()
                            ->title("✅ Bulk approved {$count} deposit(s)")
                            ->success()
                            ->send();
                    }),

                // Bulk reject
                BulkAction::make('bulk_reject')
                    ->label('Reject Selected')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Reject Selected Deposits')
                    ->schema([
                        \Filament\Forms\Components\Textarea::make('admin_note')
                            ->label('Reason (applied to all)')
                            ->rows(2)
                            ->required(),
                    ])
                    ->action(function (Collection $records, array $data) {
                        foreach ($records->where('status', 'pending') as $record) {
                            $record->update(['status' => 'rejected', 'admin_note' => $data['admin_note']]);
                            try {
                                $record->user->notify(new DepositRejectedNotification(
                                    (int)$record->amount, $record->txid ?? '', $data['admin_note']
                                ));
                            } catch (\Throwable) {}
                        }
                        FilamentNotification::make()
                            ->title('❌ Bulk rejected selected deposits')
                            ->warning()
                            ->send();
                    }),
            ]);
    }

    protected static function sendTelegramApproval(WalletFundingRequest $record, string $status): void
    {
        try {
            /** @var \App\Services\TelegramNotificationService $tg */
            $tg = app(\App\Services\TelegramNotificationService::class);
            $emoji = $status === 'approved' ? '✅' : '❌';
            $tg->send(
                "{$emoji} <b>Deposit {$status}</b>\n" .
                "👤 {$record->user->name} ({$record->user->email})\n" .
                "💳 <b>Amount:</b> {$record->amount} credits\n" .
                "🔑 <b>TXID:</b> " . ($record->txid ?? '—') . "\n" .
                "⏰ " . now()->format('Y-m-d H:i')
            );
        } catch (\Throwable) {}
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Forms\Components\TextInput::make('amount')->required()->numeric()->disabled(),
            Forms\Components\TextInput::make('txid')->disabled(),
            Forms\Components\FileUpload::make('proof_path')->disk('public')->image()->disabled(),
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
            'index' => Pages\ListWalletFundingRequests::route('/'),
            'edit'  => Pages\EditWalletFundingRequest::route('/{record}/edit'),
        ];
    }
}
