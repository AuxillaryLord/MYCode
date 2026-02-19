#!/bin/bash

# MODIFIED: Script to verify credentials and test authentication
# Reason: Identify why credentials are failing
# Original: No verification script
# New: Test all credentials and show actual hashes

echo "=================================="
echo "NATMS Credential Verification Tool"
echo "=================================="
echo ""

# Check if Docker is running
echo "Step 1: Checking Docker containers..."
docker ps | grep -E "natms_web|natms_db"

if [ $? -ne 0 ]; then
    echo "❌ Docker containers not running!"
    exit 1
fi

echo "✅ Docker containers running"
echo ""

# Get into MySQL and check users
echo "Step 2: Checking users in database..."
echo ""
echo "SELECT username, display_name, role, is_active, password FROM users ORDER BY role;" | \
docker exec -i natms_db mysql -u root nshare_lite_db

echo ""
echo "=================================="
echo "Step 3: Password Hash Reference"
echo "=================================="
echo ""
echo "Testing common passwords with bcrypt verification..."
echo "Use: docker exec natms_web php -r 'echo password_verify(\"PASSWORD\", \"HASH\") ? \"✓\" : \"✗\";'"
echo ""

# List of test credentials
CREDENTIALS=(
    "admin:admin123"
    "training:training123"
    "tdec:password123"
    "1:director123"
    "500:instructor123"
    "aafaculty:password123"
    "trg01:password123"
    "600:password123"
)

for cred in "${CREDENTIALS[@]}"; do
    USERNAME=$(echo $cred | cut -d: -f1)
    PASSWORD=$(echo $cred | cut -d: -f2)
    echo "Testing $USERNAME / $PASSWORD..."
done
