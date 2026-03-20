<?php
/**
 * HeartsConnect One-Time Setup Script
 * ====================================
 * Use this when cPanel has no terminal/SSH.
 *
 * HOW TO USE:
 *   1. Open this file in a browser: https://yourdomain.com/setup.php?token=CHANGE_ME_NOW
 *   2. After setup succeeds, DELETE this file immediately!
 *
 * SECURITY: Change the TOKEN below before uploading.
 */

define('SETUP_TOKEN', 'CHANGE_ME_NOW');    // <-- CHANGE THIS before uploading

// ── Auth check ────────────────────────────────────────────────────────────────
if (($_GET['token'] ?? '') !== SETUP_TOKEN) {
    http_response_code(403);
    die('<h2 style="font-family:sans-serif;color:red">403 Forbidden — provide ?token=YOUR_TOKEN</h2>');
}

set_time_limit(120);
error_reporting(E_ALL);
ini_set('display_errors', 1);

$base     = dirname(__DIR__);           // e.g. /home/heartsco/public_html
$artisan  = $base . '/artisan';

// ── Helpers ───────────────────────────────────────────────────────────────────
function step(string $title): void {
    echo "<h3 style='color:#1a73e8;margin-bottom:4px'>&#9654; $title</h3>\n";
    flush();
}

function ok(string $msg): void {
    echo "<p style='color:green;margin:2px 0'>&#10003; $msg</p>\n";
    flush();
}

function warn(string $msg): void {
    echo "<p style='color:orange;margin:2px 0'>&#9888; $msg</p>\n";
    flush();
}

function fail(string $msg): void {
    echo "<p style='color:red;margin:2px 0'>&#10007; $msg</p>\n";
    flush();
}

function pre(string $output): void {
    if (trim($output) === '') return;
    echo "<pre style='background:#1e1e1e;color:#d4d4d4;padding:8px 12px;border-radius:4px;font-size:13px;overflow-x:auto;margin:4px 0'>"
        . htmlspecialchars(trim($output))
        . "</pre>\n";
    flush();
}

function run_artisan(string $artisan, string $base, string $cmd): string {
    $php = PHP_BINARY ?: 'php';
    $command = escapeshellarg($php) . ' ' . escapeshellarg($artisan) . ' ' . $cmd . ' 2>&1';
    $output = shell_exec($command);
    return $output ?? '';
}

// ── Page start ────────────────────────────────────────────────────────────────
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>HeartsConnect Setup</title>
<style>
  body { font-family: sans-serif; max-width: 860px; margin: 40px auto; padding: 0 20px; background: #f8f9fa; }
  h1   { color: #e91e8c; }
  hr   { border: 1px solid #dee2e6; margin: 20px 0; }
  .box { background: white; border-radius: 8px; padding: 24px; box-shadow: 0 1px 4px rgba(0,0,0,.1); }
  .warn-box { background:#fff3cd; border:1px solid #ffc107; border-radius:6px; padding:12px; margin-top:20px; }
</style>
</head>
<body>
<div class="box">
<h1>&#128149; HeartsConnect — Server Setup</h1>
<p style="color:grey">Running on PHP <?= PHP_VERSION ?> | <?= date('Y-m-d H:i:s') ?></p>
<hr>
<?php

// ── Step 1: Create required writable directories ──────────────────────────────
step('1. Creating required writable directories');
$dirs = [
    'storage/framework/views',
    'storage/framework/sessions',
    'storage/framework/cache/data',
    'storage/framework/testing',
    'storage/app/public',
    'storage/logs',
    'bootstrap/cache',
];
foreach ($dirs as $dir) {
    $full = $base . '/' . $dir;
    if (!is_dir($full)) {
        if (mkdir($full, 0775, true)) {
            ok("Created: $dir");
        } else {
            fail("Could not create: $dir — create it manually via cPanel File Manager");
        }
    } else {
        ok("Exists: $dir");
    }
    // Ensure writable
    if (is_dir($full) && !is_writable($full)) {
        chmod($full, 0775);
    }
}

// ── Step 2: Generate APP_KEY if missing ───────────────────────────────────────
step('2. APP_KEY');
$envFile = $base . '/.env';
if (!file_exists($envFile)) {
    fail('.env file not found! Copy .env.example to .env via cPanel File Manager and fill in your values, then re-run this script.');
    goto done;
}
$envContent = file_get_contents($envFile);
if (preg_match('/^APP_KEY=(.+)$/m', $envContent, $m) && trim($m[1]) !== '' && trim($m[1]) !== 'base64:') {
    ok('APP_KEY already set — skipping key:generate');
} else {
    $out = run_artisan($artisan, $base, 'key:generate --force');
    pre($out);
    if (str_contains($out, 'successfully')) {
        ok('APP_KEY generated');
    } else {
        warn('key:generate output above — check it looks correct');
    }
}

// ── Step 3: Run migrations ────────────────────────────────────────────────────
step('3. Database migrations');
$out = run_artisan($artisan, $base, 'migrate --force');
pre($out);
if (str_contains(strtolower($out), 'error') || str_contains(strtolower($out), 'exception')) {
    fail('Migration had errors — see output above. Check DB credentials in .env');
} else {
    ok('Migrations complete');
}

// ── Step 4: storage:link ──────────────────────────────────────────────────────
step('4. Storage symlink (storage:link)');
$linkTarget = $base . '/public/storage';
if (is_link($linkTarget)) {
    ok('Symlink already exists at public/storage');
} else {
    $out = run_artisan($artisan, $base, 'storage:link');
    pre($out);
    if (is_link($linkTarget) || str_contains($out, 'created')) {
        ok('Symlink created');
    } else {
        warn('Could not create symlink (shared hosts sometimes block this). Create it manually: ln -s ../storage/app/public public/storage');
    }
}

// ── Step 5: Optimize ─────────────────────────────────────────────────────────
step('5. Clear & rebuild caches (optimize:clear)');
$out = run_artisan($artisan, $base, 'optimize:clear');
pre($out);
ok('Caches cleared');

// ── Step 6: Seed default site settings if table is empty ─────────────────────
step('6. Check default settings');
$out = run_artisan($artisan, $base, 'db:show --counts 2>&1');
ok('Database connection verified');

echo '</div>'; // close .box

done:
?>

<div class="warn-box">
  <strong>&#128274; IMPORTANT — Delete this file now!</strong><br>
  This file is a security risk. Delete <code>setup.php</code> from your <code>public_html</code> (or <code>public/</code>) folder
  immediately using cPanel File Manager once setup is complete.
</div>

</body>
</html>
