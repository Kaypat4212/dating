<?php
/**
 * Standalone production fix script — requires NO vendor/Laravel.
 * Works even when the app is completely broken.
 *
 * 1. Upload this file to your public/ folder via cPanel File Manager.
 * 2. Visit: https://heartsconnect.site/deploy-fix.php?token=HC_DEPLOY_2026
 * 3. Confirm all items show ✅
 * 4. DELETE this file immediately after.
 */

if (($_GET['token'] ?? '') !== 'HC_DEPLOY_2026') {
    http_response_code(403);
    die('<h2 style="font-family:monospace;color:red">403 – wrong token</h2>');
}

$results = [];
$base    = dirname(__DIR__); // Laravel project root (one level above public/)

// ── 1. Fix public/.htaccess — remove Options +FollowSymLinks ────────────────
$htaccessPath = __DIR__ . DIRECTORY_SEPARATOR . '.htaccess';
if (file_exists($htaccessPath)) {
    $orig = file_get_contents($htaccessPath);
    // Remove the three offending lines
    $fixed = preg_replace('/^#[^\n]*FollowSymLinks[^\n]*\n?/m', '', $orig);
    $fixed = preg_replace('/^#[^\n]*Required for cPanel[^\n]*\n?/m', '', $fixed);
    $fixed = preg_replace('/^Options \+FollowSymLinks[^\n]*\n?/m', '', $fixed);
    $fixed = preg_replace('/\n{3,}/', "\n\n", ltrim($fixed, "\n"));
    if ($fixed !== $orig) {
        file_put_contents($htaccessPath, $fixed);
        $results[] = ['ok' => true,  'label' => 'public/.htaccess', 'msg' => 'Removed "Options +FollowSymLinks"'];
    } else {
        $results[] = ['ok' => true,  'label' => 'public/.htaccess', 'msg' => 'Already clean — no changes needed'];
    }
} else {
    $results[] = ['ok' => false, 'label' => 'public/.htaccess', 'msg' => 'File not found at: ' . $htaccessPath];
}

// ── 2. Fix SmartMatch.php — remove "static" from $view property ─────────────
$smartMatchPath = $base . '/app/Filament/Pages/SmartMatch.php';
if (file_exists($smartMatchPath)) {
    $orig  = file_get_contents($smartMatchPath);
    $fixed = str_replace('protected static string $view', 'protected string $view', $orig);
    if ($fixed !== $orig) {
        file_put_contents($smartMatchPath, $fixed);
        $results[] = ['ok' => true,  'label' => 'SmartMatch.php', 'msg' => 'Removed "static" from $view — PHP fatal error fixed'];
    } else {
        $results[] = ['ok' => true,  'label' => 'SmartMatch.php', 'msg' => 'Already correct — no changes needed'];
    }
} else {
    $results[] = ['ok' => null, 'label' => 'SmartMatch.php', 'msg' => 'Not found (skipped)'];
}

// ── 3. Delete bootstrap/cache PHP files (config, routes, services) ───────────
$bootCache  = $base . '/bootstrap/cache';
$bootCleared = 0;
if (is_dir($bootCache)) {
    foreach (glob($bootCache . '/*.php') as $f) {
        if (@unlink($f)) $bootCleared++;
    }
}
$results[] = ['ok' => true, 'label' => 'bootstrap/cache', 'msg' => "Deleted {$bootCleared} cached file(s)"];

// ── 4. Delete compiled Blade views ───────────────────────────────────────────
$viewsDir    = $base . '/storage/framework/views';
$viewCleared = 0;
if (is_dir($viewsDir)) {
    foreach (glob($viewsDir . '/*.php') as $f) {
        if (@unlink($f)) $viewCleared++;
    }
}
$results[] = ['ok' => true, 'label' => 'Compiled views', 'msg' => "Deleted {$viewCleared} view file(s)"];

// ── 5. Delete application cache files ────────────────────────────────────────
function _rmdir_contents(string $dir): int {
    $n = 0;
    if (!is_dir($dir)) return 0;
    $it = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );
    foreach ($it as $f) {
        if ($f->isFile() && $f->getFilename() !== '.gitignore') {
            if (@unlink($f->getPathname())) $n++;
        }
    }
    return $n;
}
$cacheCleared = _rmdir_contents($base . '/storage/framework/cache/data');
$results[] = ['ok' => true, 'label' => 'Application cache', 'msg' => "Deleted {$cacheCleared} cache file(s)"];

// ── 6. Delete session files ─────────────────────────────────────────────────
$sessCleared = _rmdir_contents($base . '/storage/framework/sessions');
$results[] = ['ok' => true, 'label' => 'Sessions', 'msg' => "Deleted {$sessCleared} session file(s)"];

// ── 7. Create missing DB tables via raw PDO ──────────────────────────────────
// Read .env for DB credentials (no vendor/Laravel available)
function _env_val(string $key, string $envPath): string {
    if (!file_exists($envPath)) return '';
    foreach (file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#') continue;
        [$k, $v] = array_pad(explode('=', $line, 2), 2, '');
        if (trim($k) === $key) return trim($v, " \t\"'");
    }
    return '';
}
$envFile = $base . '/.env';
$dbHost  = _env_val('DB_HOST',     $envFile) ?: '127.0.0.1';
$dbPort  = _env_val('DB_PORT',     $envFile) ?: '3306';
$dbName  = _env_val('DB_DATABASE', $envFile);
$dbUser  = _env_val('DB_USERNAME', $envFile);
$dbPass  = _env_val('DB_PASSWORD', $envFile);

$missingTables = [];
$createdTables = [];
$tableErrors   = [];

if ($dbName && $dbUser) {
    try {
        $pdo = new PDO(
            "mysql:host={$dbHost};port={$dbPort};dbname={$dbName};charset=utf8mb4",
            $dbUser, $dbPass,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );

        $migrationsExists = $pdo->query("SHOW TABLES LIKE 'migrations'")->rowCount() > 0;

        // Helper: check if a migration is already recorded
        $isMigrated = function(string $name) use ($pdo, $migrationsExists): bool {
            if (!$migrationsExists) return false;
            $st = $pdo->prepare("SELECT 1 FROM `migrations` WHERE `migration` = ? LIMIT 1");
            $st->execute([$name]);
            return (bool) $st->fetchColumn();
        };

        // Helper: mark a migration as done in the next batch
        $markMigrated = function(string $name) use ($pdo, $migrationsExists): void {
            if (!$migrationsExists) return;
            $pdo->prepare(
                "INSERT IGNORE INTO `migrations` (`migration`, `batch`)
                 SELECT ?, COALESCE((SELECT MAX(`batch`) FROM `migrations` m2), 0) + 1"
            )->execute([$name]);
        };

        // ── wallet_transactions ───────────────────────────────────────────────
        // If migration was marked done but table is missing, delete the stale record
        // so we can re-create it properly.
        $walletExists = $pdo->query("SHOW TABLES LIKE 'wallet_transactions'")->rowCount() > 0;
        if (!$walletExists) {
            // Remove stale migration record (if any) before recreating
            if ($migrationsExists) {
                $pdo->prepare("DELETE FROM `migrations` WHERE `migration` = ?")->execute([
                    '2026_03_20_000001_create_wallet_transactions_table',
                ]);
            }
            $pdo->exec("CREATE TABLE `wallet_transactions` (
                `id` bigint unsigned NOT NULL AUTO_INCREMENT,
                `user_id` bigint unsigned NOT NULL,
                `type` enum('tip_sent','tip_received','deposit','withdrawal','admin_credit','admin_debit') NOT NULL,
                `amount` bigint unsigned NOT NULL,
                `balance_after` bigint unsigned NOT NULL DEFAULT 0,
                `reference_id` bigint unsigned DEFAULT NULL,
                `reference_type` varchar(50) DEFAULT NULL,
                `description` varchar(255) DEFAULT NULL,
                `created_at` timestamp NULL DEFAULT NULL,
                `updated_at` timestamp NULL DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `wallet_transactions_user_id_created_at_index` (`user_id`,`created_at`),
                CONSTRAINT `wallet_transactions_user_id_foreign`
                    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
            $createdTables[] = 'wallet_transactions (CREATED)';
        }
        $markMigrated('2026_03_20_000001_create_wallet_transactions_table');

        // ── rooms ─────────────────────────────────────────────────────────────
        $roomsExists = $pdo->query("SHOW TABLES LIKE 'rooms'")->rowCount() > 0;
        if (!$roomsExists) {
            $pdo->exec("CREATE TABLE `rooms` (
                `id` bigint unsigned NOT NULL AUTO_INCREMENT,
                `name` varchar(120) NOT NULL,
                `slug` varchar(130) NOT NULL,
                `type` enum('public','private') NOT NULL DEFAULT 'public',
                `owner_id` bigint unsigned NOT NULL,
                `avatar` varchar(255) DEFAULT NULL,
                `max_members` smallint unsigned NOT NULL DEFAULT 100,
                `is_active` tinyint(1) NOT NULL DEFAULT 1,
                `created_at` timestamp NULL DEFAULT NULL,
                `updated_at` timestamp NULL DEFAULT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `rooms_slug_unique` (`slug`),
                CONSTRAINT `rooms_owner_id_foreign`
                    FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
            $createdTables[] = 'rooms (CREATED)';
        }
        $markMigrated('2026_03_21_000001_create_rooms_table');

        // ── proof_image column on premium_payments ────────────────────────────
        // Column already exists in production but migration was never recorded —
        // just mark it as done so artisan migrate stops trying to add it again.
        $proofCols = $pdo->query("SHOW COLUMNS FROM `premium_payments` LIKE 'proof_image'")->fetchAll();
        if (!empty($proofCols)) {
            $markMigrated('2026_03_19_000001_add_proof_image_to_premium_payments');
            $createdTables[] = 'premium_payments.proof_image (already exists — migration marked done)';
        } else {
            // Column missing: add it and mark migration done
            $pdo->exec("ALTER TABLE `premium_payments` ADD COLUMN `proof_image` varchar(255) NULL AFTER `tx_hash`");
            $markMigrated('2026_03_19_000001_add_proof_image_to_premium_payments');
            $createdTables[] = 'premium_payments.proof_image (ADDED)';
        }

        // ── location_filter_uses column on users ──────────────────────────────
        $cols = $pdo->query("SHOW COLUMNS FROM `users` LIKE 'location_filter_uses'")->fetchAll();
        if (empty($cols)) {
            $pdo->exec("ALTER TABLE `users` ADD COLUMN `location_filter_uses` tinyint unsigned NOT NULL DEFAULT 0 AFTER `credit_balance`");
            $createdTables[] = 'users.location_filter_uses (ADDED)';
        }
        $markMigrated('2026_03_22_000001_add_location_filter_uses_to_users_table');

        $msg = count($createdTables) > 0
            ? 'Done: ' . implode(' | ', $createdTables)
            : 'All tables/columns already exist and migrations are recorded — nothing to do';
        $results[] = ['ok' => true, 'label' => 'DB migrations', 'msg' => $msg];

    } catch (\Throwable $ex) {
        $results[] = ['ok' => false, 'label' => 'DB migrations', 'msg' => $ex->getMessage()];
    }
} else {
    $results[] = ['ok' => false, 'label' => 'DB migrations', 'msg' => '.env not found or DB_DATABASE/DB_USERNAME missing'];
}

?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Fix – HeartsConnect</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: monospace; background: #0f0f0f; color: #e5e5e5; padding: 2rem; margin: 0; }
        h1   { color: #f43f5e; font-size: 1.4rem; }
        table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
        th, td { text-align: left; padding: .5rem .75rem; border-bottom: 1px solid #2a2a2a; font-size: .85rem; }
        th { background: #1a1a1a; color: #aaa; }
        .ok   { color: #22c55e; font-weight: bold; }
        .fail { color: #ef4444; font-weight: bold; }
        .skip { color: #888; }
        .warn { background: #450a0a; border: 1px solid #7f1d1d; border-radius: 6px;
                padding: 1rem 1.25rem; margin-top: 1.5rem; color: #fca5a5; font-size: .9rem; }
        .info { background: #052e16; border: 1px solid #166534; border-radius: 6px;
                padding: 1rem 1.25rem; margin-top: 1rem; color: #86efac; font-size: .9rem; }
    </style>
</head>
<body>
<h1>🛠 HeartsConnect — Production Fix</h1>
<p style="color:#888;font-size:.8rem">Run at: <?= date('Y-m-d H:i:s T') ?> &nbsp;|&nbsp; PHP <?= PHP_VERSION ?> &nbsp;|&nbsp; Server root: <?= htmlspecialchars($base) ?></p>

<table>
    <tr><th>Step</th><th>Result</th><th>Detail</th></tr>
    <?php foreach ($results as $r):
        $cls = $r['ok'] === true ? 'ok' : ($r['ok'] === false ? 'fail' : 'skip');
        $icon = $r['ok'] === true ? '✅' : ($r['ok'] === false ? '❌' : 'ℹ️');
    ?>
    <tr>
        <td><?= htmlspecialchars($r['label']) ?></td>
        <td class="<?= $cls ?>"><?= $icon ?></td>
        <td><?= htmlspecialchars($r['msg']) ?></td>
    </tr>
    <?php endforeach; ?>
</table>

<div class="info">
    ✅ <strong>If all rows show green</strong>: Refresh <a href="https://heartsconnect.site" style="color:#4ade80">heartsconnect.site</a> — it should be working now.
</div>

<div class="warn">
    ⚠️ <strong>DELETE this file now!</strong> Go to cPanel → File Manager → find
    <code>public/deploy-fix.php</code> and delete it. Security risk to leave it.
</div>
</body>
</html>
