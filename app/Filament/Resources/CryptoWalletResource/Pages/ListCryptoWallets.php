<?php

namespace App\Filament\Resources\CryptoWalletResource\Pages;

use App\Filament\Resources\CryptoWalletResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCryptoWallets extends ListRecords
{
    protected static string $resource = CryptoWalletResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
