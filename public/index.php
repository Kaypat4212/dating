<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// When using PHP's built-in server (`php -S ... -t public`), the app is served
// from public/ directly (no /dating/public prefix). Ignore ASSET_URL from .env
// so Vite and asset() links resolve to /build/... instead of /dating/public/build/....
if (PHP_SAPI === 'cli-server') {
    putenv('ASSET_URL');
    unset($_ENV['ASSET_URL'], $_SERVER['ASSET_URL']);
}

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(Request::capture());
