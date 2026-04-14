<?php
/**
 * Quick check: What's in database vs .env for Firebase
 */

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== FIREBASE CONFIGURATION CHECK ===\n\n";

echo "DATABASE (Admin Panel) Settings:\n";
echo str_repeat("-", 50) . "\n";
$dbSettings = \App\Models\SiteSetting::where('key', 'LIKE', 'firebase%')->get();
foreach ($dbSettings as $setting) {
    $value = strlen($setting->value) > 40 ? substr($setting->value, 0, 40) . '...' : $setting->value;
    echo sprintf("%-30s = %s\n", $setting->key, $value ?: '(empty)');
}

echo "\n.ENV File Settings:\n";
echo str_repeat("-", 50) . "\n";
$envKeys = [
    'FIREBASE_API_KEY',
    'FIREBASE_PROJECT_ID',
    'FIREBASE_MESSAGING_SENDER_ID',
    'FIREBASE_APP_ID',
    'FIREBASE_VAPID_KEY',
];

foreach ($envKeys as $key) {
    $value = env($key);
    $display = $value ? (strlen($value) > 40 ? substr($value, 0, 40) . '...' : $value) : '(not set)';
    echo sprintf("%-30s = %s\n", $key, $display);
}

echo "\nWHAT FIREBASECLOUDMESSAGINGSERVICE SEES:\n";
echo str_repeat("-", 50) . "\n";
$fcm = app(\App\Services\FirebaseCloudMessagingService::class);
$reflection = new \ReflectionClass($fcm);

$apiKeyProp = $reflection->getProperty('apiKey');
$apiKeyProp->setAccessible(true);
$activeApiKey = $apiKeyProp->getValue($fcm);

$projectIdProp = $reflection->getProperty('projectId');
$projectIdProp->setAccessible(true);
$activeProjectId = $projectIdProp->getValue($fcm);

echo "API Key: " . ($activeApiKey ? substr($activeApiKey, 0, 40) . '...' : '(MISSING)') . "\n";
echo "Project ID: " . ($activeProjectId ?: '(MISSING)') . "\n";

$dbApiKey = \App\Models\SiteSetting::get('firebase_api_key');
if ($dbApiKey && $activeApiKey === $dbApiKey) {
    echo "\nSource: ✅ Using DATABASE (Admin Panel)\n";
} elseif ($activeApiKey === env('FIREBASE_API_KEY')) {
    echo "\nSource: ✅ Using .ENV file (fallback)\n";
} else {
    echo "\nSource: ❌ Unknown or not configured\n";
}
