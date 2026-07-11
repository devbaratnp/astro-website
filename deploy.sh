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

chmod +x "$REPO_DIR/deploy.sh" 2>/dev/null || true

echo "Deployed $TIMESTAMP"
