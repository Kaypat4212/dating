<?php
/**
 * Firebase & Database Configuration Diagnostic Tool
 * 
 * Usage: Visit https://yoursite.com/public/firebase-config-check.php
 * Or run: php public/firebase-config-check.php
 */

// Bootstrap Laravel
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "<!DOCTYPE html>
<html>
<head>
    <title>Configuration Diagnostic</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; max-width: 1000px; margin: 40px auto; padding: 20px; background: #f5f5f5; }
        .panel { background: white; border-radius: 8px; padding: 20px; margin: 20px 0; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        h1 { color: #333; border-bottom: 3px solid #4CAF50; padding-bottom: 10px; }
        h2 { color: #555; margin-top: 0; }
        .success { color: #4CAF50; font-weight: bold; }
        .error { color: #f44336; font-weight: bold; }
        .warning { color: #ff9800; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        th, td { text-align: left; padding: 12px; border-bottom: 1px solid #ddd; }
        th { background: #f8f9fa; font-weight: 600; }
        .code { background: #f4f4f4; padding: 15px; border-left: 4px solid #4CAF50; font-family: 'Courier New', monospace; margin: 10px 0; overflow-x: auto; }
        .badge { display: inline-block; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold; }
        .badge-ok { background: #4CAF50; color: white; }
        .badge-error { background: #f44336; color: white; }
        .badge-warning { background: #ff9800; color: white; }
        .badge-info { background: #2196F3; color: white; }
    </style>
</head>
<body>
    <h1>🔍 Configuration Diagnostic Report</h1>
    <p>Generated: " . date('Y-m-d H:i:s') . "</p>
";

// ============================================================================
// DATABASE CONFIGURATION
// ============================================================================
echo "<div class='panel'>";
echo "<h2>💾 Database Configuration</h2>";

try {
    $dbConnection = config('database.default');
    $dbHost = config('database.connections.' . $dbConnection . '.host');
    $dbPort = config('database.connections.' . $dbConnection . '.port');
    $dbName = config('database.connections.' . $dbConnection . '.database');
    $dbUser = config('database.connections.' . $dbConnection . '.username');
    
    echo "<table>";
    echo "<tr><th>Setting</th><th>Value</th><th>Source</th></tr>";
    echo "<tr><td>Connection Type</td><td><span class='badge badge-info'>$dbConnection</span></td><td>.env (DB_CONNECTION)</td></tr>";
    echo "<tr><td>Host</td><td>$dbHost</td><td>.env (DB_HOST)</td></tr>";
    echo "<tr><td>Port</td><td>$dbPort</td><td>.env (DB_PORT)</td></tr>";
    echo "<tr><td>Database Name</td><td><strong>$dbName</strong></td><td>.env (DB_DATABASE)</td></tr>";
    echo "<tr><td>Username</td><td>$dbUser</td><td>.env (DB_USERNAME)</td></tr>";
    echo "<tr><td>Password</td><td>" . (config('database.connections.' . $dbConnection . '.password') ? '***SET***' : '<span class="error">EMPTY</span>') . "</td><td>.env (DB_PASSWORD)</td></tr>";
    echo "</table>";
    
    // Test connection
    try {
        $pdo = DB::connection()->getPdo();
        $actualDb = DB::connection()->getDatabaseName();
        echo "<p class='success'>✅ Database Connection: SUCCESSFUL</p>";
        echo "<p>Connected to: <strong>$actualDb</strong></p>";
        
        // Count site_settings
        $settingsCount = DB::table('site_settings')->count();
        echo "<p>Site Settings Records: <strong>$settingsCount</strong></p>";
        
    } catch (\Exception $e) {
        echo "<p class='error'>❌ Database Connection: FAILED</p>";
        echo "<div class='code'>" . htmlspecialchars($e->getMessage()) . "</div>";
    }
    
} catch (\Exception $e) {
    echo "<p class='error'>❌ Error reading database config</p>";
    echo "<div class='code'>" . htmlspecialchars($e->getMessage()) . "</div>";
}

echo "<div class='code'>";
echo "# Database credentials are stored in:<br>";
echo "📁 <strong>.env</strong> (Lines 29-34)<br><br>";
echo "DB_CONNECTION=mysql<br>";
echo "DB_HOST=localhost<br>";
echo "DB_PORT=3306<br>";
echo "DB_DATABASE=dating<br>";
echo "DB_USERNAME=root<br>";
echo "DB_PASSWORD=";
echo "</div>";
echo "</div>";

// ============================================================================
// FIREBASE CONFIGURATION - ENV FILE
// ============================================================================
echo "<div class='panel'>";
echo "<h2>🔥 Firebase Configuration (.env file)</h2>";

$envFirebase = [
    'API Key' => env('FIREBASE_API_KEY'),
    'Project ID' => env('FIREBASE_PROJECT_ID'),
    'Messaging Sender ID' => env('FIREBASE_MESSAGING_SENDER_ID'),
    'App ID' => env('FIREBASE_APP_ID'),
    'VAPID Key' => env('FIREBASE_VAPID_KEY'),
];

echo "<table>";
echo "<tr><th>Setting</th><th>Status</th><th>Value (First 30 chars)</th></tr>";
foreach ($envFirebase as $key => $value) {
    if ($value) {
        $badge = "<span class='badge badge-ok'>SET</span>";
        $displayValue = substr($value, 0, 30) . (strlen($value) > 30 ? '...' : '');
    } else {
        $badge = "<span class='badge badge-error'>EMPTY</span>";
        $displayValue = "<span class='error'>Not configured</span>";
    }
    echo "<tr><td>$key</td><td>$badge</td><td>$displayValue</td></tr>";
}
echo "</table>";

$envConfigCount = count(array_filter($envFirebase));
if ($envConfigCount === 5) {
    echo "<p class='success'>✅ All .env Firebase values are configured</p>";
} elseif ($envConfigCount > 0) {
    echo "<p class='warning'>⚠️ Partial configuration: $envConfigCount/5 values set</p>";
} else {
    echo "<p class='error'>❌ No .env Firebase values configured</p>";
}

echo "<div class='code'>";
echo "# .env file location: Lines 113-117<br>";
echo "FIREBASE_API_KEY=" . (env('FIREBASE_API_KEY') ?: '<span class="error">empty</span>') . "<br>";
echo "FIREBASE_PROJECT_ID=" . (env('FIREBASE_PROJECT_ID') ?: '<span class="error">empty</span>') . "<br>";
echo "FIREBASE_MESSAGING_SENDER_ID=" . (env('FIREBASE_MESSAGING_SENDER_ID') ?: '<span class="error">empty</span>') . "<br>";
echo "FIREBASE_APP_ID=" . (env('FIREBASE_APP_ID') ?: '<span class="error">empty</span>') . "<br>";
echo "FIREBASE_VAPID_KEY=" . (env('FIREBASE_VAPID_KEY') ?: '<span class="error">empty</span>');
echo "</div>";
echo "</div>";

// ============================================================================
// FIREBASE CONFIGURATION - DATABASE (Admin Panel)
// ============================================================================
echo "<div class='panel'>";
echo "<h2>🎛️ Firebase Configuration (Database - Admin Panel)</h2>";

try {
    $dbFirebase = [
        'Enabled' => \App\Models\SiteSetting::get('firebase_enabled'),
        'API Key' => \App\Models\SiteSetting::get('firebase_api_key'),
        'Auth Domain' => \App\Models\SiteSetting::get('firebase_auth_domain'),
        'Project ID' => \App\Models\SiteSetting::get('firebase_project_id'),
        'Storage Bucket' => \App\Models\SiteSetting::get('firebase_storage_bucket'),
        'Messaging Sender ID' => \App\Models\SiteSetting::get('firebase_messaging_sender_id'),
        'App ID' => \App\Models\SiteSetting::get('firebase_app_id'),
        'Measurement ID' => \App\Models\SiteSetting::get('firebase_measurement_id'),
    ];

    echo "<table>";
    echo "<tr><th>Setting</th><th>Status</th><th>Value (First 30 chars)</th></tr>";
    foreach ($dbFirebase as $key => $value) {
        if ($key === 'Enabled') {
            $badge = $value === '1' ? "<span class='badge badge-ok'>YES</span>" : "<span class='badge badge-error'>NO</span>";
            $displayValue = $value === '1' ? 'Enabled' : 'Disabled';
        } elseif ($value) {
            $badge = "<span class='badge badge-ok'>SET</span>";
            $displayValue = substr($value, 0, 30) . (strlen($value) > 30 ? '...' : '');
        } else {
            $badge = "<span class='badge badge-warning'>EMPTY</span>";
            $displayValue = "<span class='warning'>Not set in admin panel</span>";
        }
        echo "<tr><td>$key</td><td>$badge</td><td>$displayValue</td></tr>";
    }
    echo "</table>";

    $dbConfigCount = count(array_filter($dbFirebase));
    if ($dbConfigCount >= 6) {
        echo "<p class='success'>✅ Database Firebase configuration looks good</p>";
    } elseif ($dbConfigCount > 0) {
        echo "<p class='warning'>⚠️ Partial configuration: $dbConfigCount values set</p>";
    } else {
        echo "<p class='error'>❌ No database Firebase values configured</p>";
    }

} catch (\Exception $e) {
    echo "<p class='error'>❌ Error reading database settings</p>";
    echo "<div class='code'>" . htmlspecialchars($e->getMessage()) . "</div>";
}

echo "<div class='code'>";
echo "# Configured via: Admin Panel → Firebase & Analytics<br>";
echo "# Stored in: site_settings database table<br>";
echo "# Managed by: App\\Models\\SiteSetting";
echo "</div>";
echo "</div>";

// ============================================================================
// ACTIVE CONFIGURATION (What Services Actually Use)
// ============================================================================
echo "<div class='panel'>";
echo "<h2>⚡ Active Configuration (What FirebaseCloudMessagingService Uses)</h2>";

try {
    $fcmService = app(\App\Services\FirebaseCloudMessagingService::class);
    
    // Use reflection to access protected properties
    $reflection = new \ReflectionClass($fcmService);
    $apiKeyProp = $reflection->getProperty('apiKey');
    $apiKeyProp->setAccessible(true);
    $activeApiKey = $apiKeyProp->getValue($fcmService);
    
    $projectIdProp = $reflection->getProperty('projectId');
    $projectIdProp->setAccessible(true);
    $activeProjectId = $projectIdProp->getValue($fcmService);
    
    echo "<table>";
    echo "<tr><th>Setting</th><th>Status</th><th>Value</th><th>Source Priority</th></tr>";
    
    $apiKeySource = \App\Models\SiteSetting::get('firebase_api_key') ? 'Database (Admin Panel)' : 'Fallback to .env';
    $projectIdSource = \App\Models\SiteSetting::get('firebase_project_id') ? 'Database (Admin Panel)' : 'Fallback to .env';
    
    if ($activeApiKey) {
        echo "<tr><td>API Key</td><td><span class='badge badge-ok'>ACTIVE</span></td><td>" . substr($activeApiKey, 0, 30) . "...</td><td>$apiKeySource</td></tr>";
    } else {
        echo "<tr><td>API Key</td><td><span class='badge badge-error'>MISSING</span></td><td class='error'>Not configured</td><td>—</td></tr>";
    }
    
    if ($activeProjectId) {
        echo "<tr><td>Project ID</td><td><span class='badge badge-ok'>ACTIVE</span></td><td>$activeProjectId</td><td>$projectIdSource</td></tr>";
    } else {
        echo "<tr><td>Project ID</td><td><span class='badge badge-error'>MISSING</span></td><td class='error'>Not configured</td><td>—</td></tr>";
    }
    
    echo "</table>";
    
    if ($activeApiKey && $activeProjectId) {
        echo "<p class='success'>✅ FirebaseCloudMessagingService is properly configured</p>";
        echo "<p>The service will use values from: <strong>$apiKeySource</strong></p>";
    } else {
        echo "<p class='error'>❌ FirebaseCloudMessagingService is NOT configured</p>";
        echo "<p>Please configure Firebase in either Admin Panel or .env file</p>";
    }
    
} catch (\Exception $e) {
    echo "<p class='error'>❌ Error checking active configuration</p>";
    echo "<div class='code'>" . htmlspecialchars($e->getMessage()) . "</div>";
}

echo "<div class='code'>";
echo "# Configuration Priority:<br>";
echo "1. <strong>Database (Admin Panel)</strong> - Checked first<br>";
echo "2. <strong>.env file</strong> - Fallback if database is empty<br><br>";
echo "# This allows you to configure Firebase from the admin panel<br>";
echo "# without needing to SSH and edit .env manually";
echo "</div>";
echo "</div>";

// ============================================================================
// RECOMMENDATIONS
// ============================================================================
echo "<div class='panel'>";
echo "<h2>💡 Recommendations</h2>";

$recommendations = [];

// Check database
if (!isset($actualDb)) {
    $recommendations[] = "<span class='error'>❌ Fix database connection</span> - Check your DB credentials in .env";
}

// Check Firebase
$hasEnvFirebase = !empty(env('FIREBASE_API_KEY'));
$hasDbFirebase = !empty(\App\Models\SiteSetting::get('firebase_api_key'));

if (!$hasEnvFirebase && !$hasDbFirebase) {
    $recommendations[] = "<span class='error'>❌ Configure Firebase</span> - Set up Firebase in Admin Panel → Firebase & Analytics";
} elseif ($hasDbFirebase && !$hasEnvFirebase) {
    $recommendations[] = "<span class='success'>✅ Configuration OK</span> - Firebase is configured via Admin Panel (recommended)";
} elseif ($hasEnvFirebase && !$hasDbFirebase) {
    $recommendations[] = "<span class='warning'>⚠️ Consider Admin Panel</span> - You're using .env. Consider configuring via Admin Panel for easier management";
} else {
    $recommendations[] = "<span class='success'>✅ Dual Configuration</span> - Both .env and Admin Panel are configured. Admin Panel takes priority.";
}

if (empty($recommendations)) {
    echo "<p class='success'>✅ Everything looks good! No recommendations at this time.</p>";
} else {
    echo "<ul>";
    foreach ($recommendations as $rec) {
        echo "<li>$rec</li>";
    }
    echo "</ul>";
}

echo "</div>";

// ============================================================================
// QUICK ACTIONS
// ============================================================================
echo "<div class='panel'>";
echo "<h2>🚀 Quick Actions</h2>";

echo "<div class='code'>";
echo "# To configure Firebase via Admin Panel:<br>";
echo "1. Login to admin dashboard<br>";
echo "2. Navigate to: <strong>Firebase & Analytics</strong><br>";
echo "3. Enable Firebase and fill in the credentials<br>";
echo "4. Click Save Settings<br><br>";
echo "# To manually edit .env:<br>";
echo "1. SSH to your server<br>";
echo "2. Edit: <strong>.env</strong> file (lines 113-117)<br>";
echo "3. Run: <strong>php artisan config:clear</strong><br>";
echo "4. Refresh this page to verify";
echo "</div>";

echo "<p style='text-align: center; margin-top: 20px;'>";
echo "<a href='?' style='background: #4CAF50; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; display: inline-block;'>🔄 Refresh Report</a>";
echo "</p>";

echo "</div>";

echo "</body></html>";
