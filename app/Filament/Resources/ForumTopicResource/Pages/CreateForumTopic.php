<?php

namespace App\Filament\Resources\ForumTopicResource\Pages;

use App\Filament\Resources\ForumTopicResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreateForumTopic extends CreateRecord
{
    protected static string $resource = ForumTopicResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['title'] ?? '');
        }
        return $data;
    }
}
