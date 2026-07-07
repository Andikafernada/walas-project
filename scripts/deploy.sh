#!/bin/bash
set -e

echo "========================================"
echo "  WaliKelas Pro - Deployment Script"
echo "========================================"

# Configuration
APP_NAME="WalasPro"
DEPLOY_PATH="/var/www/walas-pro"
BACKUP_PATH="/var/www/walas-pro/backups"
LOG_FILE="/var/www/walas-pro/storage/logs/deploy.log"

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

log() {
    echo -e "${GREEN}[$(date '+%Y-%m-%d %H:%M:%S')]${NC} $1"
}

error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

warn() {
    echo -e "${YELLOW}[WARN]${NC} $1"
}

# Check if running as root
if [ "$EUID" -ne 0 ]; then
    error "Please run as root or with sudo"
    exit 1
fi

# Parse arguments
BRANCH="${1:-main}"
COMMIT_MSG="${2:-Deploy $(date '+%Y-%m-%d %H:%M')}"

log "Starting deployment..."
log "Branch: $BRANCH"
log "Message: $COMMIT_MSG"

# Navigate to deploy path
cd "$DEPLOY_PATH" || exit 1

# Create backup
log "Creating backup..."
if [ -d ".git" ]; then
    TIMESTAMP=$(date '+%Y%m%d_%H%M%S')
    BACKUP_NAME="backup_${TIMESTAMP}"

    mkdir -p "$BACKUP_PATH"
    tar -czf "$BACKUP_PATH/${BACKUP_NAME}.tar.gz" \
        --exclude='.git' \
        --exclude='node_modules' \
        --exclude='vendor' \
        --exclude='.env' \
        --exclude='storage/logs' \
        --exclude='storage/debugbar' \
        --exclude='storage/framework/cache' \
        -C / .

    log "Backup created: ${BACKUP_NAME}.tar.gz"
fi

# Pull latest code
if [ -d ".git" ]; then
    log "Pulling latest code..."
    git fetch origin
    git checkout "$BRANCH"
    git pull origin "$BRANCH"
fi

# Install dependencies
log "Installing dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction

# Run migrations
log "Running migrations..."
php artisan migrate --force

# Clear and rebuild caches
log "Clearing caches..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# Rebuild caches
log "Rebuilding caches..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Queue restart
log "Restarting queue workers..."
php artisan queue:restart

# Set permissions
log "Setting permissions..."
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

log "========================================"
log -e "${GREEN}Deployment completed successfully!${NC}"
log "========================================"

# Show status
php artisan tinker --execute="echo 'App: '.config('app.url').PHP_EOL;'"
