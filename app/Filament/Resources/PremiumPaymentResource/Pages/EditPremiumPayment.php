<?php

namespace App\Filament\Resources\PremiumPaymentResource\Pages;

use App\Filament\Resources\PremiumPaymentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPremiumPayment extends EditRecord
{
    protected static string $resource = PremiumPaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
