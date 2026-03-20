<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TipResource\Pages;
use App\Models\Tip;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;

class TipResource extends Resource
{
    protected static ?string $model = Tip::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-gift';
    protected static string|\UnitEnum|null $navigationGroup = 'Wallet';
    protected static ?string $label = 'Tips';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sender.name')->label('From')->searchable()->sortable(),
                TextColumn::make('sender.email')->label('Sender Email')->searchable(),
                TextColumn::make('recipient.name')->label('To')->searchable()->sortable(),
                TextColumn::make('recipient.email')->label('Recipient Email')->searchable(),
                TextColumn::make('amount')->label('Credits')->sortable(),
                TextColumn::make('message')->label('Message')->limit(50)->placeholder('—'),
                TextColumn::make('created_at')->label('Sent At')->dateTime('M d, Y H:i')->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([])
            ->toolbarActions([]);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTips::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
