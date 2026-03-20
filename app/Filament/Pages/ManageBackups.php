<?php

namespace App\Filament\Pages;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ManageBackups extends Page
{
    protected string $view = 'filament.pages.manage-backups';

    public static function getNavigationIcon(): ?string  { return 'heroicon-o-archive-box'; }
    public static function getNavigationLabel(): string  { return 'Backups'; }
    public static function getNavigationGroup(): ?string { return 'System'; }
    public static function getNavigationSort(): ?int     { return 91; }

    public function getTitle(): string | Htmlable { return 'Platform Backups'; }

    /** Only admins may access this page. */
    public static function canAccess(): bool
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        return $user instanceof \App\Models\User && ($user->hasRole('admin') || $user->id === 1);
    }

    // ── Actions ───────────────────────────────────────────────────────────────

    protected function getHeaderActions(): array
    {
        return [
            Action::make('createBackup')
                ->label('Create Backup')
                ->icon('heroicon-o-archive-box-arrow-down')
                ->color('primary')
                ->requiresConfirmation()
                ->modalHeading('Create Platform Backup')
                ->modalDescription(
                    'This will dump the full database and zip all application files ' .
                    '(excluding vendor/, node_modules/, framework cache, and logs). ' .
                    'It may take 1–3 minutes. Do not close this tab until you see the success message.'
                )
                ->modalSubmitActionLabel('Start Backup')
                ->action('createBackup'),
        ];
    }

    // ── Livewire actions ──────────────────────────────────────────────────────

    public function createBackup(): void
    {
        try {
            $exitCode = Artisan::call('backup:create');
            $output   = trim(Artisan::output());

            if ($exitCode === 0) {
                Notification::make()
                    ->title('Backup created successfully')
                    ->body($output)
                    ->success()
                    ->persistent()
                    ->send();
            } else {
                Notification::make()
                    ->title('Backup failed')
                    ->body($output ?: 'Unknown error — check storage/logs/laravel.log')
                    ->danger()
                    ->persistent()
                    ->send();
            }
        } catch (\Throwable $e) {
            Notification::make()
                ->title('Backup error')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function deleteBackup(string $filename): void
    {
        // Sanitize — reject anything that isn't a plain .zip filename
        $filename = basename($filename);
        if (! str_ends_with($filename, '.zip') || str_contains($filename, '/') || str_contains($filename, '\\')) {
            return;
        }

        $path = 'backups/' . $filename;

        if (Storage::disk('local')->exists($path)) {
            Storage::disk('local')->delete($path);
            Notification::make()
                ->title('Backup deleted')
                ->success()
                ->send();
        }
    }

    // ── View helpers ──────────────────────────────────────────────────────────

    /**
     * Returns the list of existing backups sorted newest-first.
     *
     * @return array<int, array{filename: string, size: string, created_at: string}>
     */
    public function getBackups(): array
    {
        $disk  = Storage::disk('local');
        $files = $disk->files('backups');

        $backups = [];
        foreach ($files as $file) {
            if (! str_ends_with($file, '.zip')) {
                continue;
            }

            $backups[] = [
                'filename'   => basename($file),
                'size'       => $this->formatBytes($disk->size($file)),
                'created_at' => \Illuminate\Support\Carbon::createFromTimestamp(
                    $disk->lastModified($file)
                )->format('M j, Y — H:i'),
            ];
        }

        // Newest first (filename contains the timestamp)
        usort($backups, fn($a, $b) => strcmp($b['filename'], $a['filename']));

        return $backups;
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes >= 1_073_741_824) {
            return number_format($bytes / 1_073_741_824, 2) . ' GB';
        }
        if ($bytes >= 1_048_576) {
            return number_format($bytes / 1_048_576, 2) . ' MB';
        }
        if ($bytes >= 1_024) {
            return number_format($bytes / 1_024, 2) . ' KB';
        }
        return $bytes . ' B';
    }
}
