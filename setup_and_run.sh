#!/bin/bash

# NATMS Setup & Run Script
# This script sets up MySQL, imports databases, and starts the PHP development server

set -e

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$SCRIPT_DIR"
DB_DIR="$PROJECT_ROOT/DB"
LOG_FILE="/tmp/natms_setup.log"

echo "========================================" | tee -a "$LOG_FILE"
echo "NATMS System Setup & Launch" | tee -a "$LOG_FILE"
echo "========================================" | tee -a "$LOG_FILE"

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

log() {
    echo -e "${GREEN}[$(date '+%Y-%m-%d %H:%M:%S')]${NC} $1" | tee -a "$LOG_FILE"
}

error() {
    echo -e "${RED}[ERROR]${NC} $1" | tee -a "$LOG_FILE"
}

warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1" | tee -a "$LOG_FILE"
}

# ============================================
# 1. Check and Start MySQL
# ============================================

log "Step 1: Checking MySQL installation..."

if ! command -v mysql &> /dev/null; then
    log "MySQL not found. Installing MariaDB..."
    sudo apt-get update >> "$LOG_FILE" 2>&1
    sudo apt-get install -y mariadb-server >> "$LOG_FILE" 2>&1
    log "MariaDB installed successfully."
fi

log "Checking if MySQL service is running..."
if ! systemctl is-active --quiet mysql 2>/dev/null && ! systemctl is-active --quiet mariadb 2>/dev/null; then
    log "MySQL not running. Starting MySQL service..."
    sudo systemctl start mysql || sudo systemctl start mariadb
    sleep 2
    log "MySQL started."
else
    log "MySQL is already running."
fi

# Verify MySQL connection
log "Verifying MySQL connection..."
if mysql -u root -e "SELECT 1" &>/dev/null; then
    log "MySQL connection successful."
else
    error "Failed to connect to MySQL. Check installation and try again."
    exit 1
fi

# ============================================
# 2. Drop and Import Databases
# ============================================

log "Step 2: Setting up databases..."

DATABASES=("booking" "classroom_monitoring" "training_portal" "live_network" "nshare_lite_db")

for db in "${DATABASES[@]}"; do
    SQL_FILE="$DB_DIR/${db}.sql"
    
    if [ ! -f "$SQL_FILE" ]; then
        warning "SQL file not found: $SQL_FILE"
        continue
    fi
    
    log "Processing database: $db"
    
    # Drop database if exists
    mysql -u root -e "DROP DATABASE IF EXISTS \`$db\`;" 2>/dev/null || true
    
    # Create new database and import schema
    mysql -u root -e "CREATE DATABASE IF NOT EXISTS \`$db\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
    mysql -u root "$db" < "$SQL_FILE"
    
    log "Database '$db' imported successfully."
done

log "All databases configured."

# ============================================
# 3. Verify PHP
# ============================================

log "Step 3: Verifying PHP..."

if php --version | grep -q "PHP"; then
    log "PHP is available: $(php --version | head -1)"
else
    error "PHP not found. Please install PHP 8.0+."
    exit 1
fi

# ============================================
# 4. Use existing PHP server if running, or start new one
# ============================================

log "Step 4: Starting PHP Development Server..."

PORT="8000"
HOST="127.0.0.1"

# Kill any existing PHP server on port 8000
if lsof -Pi :$PORT -sTCP:LISTEN -t >/dev/null 2>&1; then
    log "Found existing process on port $PORT. Stopping..."
    lsof -ti:$PORT | xargs kill -9 2>/dev/null || true
    sleep 1
fi

log "Starting PHP built-in server on $HOST:$PORT..."
cd "$PROJECT_ROOT"
nohup php -S $HOST:$PORT -t . > /tmp/php_server.log 2>&1 & 
PHP_PID=$!
echo $PHP_PID > /tmp/php_server.pid

sleep 2

# Verify server is running
if curl -s -I "http://$HOST:$PORT/" >/dev/null 2>&1; then
    log "PHP server started successfully (PID: $PHP_PID)"
else
    error "PHP server failed to start. Check logs: tail -n 50 /tmp/php_server.log"
    exit 1
fi

# ============================================
# 5. Summary & Instructions
# ============================================

log "========================================" 
log "✅ NATMS Setup Complete!"
log "========================================" 

echo ""
echo "═══════════════════════════════════════════════════════════════"
echo "  Access the application:"
echo "═══════════════════════════════════════════════════════════════"
echo ""
echo "  🌐 Web App:       http://$HOST:$PORT"
echo "  🌐 Dashboard:     http://$HOST:$PORT/dashboard/"
echo "  📊 Booking:       http://$HOST:$PORT/booking/"
echo "  🎓 Training:      http://$HOST:$PORT/training-portal/"
echo "  📱 Classroom Mon: http://$HOST:$PORT/classroom_monitoring/"
echo "  🌐 Live Status:   http://$HOST:$PORT/live_status/"
echo "  ☁️  NIAT Cloud:    http://$HOST:$PORT/niatcloud/"
echo ""
echo "  📝 Web Logs:      tail -f /tmp/php_server.log"
echo "  🛑 Stop Server:   kill $PHP_PID"
echo ""
echo "═══════════════════════════════════════════════════════════════"
echo ""
echo "Database Credentials:"
echo "  Host:     localhost"
echo "  User:     root"
echo "  Password: (empty)"
echo ""
echo "Databases imported:"
for db in "${DATABASES[@]}"; do
    echo "  ✓ $db"
done
echo ""
echo "Setup log: $LOG_FILE"
echo ""

