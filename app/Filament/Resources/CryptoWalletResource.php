<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CryptoWalletResource\Pages;
use App\Models\CryptoWallet;
use Filament\Forms;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\CreateAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Resources\Resource;

class CryptoWalletResource extends Resource
{
    protected static ?string $model = CryptoWallet::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-currency-dollar';
    protected static string|\UnitEnum|null $navigationGroup = 'Wallet';
    protected static ?string $label = 'Crypto Wallets';
    protected static ?string $navigationLabel = 'Wallet Addresses';
    protected static ?int $navigationSort = 0;

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('currency')->label('Currency')->searchable()->sortable(),
                TextColumn::make('network')->label('Network')->searchable(),
                TextColumn::make('label')->label('Label')->default('—'),
                TextColumn::make('address')->label('Address')->copyable()->limit(40),
                ImageColumn::make('qr_code_path')->label('QR Code')->disk('public')->imageHeight(60)->defaultImageUrl(asset('images/no-qr.png')),
                IconColumn::make('is_active')->label('Active')->boolean(),
                TextColumn::make('sort_order')->label('Order')->sortable(),
            ])
            ->defaultSort('sort_order')
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->headerActions([
                CreateAction::make(),
            ]);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Grid::make(2)->schema([
                Forms\Components\TextInput::make('currency')
                    ->label('Currency (e.g. BTC, ETH, USDT)')
                    ->required()
                    ->maxLength(20),
                Forms\Components\TextInput::make('network')
                    ->label('Network (e.g. Bitcoin Mainnet, ERC-20)')
                    ->maxLength(60),
                Forms\Components\TextInput::make('label')
                    ->label('Display Label')
                    ->maxLength(100),
                Forms\Components\TextInput::make('sort_order')
                    ->label('Sort Order')
                    ->numeric()
                    ->default(0),
            ]),
            Forms\Components\Textarea::make('address')
                ->label('Wallet Address')
                ->required()
                ->rows(2),
            Forms\Components\FileUpload::make('qr_code_path')
                ->label('QR Code Image')
                ->helperText('Upload a QR code image for this wallet address so users can scan it.')
                ->disk('public')
                ->directory('wallet_qr')
                ->image()
                ->imagePreviewHeight('120')
                ->nullable(),
            Forms\Components\Toggle::make('is_active')
                ->label('Active (visible to users)')
                ->default(true),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListCryptoWallets::route('/'),
            'create' => Pages\CreateCryptoWallet::route('/create'),
            'edit'   => Pages\EditCryptoWallet::route('/{record}/edit'),
        ];
    }
}
