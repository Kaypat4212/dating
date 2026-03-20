<?php

namespace App\Filament\Resources\EmailTemplateResource\Pages;

use App\Filament\Resources\EmailTemplateResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditEmailTemplate extends EditRecord
{
    protected static string $resource = EmailTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('preview')
                ->label('Preview Email')
                ->icon('heroicon-o-eye')
                ->color('gray')
                ->url(fn () => route('admin.email-templates.preview', $this->record))
                ->openUrlInNewTab(),
        ];
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->title('Email template saved!')
            ->body('Changes will take effect on the next email send.')
            ->success();
    }
}
