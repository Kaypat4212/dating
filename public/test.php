<?php
// Minimal connectivity test — no dependencies.
// Visit: https://heartsconnect.site/test.php
// DELETE this file immediately after use!
header('Content-Type: text/plain; charset=utf-8');
echo "PHP " . PHP_VERSION . " OK\n";
echo "Server: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'unknown') . "\n";
echo "Document root: " . ($_SERVER['DOCUMENT_ROOT'] ?? 'unknown') . "\n";
echo "Script path:   " . __FILE__ . "\n";
echo "Project root:  " . dirname(__DIR__) . "\n";
echo "htaccess exists: " . (file_exists(__DIR__ . '/.htaccess') ? 'YES' : 'NO') . "\n";
echo "vendor exists:   " . (is_dir(dirname(__DIR__) . '/vendor') ? 'YES' : 'NO') . "\n";
echo "bootstrap/cache: " . (is_dir(dirname(__DIR__) . '/bootstrap/cache') ? 'YES' : 'NO') . "\n";
