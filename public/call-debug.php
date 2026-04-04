<?php
/**
 * Voice Call Diagnostic Script
 * Upload to public/ then visit https://heartsconnect.cc/call-debug.php?secret=HC_calldebug
 * DELETE after debugging is done.
 */

if (($_GET['secret'] ?? '') !== 'HC_calldebug') {
    http_response_code(403);
    exit('Forbidden');
}

// Boot Laravel
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
// Load config only (no HTTP dispatch)
$app->make('config');

header('Content-Type: text/plain; charset=utf-8');

$pass = '✅';
$fail = '❌';

// ── 1. voice_calls table ──────────────────────────────────────────────────────
echo "=== voice_calls table ===\n";
try {
    $db = $app->make('db');
    $exists = $db->getSchemaBuilder()->hasTable('voice_calls');
    echo ($exists ? $pass : $fail) . " voice_calls table " . ($exists ? "EXISTS" : "MISSING — run migration!") . "\n";
    if ($exists) {
        $cols = $db->getSchemaBuilder()->getColumnListing('voice_calls');
        echo "   Columns: " . implode(', ', $cols) . "\n";
    }
} catch (\Throwable $e) {
    echo "$fail DB error: " . $e->getMessage() . "\n";
}

// ── 2. Agora config ───────────────────────────────────────────────────────────
echo "\n=== Agora config ===\n";
$appId   = config('services.agora.app_id');
$appCert = config('services.agora.app_certificate');

echo ($appId   ? $pass : $fail) . " AGORA_APP_ID:          " . ($appId   ? substr($appId, 0, 8) . '...' : 'NOT SET') . "\n";
echo ($appCert ? $pass : $fail) . " AGORA_APP_CERTIFICATE: " . ($appCert ? substr($appCert, 0, 8) . '...' : 'NOT SET') . "\n";

// ── 3. Broadcast config ───────────────────────────────────────────────────────
echo "\n=== Broadcast config ===\n";
$broadcastConn = config('broadcasting.default');
echo "   BROADCAST_CONNECTION = $broadcastConn\n";

if ($broadcastConn === 'reverb') {
    $host = config('broadcasting.connections.reverb.options.host') ?? env('REVERB_HOST', '0.0.0.0');
    $port = config('broadcasting.connections.reverb.options.port') ?? env('REVERB_PORT', 8080);
    echo "   Reverb host: $host  port: $port\n";

    // Try connecting to Reverb
    echo "   Testing TCP connection to 127.0.0.1:8080 ... ";
    $sock = @fsockopen('127.0.0.1', 8080, $errno, $errstr, 2);
    if ($sock) {
        fclose($sock);
        echo $pass . " Reverb is REACHABLE on port 8080\n";
    } else {
        echo $fail . " Reverb NOT reachable ($errno: $errstr) — is `php artisan reverb:start` running?\n";
    }
} else {
    echo $pass . " Broadcasting set to '$broadcastConn' (Reverb not required)\n";
}

// ── 4. AgoraTokenService smoke test ──────────────────────────────────────────
echo "\n=== AgoraTokenService ===\n";
try {
    $agora = $app->make(\App\Services\AgoraTokenService::class);
    if ($agora->isConfigured()) {
        $token = $agora->generateRtcToken('test-channel', 1);
        echo $pass . " Token generated OK (length: " . strlen($token) . ")\n";
    } else {
        echo $fail . " AgoraTokenService not configured (keys empty)\n";
    }
} catch (\Throwable $e) {
    echo $fail . " AgoraTokenService error: " . $e->getMessage() . "\n";
}

// ── 5. .env check ────────────────────────────────────────────────────────────
echo "\n=== Key env variables ===\n";
$envVars = [
    'APP_ENV', 'APP_KEY', 'DB_CONNECTION', 'DB_DATABASE',
    'BROADCAST_CONNECTION', 'AGORA_APP_ID', 'AGORA_APP_CERTIFICATE',
    'VITE_REVERB_HOST', 'VITE_REVERB_PORT', 'VITE_REVERB_SCHEME',
];
foreach ($envVars as $v) {
    $val = env($v, '');
    $icon = $val ? $pass : '⚠️';
    $display = in_array($v, ['APP_KEY', 'AGORA_APP_CERTIFICATE']) ? ($val ? '(set)' : '(empty)') : $val;
    echo "$icon $v = $display\n";
}

echo "\n=== Done ===\n";
echo "Remember to DELETE this file after debugging!\n";
