<?php
/**
 * One-time deployment helper for cPanel shared hosting.
 * Upload this file to your public/ folder, visit it once in the browser,
 * then DELETE it immediately via cPanel File Manager.
 *
 * URL: https://heartsconnect.site/deploy-fix.php?token=HC_DEPLOY_2026
 */

define('SECRET_TOKEN', 'HC_DEPLOY_2026');

if (($_GET['token'] ?? '') !== SECRET_TOKEN) {
    http_response_code(403);
    die('<h2 style="font-family:sans-serif;color:red">403 – Forbidden. Wrong or missing token.</h2>');
}

// Bootstrap Laravel so we can use artisan
define('LARAVEL_START', microtime(true));
$base = dirname(__DIR__);

require $base . '/vendor/autoload.php';

$app = require_once $base . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$results = [];

$commands = [
    'config:clear',
    'view:clear',
    'cache:clear',
    'route:clear',
    'event:clear',
    'filament:optimize-clear',
];

foreach ($commands as $cmd) {
    try {
        $exitCode = $kernel->call($cmd);
        $output   = trim($kernel->output());
        $results[$cmd] = ['ok' => $exitCode === 0, 'out' => $output ?: '(no output)'];
    } catch (\Throwable $e) {
        $results[$cmd] = ['ok' => false, 'out' => $e->getMessage()];
    }
}

$kernel->terminate(
    Illuminate\Http\Request::capture(),
    new Illuminate\Http\Response()
);

?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Deploy Fix – HeartsConnect</title>
    <style>
        body { font-family: sans-serif; background:#0f0f0f; color:#e5e5e5; padding:2rem; }
        h1   { color:#f43f5e; }
        .ok  { color:#22c55e; font-weight:bold; }
        .err { color:#ef4444; font-weight:bold; }
        pre  { background:#1e1e1e; padding:1rem; border-radius:6px; font-size:.85rem; }
        .warning { background:#7c2d12; border:1px solid #ef4444; padding:1rem;
                   border-radius:6px; margin-top:2rem; font-size:1.1rem; }
    </style>
</head>
<body>
<h1>🛠 Deploy Fix — HeartsConnect</h1>
<p>Ran at: <strong><?= date('Y-m-d H:i:s T') ?></strong></p>
<table style="border-collapse:collapse;width:100%;max-width:700px">
    <tr style="background:#1e1e1e">
        <th style="text-align:left;padding:.5rem 1rem">Command</th>
        <th style="text-align:left;padding:.5rem 1rem">Status</th>
        <th style="text-align:left;padding:.5rem 1rem">Output</th>
    </tr>
    <?php foreach ($results as $cmd => $r): ?>
    <tr style="border-top:1px solid #333">
        <td style="padding:.5rem 1rem;font-family:monospace">php artisan <?= htmlspecialchars($cmd) ?></td>
        <td style="padding:.5rem 1rem" class="<?= $r['ok'] ? 'ok' : 'err' ?>"><?= $r['ok'] ? '✅ OK' : '❌ FAIL' ?></td>
        <td style="padding:.5rem 1rem;font-size:.8rem;opacity:.7"><?= htmlspecialchars($r['out']) ?></td>
    </tr>
    <?php endforeach; ?>
</table>

<div class="warning">
    ⚠️ <strong>DELETE THIS FILE NOW</strong> — open cPanel File Manager,
    navigate to <code>public_html/public/deploy-fix.php</code> and delete it.
    Leaving it accessible is a security risk.
</div>
</body>
</html>
