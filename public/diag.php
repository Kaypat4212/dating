<?php
/**
 * HeartsConnect — Server Diagnostic (delete after use!)
 * Visit: https://heartsconnect.site/diag.php?t=hc_diag_2026
 * DELETE this file from the server once you have identified the error.
 */
if (($_GET['t'] ?? '') !== 'hc_diag_2026') { http_response_code(403); exit('403'); }

error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');

header('Content-Type: text/plain; charset=utf-8');

$root = dirname(__DIR__);

echo "=== PHP ===\n";
echo "Version : " . PHP_VERSION . "\n";
echo "SAPI    : " . PHP_SAPI . "\n";
echo "Binary  : " . (PHP_BINARY ?: 'unknown') . "\n";
echo "Root    : $root\n\n";

echo "=== FILE CHECKS ===\n";
$checks = [
    '.env'             => $root . '/.env',
    'vendor/'          => $root . '/vendor',
    'vendor/autoload'  => $root . '/vendor/autoload.php',
    'bootstrap/app'    => $root . '/bootstrap/app.php',
    'storage/'         => $root . '/storage',
    'bootstrap/cache/' => $root . '/bootstrap/cache',
];
foreach ($checks as $label => $path) {
    $exists    = file_exists($path) || is_dir($path);
    $writable  = $exists && is_writable($path) ? ' (writable)' : ($exists ? ' (NOT writable)' : '');
    echo str_pad($label, 20) . ($exists ? "EXISTS$writable" : "MISSING") . "\n";
}

echo "\n=== .ENV CONTENTS (sanitised) ===\n";
$envPath = $root . '/.env';
if (file_exists($envPath)) {
    foreach (file($envPath) as $line) {
        $line = rtrim($line);
        if (preg_match('/^\s*#|^\s*$/', $line)) continue; // skip comments/blanks
        // Mask sensitive values
        if (preg_match('/^(DB_PASSWORD|MAIL_PASSWORD|APP_KEY|.*SECRET.*|.*_KEY.*)\s*=/i', $line)) {
            $line = preg_replace('/=.+/', '=**MASKED** (' . strlen(explode('=', $line, 2)[1] ?? '') . ' chars)', $line);
        }
        echo $line . "\n";
    }
} else {
    echo ".env NOT FOUND on this server!\n";
}

echo "\n=== AUTOLOADER ===\n";
try {
    require $root . '/vendor/autoload.php';
    echo "vendor/autoload.php loaded OK\n";
} catch (\Throwable $e) {
    echo "FAILED: " . $e->getMessage() . "\n";
    exit;
}

echo "\n=== BOOTSTRAP ===\n";
try {
    $app = require_once $root . '/bootstrap/app.php';
    echo "bootstrap/app.php loaded OK\n";
    echo "App class: " . get_class($app) . "\n";
} catch (\Throwable $e) {
    echo "FAILED: " . get_class($e) . "\n";
    echo "Message : " . $e->getMessage() . "\n";
    echo "File    : " . $e->getFile() . ':' . $e->getLine() . "\n";
    echo "\nStack trace:\n" . $e->getTraceAsString() . "\n";
    exit;
}

echo "\n=== DB CONNECTION ===\n";
try {
    $kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();
    $pdo = \Illuminate\Support\Facades\DB::connection()->getPdo();
    echo "DB connected OK — " . $pdo->getAttribute(PDO::ATTR_SERVER_VERSION) . "\n";
} catch (\Throwable $e) {
    echo "DB FAILED: " . $e->getMessage() . "\n";
}

echo "\n=== STORAGE PERMISSIONS ===\n";
$dirs = [
    $root . '/storage/logs',
    $root . '/storage/framework/sessions',
    $root . '/storage/framework/cache',
    $root . '/storage/framework/views',
    $root . '/bootstrap/cache',
];
foreach ($dirs as $dir) {
    echo str_pad(str_replace($root.'/', '', $dir), 35)
        . (is_dir($dir) ? (is_writable($dir) ? 'writable' : '** NOT WRITABLE **') : 'MISSING') . "\n";
}

echo "\n=== DONE ===\n";
echo "DELETE this file now: cPanel → File Manager → public_html → diag.php\n";
