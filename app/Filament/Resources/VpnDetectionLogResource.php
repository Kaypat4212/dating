<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VpnDetectionLogResource\Pages;
use App\Models\VpnDetectionLog;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class VpnDetectionLogResource extends Resource
{
    protected static ?string $model = VpnDetectionLog::class;

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-shield-exclamation';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Security';
    }

    public static function getNavigationLabel(): string
    {
        return 'VPN Detections';
    }

    public static function getNavigationSort(): ?int
    {
        return 1;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Detection Information')->schema([
                Forms\Components\TextInput::make('ip_address')
                    ->label('IP Address')
                    ->disabled(),
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->label('User')
                    ->disabled(),
                Forms\Components\Toggle::make('is_vpn')
                    ->label('VPN Detected')
                    ->disabled(),
                Forms\Components\TextInput::make('confidence')
                    ->label('Confidence %')
                    ->disabled(),
                Forms\Components\TextInput::make('provider')
                    ->label('Detected Provider')
                    ->disabled(),
                Forms\Components\Textarea::make('detection_details')
                    ->label('Detection Details')
                    ->disabled()
                    ->rows(5),
                Forms\Components\TextInput::make('action_taken')
                    ->label('Action Taken')
                    ->disabled(),
                Forms\Components\Textarea::make('user_agent')
                    ->label('User Agent')
                    ->disabled()
                    ->rows(2),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Detected At')
                    ->dateTime('M d, Y H:i')
                    ->sortable()
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('ip_address')
                    ->label('IP Address')
                    ->searchable()
                    ->copyable()
                    ->icon('heroicon-o-globe-alt'),
                
                Tables\Columns\IconColumn::make('is_vpn')
                    ->label('VPN')
                    ->boolean()
                    ->trueIcon('heroicon-o-shield-exclamation')
                    ->falseIcon('heroicon-o-shield-check')
                    ->trueColor('danger')
                    ->falseColor('success'),
                
                Tables\Columns\TextColumn::make('confidence')
                    ->label('Confidence')
                    ->formatStateUsing(fn ($state) => "{$state}%")
                    ->badge()
                    ->color(fn ($state) => match(true) {
                        $state >= 80 => 'danger',
                        $state >= 50 => 'warning',
                        $state >= 30 => 'info',
                        default => 'gray',
                    })
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('provider')
                    ->label('Provider')
                    ->searchable()
                    ->limit(30)
                    ->tooltip(function ($record) {
                        return $record->provider;
                    }),
                
                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->url(fn ($record) => $record->user ? route('filament.admin.resources.users.edit', $record->user) : null),
                
                Tables\Columns\TextColumn::make('action_taken')
                    ->label('Action')
                    ->badge()
                    ->color(fn ($state) => match($state) {
                        'blocked' => 'danger',
                        'allowed' => 'success',
                        default => 'gray',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('is_vpn')
                    ->label('VPN Status')
                    ->options([
                        '1' => 'VPN Detected',
                        '0' => 'No VPN',
                    ]),
                
                Tables\Filters\SelectFilter::make('action_taken')
                    ->label('Action')
                    ->options([
                        'blocked' => 'Blocked',
                        'allowed' => 'Allowed',
                        'logged' => 'Logged Only',
                    ]),
                
                Tables\Filters\Filter::make('high_confidence')
                    ->label('High Confidence (80%+)')
                    ->query(fn (Builder $query) => $query->where('confidence', '>=', 80)),
                
                Tables\Filters\Filter::make('created_at')
                    ->schema([
                        Forms\Components\DatePicker::make('from')->label('From Date'),
                        Forms\Components\DatePicker::make('until')->label('Until Date'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from'], fn ($q, $date) => $q->whereDate('created_at', '>=', $date))
                            ->when($data['until'], fn ($q, $date) => $q->whereDate('created_at', '<=', $date));
                    }),
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->poll('30s'); // Auto-refresh every 30 seconds
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVpnDetectionLogs::route('/'),
            'view' => Pages\ViewVpnDetectionLog::route('/{record}'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('is_vpn', true)
            ->where('created_at', '>=', now()->subDays(7))
            ->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $count = (int) static::getNavigationBadge();
        return $count > 10 ? 'danger' : ($count > 0 ? 'warning' : null);
    }
}
