#!/usr/bin/env bash
set -euo pipefail

REPO_DIR="$HOME/astroshreehari.com"
BACKUP_DIR="$HOME/deploy-backups"
TIMESTAMP=$(date +%Y%m%d-%H%M%S)

mkdir -p "$BACKUP_DIR"
mkdir -p "$REPO_DIR/logs"

if [ ! -f "$REPO_DIR/logs/.htaccess" ]; then
  echo "Require all denied" > "$REPO_DIR/logs/.htaccess"
fi

cd "$REPO_DIR"

git fetch origin main
git reset --hard origin/main

if [ ! -f "$REPO_DIR/backend/config/database.credentials.php" ] \
  && { [ -z "${DB_USER:-}" ] || [ -z "${DB_PASS:-}" ]; }; then
  echo "ERROR: database credentials are missing." >&2
  echo "Create backend/config/database.credentials.php from database.credentials.example.php" >&2
  echo "or configure DB_USER and DB_PASS in the hosting environment." >&2
  exit 1
fi

chmod +x "$REPO_DIR/deploy.sh" 2>/dev/null || true

echo "Deployed $TIMESTAMP"
