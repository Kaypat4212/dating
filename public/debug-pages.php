<?php
/**
 * HeartsConnect — Page-specific debug probe (DELETE after use!)
 * Visit: https://heartsconnect.cc/debug-pages.php?t=dbg2026
 */
if (($_GET['t'] ?? '') !== 'dbg2026') { http_response_code(403); exit('403'); }

set_exception_handler(null);
set_error_handler(null);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

$root = dirname(__DIR__);

// ── Helpers ──────────────────────────────────────────────────────────────────
function badge(bool $ok, string $okLabel = 'OK', string $failLabel = 'FAIL'): string {
    $cls = $ok ? 'badge-ok' : 'badge-fail';
    $lbl = $ok ? $okLabel : $failLabel;
    return "<span class=\"badge {$cls}\">{$lbl}</span>";
}
function row(string $label, string $value, bool $ok = true): string {
    return "<tr class=\"" . ($ok ? '' : 'row-fail') . "\">
              <td class=\"label\">" . htmlspecialchars($label) . "</td>
              <td>" . $value . "</td>
            </tr>";
}
function card(string $title, string $icon, string $body): string {
    return "<div class=\"card\">
              <div class=\"card-header\"><span class=\"card-icon\">{$icon}</span>{$title}</div>
              <div class=\"card-body\">{$body}</div>
            </div>";
}

// ── Run all checks, collect results ──────────────────────────────────────────
$cards = [];

// 1. Autoloader
$autoloadOk = false;
try {
    require $root . '/vendor/autoload.php';
    $autoloadOk = true;
    $autoloadMsg = 'vendor/autoload.php loaded successfully';
} catch (\Throwable $e) {
    $autoloadMsg = $e->getMessage();
}
$cards[] = card('Autoloader', '📦', '<table>' . row('vendor/autoload.php', badge($autoloadOk) . ' ' . htmlspecialchars($autoloadMsg), $autoloadOk) . '</table>');

if (!$autoloadOk) {
    // Can't continue without autoloader
    renderPage($cards, fatal: true);
    exit;
}

// 2. Bootstrap
$app = null;
$bootstrapOk = false;
$bootstrapMsg = '';
try {
    $app = require_once $root . '/bootstrap/app.php';
    $bootstrapOk = true;
    $bootstrapMsg = 'bootstrap/app.php loaded successfully';
} catch (\Throwable $e) {
    $bootstrapMsg = get_class($e) . ': ' . $e->getMessage() . ' at ' . $e->getFile() . ':' . $e->getLine();
}
$cards[] = card('Bootstrap', '🚀', '<table>' . row('bootstrap/app.php', badge($bootstrapOk) . ' ' . htmlspecialchars($bootstrapMsg), $bootstrapOk) . '</table>');

// 3. Kernel bootstrap
$kernelOk = false;
$kernelMsg = '';
if ($app) {
    try {
        $kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
        $kernel->bootstrap();
        $kernelOk = true;
        $kernelMsg = 'Kernel bootstrapped successfully';
    } catch (\Throwable $e) {
        $kernelMsg = get_class($e) . ': ' . $e->getMessage() . ' at ' . $e->getFile() . ':' . $e->getLine();
    }
}
$cards[] = card('Kernel', '⚙️', '<table>' . row('Console Kernel', badge($kernelOk) . ' ' . htmlspecialchars($kernelMsg), $kernelOk) . '</table>');

// 4. Git commits
$gitOutput = shell_exec("cd {$root} && git log --oneline -5 2>&1") ?? 'Could not run git';
$gitRows = '';
foreach (array_filter(explode("\n", trim($gitOutput))) as $line) {
    [$hash, $msg] = [substr($line, 0, 7), substr($line, 8)];
    $gitRows .= row('<code>' . htmlspecialchars($hash) . '</code>', htmlspecialchars($msg));
}
$cards[] = card('Recent Commits', '🔀', '<table>' . $gitRows . '</table>');

// 5. DB tables
$dbRows = '';
if ($kernelOk) {
    try {
        $tables = \Illuminate\Support\Facades\DB::select("SHOW TABLES");
        $names = array_map(fn($t) => array_values((array)$t)[0], $tables);
        foreach (['wallet_transactions','wallet_funding_requests','wallet_withdrawal_requests','homepage_visits','site_settings','users'] as $t) {
            $exists = in_array($t, $names);
            $dbRows .= row($t, badge($exists, 'EXISTS', 'MISSING'), $exists);
        }
    } catch (\Throwable $e) {
        $dbRows = row('DB connection', badge(false) . ' ' . htmlspecialchars($e->getMessage()), false);
    }
}
$cards[] = card('Database Tables', '🗄️', '<table>' . $dbRows . '</table>');

// 6. Class / method checks
$classChecks = [
    'SmartMatch instantiation' => function() {
        $sm = new \App\Filament\Pages\SmartMatch(); return 'class loads fine';
    },
    'FinanceDashboard instantiation' => function() {
        $fd = new \App\Filament\Pages\FinanceDashboard(); return 'class loads fine';
    },
    'FinanceDashboard->getStats()' => function() {
        $fd = new \App\Filament\Pages\FinanceDashboard();
        $stats = $fd->getStats();
        return 'returned ' . count($stats) . ' keys: ' . implode(', ', array_keys($stats));
    },
    'WalletTransaction count' => function() {
        $c = \App\Models\WalletTransaction::count(); return "{$c} rows";
    },
    'WalletFundingRequest count' => function() {
        $c = \App\Models\WalletFundingRequest::count(); return "{$c} rows";
    },
    'SmartMatch->getNewUsers()' => function() {
        $sm = new \App\Filament\Pages\SmartMatch();
        $u = $sm->getNewUsers(); return 'returned ' . $u->count() . ' users';
    },
];

$classRows = '';
if ($kernelOk) {
    foreach ($classChecks as $label => $fn) {
        try {
            $result = $fn();
            $classRows .= row($label, badge(true) . ' ' . htmlspecialchars($result));
        } catch (\Throwable $e) {
            $classRows .= row($label,
                badge(false) . ' <span class="err-class">' . htmlspecialchars(get_class($e)) . '</span>: '
                . htmlspecialchars($e->getMessage())
                . ' <span class="err-loc">at ' . htmlspecialchars(basename($e->getFile())) . ':' . $e->getLine() . '</span>',
                false
            );
        }
    }
}
$cards[] = card('Class & Method Checks', '🧪', '<table>' . $classRows . '</table>');

// 7. Laravel log (last 40 lines)
$logFile = $root . '/storage/logs/laravel.log';
if (file_exists($logFile)) {
    $lines = array_slice(file($logFile), -40);
    $logHtml = '<pre class="log-output">' . htmlspecialchars(implode('', $lines)) . '</pre>';
} else {
    $logHtml = '<p class="no-data">No laravel.log file found.</p>';
}
$cards[] = card('Laravel Log <span class="subtle">(last 40 lines)</span>', '📋', $logHtml);

// ── Render ───────────────────────────────────────────────────────────────────
function renderPage(array $cards, bool $fatal = false): void {}

header('Content-Type: text/html; charset=utf-8');
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>HeartsConnect — Debug Probe</title>
<style>
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

  body {
    font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
    background: #0f1117;
    color: #d1d5db;
    min-height: 100vh;
    padding: 2rem 1rem;
  }

  /* ── Header ── */
  .page-header {
    max-width: 1100px;
    margin: 0 auto 2rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    padding-bottom: 1.25rem;
    border-bottom: 1px solid #1f2937;
  }
  .page-header .logo { font-size: 2rem; }
  .page-header h1 {
    font-size: 1.5rem;
    font-weight: 700;
    color: #f9fafb;
    letter-spacing: -.02em;
  }
  .page-header .subtitle {
    font-size: .8rem;
    color: #6b7280;
    margin-top: 2px;
  }
  .page-header .ts {
    margin-left: auto;
    font-size: .75rem;
    color: #4b5563;
    text-align: right;
  }

  /* ── Grid ── */
  .grid {
    max-width: 1100px;
    margin: 0 auto;
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(480px, 1fr));
    gap: 1.25rem;
  }
  /* log card spans full width */
  .grid .card:last-child { grid-column: 1 / -1; }

  /* ── Card ── */
  .card {
    background: #161b27;
    border: 1px solid #1e2736;
    border-radius: 10px;
    overflow: hidden;
  }
  .card-header {
    display: flex;
    align-items: center;
    gap: .6rem;
    padding: .85rem 1.1rem;
    background: #1a2033;
    border-bottom: 1px solid #1e2736;
    font-size: .85rem;
    font-weight: 600;
    color: #e5e7eb;
    letter-spacing: .01em;
  }
  .card-icon { font-size: 1.1rem; }
  .card-header .subtle { font-weight: 400; color: #4b5563; font-size: .78rem; margin-left: .25rem; }
  .card-body { padding: .75rem 1rem 1rem; }

  /* ── Table ── */
  table { width: 100%; border-collapse: collapse; font-size: .82rem; }
  tr { border-bottom: 1px solid #1e2736; }
  tr:last-child { border-bottom: none; }
  tr.row-fail { background: rgba(239,68,68,.06); }
  td { padding: .5rem .4rem; vertical-align: top; }
  td.label {
    width: 42%;
    color: #9ca3af;
    font-family: 'Cascadia Code', 'Fira Code', monospace;
    font-size: .78rem;
    padding-right: .75rem;
    word-break: break-word;
  }
  code { font-family: 'Cascadia Code', 'Fira Code', monospace; font-size: .8em; color: #a78bfa; }

  /* ── Badges ── */
  .badge {
    display: inline-flex;
    align-items: center;
    padding: .15rem .55rem;
    border-radius: 9999px;
    font-size: .7rem;
    font-weight: 700;
    letter-spacing: .04em;
    text-transform: uppercase;
    margin-right: .4rem;
  }
  .badge-ok   { background: rgba(16,185,129,.15); color: #34d399; border: 1px solid rgba(16,185,129,.3); }
  .badge-fail { background: rgba(239,68,68,.15);  color: #f87171; border: 1px solid rgba(239,68,68,.3); }
  .badge-warn { background: rgba(245,158,11,.15); color: #fbbf24; border: 1px solid rgba(245,158,11,.3); }

  /* ── Error details ── */
  .err-class { color: #f87171; font-weight: 600; }
  .err-loc   { color: #6b7280; font-size: .75em; }

  /* ── Log output ── */
  .log-output {
    background: #0b0f1a;
    border: 1px solid #1e2736;
    border-radius: 6px;
    padding: .85rem 1rem;
    font-family: 'Cascadia Code', 'Fira Code', 'Courier New', monospace;
    font-size: .73rem;
    line-height: 1.6;
    color: #9ca3af;
    overflow-x: auto;
    max-height: 420px;
    overflow-y: auto;
    white-space: pre-wrap;
    word-break: break-all;
  }
  .no-data { color: #4b5563; font-size: .82rem; padding: .5rem 0; }

  /* ── Footer ── */
  .page-footer {
    max-width: 1100px;
    margin: 2rem auto 0;
    padding-top: 1rem;
    border-top: 1px solid #1f2937;
    font-size: .72rem;
    color: #374151;
    text-align: center;
  }
  .page-footer strong { color: #ef4444; }
</style>
</head>
<body>

<header class="page-header">
  <span class="logo">💗</span>
  <div>
    <h1>HeartsConnect — Debug Probe</h1>
    <div class="subtitle">Server diagnostic dashboard &nbsp;·&nbsp; probe v2</div>
  </div>
  <div class="ts">
    <?= date('D, d M Y') ?><br>
    <?= date('H:i:s T') ?>
  </div>
</header>

<main class="grid">
  <?php foreach ($cards as $c) echo $c; ?>
</main>

<footer class="page-footer">
  <strong>⚠ DELETE this file (debug-pages.php) from the server when done.</strong>
</footer>

</body>
</html>
