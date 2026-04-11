<?php

namespace App\Filament\Pages;

use App\Models\BackupRecord;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
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

            Action::make('syncBackupRecords')
                ->label('Sync Records')
                ->icon('heroicon-o-arrow-path')
                ->color('gray')
                ->action(function (): void {
                    $count = $this->syncBackupRecords();

                    Notification::make()
                        ->title('Backup records synced')
                        ->body("{$count} backup record(s) refreshed from storage.")
                        ->success()
                        ->send();
                }),
        ];
    }

    // ── Livewire actions ──────────────────────────────────────────────────────

    public function createBackup(): void
    {
        try {
            $exitCode = Artisan::call('backup:create');
            $output   = trim(Artisan::output());

            $this->syncBackupRecords();

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

    public function restoreBackup(string $filename): void
    {
        $filename = basename($filename);
        if (! str_ends_with($filename, '.zip') || str_contains($filename, '/') || str_contains($filename, '\\')) {
            return;
        }

        try {
            $exitCode = Artisan::call('backup:restore', [
                'filename' => $filename,
                '--force' => true,
            ]);

            $output = trim(Artisan::output());

            if (Schema::hasTable('backup_records')) {
                BackupRecord::where('filename', $filename)->update([
                    'restored_at' => now(),
                    'restored_by' => Auth::id(),
                    'status' => $exitCode === 0 ? 'restored' : 'restore_failed',
                    'notes' => $output ?: null,
                ]);
            }

            Notification::make()
                ->title($exitCode === 0 ? 'Backup restored' : 'Restore failed')
                ->body($output ?: 'No restore output available.')
                ->color($exitCode === 0 ? 'success' : 'danger')
                ->persistent()
                ->send();
        } catch (\Throwable $e) {
            Notification::make()
                ->title('Restore error')
                ->body($e->getMessage())
                ->danger()
                ->persistent()
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

            if (Schema::hasTable('backup_records')) {
                BackupRecord::updateOrCreate(
                    ['filename' => $filename],
                    [
                        'disk' => 'local',
                        'path' => $path,
                        'status' => 'deleted',
                        'notes' => 'Backup deleted from the admin page.',
                    ]
                );
            }

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
        $this->syncBackupRecords();

        if (! Schema::hasTable('backup_records')) {
            return [];
        }

        return BackupRecord::query()
            ->orderByRaw("CASE WHEN status IN ('available', 'restored') THEN 0 ELSE 1 END")
            ->orderByDesc('file_created_at')
            ->orderByDesc('created_at')
            ->get()
            ->map(function (BackupRecord $backup): array {
                $statusTone = match ($backup->status) {
                    'available' => 'ok',
                    'restored' => 'ok',
                    'restore_failed' => 'danger',
                    'missing' => 'warn',
                    'deleted' => 'muted',
                    default => 'info',
                };

                $createdAt = $backup->file_created_at ?? $backup->created_at;

                return [
                    'filename' => $backup->filename,
                    'size' => $backup->size_bytes ? $this->formatBytes($backup->size_bytes) : '—',
                    'size_bytes' => $backup->size_bytes ?? 0,
                    'created_at' => $createdAt ? $createdAt->format('M j, Y — H:i') : 'Unknown',
                    'status' => $backup->status,
                    'status_label' => str($backup->status)->replace('_', ' ')->title()->toString(),
                    'status_tone' => $statusTone,
                    'source' => str($backup->source)->title()->toString(),
                    'last_restored_at' => $backup->restored_at?->format('M j, Y — H:i'),
                    'notes' => $backup->notes,
                    'can_download' => in_array($backup->status, ['available', 'restored'], true),
                    'can_restore' => in_array($backup->status, ['available', 'restored'], true),
                ];
            })
            ->all();
    }

    public function syncBackupRecords(): int
    {
        if (! Schema::hasTable('backup_records')) {
            return 0;
        }

        $disk = Storage::disk('local');
        $files = collect($disk->files('backups'))
            ->filter(fn (string $file): bool => str_ends_with($file, '.zip'));

        $synced = 0;

        foreach ($files as $file) {
            $filename = basename($file);

            BackupRecord::updateOrCreate(
                ['filename' => $filename],
                [
                    'disk' => 'local',
                    'path' => $file,
                    'source' => BackupRecord::query()->where('filename', $filename)->value('source') ?? 'scan',
                    'status' => 'available',
                    'size_bytes' => $disk->size($file),
                    'file_created_at' => Carbon::createFromTimestamp($disk->lastModified($file)),
                ]
            );

            $synced++;
        }

        BackupRecord::query()
            ->whereNotIn('filename', $files->map(fn (string $file): string => basename($file))->all())
            ->whereNotIn('status', ['deleted', 'restore_failed'])
            ->update([
                'status' => 'missing',
                'notes' => 'Backup file is no longer present on disk.',
            ]);

        return $synced;
    }

    public function formatBytes(int $bytes): string
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
