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
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\Action;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Support\Facades\DB;

class WalletWithdrawalRequestResource extends Resource
{
    protected static ?string $model = WalletWithdrawalRequest::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-arrow-down-circle';
    protected static string|\UnitEnum|null $navigationGroup = 'Wallet';
    protected static ?string $label = 'Withdrawal Requests';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')->label('User')->searchable()->sortable(),
                TextColumn::make('user.email')->label('Email')->searchable(),
                TextColumn::make('amount')->label('Credits')->sortable(),
                TextColumn::make('usd_equivalent')
                    ->label('USD Equiv.')
                    ->state(function ($record): string {
                        $rate = (float) SiteSetting::get('credits_per_usd', 10);
                        if ($rate <= 0) return '—';
                        return '$' . number_format($record->amount / $rate, 2) . ' USD';
                    })
                    ->color('success'),
                TextColumn::make('currency')->label('Crypto')->badge()->default('—'),
                TextColumn::make('network')->label('Network')->default('—'),
                TextColumn::make('destination')->label('Destination Address')->copyable()->limit(30),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default    => 'warning',
                    }),
                TextColumn::make('admin_note')->label('Admin Note')->limit(40)->placeholder('—'),
                TextColumn::make('created_at')->dateTime('M d, Y H:i')->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                Action::make('approve')
                    ->label('Approve')
                    ->color('success')
                    ->icon('heroicon-o-check-circle')
                    ->requiresConfirmation()
                    ->modalHeading('Approve Withdrawal')
                    ->modalDescription(function ($record): string {
                        $rate = (float) SiteSetting::get('credits_per_usd', 10);
                        $usd  = $rate > 0 ? number_format($record->amount / $rate, 2) : '?';
                        return "Approve {$record->user->name}'s withdrawal of {$record->amount} credits (≈ \${$usd} USD). Send \${$usd} USD to: {$record->destination} ({$record->currency}" . ($record->network ? " / {$record->network}" : '') . ').';
                    })
                    ->visible(fn ($record) => $record->status === 'pending')
                    ->action(function ($record) {
                        DB::transaction(function () use ($record) {
                            // Credits were already held (deducted) on submission
                            $record->update(['status' => 'approved']);
                        });
                        $fresh = $record->fresh();
                        try {
                            $fresh->user->notify(new WalletWithdrawalProcessedNotification(
                                (int) $fresh->amount,
                                'approved',
                                null,
                                $fresh->destination,
                                $fresh->currency,
                                $fresh->network,
                            ));
                        } catch (\Throwable) {
                            // Mail server unavailable — notification skipped, DB already updated
                        }
                        FilamentNotification::make()
                            ->title("Approved — {$fresh->user->name}'s withdrawal is marked as processing")
                            ->success()
                            ->send();
                    }),

                Action::make('reject')
                    ->label('Reject & Refund')
                    ->color('danger')
                    ->icon('heroicon-o-x-circle')
                    ->requiresConfirmation()
                    ->modalHeading('Reject & Refund')
                    ->schema([
                        \Filament\Forms\Components\Textarea::make('admin_note')
                            ->label('Reason for rejection (shown to user)')
                            ->placeholder('e.g. Invalid address, insufficient confirmations...')
                            ->rows(3)
                            ->required(),
                    ])
                    ->visible(fn ($record) => $record->status === 'pending')
                    ->action(function ($record, array $data) {
                        DB::transaction(function () use ($record, $data) {
                            // Refund the held credits back to the user
                            $record->user()->increment('credit_balance', (int) $record->amount);
                            $record->update([
                                'status'     => 'rejected',
                                'admin_note' => $data['admin_note'],
                            ]);
                        });
                        $fresh = $record->fresh();
                        try {
                            $fresh->user->notify(new WalletWithdrawalProcessedNotification(
                                (int) $fresh->amount,
                                'rejected',
                                $data['admin_note'],
                                $fresh->destination,
                                $fresh->currency,
                                $fresh->network,
                            ));
                        } catch (\Throwable) {
                            // Mail server unavailable — notification skipped, DB already updated
                        }
                        FilamentNotification::make()
                            ->title("Rejected — {$fresh->amount} credits refunded to {$fresh->user->name}")
                            ->warning()
                            ->send();
                    }),
            ]);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Forms\Components\TextInput::make('amount')->required()->numeric()->disabled(),
            Forms\Components\TextInput::make('destination')->required()->disabled(),
            Forms\Components\Select::make('status')
                ->options([
                    'pending'  => 'Pending',
                    'approved' => 'Approved',
                    'rejected' => 'Rejected',
                ])
                ->disabled()
                ->helperText('Use the Approve / Reject actions on the list page to change status.'),
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
