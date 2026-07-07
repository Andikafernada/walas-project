#!/bin/bash
set -e

echo "========================================"
echo "  WaliKelas Pro - Rollback Script"
echo "========================================"

BACKUP_PATH="/var/www/walas-pro/backups"

# List available backups
echo ""
echo "Available backups:"
echo "----------------------------------------"
ls -lah "$BACKUP_PATH"/*.tar.gz 2>/dev/null || echo "No backups found"
echo ""

# Get backup name from argument
BACKUP_NAME="${1:-}"

if [ -z "$BACKUP_NAME" ]; then
    echo "Usage: ./rollback.sh <backup_filename>"
    echo ""
    echo "Available backups:"
    ls -1 "$BACKUP_PATH"/*.tar.gz 2>/dev/null || echo "No backups found"
    exit 1
fi

BACKUP_FILE="$BACKUP_PATH/$BACKUP_NAME"

if [ ! -f "$BACKUP_FILE" ]; then
    echo "Backup not found: $BACKUP_FILE"
    exit 1
fi

echo "This will restore: $BACKUP_NAME"
echo "Press Ctrl+C to cancel or Enter to continue..."
read

echo "Stopping application..."
docker-compose down

echo "Restoring backup..."
rm -rf /var/www/walas-pro/*
tar -xzf "$BACKUP_FILE" -C /var/www/walas-pro

echo "Restoring permissions..."
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

echo "Starting application..."
docker-compose up -d

echo "========================================"
echo "Rollback completed!"
echo "========================================"
