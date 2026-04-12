<?php
/**
 * Daily.co API Key Diagnostic
 * Access: /call-test.php
 * Delete this file after debugging!
 */

// Load Laravel env without booting the full application
$envFile = __DIR__ . '/../.env';
if (!file_exists($envFile)) {
    die('Cannot find .env file');
}

// Parse .env
$env = [];
foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
    if (str_starts_with(trim($line), '#') || !str_contains($line, '=')) continue;
    [$key, $val] = explode('=', $line, 2);
    $env[trim($key)] = trim($val, " \"'");
}

$apiKey = $env['DAILY_CO_API_KEY'] ?? '';
$domain = $env['DAILY_CO_DOMAIN'] ?? '';
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Daily.co Diagnostic</title>
<style>
  body { font-family: monospace; background: #0f172a; color: #e2e8f0; padding: 32px; }
  h1 { color: #f43f5e; }
  .box { background: #1e293b; border-radius: 8px; padding: 20px; margin: 16px 0; }
  .ok { color: #4ade80; } .warn { color: #facc15; } .err { color: #f87171; }
  pre { white-space: pre-wrap; word-break: break-all; }
  .label { color: #94a3b8; font-size: .85em; }
</style>
</head>
<body>
<h1>🔧 Daily.co API Diagnostic</h1>

<div class="box">
  <div class="label">DAILY_CO_API_KEY</div>
  <?php if (empty($apiKey)): ?>
    <span class="err">❌ NOT SET — add DAILY_CO_API_KEY=... to your .env</span>
  <?php else: ?>
    <span class="ok">✅ Set (<?= strlen($apiKey) ?> chars): <?= htmlspecialchars(substr($apiKey, 0, 8)) ?>…<?= htmlspecialchars(substr($apiKey, -4)) ?></span>
  <?php endif; ?>
  <br><br>
  <div class="label">DAILY_CO_DOMAIN</div>
  <?php if (empty($domain)): ?>
    <span class="warn">⚠️ NOT SET (optional — room URLs come from API response)</span>
  <?php else: ?>
    <span class="ok">✅ Set: <?= htmlspecialchars($domain) ?></span>
  <?php endif; ?>
</div>

<?php if (!empty($apiKey)): ?>

<div class="box">
  <b>Test 1: Verify API key (GET /v1/rooms)</b><br><br>
  <?php
  $ch = curl_init('https://api.daily.co/v1/rooms');
  curl_setopt_array($ch, [
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_HTTPHEADER => ["Authorization: Bearer $apiKey", "Content-Type: application/json"],
      CURLOPT_TIMEOUT => 10,
  ]);
  $body = curl_exec($ch);
  $http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  $err  = curl_error($ch);
  curl_close($ch);

  if ($err): ?>
    <span class="err">❌ cURL error: <?= htmlspecialchars($err) ?></span>
  <?php elseif ($http === 200): ?>
    <span class="ok">✅ API key is VALID (HTTP 200)</span>
  <?php elseif ($http === 401): ?>
    <span class="err">❌ API key INVALID or expired (HTTP 401). Get a fresh key from dashboard.daily.co</span>
  <?php else: ?>
    <span class="warn">⚠️ Unexpected status: HTTP <?= $http ?></span>
  <?php endif; ?>
  <br><br><pre><?= htmlspecialchars(json_encode(json_decode($body), JSON_PRETTY_PRINT)) ?></pre>
</div>

<div class="box">
  <b>Test 2: Create a test room</b><br><br>
  <?php
  $testRoom = 'hc-test-' . substr(md5(time()), 0, 8);
  $payload  = json_encode([
      'name'       => $testRoom,
      'properties' => ['exp' => time() + 600, 'max_participants' => 2],
  ]);
  $ch = curl_init('https://api.daily.co/v1/rooms');
  curl_setopt_array($ch, [
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_POST           => true,
      CURLOPT_POSTFIELDS     => $payload,
      CURLOPT_HTTPHEADER     => ["Authorization: Bearer $apiKey", "Content-Type: application/json"],
      CURLOPT_TIMEOUT        => 10,
  ]);
  $roomBody = curl_exec($ch);
  $roomHttp = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  $roomErr  = curl_error($ch);
  curl_close($ch);
  $roomData = json_decode($roomBody, true);

  if ($roomErr): ?>
    <span class="err">❌ cURL error: <?= htmlspecialchars($roomErr) ?></span>
  <?php elseif ($roomHttp === 200 && !empty($roomData['url'])): ?>
    <span class="ok">✅ Room created successfully!</span><br>
    <b>Room URL:</b> <a href="<?= htmlspecialchars($roomData['url']) ?>" target="_blank" style="color:#60a5fa"><?= htmlspecialchars($roomData['url']) ?></a><br>
    <small class="warn">↑ Open this in a new tab to test your Daily.co room directly</small>
  <?php elseif ($roomHttp === 400 && str_contains($roomBody, 'already exists')): ?>
    <span class="warn">⚠️ Room name already exists (benign, means API key works)</span>
  <?php else: ?>
    <span class="err">❌ Room creation failed (HTTP <?= $roomHttp ?>)</span>
  <?php endif; ?>
  <br><br><pre><?= htmlspecialchars(json_encode(json_decode($roomBody), JSON_PRETTY_PRINT)) ?></pre>
</div>

<div class="box">
  <b>Test 3: Create a meeting token</b><br><br>
  <?php
  $tokenPayload = json_encode(['properties' => ['room_name' => $testRoom, 'user_id' => 'test-user', 'exp' => time() + 600]]);
  $ch = curl_init('https://api.daily.co/v1/meeting-tokens');
  curl_setopt_array($ch, [
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_POST           => true,
      CURLOPT_POSTFIELDS     => $tokenPayload,
      CURLOPT_HTTPHEADER     => ["Authorization: Bearer $apiKey", "Content-Type: application/json"],
      CURLOPT_TIMEOUT        => 10,
  ]);
  $tokBody = curl_exec($ch);
  $tokHttp = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  curl_close($ch);
  $tokData = json_decode($tokBody, true);

  if ($tokHttp === 200 && !empty($tokData['token'])): ?>
    <span class="ok">✅ Token created successfully</span>
  <?php else: ?>
    <span class="err">❌ Token creation failed (HTTP <?= $tokHttp ?>)</span>
    <pre><?= htmlspecialchars(json_encode(json_decode($tokBody), JSON_PRETTY_PRINT)) ?></pre>
  <?php endif; ?>
</div>

<?php endif; ?>

<div class="box">
  <b>PHP / Server Info</b><br>
  PHP version: <?= PHP_VERSION ?><br>
  cURL enabled: <?= function_exists('curl_init') ? '<span class="ok">Yes</span>' : '<span class="err">No — install php-curl</span>' ?><br>
  OpenSSL: <?= extension_loaded('openssl') ? '<span class="ok">Yes</span>' : '<span class="err">No</span>' ?>
</div>

<p class="warn">⚠️ Delete this file after debugging: <code>rm public/call-test.php</code></p>
</body>
</html>
