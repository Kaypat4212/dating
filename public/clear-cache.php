<?php
/**
 * Cache clearing utility for production deployment
 * Visit https://heartsconnect.cc/clear-cache.php after deployment
 * DELETE THIS FILE after use for security
 */

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

echo "<h1>Clearing Laravel Caches...</h1>";
echo "<pre>";

echo "Clearing view cache...\n";
$kernel->call('view:clear');

echo "Clearing config cache...\n";
$kernel->call('config:clear');

echo "Clearing route cache...\n";
$kernel->call('route:clear');

echo "Clearing application cache...\n";
$kernel->call('cache:clear');

echo "\n✅ All caches cleared successfully!\n";
echo "\n⚠️ Remember to DELETE this file immediately for security!\n";
echo "</pre>";
