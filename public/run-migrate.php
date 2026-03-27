<?php
// Show all PHP errors so 500s reveal the actual cause
error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');

/**
 * ONE-TIME artisan runner for cPanel shared hosting.
 * DELETE this file from the server after use.
 */

define('SECRET', 'HC_migrate_2026');

if (($_GET['secret'] ?? '') !== SECRET) {
    http_response_code(403);
    die('Forbidden.');
}

$allowed = ['migrate', 'db:seed', 'storage:link', 'optimize:clear', 'config:clear', 'cache:clear', 'view:clear'];
$cmd = $_GET['cmd'] ?? 'migrate';

if (! in_array($cmd, $allowed, true)) {
    die('Command not allowed. Allowed: ' . implode(', ', $allowed));
}

// Bootstrap Laravel (works with Laravel 10 / 11 / 12)
// Auto-detect root: vendor/ may be a sibling of public/ OR in the same dir as this script
$laravelRoot = is_dir(__DIR__ . '/../vendor') ? realpath(__DIR__ . '/..') : __DIR__;
if (! is_dir($laravelRoot . '/vendor')) {
    die('Cannot locate vendor/ directory. Expected at: ' . $laravelRoot . '/vendor');
}
require $laravelRoot . '/vendor/autoload.php';
$app = require_once $laravelRoot . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$args = [];
if ($cmd === 'migrate') {
    $args = ['--force' => true];
} elseif ($cmd === 'db:seed') {
    $args = ['--class' => 'Database\\Seeders\\CommunityDataSeeder', '--force' => true];
}

echo '<pre style="font-family:monospace;font-size:13px;">';
echo "=== Path Diagnostics ===\n";
echo "Laravel root : {$laravelRoot}\n";
echo "public_path(): " . public_path() . "\n";
echo "storage_path(): " . storage_path() . "\n";
echo "storage/link target: " . public_path('storage') . "\n";
echo "========================\n\n";
echo "Running: php artisan {$cmd}\n\n";

// Use $kernel->call() — more reliable than Artisan facade in web context
$exitCode = $kernel->call($cmd, $args);
echo htmlspecialchars($kernel->output());
echo "\nExit code: {$exitCode}\n";
echo $exitCode === 0 ? "\nDone! DELETE this file now." : "\nFailed. Check output above.";
echo '</pre>';
