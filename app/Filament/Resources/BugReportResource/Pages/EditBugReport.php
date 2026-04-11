<?php

namespace App\Filament\Resources\BugReportResource\Pages;

use App\Filament\Resources\BugReportResource;
use Filament\Resources\Pages\EditRecord;

class EditBugReport extends EditRecord
{
    protected static string $resource = BugReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\DeleteAction::make(),
        ];
    }
}
