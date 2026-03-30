<?php

namespace App\Filament\Resources\ForumCategoryResource\Pages;

use App\Filament\Resources\ForumCategoryResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions;

class ListForumCategories extends ListRecords
{
    protected static string $resource = ForumCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
