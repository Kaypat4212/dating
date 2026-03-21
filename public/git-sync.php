<?php
/**
 * One-time git sync helper.
 * Visit: https://heartsconnect.site/git-sync.php?token=sync2026
 * DELETE this file immediately after use.
 */

if (($_GET['token'] ?? '') !== 'sync2026') {
    http_response_code(403);
    die('Forbidden.');
}

$root = dirname(__DIR__); // Laravel root (one level up from public/)

$commands = [
    'Checkout (discard server-side changes to SmartMatch.php)' =>
        "cd {$root} && git checkout app/Filament/Pages/SmartMatch.php",

    'Pull latest from master' =>
        "cd {$root} && git pull origin master 2>&1",
];

echo '<!DOCTYPE html><html><head><meta charset="utf-8">
<title>Git Sync</title>
<style>
  body{font-family:monospace;background:#0d0d1a;color:#e2e8f0;margin:2rem}
  h1{color:#f43f5e}
  h3{color:#a855f7;margin-top:1.5rem}
  pre{background:#1a1a2e;border:1px solid #333;padding:1rem;border-radius:.5rem;white-space:pre-wrap;word-break:break-all}
  .ok{color:#34d399}.err{color:#f87171}
</style></head><body>';

echo '<h1>Git Sync</h1>';

foreach ($commands as $label => $cmd) {
    echo "<h3>" . htmlspecialchars($label) . "</h3>";
    $output = shell_exec($cmd . ' 2>&1');
    $output = $output ?: '(no output)';
    $class  = (stripos($output, 'error') !== false || stripos($output, 'fatal') !== false) ? 'err' : 'ok';
    echo "<pre class='{$class}'>" . htmlspecialchars($output) . "</pre>";
}

echo '<p style="color:#fbbf24;margin-top:2rem">⚠️ <strong>Delete this file now</strong> via cPanel File Manager → public/git-sync.php</p>';
echo '</body></html>';
