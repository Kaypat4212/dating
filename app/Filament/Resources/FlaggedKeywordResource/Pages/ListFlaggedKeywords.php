<?php

namespace App\Filament\Resources\FlaggedKeywordResource\Pages;

use App\Filament\Resources\FlaggedKeywordResource;
use Filament\Resources\Pages\ListRecords;

class ListFlaggedKeywords extends ListRecords
{
    protected static string $resource = FlaggedKeywordResource::class;
}
