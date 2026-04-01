<?php
/**
 * Emergency cache buster — DELETE after use!
 * Visit: https://heartsconnect.cc/fix.php
 * NO Laravel bootstrap — works even when site is down.
 */
error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
set_time_limit(10);
header('Content-Type: text/plain; charset=utf-8');

$root = dirname(__DIR__);

echo "=== STEP 0: Delete stale bootstrap cache ===\n";
$cacheFiles = [
    $root . '/bootstrap/cache/config.php',
    $root . '/bootstrap/cache/packages.php',
    $root . '/bootstrap/cache/services.php',
    $root . '/bootstrap/cache/routes-v7.php',
    $root . '/bootstrap/cache/events.php',
    $root . '/bootstrap/cache/filament/pages.php',
    $root . '/bootstrap/cache/filament/resources.php',
    $root . '/bootstrap/cache/filament/widgets.php',
];
foreach ($cacheFiles as $f) {
    if (file_exists($f)) {
        unlink($f) ? print("✅ Deleted: $f\n") : print("❌ Could not delete: $f\n");
    } else {
        echo "   (not found) $f\n";
    }
}

echo "\n=== STEP 2: .env check (no Laravel) ===\n";
$envPath = $root . '/.env';
if (!file_exists($envPath)) {
    echo "❌ .env NOT FOUND — rename env.production.txt to .env in File Manager!\n";
} else {
    echo "✅ .env exists\n";
    $env = parse_ini_file($envPath);
    $show = ['APP_ENV','APP_URL','BROADCAST_CONNECTION','REDIS_CLIENT','CACHE_STORE','QUEUE_CONNECTION'];
    foreach ($show as $k) {
        echo "  $k = " . ($env[$k] ?? '(not set)') . "\n";
    }
}

echo "\n=== STEP 3: Cache files on disk ===\n";
$check = [
    $root . '/bootstrap/cache/config.php',
    $root . '/bootstrap/cache/routes-v7.php',
    $root . '/bootstrap/cache/packages.php',
    $root . '/bootstrap/cache/services.php',
];
foreach ($check as $f) {
    echo (file_exists($f) ? "⚠️ EXISTS (stale!): " : "✅ Gone: ") . basename($f) . "\n";
}

echo "\n=== DONE ===\n";
echo "If any cache files show ⚠️ EXISTS above, they are causing the 504.\n";
echo "All stale files were deleted in Step 0 above.\n";
echo "Now visit https://heartsconnect.cc — it should load.\n";
echo "DELETE this file after use!\n";
