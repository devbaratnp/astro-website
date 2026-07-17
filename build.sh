#!/usr/bin/env bash
# Astro Shree Hari — Build script (Bash)
# Run from the project root: ./build.sh

set -euo pipefail

ProjectRoot="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
SourceDir="$ProjectRoot/astro-shree-hari-source"
ReleaseDir="$ProjectRoot/release"
PublicHtml="$ReleaseDir/public_html"

echo "=== Astro Shree Hari Build ==="
echo ""

# Step 1: Install frontend dependencies
echo "[1/4] Installing frontend dependencies..."
cd "$SourceDir"
npm ci --silent
echo "  Done."

# Step 2: Build frontend (Vite)
echo "[2/4] Building frontend (Vite)..."
npm run build
echo "  Done."

# Step 3: Create release package
echo "[3/4] Creating release package..."

# Clean release directory
rm -rf "$ReleaseDir"
mkdir -p "$PublicHtml"

# Copy Vite build output
BuildOut="$SourceDir/dist"
if [ ! -d "$BuildOut" ]; then
    echo "ERROR: Build output not found at $BuildOut" >&2
    exit 1
fi
cp -r "$BuildOut"/* "$PublicHtml/"

# Copy static root files
for dir in assets admin backend docs lang logs; do
    src="$ProjectRoot/$dir"
    if [ -d "$src" ]; then
        cp -r "$src" "$PublicHtml/"
    fi
done

# Copy root config files
for file in .htaccess robots.txt sitemap.xml manifest.json sw.js webhook.php; do
    src="$ProjectRoot/$file"
    if [ -f "$src" ]; then
        cp "$src" "$PublicHtml/$file"
    fi
done

# Remove sensitive / dev-only files from backend
BackendTarget="$PublicHtml/backend"
rm -f "$BackendTarget/config/database.credentials.php"

# Validate release
RequiredFiles=(
    "index.html"
    ".htaccess"
    "robots.txt"
    "sitemap.xml"
    "backend/api/contact.php"
    "backend/api/appointments.php"
    "backend/config/database.php"
    "backend/config/database.credentials.example.php"
)
for file in "${RequiredFiles[@]}"; do
    if [ ! -f "$PublicHtml/$file" ]; then
        echo "ERROR: Release validation failed: missing $file" >&2
        exit 1
    fi
done

echo "  Done."

# Step 4: Show summary
echo ""
echo "=== Build Complete ==="
echo "Release package created at: $ReleaseDir"
echo "Upload contents of:        $PublicHtml"
echo "  to your hosting public_html directory."
echo ""
echo "Don't forget to:"
echo "  1. On the server, copy backend/config/database.credentials.example.php"
echo "     to backend/config/database.credentials.php and fill in credentials."
echo "  2. Import database schema from backend/db/ if applicable."
echo "  3. Ensure PHP 8+ and mod_rewrite are enabled on your server."

cd "$ProjectRoot"
