<?php
/**
 * HeartsConnect — Step 1: Extract production zip (no exec/shell needed)
 * ======================================================================
 * 1. Upload dating-production.zip to /home/heartsco/  (one level ABOVE public_html)
 * 2. Upload this file to public_html/
 * 3. Visit: https://heartsconnect.site/unzip.php?t=hc_unzip_2026
 * 4. Then visit /install.php  to complete setup
 * 5. DELETE both files when done!
 */
if (($_GET['t'] ?? '') !== 'hc_unzip_2026') { http_response_code(403); exit('403'); }

set_time_limit(300);
error_reporting(E_ALL);
ini_set('display_errors', '1');

$publicHtml = __DIR__;                          // /home/heartsco/public_html
$root       = dirname($publicHtml);             // /home/heartsco
$zipFile    = $root . '/dating-production.zip'; // uploaded here by you

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><title>HeartsConnect Unzip</title>
<style>
  body{font-family:sans-serif;max-width:860px;margin:30px auto;padding:0 16px}
  h2{color:#e91e63}
  .ok{color:#16a34a;font-weight:600}
  .err{color:#dc2626;font-weight:600}
  pre{background:#1e1e1e;color:#d4d4d4;padding:10px;border-radius:6px;font-size:12px;overflow-x:auto}
  .warn{background:#fef9c3;border:1px solid #fbbf24;padding:12px;border-radius:6px;margin-top:20px}
</style>
</head>
<body>
<h2>🗜 HeartsConnect — Extract Production Zip</h2>
<p style="color:#666">PHP <?= PHP_VERSION ?> | public_html: <?= htmlspecialchars($publicHtml) ?> | App root: <?= htmlspecialchars($root) ?></p>
<hr>

<?php

// ── Pre-flight checks ─────────────────────────────────────────────────────────
echo "<h3>Pre-flight checks</h3>\n";

if (!class_exists('ZipArchive')) {
    echo '<p class="err">✘ ZipArchive extension is not available on this PHP build. Contact your host to enable php-zip.</p>';
    exit;
}
echo '<p class="ok">✔ ZipArchive available</p>';

if (!file_exists($zipFile)) {
    echo '<p class="err">✘ ZIP not found at: ' . htmlspecialchars($zipFile) . '</p>';
    echo '<p>Please upload <code>dating-production.zip</code> to <code>' . htmlspecialchars($root) . '/</code> via cPanel File Manager, then refresh this page.</p>';
    exit;
}
$sizeMB = round(filesize($zipFile) / 1048576, 1);
echo '<p class="ok">✔ ZIP found (' . $sizeMB . ' MB)</p>';

if (!is_writable($root)) {
    echo '<p class="err">✘ ' . htmlspecialchars($root) . '/ is not writable.</p>';
    exit;
}
echo '<p class="ok">✔ Target directory is writable</p>';

// ── Extract ───────────────────────────────────────────────────────────────────
echo "<h3>Extracting…</h3>\n";
flush();

$zip = new ZipArchive();
$res = $zip->open($zipFile);
if ($res !== true) {
    echo '<p class="err">✘ ZipArchive::open() failed with code ' . $res . '</p>';
    exit;
}

$total     = $zip->numFiles;
$extracted = 0;
$errors    = [];

for ($i = 0; $i < $total; $i++) {
    $name = $zip->getNameIndex($i);
    if ($name === false) continue;

    $dest = $root . '/' . $name;

    // Security: prevent path traversal
    if (strpos(realpath(dirname($dest)) ?: $dest, realpath($root)) !== 0) {
        $errors[] = "Skipped (path traversal attempt): $name";
        continue;
    }

    // Directory entry
    if (substr($name, -1) === '/') {
        if (!is_dir($dest)) {
            mkdir($dest, 0755, true);
        }
        continue;
    }

    // File entry — ensure parent dir exists
    $dir = dirname($dest);
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }

    // Skip overwriting the server's own .env if it already exists
    if (basename($name) === '.env' && file_exists($dest)) {
        continue;
    }

    $content = $zip->getFromIndex($i);
    if ($content === false) {
        $errors[] = "Could not read: $name";
        continue;
    }

    file_put_contents($dest, $content);
    $extracted++;
}
$zip->close();

echo '<p class="ok">✔ Extracted ' . $extracted . ' / ' . $total . ' entries to <code>' . htmlspecialchars($root) . '/</code></p>' . "\n";

if ($errors) {
    echo '<pre>' . htmlspecialchars(implode("\n", $errors)) . '</pre>';
}

// ── Move public/ contents → public_html/ ─────────────────────────────────────
// The zip contains a public/ folder. Its contents must live in public_html/.
echo "<h3>Syncing public/ → public_html/</h3>\n";
flush();

$srcPublic = $root . '/public';
if (is_dir($srcPublic)) {
    $moved = 0;
    $iter  = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($srcPublic, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );
    foreach ($iter as $item) {
        $rel  = substr($item->getPathname(), strlen($srcPublic) + 1);
        $dest = $publicHtml . '/' . $rel;
        if ($item->isDir()) {
            if (!is_dir($dest)) mkdir($dest, 0755, true);
        } else {
            // Don't overwrite install/diag scripts so you can still run them
            if (in_array(basename($rel), ['unzip.php','install.php','diag.php'], true)) continue;
            $destDir = dirname($dest);
            if (!is_dir($destDir)) mkdir($destDir, 0755, true);
            copy($item->getPathname(), $dest);
            $moved++;
        }
    }
    echo '<p class="ok">✔ Synced ' . $moved . ' files from public/ → public_html/</p>';
} else {
    echo '<p class="err">✘ public/ directory not found in extracted zip. Check zip contents.</p>';
}

// ── Fix permissions on storage/ and bootstrap/cache/ ─────────────────────────
echo "<h3>Setting permissions</h3>\n";
flush();

$writableDirs = [
    $root . '/storage',
    $root . '/bootstrap/cache',
];
foreach ($writableDirs as $d) {
    if (!is_dir($d)) { echo '<p class="err">✘ Missing: ' . htmlspecialchars($d) . '</p>'; continue; }
    $iter2 = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($d, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );
    chmod($d, 0775);
    foreach ($iter2 as $item) {
        chmod($item->getPathname(), $item->isDir() ? 0775 : 0664);
    }
    echo '<p class="ok">✔ 775/664 set on ' . htmlspecialchars(str_replace($root.'/', '', $d)) . '</p>';
}

// ── Done ─────────────────────────────────────────────────────────────────────
echo '<hr>';
$envExists = file_exists($root . '/.env');
echo '<h3>Next step</h3>';
if (!$envExists) {
    echo '<p class="err">⚠ No <code>.env</code> found at <code>' . htmlspecialchars($root) . '/.env</code></p>';
    echo '<p>Create it now via cPanel File Manager with your production values, then visit <a href="/install.php?t=hc_install_2026"><strong>/install.php?t=hc_install_2026</strong></a></p>';
} else {
    echo '<p class="ok">✔ .env exists — visit <a href="/install.php?t=hc_install_2026"><strong>/install.php?t=hc_install_2026</strong></a> to run key:generate, migrate, optimize</p>';
}
?>
<div class="warn">
  <strong>⚠️ DELETE both unzip.php and install.php when all steps are done!</strong>
</div>
</body>
</html>
