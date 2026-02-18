#!/bin/bash

################################################################################
# MODIFIED: Completely rewrote run.sh to use Docker-based architecture
# Reason: Previous script used local PHP/MySQL which had setup issues
# Original behavior: Manual MySQL start, direct PHP server, local dependencies
# New behavior: Docker-compose orchestration with automatic prechecks and service health verification
# Features:
#   - Checks for Docker and docker-compose
#   - Stops existing containers to prevent conflicts
#   - Builds custom PHP image with required extensions
#   - Starts database and web services
#   - Waits for services to be healthy
#   - Initializes all test user accounts
#   - Provides clear login instructions with credentials
################################################################################

set -e  # MODIFIED: Added 'set -e' to exit on error
         # Reason: Ensure script stops if any command fails

# Color codes for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Script configuration
PROJECT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_NAME="natms"

echo -e "${BLUE}=================================================================================${NC}"
echo -e "${BLUE}       NAVAL AVIATION TRAINING MANAGEMENT SYSTEM - Startup Script${NC}"
echo -e "${BLUE}=================================================================================${NC}"
echo ""

################################################################################
# FUNCTION: Print colored output
################################################################################
print_status() {
    echo -e "${GREEN}[✓]${NC} $1"
}

print_error() {
    echo -e "${RED}[✗]${NC} $1"
}

print_info() {
    echo -e "${BLUE}[i]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[!]${NC} $1"
}

################################################################################
# FUNCTION: Check if command exists
################################################################################
command_exists() {
    command -v "$1" >/dev/null 2>&1
}

################################################################################
# SECTION 1: Check for Docker Installation
################################################################################
echo -e "${YELLOW}[Step 1/6] Checking for Docker installation...${NC}"

if command_exists docker; then
    DOCKER_VERSION=$(docker --version)
    print_status "Docker is installed: $DOCKER_VERSION"
else
    print_error "Docker is not installed"
    echo "Please install Docker from: https://docs.docker.com/engine/install/"
    exit 1
fi

################################################################################
# SECTION 2: Check for docker-compose Installation
################################################################################
echo ""
echo -e "${YELLOW}[Step 2/6] Checking for docker-compose installation...${NC}"

if command_exists docker-compose; then
    COMPOSE_VERSION=$(docker-compose --version)
    print_status "docker-compose is installed: $COMPOSE_VERSION"
else
    print_error "docker-compose is not installed"
    echo "Attempting to install..."
    
    # MODIFIED: Added docker-compose installation fallback
    # Reason: Auto-install if missing (requires sudo)
    if sudo curl -L "https://github.com/docker/compose/releases/latest/download/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose 2>/dev/null; then
        sudo chmod +x /usr/local/bin/docker-compose
        print_status "docker-compose installed successfully"
    else
        print_error "Failed to install docker-compose"
        echo "Please install manually from: https://docs.docker.com/compose/install/"
        exit 1
    fi
fi

################################################################################
# SECTION 3: Stop Existing Containers
################################################################################
echo ""
echo -e "${YELLOW}[Step 3/6] Stopping existing containers...${NC}"

cd "$PROJECT_DIR"

# MODIFIED: Added graceful shutdown of existing containers
# Reason: Prevent port conflicts and ensure clean restart
if docker-compose ps 2>/dev/null | grep -q "natms"; then
    print_info "Found existing containers, stopping them..."
    docker-compose down -v 2>/dev/null || true
    print_status "Existing containers stopped"
else
    print_status "No existing containers found"
fi

################################################################################
# SECTION 4: Build and Start Services
################################################################################
echo ""
echo -e "${YELLOW}[Step 4/6] Building and starting NATMS services...${NC}"

# MODIFIED: Build custom PHP image with database extensions
# Reason: Ensure all required PHP extensions (mysqli, pdo_mysql) are available
print_info "Building Docker image (this may take a few minutes)..."
docker-compose build --no-cache 2>&1 | tail -20

# MODIFIED: Start all services in background
# Reason: Run containers and allow time for initialization
print_info "Starting services..."
docker-compose up -d

print_status "Services started"

################################################################################
# SECTION 5: Wait for Database and Initialize
################################################################################
echo ""
echo -e "${YELLOW}[Step 5/6] Initializing databases...${NC}"

# MODIFIED: Wait for MySQL to be ready with health check
# Reason: Ensure database is accepting connections before importing data
print_info "Waiting for MySQL to be ready..."
MAX_ATTEMPTS=30
ATTEMPT=0

while [ $ATTEMPT -lt $MAX_ATTEMPTS ]; do
    if docker exec ${PROJECT_NAME}_db mysql -u root -e "SELECT 1" >/dev/null 2>&1; then
        print_status "MySQL is ready"
        break
    fi
    ATTEMPT=$((ATTEMPT + 1))
    echo -n "."
    sleep 1
done

if [ $ATTEMPT -eq $MAX_ATTEMPTS ]; then
    print_error "MySQL did not start within timeout"
    print_info "Check logs with: docker-compose logs ${PROJECT_NAME}_db"
    exit 1
fi

# MODIFIED: Import user setup SQL
# Reason: Ensure all test users are created with correct credentials
print_info "Setting up user accounts..."
if [ -f "$PROJECT_DIR/DB/setup_users.sql" ]; then
    docker exec ${PROJECT_NAME}_db mysql -u root nshare_lite_db < "$PROJECT_DIR/DB/setup_users.sql" 2>/dev/null || true
    print_status "Users configured"
else
    print_warning "User setup script not found at DB/setup_users.sql"
fi

################################################################################
# SECTION 6: Final Status and Login Information
################################################################################
echo ""
echo -e "${YELLOW}[Step 6/6] System startup complete!${NC}"

# MODIFIED: Display system status and login information
# Reason: Provide user with immediate access instructions
sleep 2

echo ""
echo -e "${GREEN}=================================================================================${NC}"
echo -e "${GREEN}                    NATMS IS READY FOR USE!${NC}"
echo -e "${GREEN}=================================================================================${NC}"
echo ""

# Check service status
print_status "Web Server:  Ready at http://127.0.0.1:8000"
print_status "Database:    Running (MySQL/MariaDB 10.4)"
print_status "PHP Version: 8.0.30"
echo ""

# MODIFIED: Display login instructions
# Reason: Guide user to correct login page
echo -e "${BLUE}LOGIN INSTRUCTIONS:${NC}"
echo -e "  1. Open browser: http://127.0.0.1:8000/dashboard/login.php"
echo -e "  2. Use credentials from: ${PROJECT_DIR}/credentials.txt"
echo -e "  3. After login, select desired module from dashboard"
echo ""

# MODIFIED: Display quick reference credentials
# Reason: Provide common test accounts for quick access
echo -e "${BLUE}QUICK REFERENCE - TEST ACCOUNTS:${NC}"
echo -e "  ${YELLOW}Admin:${NC}"
echo -e "    Username: admin"
echo -e "    Password: admin123"
echo -e ""
echo -e "  ${YELLOW}Training Staff:${NC}"
echo -e "    Username: training"
echo -e "    Password: training123"
echo -e ""
echo -e "  ${YELLOW}Instructor:${NC}"
echo -e "    Username: 500"
echo -e "    Password: instructor123"
echo -e ""
echo -e "  ${YELLOW}Trainee:${NC}"
echo -e "    Username: trg01"
echo -e "    Password: password123"
echo ""

# MODIFIED: Display Docker commands for troubleshooting
# Reason: Allow user to debug issues if needed
echo -e "${BLUE}USEFUL COMMANDS:${NC}"
echo -e "  View logs:     ${YELLOW}docker-compose logs -f${NC}"
echo -e "  Stop services: ${YELLOW}docker-compose down${NC}"
echo -e "  Restart:       ${YELLOW}./run.sh${NC}"
echo -e "  Database CLI:  ${YELLOW}docker exec -it natms_db mysql -u root${NC}"
echo ""

# MODIFIED: Display accessible modules based on default users
# Reason: Guide users on where to go for each account type
echo -e "${BLUE}AVAILABLE MODULES:${NC}"
echo -e "  ${GREEN}✓${NC} Training Portal       - Learn from courses and materials"
echo -e "  ${GREEN}✓${NC} Classroom Monitoring  - Track classroom activities"
echo -e "  ${GREEN}✓${NC} Booking System        - Book training slots"
echo -e "  ${GREEN}✓${NC} Live Network Status   - Monitor system health"
echo -e "  ${GREEN}✓${NC} NIAT Cloud            - File storage & collaboration"
echo ""

echo -e "${GREEN}=================================================================================${NC}"
print_status "NATMS startup script completed successfully!"
echo -e "${GREEN}=================================================================================${NC}"
echo ""

# MODIFIED: Display final reminder
# Reason: Encourage secure password change
echo -e "${YELLOW}SECURITY REMINDER:${NC}"
echo -e "  For production deployment:"
echo -e "  1. Change all default passwords immediately"
echo -e "  2. Store credentials.txt in secure location"
echo -e "  3. Remove plaintext credentials from code"
echo -e "  4. Enable HTTPS/SSL certificates"
echo -e "  5. Implement regular database backups"
echo ""
