<?php
/**
 * Emergency debug — DELETE after use!
 * Visit: https://heartsconnect.cc/fix.php
 */
error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
header('Content-Type: text/plain; charset=utf-8');

$root = dirname(__DIR__);

echo "=== STEP 1: .env file ===\n";
$envPath = $root . '/.env';
if (!file_exists($envPath)) {
    echo "❌ .env NOT FOUND at: $envPath\n";
    echo "   → You need to rename env.production.txt to .env in File Manager!\n\n";
} else {
    echo "✅ .env exists\n\n";

    echo "=== Key .env values ===\n";
    $env = parse_ini_file($envPath);
    $show = ['APP_ENV','APP_URL','APP_DEBUG','DB_HOST','DB_DATABASE','DB_USERNAME','SESSION_DOMAIN','SESSION_SECURE_COOKIE','REVERB_HOST'];
    foreach ($show as $k) {
        $v = $env[$k] ?? '(not set)';
        if (in_array($k, ['DB_PASSWORD','APP_KEY'])) $v = '***masked***';
        echo "  $k = $v\n";
    }
    echo "\n";
}

echo "=== STEP 2: vendor/autoload.php ===\n";
if (!file_exists($root . '/vendor/autoload.php')) {
    echo "❌ vendor/autoload.php MISSING — composer install was never run!\n\n";
    exit;
}
require $root . '/vendor/autoload.php';
echo "✅ autoload OK\n\n";

echo "=== STEP 3: Bootstrap Laravel ===\n";
try {
    $app = require_once $root . '/bootstrap/app.php';
    echo "✅ bootstrap/app.php OK\n\n";
} catch (\Throwable $e) {
    echo "❌ BOOTSTRAP FAILED!\n";
    echo "   Error  : " . $e->getMessage() . "\n";
    echo "   File   : " . $e->getFile() . ':' . $e->getLine() . "\n";
    exit;
}

echo "=== STEP 4: Config load ===\n";
try {
    $kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();
    echo "✅ Config loaded OK\n\n";
} catch (\Throwable $e) {
    echo "❌ CONFIG FAILED!\n";
    echo "   Error  : " . $e->getMessage() . "\n";
    echo "   File   : " . $e->getFile() . ':' . $e->getLine() . "\n";
    exit;
}

echo "=== STEP 5: Database connection ===\n";
try {
    \Illuminate\Support\Facades\DB::connection()->getPdo();
    echo "✅ Database connected OK\n\n";
} catch (\Throwable $e) {
    echo "❌ DATABASE FAILED!\n";
    echo "   Error  : " . $e->getMessage() . "\n\n";
}

echo "=== STEP 6: Clear all caches ===\n";
try {
    \Illuminate\Support\Facades\Artisan::call('optimize:clear');
    echo "✅ Cache cleared!\n\n";
} catch (\Throwable $e) {
    echo "⚠️ Cache clear failed: " . $e->getMessage() . "\n\n";
}

echo "=== DONE ===\n";
echo "All checks complete. If all steps show ✅, refresh your site.\n";
echo "DELETE this file from public/ after diagnosis!\n";
