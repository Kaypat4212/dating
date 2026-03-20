<?php
/**
 * ONE-TIME Git conflict resolver + full deploy runner
 * =====================================================
 * 1. Upload this file to your server's public_html/ via File Manager
 * 2. Visit: https://heartsconnect.site/gitfix.php?token=hc_fix_2026
 * 3. DELETE this file immediately after it runs!
 */

define('TOKEN', 'hc_fix_2026');

if (($_GET['token'] ?? '') !== TOKEN) {
    http_response_code(403);
    die('<h2 style="color:red;font-family:sans-serif">403 Forbidden</h2>');
}

set_time_limit(300);
ini_set('display_errors', 1);
error_reporting(E_ALL);

$root = dirname(__DIR__); // /home/user/public_html
$php  = PHP_BINARY ?: 'php';

function run(string $cmd): array {
    exec($cmd . ' 2>&1', $out, $code);
    return ['output' => implode("\n", $out), 'code' => $code];
}

function block(string $title, array $result): void {
    $ok = $result['code'] === 0;
    $color = $ok ? '#16a34a' : '#dc2626';
    $icon  = $ok ? '✔' : '✘';
    echo "<div style='margin-bottom:16px'>";
    echo "<div style='font-weight:600;color:{$color};font-size:15px'>{$icon} {$title}</div>";
    if (trim($result['output'])) {
        echo "<pre style='background:#1e1e1e;color:#d4d4d4;padding:10px;border-radius:6px;font-size:12px;overflow-x:auto;margin:4px 0'>"
            . htmlspecialchars($result['output']) . "</pre>";
    }
    echo "</div>";
    flush();
}

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>HeartsConnect Deploy Fix</title>
<style>
  body { font-family: sans-serif; max-width: 800px; margin: 30px auto; padding: 0 16px; }
  h2   { color: #e91e63; }
</style>
</head>
<body>
<h2>🔧 HeartsConnect — Deploy Fix</h2>
<p style="color:#666">Running as: <?= htmlspecialchars(get_current_user()) ?> | PHP <?= PHP_VERSION ?> | Root: <?= htmlspecialchars($root) ?></p>
<hr>

<?php

// ── Step 1: Git — discard server-side conflicts and pull latest ───────────────
$r = run("cd " . escapeshellarg($root) . " && git fetch origin 2>&1");
block('Git fetch origin', $r);

$r = run("cd " . escapeshellarg($root) . " && git checkout -- .cpanel.yml .gitignore .htaccess public/.htaccess 2>&1");
block('Git discard conflicted files', $r);

$r = run("cd " . escapeshellarg($root) . " && git reset --hard origin/master 2>&1");
block('Git reset --hard origin/master', $r);

// ── Step 2: Composer ──────────────────────────────────────────────────────────
$r = run("cd " . escapeshellarg($root) . " && composer install --no-dev --optimize-autoloader --no-interaction 2>&1");
block('Composer install', $r);

// ── Step 3: .env check ────────────────────────────────────────────────────────
if (!file_exists($root . '/.env')) {
    copy($root . '/.env.example', $root . '/.env');
    block('.env created from .env.example', ['output' => 'Copied .env.example → .env', 'code' => 0]);
} else {
    block('.env already exists — preserved', ['output' => 'No change made.', 'code' => 0]);
}

// ── Step 4: Key generate if blank ─────────────────────────────────────────────
$envContent = file_get_contents($root . '/.env');
if (!preg_match('/^APP_KEY=base64:/m', $envContent)) {
    $r = run(escapeshellarg($php) . " " . escapeshellarg($root . "/artisan") . " key:generate --force 2>&1");
    block('php artisan key:generate', $r);
} else {
    block('APP_KEY already set — skipped', ['output' => 'Key present.', 'code' => 0]);
}

// ── Step 5: Migrate ───────────────────────────────────────────────────────────
$r = run(escapeshellarg($php) . " " . escapeshellarg($root . "/artisan") . " migrate --force 2>&1");
block('php artisan migrate --force', $r);

// ── Step 6: Optimize ─────────────────────────────────────────────────────────
$r = run(escapeshellarg($php) . " " . escapeshellarg($root . "/artisan") . " optimize:clear 2>&1");
block('php artisan optimize:clear', $r);

$r = run(escapeshellarg($php) . " " . escapeshellarg($root . "/artisan") . " optimize 2>&1");
block('php artisan optimize', $r);

// ── Step 7: Storage link ──────────────────────────────────────────────────────
$r = run(escapeshellarg($php) . " " . escapeshellarg($root . "/artisan") . " storage:link --force 2>&1");
block('php artisan storage:link', $r);

// ── Step 8: Permissions ───────────────────────────────────────────────────────
$r = run("chmod -R 775 " . escapeshellarg($root . "/storage") . " " . escapeshellarg($root . "/bootstrap/cache") . " 2>&1");
block('chmod 775 storage/ bootstrap/cache/', $r);

?>

<hr>
<div style="background:#fef9c3;border:1px solid #fbbf24;padding:12px;border-radius:6px;margin-top:20px">
  <strong>⚠️ DELETE THIS FILE NOW!</strong><br>
  Go to cPanel → File Manager → public_html → delete <code>gitfix.php</code> immediately.
</div>
</body>
</html>
