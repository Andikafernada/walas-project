#!/bin/bash
set -e

echo "========================================="
echo "  WaliKelas Pro - Domain Setup"
echo "========================================="
echo ""
echo "Configuring domain: walas.my.id"
echo ""

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

log() {
    echo -e "${GREEN}[OK]${NC} $1"
}

warn() {
    echo -e "${YELLOW}[WARN]${NC} $1"
}

# Check if running as root
if [ "$EUID" -eq 0 ]; then
    SUDO=""
else
    SUDO="sudo"
fi

# 1. Add hosts entry
echo "1. Configuring /etc/hosts..."
if grep -q "walas.my.id" /etc/hosts 2>/dev/null; then
    warn "/etc/hosts entry already exists"
else
    echo "127.0.0.1 walas.my.id" | $SUDO tee -a /etc/hosts > /dev/null
    log "Added hosts entry for walas.my.id"
fi

# 2. Update Laravel config
echo ""
echo "2. Updating Laravel configuration..."
php artisan config:clear 2>/dev/null || true
php artisan config:cache 2>/dev/null || true
log "Laravel config updated"

# 3. Create SSL certificates (optional, for HTTPS)
echo ""
echo "3. SSL Certificate Setup:"
if command -v mkcert &> /dev/null; then
    if [ ! -f "storage/certificates/walas.my.id.pem" ]; then
        mkdir -p storage/certificates
        mkcert -cert-file storage/certificates/walas.my.id.pem -key-file storage/certificates/walas.my.id-key.pem walas.my.id "*.walas.my.id" localhost 127.0.0.1 ::1
        log "SSL certificates created"
    else
        warn "SSL certificates already exist"
    fi
else
    echo "   mkcert not found. Install with: brew install mkcert (macOS) or apt install mkcert (Ubuntu)"
fi

# 4. Show configuration
echo ""
echo "4. Configuration Complete!"
echo ""
echo "========================================="
echo ""
echo "Local URLs:"
echo "  - http://walas.my.id"
echo "  - http://walas.my.id:8000"
echo ""
echo "API URLs:"
echo "  - http://walas.my.id/api/v1/..."
echo ""
echo "Queue Worker:"
echo "  php artisan queue:work"
echo ""
echo "Scheduler (run every minute in separate terminal):"
echo "  php artisan schedule:work"
echo ""
echo "========================================="
echo ""
echo -e "${GREEN}Setup complete! Access http://walas.my.id${NC}"
echo ""
