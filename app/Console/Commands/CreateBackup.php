<?php

namespace App\Console\Commands;

use App\Models\BackupRecord;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use ZipArchive;

class CreateBackup extends Command
{
    protected $signature   = 'backup:create';
    protected $description = 'Create a full platform backup (database dump + application files).';

    public function handle(): int
    {
        $dir = storage_path('app/backups');
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $ts      = now()->format('Y-m-d_H-i-s');
        $zipPath = "{$dir}/backup_{$ts}.zip";
        $sqlFile = sys_get_temp_dir() . "/heartsc_db_{$ts}.sql";

        // ── 1. Database dump ──────────────────────────────────────────────────
        $this->info('Dumping database…');
        try {
            $this->dumpDatabase($sqlFile);
            $this->info('  → ' . number_format(filesize($sqlFile) / 1024, 1) . ' KB written to temp SQL file.');
        } catch (\Throwable $e) {
            $this->error('Database dump failed: ' . $e->getMessage());
            @unlink($sqlFile);
            return self::FAILURE;
        }

        // ── 2. Create zip ─────────────────────────────────────────────────────
        $this->info('Creating zip archive…');
        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            $this->error('Cannot create zip at: ' . $zipPath);
            @unlink($sqlFile);
            return self::FAILURE;
        }

        // SQL dump goes in the archive root
        $zip->addFile($sqlFile, 'database.sql');

        // Application files (excludes vendor/, node_modules/, etc.)
        $base    = base_path();
        $exclude = $this->buildExcludeList($base);
        $this->addDirectoryToZip($zip, $base, $base, $exclude);

        $zip->close();
        @unlink($sqlFile);

        if (Schema::hasTable('backup_records')) {
            BackupRecord::updateOrCreate(
                ['filename' => basename($zipPath)],
                [
                    'disk' => 'local',
                    'path' => 'backups/' . basename($zipPath),
                    'source' => 'artisan',
                    'status' => 'available',
                    'size_bytes' => filesize($zipPath) ?: null,
                    'file_created_at' => now(),
                    'notes' => 'Backup archive created successfully.',
                ]
            );
        }

        $sizeMb = number_format(filesize($zipPath) / 1_048_576, 2);
        $this->info("Backup created: backup_{$ts}.zip ({$sizeMb} MB)");

        return self::SUCCESS;
    }

    // ── Database dump (pure PHP / PDO — no mysqldump binary needed) ───────────

    private function dumpDatabase(string $sqlFile): void
    {
        $pdo    = DB::connection()->getPdo();
        $dbName = config('database.connections.' . config('database.default') . '.database');

        $fh = fopen($sqlFile, 'w');

        fwrite($fh, "-- HeartsConnect platform backup\n");
        fwrite($fh, "-- Database : {$dbName}\n");
        fwrite($fh, "-- Generated: " . now()->toDateTimeString() . "\n\n");
        fwrite($fh, "SET FOREIGN_KEY_CHECKS=0;\n\n");

        $tables = $pdo->query('SHOW TABLES')->fetchAll(\PDO::FETCH_COLUMN);

        foreach ($tables as $table) {
            // CREATE TABLE
            $createRow = $pdo->query("SHOW CREATE TABLE `{$table}`")->fetch(\PDO::FETCH_NUM);
            fwrite($fh, "-- Table: {$table}\n");
            fwrite($fh, "DROP TABLE IF EXISTS `{$table}`;\n");
            fwrite($fh, $createRow[1] . ";\n\n");

            // Rows in chunks to keep memory low
            $stmt = $pdo->query("SELECT * FROM `{$table}`");
            $batch = [];

            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $batch[] = $row;

                if (count($batch) >= 500) {
                    fwrite($fh, $this->buildInsertStatement($pdo, $table, $batch));
                    $batch = [];
                }
            }

            if (! empty($batch)) {
                fwrite($fh, $this->buildInsertStatement($pdo, $table, $batch));
            }

            fwrite($fh, "\n");
        }

        fwrite($fh, "SET FOREIGN_KEY_CHECKS=1;\n");
        fclose($fh);
    }

    private function buildInsertStatement(\PDO $pdo, string $table, array $rows): string
    {
        $cols   = '`' . implode('`, `', array_keys($rows[0])) . '`';
        $values = [];

        foreach ($rows as $row) {
            $vals = array_map(
                fn($v) => $v === null ? 'NULL' : $pdo->quote((string) $v),
                array_values($row)
            );
            $values[] = '(' . implode(', ', $vals) . ')';
        }

        return "INSERT INTO `{$table}` ({$cols}) VALUES\n"
            . implode(",\n", $values) . ";\n";
    }

    // ── File zipping ──────────────────────────────────────────────────────────

    /**
     * Returns an array of absolute paths to exclude from the zip.
     * Paths are resolved so that str_starts_with comparisons are reliable.
     */
    private function buildExcludeList(string $base): array
    {
        $relative = [
            'vendor',
            'node_modules',
            '.git',
            'storage' . DIRECTORY_SEPARATOR . 'framework',
            'storage' . DIRECTORY_SEPARATOR . 'logs',
            // Exclude backups dir to avoid zipping previous backups inside the new one
            'storage' . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'backups',
        ];

        return array_map(function (string $rel) use ($base): string {
            $full = $base . DIRECTORY_SEPARATOR . $rel;
            return realpath($full) ?: $full;
        }, $relative);
    }

    private function addDirectoryToZip(ZipArchive $zip, string $dir, string $base, array $exclude): void
    {
        try {
            $iter = new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS);
        } catch (\UnexpectedValueException) {
            // Unreadable directory — skip silently
            return;
        }

        foreach ($iter as $item) {
            $realPath = $item->getRealPath() ?: $item->getPathname();

            // Skip any path that starts with an excluded prefix
            foreach ($exclude as $ex) {
                if (str_starts_with($realPath, $ex)) {
                    continue 2;
                }
            }

            if ($item->isDir()) {
                $this->addDirectoryToZip($zip, $item->getPathname(), $base, $exclude);
            } else {
                // zip path: files/path/relative/to/base
                $rel          = ltrim(substr($realPath, strlen($base)), DIRECTORY_SEPARATOR . '/');
                $zipEntryPath = 'files/' . str_replace('\\', '/', $rel);
                $zip->addFile($realPath, $zipEntryPath);
            }
        }
    }
}
