<?php
/**
 * Clear All Laravel Caches
 * Upload this to public/ folder and visit: https://heartsconnect.cc/clear-all-cache.php
 * 
 * This clears:
 * - Config cache
 * - Route cache
 * - View cache
 * - Application cache
 * - Compiled services
 */

// Change to Laravel root directory
chdir(__DIR__ . '/..');

// Check if we're in the right directory
if (!file_exists('artisan')) {
    die('❌ Error: artisan file not found. Make sure this script is in the public/ folder.');
}

echo "<pre style='background:#1a1a1a;color:#00ff00;padding:20px;font-family:monospace;'>";
echo "╔════════════════════════════════════════════════╗\n";
echo "║  Hearts Connect - Clear All Caches             ║\n";
echo "╚════════════════════════════════════════════════╝\n\n";

$commands = [
    'config:clear'   => 'Clearing config cache...',
    'cache:clear'    => 'Clearing application cache...',
    'route:clear'    => 'Clearing route cache...',
    'view:clear'     => 'Clearing view cache...',
    'clear-compiled' => 'Clearing compiled services...',
];

foreach ($commands as $command => $message) {
    echo "🔧 $message\n";
    
    // Execute artisan command
    $output = [];
    $returnVar = 0;
    exec("php artisan $command 2>&1", $output, $returnVar);
    
    if ($returnVar === 0) {
        echo "   ✅ Success: " . implode("\n   ", $output) . "\n\n";
    } else {
        echo "   ⚠️  Warning: " . implode("\n   ", $output) . "\n\n";
    }
}

echo "════════════════════════════════════════════════\n";
echo "✅ All caches cleared!\n\n";

// Test Daily.co configuration
echo "📋 Testing Daily.co Configuration:\n";
echo "════════════════════════════════════════════════\n\n";

// Load .env file
if (file_exists('.env')) {
    $envContent = file_get_contents('.env');
    
    // Check for DAILY_CO_API_KEY
    if (preg_match('/^DAILY_CO_API_KEY=(.+)$/m', $envContent, $matches)) {
        $apiKey = trim($matches[1]);
        if (!empty($apiKey) && $apiKey !== 'your-daily-api-key') {
            echo "✅ DAILY_CO_API_KEY: Set (" . substr($apiKey, 0, 10) . "...)\n";
        } else {
            echo "❌ DAILY_CO_API_KEY: Empty or default value\n";
        }
    } else {
        echo "❌ DAILY_CO_API_KEY: Not found in .env\n";
    }
    
    // Check for DAILY_CO_DOMAIN
    if (preg_match('/^DAILY_CO_DOMAIN=(.+)$/m', $envContent, $matches)) {
        $domain = trim($matches[1]);
        if (!empty($domain) && $domain !== 'your-domain.daily.co') {
            echo "✅ DAILY_CO_DOMAIN: Set ($domain)\n";
        } else {
            echo "❌ DAILY_CO_DOMAIN: Empty or default value\n";
        }
    } else {
        echo "❌ DAILY_CO_DOMAIN: Not found in .env\n";
    }
} else {
    echo "❌ .env file not found!\n";
}

echo "\n════════════════════════════════════════════════\n";
echo "🎯 Next Steps:\n";
echo "════════════════════════════════════════════════\n\n";
echo "1. Go to Admin Panel → API Key Tester\n";
echo "2. Test Daily.co connection\n";
echo "3. If still not working, check .env file has:\n";
echo "   DAILY_CO_API_KEY=04ca3d1b4865bfd6d60c10895055b37fb5e00f0cd478a102743bd14e72a44be3\n";
echo "   DAILY_CO_DOMAIN=your-domain.daily.co\n\n";

echo "🔒 Delete this file after use for security!\n";
echo "   rm public/clear-all-cache.php\n\n";

echo "</pre>";
