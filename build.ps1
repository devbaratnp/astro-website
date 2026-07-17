# Astro Shree Hari — Build script (PowerShell)
# Run from the project root: .\build.ps1

$ErrorActionPreference = "Stop"
$ProjectRoot = Split-Path -Parent $MyInvocation.MyCommand.Path
$SourceDir   = Join-Path $ProjectRoot "astro-shree-hari-source"
$ReleaseDir  = Join-Path $ProjectRoot "release"
$PublicHtml  = Join-Path $ReleaseDir "public_html"

Write-Host "=== Astro Shree Hari Build ===" -ForegroundColor Cyan
Write-Host ""

# Step 1: Install frontend dependencies
Write-Host "[1/4] Installing frontend dependencies..." -ForegroundColor Yellow
Set-Location $SourceDir
npm ci --silent
if ($LASTEXITCODE -ne 0) { throw "npm ci failed" }
Write-Host "  Done." -ForegroundColor Green

# Step 2: Build frontend (Vite)
Write-Host "[2/4] Building frontend (Vite)..." -ForegroundColor Yellow
npm run build
if ($LASTEXITCODE -ne 0) { throw "npm build failed" }
Write-Host "  Done." -ForegroundColor Green

# Step 3: Create release package
Write-Host "[3/4] Creating release package..." -ForegroundColor Yellow

# Clean release directory
if (Test-Path $ReleaseDir) {
    $ResolvedRelease = (Resolve-Path -LiteralPath $ReleaseDir).Path
    if (-not $ResolvedRelease.StartsWith($ProjectRoot, [System.StringComparison]::OrdinalIgnoreCase)) {
        throw "Refusing to clean a release directory outside the project root"
    }
    Remove-Item -LiteralPath $ResolvedRelease -Recurse -Force
}
New-Item -ItemType Directory -Path $PublicHtml -Force | Out-Null

# Copy Vite build output
$BuildOut = Join-Path $SourceDir "dist"
if (Test-Path $BuildOut) {
    Copy-Item -Path "$BuildOut\*" -Destination $PublicHtml -Recurse -Force
} else {
    throw "Build output not found at $BuildOut"
}

# Copy static root files (assets, admin, docs, etc.)
$StaticDirs = @("assets", "admin", "backend", "docs", "lang", "logs")
foreach ($dir in $StaticDirs) {
    $src = Join-Path $ProjectRoot $dir
    if (Test-Path $src) {
        Copy-Item -Path $src -Destination $PublicHtml -Recurse -Force
    }
}

# Copy root config files
$RootFiles = @(".htaccess", "robots.txt", "sitemap.xml", "manifest.json", "sw.js", "webhook.php")
foreach ($file in $RootFiles) {
    $src = Join-Path $ProjectRoot $file
    if (Test-Path $src) {
        Copy-Item -Path $src -Destination (Join-Path $PublicHtml $file) -Force
    }
}

# Remove sensitive / dev-only files from backend
$BackendTarget = Join-Path $PublicHtml "backend"
$RemoveFromBackend = @("database.credentials.php")
foreach ($file in $RemoveFromBackend) {
    $target = Join-Path $BackendTarget "config" $file
    if (Test-Path $target) { Remove-Item -Path $target -Force }
}

# Validate release
$RequiredFiles = @(
    "index.html",
    ".htaccess",
    "robots.txt",
    "sitemap.xml",
    "backend/api/contact.php",
    "backend/api/appointments.php",
    "backend/config/database.php",
    "backend/config/database.credentials.example.php"
)
foreach ($file in $RequiredFiles) {
    if (-not (Test-Path (Join-Path $PublicHtml $file))) {
        throw "Release validation failed: missing $file"
    }
}

Write-Host "  Done." -ForegroundColor Green

# Step 4: Show summary
Write-Host ""
Write-Host "=== Build Complete ===" -ForegroundColor Cyan
Write-Host "Release package created at: $ReleaseDir" -ForegroundColor Green
Write-Host "Upload contents of:        $PublicHtml" -ForegroundColor Green
Write-Host "  to your hosting public_html directory." -ForegroundColor Green
Write-Host ""
Write-Host "Don't forget to:" -ForegroundColor Yellow
Write-Host "  1. On the server, copy backend/config/database.credentials.example.php" -ForegroundColor Yellow
Write-Host "     to backend/config/database.credentials.php and fill in credentials." -ForegroundColor Yellow
Write-Host "  2. Import database schema from backend/db/ if applicable." -ForegroundColor Yellow
Write-Host "  3. Ensure PHP 8+ and mod_rewrite are enabled on your server." -ForegroundColor Yellow

Set-Location $ProjectRoot
