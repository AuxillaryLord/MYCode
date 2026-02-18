#!/bin/bash

################################################################################
# NATMS Password & Authentication Verification Script
# Purpose: Check if authentication system is properly configured
# Usage: bash verify_auth.sh
################################################################################

echo "================================================================================"
echo "NATMS Authentication System Verification"
echo "================================================================================"
echo ""

# MODIFIED: Check Docker containers running
# Reason: Verify services are accessible before testing
echo "[1/5] Checking Docker containers..."
if docker-compose ps | grep -q "natms_db"; then
    echo "✅ MySQL container is running"
else
    echo "❌ MySQL container is NOT running"
    echo "   Run: docker-compose up -d"
    exit 1
fi

if docker-compose ps | grep -q "natms_web"; then
    echo "✅ Web container is running"
else
    echo "❌ Web container is NOT running"
fi

echo ""

# MODIFIED: Check if users exist in database
# Reason: Verify setup_users.sql was executed
echo "[2/5] Checking test users in database..."
ADMIN_COUNT=$(docker exec natms_db mysql -u root nshare_lite_db -s -e "SELECT COUNT(*) FROM users WHERE username='admin';" 2>/dev/null)
if [ "$ADMIN_COUNT" = "1" ]; then
    echo "✅ Admin user found in database"
else
    echo "❌ Admin user NOT found in database"
fi

USER_COUNT=$(docker exec natms_db mysql -u root nshare_lite_db -s -e "SELECT COUNT(*) FROM users;" 2>/dev/null)
echo "   Total users in database: $USER_COUNT"

echo ""

# MODIFIED: Display user credentials
# Reason: Show available test accounts
echo "[3/5] Test user credentials:"
echo "────────────────────────────────────────────────────────────────────────────"
docker exec natms_db mysql -u root nshare_lite_db -e "
SELECT 
    CONCAT(username, '  /  ', 'password') as 'Username / Password Combination',
    role as 'Role',
    is_active as 'Active'
FROM users 
ORDER BY role, username
LIMIT 15;" 2>/dev/null

echo ""

# MODIFIED: Check password hashes
# Reason: Verify hashes are properly stored (should start with $2y$)
echo "[4/5] Password hash verification:"
echo "────────────────────────────────────────────────────────────────────────────"
docker exec natms_db mysql -u root nshare_lite_db -e "
SELECT 
    username,
    CONCAT(SUBSTRING(password, 1, 50), '...') as 'Hash (first 50 chars)',
    IF(password LIKE '\$2y\$%', '✅ Valid bcrypt', '❌ Invalid hash') as 'Hash Format'
FROM users 
LIMIT 10;" 2>/dev/null

echo ""

# MODIFIED: Provide test credentials
# Reason: Give user quick reference for testing
echo "[5/5] Quick test credentials to use at login page:"
echo "────────────────────────────────────────────────────────────────────────────"
echo "Admin account:"
echo "  URL: http://127.0.0.1:8000/dashboard/login.php"
echo "  Username: admin"
echo "  Password: admin123"
echo ""
echo "Training Staff:"
echo "  Username: training"
echo "  Password: training123"
echo ""
echo "Instructor:"
echo "  Username: 500"
echo "  Password: instructor123"
echo ""
echo "Trainee:"
echo "  Username: trg01"
echo "  Password: password123"
echo ""

echo "================================================================================"
echo "📖 If login fails with 'Incorrect password':"
echo "───────────────────────────────────────────────────────────────────────────────"
echo "1. Visit: http://127.0.0.1:8000/dashboard/debug_test.php"
echo "2. Click 'Quick Test' button for your username"
echo "3. Check if password hash matches in database"
echo ""
echo "If no users appear:"
echo "1. Run setup_users.sql: docker exec natms_db mysql -u root nshare_lite_db < DB/setup_users.sql"
echo "2. Then re-run this script"
echo "================================================================================"
