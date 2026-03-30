<?php
/**
 * HeartsConnect — Page-specific debug probe (DELETE after use!)
 * Visit: https://heartsconnect.cc/debug-pages.php?t=dbg2026
 */
if (($_GET['t'] ?? '') !== 'dbg2026') { http_response_code(403); exit('403'); }

// Override ANY Laravel error handler — we handle everything ourselves
set_exception_handler(null);
set_error_handler(null);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

header('Content-Type: text/plain; charset=utf-8');
echo "=== PROBE v2 STARTED ===\n";
flush();

$root = dirname(__DIR__);

echo "=== AUTOLOADER ===\n";
try {
    require $root . '/vendor/autoload.php';
    echo "OK\n";
} catch (\Throwable $e) {
    echo "FAILED: " . $e->getMessage() . "\n"; die();
}
flush();

echo "=== BOOTSTRAP ===\n";
$app = null;
try {
    $app = require_once $root . '/bootstrap/app.php';
    echo "OK\n";
} catch (\Throwable $e) {
    echo "FAILED: " . get_class($e) . ": " . $e->getMessage() . "\n";
    echo "  at " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo $e->getTraceAsString() . "\n";
    die();
}
flush();

echo "=== KERNEL BOOTSTRAP ===\n";
try {
    $kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();
    echo "OK\n";
} catch (\Throwable $e) {
    echo "FAILED: " . get_class($e) . ": " . $e->getMessage() . "\n";
    echo "  at " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo $e->getTraceAsString() . "\n";
    die();
}
flush();

echo "=== GIT COMMIT ===\n";
echo shell_exec("cd {$root} && git log --oneline -3 2>&1") . "\n";

echo "=== DB TABLES (wallet/homepage) ===\n";
try {
    $tables = \Illuminate\Support\Facades\DB::select("SHOW TABLES");
    $names = array_map(fn($t) => array_values((array)$t)[0], $tables);
    foreach (['wallet_transactions','wallet_funding_requests','wallet_withdrawal_requests','homepage_visits','site_settings','users'] as $t) {
        echo str_pad($t, 35) . (in_array($t, $names) ? "EXISTS" : "MISSING") . "\n";
    }
} catch (\Throwable $e) {
    echo "DB ERROR: " . $e->getMessage() . "\n";
}

echo "\n=== SmartMatch instantiation ===\n";
try {
    $sm = new \App\Filament\Pages\SmartMatch();
    echo "OK — class loads fine\n";
} catch (\Throwable $e) {
    echo "ERROR: " . get_class($e) . "\n";
    echo "Message: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

echo "\n=== FinanceDashboard instantiation ===\n";
try {
    $fd = new \App\Filament\Pages\FinanceDashboard();
    echo "OK — class loads fine\n";
} catch (\Throwable $e) {
    echo "ERROR: " . get_class($e) . "\n";
    echo "Message: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

echo "\n=== FinanceDashboard->getStats() ===\n";
try {
    $fd = new \App\Filament\Pages\FinanceDashboard();
    $stats = $fd->getStats();
    echo "OK — getStats() returned " . count($stats) . " keys\n";
    echo implode(', ', array_keys($stats)) . "\n";
} catch (\Throwable $e) {
    echo "ERROR: " . get_class($e) . "\n";
    echo "Message: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

echo "\n=== WalletTransaction model ===\n";
try {
    $count = \App\Models\WalletTransaction::count();
    echo "OK — {$count} rows\n";
} catch (\Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}

echo "\n=== WalletFundingRequest model ===\n";
try {
    $count = \App\Models\WalletFundingRequest::count();
    echo "OK — {$count} rows\n";
} catch (\Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}

echo "\n=== SmartMatch getNewUsers() ===\n";
try {
    $sm = new \App\Filament\Pages\SmartMatch();
    $users = $sm->getNewUsers();
    echo "OK — returned " . $users->count() . " users\n";
} catch (\Throwable $e) {
    echo "ERROR: " . get_class($e) . "\n";
    echo "Message: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

echo "\n=== LAST 30 LINES OF LARAVEL LOG ===\n";
$logFile = $root . '/storage/logs/laravel.log';
if (file_exists($logFile)) {
    $lines = file($logFile);
    $last = array_slice($lines, -30);
    echo implode('', $last);
} else {
    echo "No log file found\n";
}

echo "\n=== DONE ===\n";
