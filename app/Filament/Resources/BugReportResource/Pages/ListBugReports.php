<?php

namespace App\Filament\Resources\BugReportResource\Pages;

use App\Filament\Resources\BugReportResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\CreateAction;

class ListBugReports extends ListRecords
{
    protected static string $resource = BugReportResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
