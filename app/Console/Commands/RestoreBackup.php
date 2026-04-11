<?php

namespace App\Console\Commands;

use App\Models\BackupRecord;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use ZipArchive;

class RestoreBackup extends Command
{
    protected $signature = 'backup:restore {filename : The backup zip filename inside storage/app/backups} {--force : Confirm the restore operation}';

    protected $description = 'Restore the platform database and files from a backup archive.';

    public function handle(): int
    {
        if (! $this->option('force')) {
            $this->error('Restore requires --force because it overwrites the current database and files.');
            return self::FAILURE;
        }

        $filename = basename((string) $this->argument('filename'));
        if (! str_ends_with($filename, '.zip') || str_contains($filename, '/') || str_contains($filename, '\\')) {
            $this->error('Invalid backup filename.');
            return self::FAILURE;
        }

        $zipPath = storage_path('app/backups/' . $filename);
        if (! is_file($zipPath)) {
            $this->error('Backup archive not found: ' . $filename);
            return self::FAILURE;
        }

        $extractDir = storage_path('app/backup-restores/' . pathinfo($filename, PATHINFO_FILENAME) . '_' . now()->format('YmdHis'));
        if (! is_dir($extractDir) && ! mkdir($extractDir, 0755, true) && ! is_dir($extractDir)) {
            $this->error('Unable to create restore workspace.');
            return self::FAILURE;
        }

        $zip = new ZipArchive();
        if ($zip->open($zipPath) !== true) {
            $this->cleanupDirectory($extractDir);
            $this->error('Unable to open backup archive.');
            return self::FAILURE;
        }

        if (! $zip->extractTo($extractDir)) {
            $zip->close();
            $this->cleanupDirectory($extractDir);
            $this->error('Unable to extract backup archive.');
            return self::FAILURE;
        }

        $zip->close();

        $sqlPath = $extractDir . DIRECTORY_SEPARATOR . 'database.sql';
        $filesDir = $extractDir . DIRECTORY_SEPARATOR . 'files';

        try {
            if (is_file($sqlPath)) {
                $this->info('Restoring database...');
                $sql = file_get_contents($sqlPath);
                if ($sql === false) {
                    throw new \RuntimeException('Unable to read extracted database.sql');
                }
                DB::unprepared($sql);
            }

            if (is_dir($filesDir)) {
                $this->info('Restoring application files...');
                $this->restoreFiles($filesDir, base_path());
            }

            if (Schema::hasTable('backup_records')) {
                BackupRecord::where('filename', $filename)->update([
                    'status' => 'restored',
                    'restored_at' => now(),
                    'notes' => 'Backup restored successfully via admin or artisan command.',
                ]);
            }

            $this->info('Restore completed successfully.');
            return self::SUCCESS;
        } catch (\Throwable $e) {
            if (Schema::hasTable('backup_records')) {
                BackupRecord::where('filename', $filename)->update([
                    'status' => 'restore_failed',
                    'notes' => 'Restore failed: ' . $e->getMessage(),
                ]);
            }

            $this->error('Restore failed: ' . $e->getMessage());
            return self::FAILURE;
        } finally {
            $this->cleanupDirectory($extractDir);
        }
    }

    private function restoreFiles(string $sourceDir, string $targetDir): void
    {
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($sourceDir, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $item) {
            $relativePath = ltrim(substr($item->getPathname(), strlen($sourceDir)), DIRECTORY_SEPARATOR . '/');

            if ($this->shouldSkipRestorePath($relativePath)) {
                continue;
            }

            $destination = $targetDir . DIRECTORY_SEPARATOR . $relativePath;

            if ($item->isDir()) {
                if (! is_dir($destination)) {
                    mkdir($destination, 0755, true);
                }
                continue;
            }

            $destinationDir = dirname($destination);
            if (! is_dir($destinationDir)) {
                mkdir($destinationDir, 0755, true);
            }

            copy($item->getPathname(), $destination);
        }
    }

    private function shouldSkipRestorePath(string $relativePath): bool
    {
        $normalized = str_replace('\\', '/', $relativePath);

        if ($normalized === '.env' || str_starts_with($normalized, '.git/')) {
            return true;
        }

        foreach ([
            'vendor/',
            'node_modules/',
            'storage/framework/',
            'storage/logs/',
            'storage/app/backups/',
            'storage/app/backup-restores/',
        ] as $prefix) {
            if (str_starts_with($normalized, $prefix)) {
                return true;
            }
        }

        return false;
    }

    private function cleanupDirectory(string $directory): void
    {
        if (! is_dir($directory)) {
            return;
        }

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($directory, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($iterator as $item) {
            if ($item->isDir()) {
                @rmdir($item->getPathname());
            } else {
                @unlink($item->getPathname());
            }
        }

        @rmdir($directory);
    }
}