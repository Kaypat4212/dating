<?php

/**
 * Root-level Laravel front controller.
 *
 * Allows the site to be served from the project root (e.g. public_html/)
 * without requiring the domain document root to be set to public/.
 * The public/ sub-directory is still used for static assets.
 */

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

if (file_exists($maintenance = __DIR__.'/storage/framework/maintenance.php')) {
    require $maintenance;
}

require __DIR__.'/vendor/autoload.php';

/** @var Application $app */
$app = require_once __DIR__.'/bootstrap/app.php';

$app->handleRequest(Request::capture());
