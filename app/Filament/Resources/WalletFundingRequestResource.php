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
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Actions\Action;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Support\Facades\DB;

class WalletFundingRequestResource extends Resource
{
    protected static ?string $model = WalletFundingRequest::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-currency-dollar';
    protected static string|\UnitEnum|null $navigationGroup = 'Wallet';
    protected static ?string $label = 'Funding Requests';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')->label('User')->searchable()->sortable(),
                TextColumn::make('user.email')->label('Email')->searchable(),
                TextColumn::make('amount')->label('Credits')->sortable(),
                TextColumn::make('txid')->label('TXID')->copyable(),
                ImageColumn::make('proof_path')->label('Proof')->disk('public')->imageHeight(60),
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
                    ->modalHeading('Approve Deposit')
                    ->modalDescription(fn ($record) => "Credit {$record->amount} credits to {$record->user->name}?")
                    ->visible(fn ($record) => $record->status === 'pending')
                    ->action(function ($record) {
                        DB::transaction(function () use ($record) {
                            $record->update(['status' => 'approved']);
                            $record->user()->increment('credit_balance', (int) $record->amount);
                        });
                        $fresh = $record->fresh();
                        try {
                            $fresh->user->notify(new WalletFundedNotification(
                                (int) $fresh->amount,
                                $fresh->txid ?? '',
                            ));
                        } catch (\Throwable) {
                            // Mail server unavailable — notification skipped, DB already updated
                        }
                        FilamentNotification::make()
                            ->title("Approved — {$fresh->amount} credits added to {$fresh->user->name}")
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
                            ->placeholder('e.g. Transaction not confirmed, invalid TXID...')
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
                        } catch (\Throwable) {
                            // Mail server unavailable — notification skipped, DB already updated
                        }
                        FilamentNotification::make()
                            ->title("Rejected — {$fresh->user->name} has been notified")
                            ->warning()
                            ->send();
                    }),
            ]);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Forms\Components\TextInput::make('amount')->required()->numeric()->disabled(),
            Forms\Components\TextInput::make('txid')->required()->disabled(),
            Forms\Components\FileUpload::make('proof_path')->disk('public')->image()->disabled(),
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
            'index' => Pages\ListWalletFundingRequests::route('/'),
            'edit'  => Pages\EditWalletFundingRequest::route('/{record}/edit'),
        ];
    }
}
