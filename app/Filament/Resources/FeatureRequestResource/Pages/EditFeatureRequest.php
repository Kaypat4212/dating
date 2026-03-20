<?php

namespace App\Filament\Resources\FeatureRequestResource\Pages;

use App\Filament\Resources\FeatureRequestResource;
use App\Models\FeatureRequest;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFeatureRequest extends EditRecord
{
    protected static string $resource = FeatureRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        /** @var FeatureRequest $record */
        $record = $this->getRecord();
        $data   = $this->form->getState();

        if (!empty($data['admin_response']) && $record->wasChanged('admin_response')) {
            if ($record->responded_at === null) {
                $record->update(['responded_at' => now()]);
            }
            FeatureRequestResource::sendReplyEmail($record, $data['admin_response']);
        }
    }
}
