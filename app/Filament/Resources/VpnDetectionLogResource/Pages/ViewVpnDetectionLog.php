<?php

namespace App\Filament\Resources\VpnDetectionLogResource\Pages;

use App\Filament\Resources\VpnDetectionLogResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewVpnDetectionLog extends ViewRecord
{
    protected static string $resource = VpnDetectionLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
