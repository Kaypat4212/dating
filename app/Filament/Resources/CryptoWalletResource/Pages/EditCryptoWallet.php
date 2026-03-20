<?php

namespace App\Filament\Resources\CryptoWalletResource\Pages;

use App\Filament\Resources\CryptoWalletResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCryptoWallet extends EditRecord
{
    protected static string $resource = CryptoWalletResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
