
# ===========================================================================
# build-cpanel.ps1 - Run this script from the project root to create a
# production-ready ZIP for cPanel upload.
# Usage: powershell -ExecutionPolicy Bypass -File build-cpanel.ps1
# ===========================================================================

$ErrorActionPreference = "Stop"
$src     = $PSScriptRoot
# Write ZIP outside the VS Code workspace to avoid file-watcher lock.
$zipPath = "$env:USERPROFILE\Desktop\dating-production.zip"

Write-Host "=== HeartsConnect cPanel Build Script ===" -ForegroundColor Cyan

# 1. Build front-end assets
Write-Host "`n[1/4] Building front-end assets..." -ForegroundColor Yellow
Set-Location $src
$npmLog = "$env:TEMP\dating-npm-build-$(Get-Random).log"
# Run via cmd.exe to bypass PowerShell's npm.ps1 wrapper which writes to stderr
# even on success, tripping $ErrorActionPreference = "Stop".
cmd /c "npm run build > `"$npmLog`" 2>&1"
$npmExit = $LASTEXITCODE
if ($npmExit -ne 0) {
    Write-Host "  npm build FAILED. See log: $npmLog" -ForegroundColor Red
    Get-Content $npmLog -Tail 20
    exit 1
}
Write-Host "  Assets built OK." -ForegroundColor Green

# 2. Clear all Laravel caches
Write-Host "`n[2/4] Clearing Laravel caches..." -ForegroundColor Yellow
php artisan optimize:clear

# 3. Remove old zip (retry loop in case it's briefly locked by Explorer/AV)
if (Test-Path $zipPath) {
    $retries = 0
    while ($retries -lt 5) {
        try {
            Remove-Item $zipPath -Force -ErrorAction Stop
            break
        } catch {
            $retries++
            if ($retries -ge 5) { Write-Error "Could not delete old zip. Close any app that has it open, then retry."; exit 1 }
            Write-Host "  ZIP is locked, waiting 3s... ($retries/5)" -ForegroundColor Yellow
            Start-Sleep -Seconds 3
        }
    }
}

# 4. Create zip via robocopy (copy to temp dir with exclusions) + ZipFile::CreateFromDirectory
Write-Host "`n[3/4] Creating ZIP (this may take a minute)..." -ForegroundColor Yellow

$tmpDir = Join-Path $env:TEMP "dating-build-$(Get-Random)"
New-Item -ItemType Directory -Path $tmpDir -Force | Out-Null

Write-Host "  Copying project files (excluding node_modules, .git, logs)..."
$robocopyArgs = @(
    $src, $tmpDir, "/E",
    "/XD",
        "node_modules", ".git", "tests",
        (Join-Path $src "public\storage"),          # symlink - re-created on server via: php artisan storage:link
        (Join-Path $src "storage\framework\views"),
        (Join-Path $src "storage\framework\sessions"),
        (Join-Path $src "storage\framework\cache\data"),
        (Join-Path $src "storage\app\public"),       # uploaded user files - stay on server
        (Join-Path $src "storage\logs"),
    "/XF",
        ".env", ".env.bak", "mailtest.php", "phpunit.xml", "build-cpanel.ps1",
        "DEPLOY.md", "build-cpanel.ps1", "setup.php",
    "/NFL", "/NDL", "/NJH", "/NJS"
)
$ErrorActionPreference = "Continue"
& robocopy @robocopyArgs | Out-Null
# robocopy: exit 0-7 = success; 8+ = error
if ($LASTEXITCODE -ge 8) {
    Remove-Item $tmpDir -Recurse -Force -ErrorAction SilentlyContinue
    Write-Error "robocopy failed (exit code $LASTEXITCODE)"; exit 1
}
$ErrorActionPreference = "Stop"

# Ensure required Laravel writable directories exist in the zip (contents excluded, folders kept)
$requiredDirs = @(
    "storage\framework\views",
    "storage\framework\sessions",
    "storage\framework\cache\data",
    "storage\framework\testing",
    "storage\app\public",
    "storage\logs",
    "bootstrap\cache"
)
foreach ($d in $requiredDirs) {
    $fullPath = Join-Path $tmpDir $d
    if (-not (Test-Path $fullPath)) {
        New-Item -ItemType Directory -Path $fullPath -Force | Out-Null
    }
    # Add a .gitkeep so the directory is not empty (zip tools drop empty dirs)
    $keepFile = Join-Path $fullPath ".gitkeep"
    if (-not (Test-Path $keepFile)) {
        New-Item -ItemType File -Path $keepFile -Force | Out-Null
    }
}

Write-Host "  Compressing..."
# Guard against 'already loaded' error when running from a reused PS session
try { Add-Type -AssemblyName System.IO.Compression.FileSystem } catch { $null = $null }
try {
    [System.IO.Compression.ZipFile]::CreateFromDirectory(
        $tmpDir, $zipPath,
        [System.IO.Compression.CompressionLevel]::Fastest,
        $false
    )
} catch {
    Remove-Item $tmpDir -Recurse -Force -ErrorAction SilentlyContinue
    Write-Error "ZIP compression failed: $_"
    exit 1
}

Remove-Item $tmpDir -Recurse -Force -ErrorAction SilentlyContinue

$sizeMB = [math]::Round((Get-Item $zipPath).Length / 1MB, 1)
Write-Host "`n[4/4] Done! ZIP created: $zipPath ($sizeMB MB)" -ForegroundColor Green
Write-Host "  -> Find it on your Desktop: dating-production.zip" -ForegroundColor Green
Write-Host ""
Write-Host "--- cPanel Deployment Steps ---" -ForegroundColor Cyan
Write-Host "1. Upload dating-production.zip to your cPanel home (e.g. /home/user/)"
Write-Host "2. Extract via cPanel File Manager - it creates a dating/ folder"
Write-Host "3. Point domain document root to dating/public"
Write-Host "4. Copy .env.example -> .env and fill in all values"
Write-Host "5. php artisan key:generate"
Write-Host "6. php artisan migrate --force"
Write-Host "7. php artisan storage:link"
Write-Host "8. chmod -R 775 storage bootstrap/cache"
Write-Host "9. php artisan optimize:clear"
Write-Host "-------------------------------" -ForegroundColor Cyan
