<?php
/**
 * HeartsConnect — Step 2: Post-install setup (no exec/shell needed)
 * ==================================================================
 * Run AFTER unzip.php has completed and .env is in place.
 * Visit: https://heartsconnect.site/install.php?t=hc_install_2026
 * DELETE this file when done!
 */
if (($_GET['t'] ?? '') !== 'hc_install_2026') { http_response_code(403); exit('403'); }

set_time_limit(300);
error_reporting(E_ALL);
ini_set('display_errors', '1');

$root = dirname(__DIR__); // /home/heartsco

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><title>HeartsConnect Install</title>
<style>
  body{font-family:sans-serif;max-width:860px;margin:30px auto;padding:0 16px}
  h2{color:#e91e63} h3{color:#374151;margin-bottom:4px}
  .ok{color:#16a34a;font-weight:600}
  .err{color:#dc2626;font-weight:600}
  pre{background:#1e1e1e;color:#d4d4d4;padding:10px;border-radius:6px;font-size:12px;overflow-x:auto;margin:4px 0;white-space:pre-wrap}
  .warn{background:#fef9c3;border:1px solid #fbbf24;padding:12px;border-radius:6px;margin-top:20px}
  .box{margin-bottom:20px}
</style>
</head>
<body>
<h2>⚙️ HeartsConnect — Post-install Setup</h2>
<p style="color:#666">PHP <?= PHP_VERSION ?> | Root: <?= htmlspecialchars($root) ?></p>
<hr>

<?php
function ok(string $msg, string $detail = ''): void {
    echo '<div class="box"><span class="ok">✔ ' . htmlspecialchars($msg) . '</span>';
    if ($detail) echo '<pre>' . htmlspecialchars($detail) . '</pre>';
    echo '</div>'; flush();
}
function fail(string $msg, string $detail = ''): void {
    echo '<div class="box"><span class="err">✘ ' . htmlspecialchars($msg) . '</span>';
    if ($detail) echo '<pre>' . htmlspecialchars($detail) . '</pre>';
    echo '</div>'; flush();
}

// ── 1. Check vendor/autoload ──────────────────────────────────────────────────
echo "<h3>1. Autoloader</h3>";
if (!file_exists($root . '/vendor/autoload.php')) {
    fail('vendor/autoload.php missing — run unzip.php first.'); exit;
}
require $root . '/vendor/autoload.php';
ok('vendor/autoload.php loaded');

// ── 2. Check .env ─────────────────────────────────────────────────────────────
echo "<h3>2. .env</h3>";
$envPath = $root . '/.env';
if (!file_exists($envPath)) {
    fail('.env not found at ' . $root . '/.env');
    echo '<p>Create it via cPanel File Manager with your production values. See template below.</p>';
    $template = file_exists($root . '/.env.example') ? file_get_contents($root . '/.env.example') : '(no .env.example found)';
    echo '<pre>' . htmlspecialchars($template) . '</pre>'; exit;
}
ok('.env exists');

// ── 3. Bootstrap Laravel ──────────────────────────────────────────────────────
echo "<h3>3. Bootstrap Laravel</h3>";
try {
    $app = require_once $root . '/bootstrap/app.php';
    ok('bootstrap/app.php loaded — ' . get_class($app));
} catch (\Throwable $e) {
    fail('Bootstrap failed', $e->getMessage() . "\n" . $e->getFile() . ':' . $e->getLine());
    exit;
}

// Boot the kernel so all service providers & config are loaded
try {
    $kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();
    ok('Kernel bootstrapped');
} catch (\Throwable $e) {
    fail('Kernel bootstrap failed', $e->getMessage());
    exit;
}

// ── 4. Check APP_KEY — generate if missing ────────────────────────────────────
echo "<h3>4. APP_KEY</h3>";
$envContent = file_get_contents($envPath);
if (!preg_match('/^APP_KEY=base64:.+/m', $envContent)) {
    try {
        $key = 'base64:' . base64_encode(random_bytes(32));
        // Write into .env
        if (preg_match('/^APP_KEY=/m', $envContent)) {
            $envContent = preg_replace('/^APP_KEY=.*/m', 'APP_KEY=' . $key, $envContent);
        } else {
            $envContent .= "\nAPP_KEY=" . $key;
        }
        file_put_contents($envPath, $envContent);
        // Also tell the running app
        $app->make('config')->set('app.key', $key);
        ok('APP_KEY generated and written to .env', $key);
    } catch (\Throwable $e) {
        fail('Could not generate APP_KEY', $e->getMessage()); exit;
    }
} else {
    ok('APP_KEY already set');
}

// ── 5. Database connection ────────────────────────────────────────────────────
echo "<h3>5. Database</h3>";
try {
    $pdo = \Illuminate\Support\Facades\DB::connection()->getPdo();
    ok('DB connected — MySQL ' . $pdo->getAttribute(PDO::ATTR_SERVER_VERSION));
} catch (\Throwable $e) {
    fail('DB connection failed', $e->getMessage());
    echo '<p>Check DB_HOST, DB_DATABASE, DB_USERNAME, DB_PASSWORD in your .env</p>';
    // Don't exit — show remaining checks
}

// ── 6. Migrate ────────────────────────────────────────────────────────────────
echo "<h3>6. Migrations</h3>";
try {
    ob_start();
    \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
    $out = ob_get_clean() . \Illuminate\Support\Facades\Artisan::output();
    ok('migrate --force', trim($out) ?: 'Nothing to migrate.');
} catch (\Throwable $e) {
    fail('migrate failed', $e->getMessage());
}

// ── 7. Clear + cache config ───────────────────────────────────────────────────
echo "<h3>7. Optimize</h3>";
$cmds = ['optimize:clear', 'config:cache', 'route:cache', 'view:cache'];
foreach ($cmds as $cmd) {
    try {
        ob_start();
        \Illuminate\Support\Facades\Artisan::call($cmd);
        $out = ob_get_clean() . \Illuminate\Support\Facades\Artisan::output();
        ok($cmd, trim($out));
    } catch (\Throwable $e) {
        fail($cmd, $e->getMessage());
    }
}

// ── 8. Storage symlink ────────────────────────────────────────────────────────
echo "<h3>8. Storage link</h3>";
$linkTarget = $root . '/storage/app/public';
$linkPath   = __DIR__ . '/storage'; // public_html/storage
if (is_link($linkPath)) {
    ok('storage symlink already exists → ' . readlink($linkPath));
} elseif (is_dir($linkPath)) {
    ok('public_html/storage is a real directory (symlink not needed or already merged)');
} else {
    try {
        ob_start();
        \Illuminate\Support\Facades\Artisan::call('storage:link', ['--force' => true]);
        $out = ob_get_clean() . \Illuminate\Support\Facades\Artisan::output();
        ok('storage:link', trim($out));
    } catch (\Throwable $e) {
        fail('storage:link failed', $e->getMessage() . "\nTry: ln -s $linkTarget $linkPath via cPanel terminal");
    }
}

// ── 9. Summary ────────────────────────────────────────────────────────────────
echo '<hr>';
echo '<h3>✅ Done! Visit <a href="/">' . htmlspecialchars(config('app.url', 'https://heartsconnect.site')) . '</a></h3>';
?>
<div class="warn">
  <strong>⚠️ DELETE unzip.php, install.php, diag.php, gitfix.php from public_html immediately!</strong>
</div>
</body>
</html>
