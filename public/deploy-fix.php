<?php
/**
 * Standalone production fix script — requires NO vendor/Laravel.
 * Works even when the app is completely broken.
 *
 * 1. Upload this file to your public/ folder via cPanel File Manager.
 * 2. Visit: https://heartsconnect.site/deploy-fix.php?token=HC_DEPLOY_2026
 * 3. Confirm all items show ✅
 * 4. DELETE this file immediately after.
 */

if (($_GET['token'] ?? '') !== 'HC_DEPLOY_2026') {
    http_response_code(403);
    die('<h2 style="font-family:monospace;color:red">403 – wrong token</h2>');
}

$results = [];
$base    = dirname(__DIR__); // Laravel project root (one level above public/)

// ── 1. Fix public/.htaccess — remove Options +FollowSymLinks ────────────────
$htaccessPath = __DIR__ . DIRECTORY_SEPARATOR . '.htaccess';
if (file_exists($htaccessPath)) {
    $orig = file_get_contents($htaccessPath);
    // Remove the three offending lines
    $fixed = preg_replace('/^#[^\n]*FollowSymLinks[^\n]*\n?/m', '', $orig);
    $fixed = preg_replace('/^#[^\n]*Required for cPanel[^\n]*\n?/m', '', $fixed);
    $fixed = preg_replace('/^Options \+FollowSymLinks[^\n]*\n?/m', '', $fixed);
    $fixed = preg_replace('/\n{3,}/', "\n\n", ltrim($fixed, "\n"));
    if ($fixed !== $orig) {
        file_put_contents($htaccessPath, $fixed);
        $results[] = ['ok' => true,  'label' => 'public/.htaccess', 'msg' => 'Removed "Options +FollowSymLinks"'];
    } else {
        $results[] = ['ok' => true,  'label' => 'public/.htaccess', 'msg' => 'Already clean — no changes needed'];
    }
} else {
    $results[] = ['ok' => false, 'label' => 'public/.htaccess', 'msg' => 'File not found at: ' . $htaccessPath];
}

// ── 2. Fix SmartMatch.php — remove "static" from $view property ─────────────
$smartMatchPath = $base . '/app/Filament/Pages/SmartMatch.php';
if (file_exists($smartMatchPath)) {
    $orig  = file_get_contents($smartMatchPath);
    $fixed = str_replace('protected static string $view', 'protected string $view', $orig);
    if ($fixed !== $orig) {
        file_put_contents($smartMatchPath, $fixed);
        $results[] = ['ok' => true,  'label' => 'SmartMatch.php', 'msg' => 'Removed "static" from $view — PHP fatal error fixed'];
    } else {
        $results[] = ['ok' => true,  'label' => 'SmartMatch.php', 'msg' => 'Already correct — no changes needed'];
    }
} else {
    $results[] = ['ok' => null, 'label' => 'SmartMatch.php', 'msg' => 'Not found (skipped)'];
}

// ── 3. Delete bootstrap/cache PHP files (config, routes, services) ───────────
$bootCache  = $base . '/bootstrap/cache';
$bootCleared = 0;
if (is_dir($bootCache)) {
    foreach (glob($bootCache . '/*.php') as $f) {
        if (@unlink($f)) $bootCleared++;
    }
}
$results[] = ['ok' => true, 'label' => 'bootstrap/cache', 'msg' => "Deleted {$bootCleared} cached file(s)"];

// ── 4. Delete compiled Blade views ───────────────────────────────────────────
$viewsDir    = $base . '/storage/framework/views';
$viewCleared = 0;
if (is_dir($viewsDir)) {
    foreach (glob($viewsDir . '/*.php') as $f) {
        if (@unlink($f)) $viewCleared++;
    }
}
$results[] = ['ok' => true, 'label' => 'Compiled views', 'msg' => "Deleted {$viewCleared} view file(s)"];

// ── 5. Delete application cache files ────────────────────────────────────────
function _rmdir_contents(string $dir): int {
    $n = 0;
    if (!is_dir($dir)) return 0;
    $it = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );
    foreach ($it as $f) {
        if ($f->isFile() && $f->getFilename() !== '.gitignore') {
            if (@unlink($f->getPathname())) $n++;
        }
    }
    return $n;
}
$cacheCleared = _rmdir_contents($base . '/storage/framework/cache/data');
$results[] = ['ok' => true, 'label' => 'Application cache', 'msg' => "Deleted {$cacheCleared} cache file(s)"];

// ── 6. Delete session files ─────────────────────────────────────────────────
$sessCleared = _rmdir_contents($base . '/storage/framework/sessions');
$results[] = ['ok' => true, 'label' => 'Sessions', 'msg' => "Deleted {$sessCleared} session file(s)"];

?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Fix – HeartsConnect</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: monospace; background: #0f0f0f; color: #e5e5e5; padding: 2rem; margin: 0; }
        h1   { color: #f43f5e; font-size: 1.4rem; }
        table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
        th, td { text-align: left; padding: .5rem .75rem; border-bottom: 1px solid #2a2a2a; font-size: .85rem; }
        th { background: #1a1a1a; color: #aaa; }
        .ok   { color: #22c55e; font-weight: bold; }
        .fail { color: #ef4444; font-weight: bold; }
        .skip { color: #888; }
        .warn { background: #450a0a; border: 1px solid #7f1d1d; border-radius: 6px;
                padding: 1rem 1.25rem; margin-top: 1.5rem; color: #fca5a5; font-size: .9rem; }
        .info { background: #052e16; border: 1px solid #166534; border-radius: 6px;
                padding: 1rem 1.25rem; margin-top: 1rem; color: #86efac; font-size: .9rem; }
    </style>
</head>
<body>
<h1>🛠 HeartsConnect — Production Fix</h1>
<p style="color:#888;font-size:.8rem">Run at: <?= date('Y-m-d H:i:s T') ?> &nbsp;|&nbsp; PHP <?= PHP_VERSION ?> &nbsp;|&nbsp; Server root: <?= htmlspecialchars($base) ?></p>

<table>
    <tr><th>Step</th><th>Result</th><th>Detail</th></tr>
    <?php foreach ($results as $r):
        $cls = $r['ok'] === true ? 'ok' : ($r['ok'] === false ? 'fail' : 'skip');
        $icon = $r['ok'] === true ? '✅' : ($r['ok'] === false ? '❌' : 'ℹ️');
    ?>
    <tr>
        <td><?= htmlspecialchars($r['label']) ?></td>
        <td class="<?= $cls ?>"><?= $icon ?></td>
        <td><?= htmlspecialchars($r['msg']) ?></td>
    </tr>
    <?php endforeach; ?>
</table>

<div class="info">
    ✅ <strong>If all rows show green</strong>: Refresh <a href="https://heartsconnect.site" style="color:#4ade80">heartsconnect.site</a> — it should be working now.
</div>

<div class="warn">
    ⚠️ <strong>DELETE this file now!</strong> Go to cPanel → File Manager → find
    <code>public/deploy-fix.php</code> and delete it. Security risk to leave it.
</div>
</body>
</html>
