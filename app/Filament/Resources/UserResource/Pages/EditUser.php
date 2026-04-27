<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Load profile data for the form
        $user = $this->getRecord()->load('profile');
        
        if ($user->profile) {
            $data['profile'] = [
                'city'      => $user->profile->city,
                'state'     => $user->profile->state,
                'country'   => $user->profile->country,
                'latitude'  => $user->profile->latitude,
                'longitude' => $user->profile->longitude,
            ];
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Extract profile data to save separately
        $profileData = $data['profile'] ?? [];
        unset($data['profile']);

        // Save profile data
        if (!empty($profileData)) {
            $user = $this->getRecord();
            
            // Create or update profile
            if ($user->profile) {
                $user->profile->update(array_filter($profileData, fn($value) => $value !== null && $value !== ''));
            } else {
                $user->profile()->create(array_filter($profileData, fn($value) => $value !== null && $value !== ''));
            }
        }

        return $data;
    }
}
