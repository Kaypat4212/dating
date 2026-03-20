<?php

namespace App\Filament\Resources\VerificationResource\Pages;

use App\Filament\Resources\VerificationResource;
use App\Models\UserVerification;
use App\Notifications\VerificationApprovedNotification;
use App\Notifications\VerificationRejectedNotification;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Auth;

class ViewVerification extends ViewRecord
{
    protected static string $resource = VerificationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('approve')
                ->label('Approve ✅')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->modalDescription('This will grant the Verified badge to the user and send them a notification.')
                ->visible(fn () => $this->record->status !== 'approved')
                ->action(function () {
                    $this->record->update([
                        'status'      => 'approved',
                        'reviewed_by' => Auth::id(),
                        'reviewed_at' => now(),
                    ]);
                    $this->record->user->update(['is_verified' => true]);
                    $this->record->user->notify(new VerificationApprovedNotification());

                    Notification::make()
                        ->title('Verification approved')
                        ->success()
                        ->send();

                    $this->refreshFormData(['status', 'reviewed_at']);
                }),

            Actions\Action::make('reject')
                ->label('Reject ❌')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->schema([
                    Forms\Components\Textarea::make('reason')
                        ->label('Reason for rejection (will be shown to the user)')
                        ->rows(3),
                ])
                ->visible(fn () => $this->record->status !== 'rejected')
                ->action(function (array $data) {
                    $this->record->update([
                        'status'      => 'rejected',
                        'admin_notes' => $data['reason'] ?? null,
                        'reviewed_by' => Auth::id(),
                        'reviewed_at' => now(),
                    ]);
                    $this->record->user->update(['is_verified' => false]);
                    $this->record->user->notify(new VerificationRejectedNotification($data['reason'] ?? null));

                    Notification::make()
                        ->title('Verification rejected')
                        ->warning()
                        ->send();

                    $this->refreshFormData(['status', 'admin_notes', 'reviewed_at']);
                }),
        ];
    }
}
