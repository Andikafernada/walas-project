#!/bin/bash
set -e

echo "========================================"
echo "  WaliKelas Pro - Health Check"
echo "========================================"

BASE_URL="${APP_URL:-http://localhost}"
FAILED=0

check_endpoint() {
    local name="$1"
    local url="$2"
    local expected="$3"

    echo -n "Checking $name... "

    response=$(curl -s -o /dev/null -w "%{http_code}" "$url" 2>/dev/null || echo "000")

    if [ "$response" = "$expected" ]; then
        echo "✓ OK ($response)"
    else
        echo "✗ FAILED (got $response, expected $expected)"
        FAILED=$((FAILED + 1))
    fi
}

echo ""
echo "Base URL: $BASE_URL"
echo "----------------------------------------"

# Check main app
check_endpoint "Health endpoint" "$BASE_URL/up" "200"

# Check queue
check_endpoint "Queue workers" "http://localhost:2375/info" "200" 2>/dev/null || echo "Queue workers: ⚠ Manual check needed"

# Check database
php artisan tinker --execute="
    try {
        DB::connection()->getPdo();
        echo 'Database: ✓ OK' . PHP_EOL;
    } catch (\Exception \$e) {
        echo 'Database: ✗ FAILED - ' . \$e->getMessage() . PHP_EOL;
    }
" 2>/dev/null || echo "Database: ⚠ Could not check"

# Check Redis
php artisan tinker --execute="
    try {
        \$ping = Redis::ping();
        echo 'Redis: ✓ OK' . PHP_EOL;
    } catch (\Exception \$e) {
        echo 'Redis: ⚠ ' . \$e->getMessage() . PHP_EOL;
    }
" 2>/dev/null || echo "Redis: ⚠ Could not check"

# Check storage
if [ -d "storage/app/public" ]; then
    echo "Storage: ✓ OK"
else
    echo "Storage: ✗ FAILED - Directory not found"
    FAILED=$((FAILED + 1))
fi

echo "----------------------------------------"
echo ""

if [ $FAILED -eq 0 ]; then
    echo -e "\e[32m✓ All checks passed!\e[0m"
    exit 0
else
    echo -e "\e[31m✗ $FAILED check(s) failed\e[0m"
    exit 1
fi
