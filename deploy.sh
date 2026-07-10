#!/usr/bin/env bash
set -euo pipefail

REPO_DIR="$HOME/repositories/astro-website"
HTML_DIR="$HOME/astroshreehari.com"
BACKUP_DIR="$HOME/deploy-backups"
TIMESTAMP=$(date +%Y%m%d-%H%M%S)
SSH_KEY="$HOME/.ssh/github_repo"

mkdir -p "$BACKUP_DIR"

tar -czf "$BACKUP_DIR/astroshreehari-$TIMESTAMP.tar.gz" \
  -C "$(dirname "$HTML_DIR")" "$(basename "$HTML_DIR")" \
  2>/dev/null || true

cd "$REPO_DIR"

GIT_SSH_COMMAND="ssh -i $SSH_KEY -o StrictHostKeyChecking=no" \
  git fetch origin main
GIT_SSH_COMMAND="ssh -i $SSH_KEY -o StrictHostKeyChecking=no" \
  git reset --hard origin/main

rsync -a --delete \
  --exclude='.git' \
  --exclude='.gitignore' \
  --exclude='.nojekyll' \
  --exclude='README.md' \
  --exclude='GITHUB-PAGES-SETUP.md' \
  --exclude='preview-single-file.html' \
  --exclude='docs/' \
  --exclude='tmp_test.php' \
  --exclude='deploy-backups/' \
  --exclude='webhook.example.php' \
  "$REPO_DIR/" "$HTML_DIR/"

# deploy webhook.php only if it doesn't already exist (preserves secret)
if [ ! -f "$HTML_DIR/webhook.php" ]; then
    cp "$REPO_DIR/webhook.example.php" "$HTML_DIR/webhook.php"
fi

chmod +x "$HTML_DIR/deploy.sh" 2>/dev/null || true

echo "Deployed $TIMESTAMP"
