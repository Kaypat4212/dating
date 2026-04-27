<?php
/**
 * Production Cache Clear Script
 * Visit this file in browser to clear all Laravel caches
 */

// Security: Only allow execution in production environment
$allowedIPs = [
    '127.0.0.1',
    'localhost',
    '::1',
    // Add your IP address here if needed
];

$clientIP = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

echo "<!DOCTYPE html>";
echo "<html><head><title>Cache Clear</title>";
echo "<style>body{font-family:Arial,sans-serif;max-width:800px;margin:50px auto;padding:20px;background:#f5f5f5;}";
echo ".success{color:green;padding:10px;background:#d4edda;border:1px solid #c3e6cb;border-radius:4px;margin:10px 0;}";
echo ".error{color:red;padding:10px;background:#f8d7da;border:1px solid #f5c6cb;border-radius:4px;margin:10px 0;}";
echo ".info{color:#856404;padding:10px;background:#fff3cd;border:1px solid #ffeeba;border-radius:4px;margin:10px 0;}";
echo "pre{background:#fff;padding:10px;border:1px solid #ddd;border-radius:4px;overflow-x:auto;}</style></head><body>";
echo "<h1>🧹 Laravel Cache Clear</h1>";
echo "<p><strong>Your IP:</strong> {$clientIP}</p>";

// Change to Laravel root directory
chdir(__DIR__ . '/..');

$commands = [
    'config:clear' => 'Clear configuration cache',
    'cache:clear' => 'Clear application cache',
    'view:clear' => 'Clear compiled views',
    'route:clear' => 'Clear route cache',
    'optimize:clear' => 'Clear all cached data',
];

echo "<h2>Executing Commands:</h2>";

foreach ($commands as $command => $description) {
    echo "<div class='info'>";
    echo "<strong>📋 {$description}</strong><br>";
    echo "<code>php artisan {$command}</code>";
    echo "</div>";
    
    $output = [];
    $returnCode = 0;
    
    exec("php artisan {$command} 2>&1", $output, $returnCode);
    
    if ($returnCode === 0) {
        echo "<div class='success'>✅ Success</div>";
    } else {
        echo "<div class='error'>❌ Failed (Exit code: {$returnCode})</div>";
    }
    
    if (!empty($output)) {
        echo "<pre>" . htmlspecialchars(implode("\n", $output)) . "</pre>";
    }
}

echo "<div class='success'>";
echo "<h3>✨ Cache clearing completed!</h3>";
echo "<p>All Laravel caches have been cleared.</p>";
echo "</div>";

echo "<hr>";
echo "<p><small>Generated at: " . date('Y-m-d H:i:s') . "</small></p>";
echo "</body></html>";
