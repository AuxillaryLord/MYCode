# NATMS System - Complete Setup Guide

## Quick Start (Copy & Paste)

### 1. Start MySQL (if not already running)
```bash
sudo systemctl start mysql
# or
sudo systemctl start mariadb
```

### 2. Verify MySQL is accessible
```bash
mysql -u root -e "SELECT VERSION();"
```

If this fails with "access denied", MySQL may need a password or may not be running.

### 3. One-command setup (creates databases + runs PHP server)
```bash
cd /workspaces/MYCode && bash -c '
echo "Setting up databases..."
for db in booking classroom_monitoring training_portal live_network nshare_lite_db; do
    mysql -u root -e "DROP DATABASE IF EXISTS \`$db\`;" 2>/dev/null
    mysql -u root -e "CREATE DATABASE \`$db\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
    mysql -u root "$db" < "DB/${db}.sql" && echo "✓ $db"
done

echo ""
echo "Starting PHP server on http://127.0.0.1:8000..."
lsof -ti:8000 2>/dev/null | xargs kill -9 2>/dev/null || true
sleep 1
nohup php -S 127.0.0.1:8000 -t . > /tmp/php.log 2>&1 &
sleep 2

if curl -s http://127.0.0.1:8000 >/dev/null; then
    echo "✅ Server running!"
    echo "Access: http://127.0.0.1:8000"
    echo "Logs: tail -f /tmp/php.log"
else
    echo "❌ Server failed. Logs:"
    tail -20 /tmp/php.log
fi
'
```

### 4. Or step-by-step (manual):

#### Import Databases
```bash
cd /workspaces/MYCode

# Drop existing databases
mysql -u root -e "DROP DATABASE IF EXISTS booking;"
mysql -u root -e "DROP DATABASE IF EXISTS classroom_monitoring;"
mysql -u root -e "DROP DATABASE IF EXISTS training_portal;"
mysql -u root -e "DROP DATABASE IF EXISTS live_network;"
mysql -u root -e "DROP DATABASE IF EXISTS nshare_lite_db;"

# Create fresh databases
mysql -u root -e "CREATE DATABASE booking CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -u root -e "CREATE DATABASE classroom_monitoring CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -u root -e "CREATE DATABASE training_portal CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -u root -e "CREATE DATABASE live_network CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -u root -e "CREATE DATABASE nshare_lite_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Import schemas
mysql -u root booking < DB/booking.sql
mysql -u root classroom_monitoring < DB/classroom_monitoring.sql
mysql -u root training_portal < DB/training_portal.sql
mysql -u root live_network < DB/live_network.sql
mysql -u root nshare_lite_db < DB/nshare_lite_db.sql
```

#### Start PHP Server
```bash
cd /workspaces/MYCode

# Kill any existing server on port 8000
lsof -ti:8000 | xargs kill -9 2>/dev/null || true

# Start PHP built-in server in foreground
php -S 127.0.0.1:8000 -t .

# OR in background
nohup php -S 127.0.0.1:8000 -t . > /tmp/php.log 2>&1 &
tail -f /tmp/php.log
```

### 5. Open in Browser
Once the server is running, open:
```
http://127.0.0.1:8000
```

For specific modules:
- Dashboard: `http://127.0.0.1:8000/dashboard/`
- Booking System: `http://127.0.0.1:8000/booking/`
- Training Portal: `http://127.0.0.1:8000/training-portal/`
- Classroom Monitoring: `http://127.0.0.1:8000/classroom_monitoring/`
- Live Status: `http://127.0.0.1:8000/live_status/`
- NIAT Cloud: `http://127.0.0.1:8000/niatcloud/`

---

## Database Credentials
- **Host:** localhost
- **User:** root
- **Password:** (empty)

## Database Schemas
| Database | Purpose |
|----------|---------|
| `booking` | Facility booking system |
| `classroom_monitoring` | Classroom attendance & scheduling |
| `training_portal` | Training materials & courses |
| `live_network` | Network device status monitoring |
| `nshare_lite_db` | File storage & NIAT Cloud |

---

## Troubleshooting

### MySQL not starting
```bash
# Check if already running
ps aux | grep mysql

# Start manually
sudo /usr/sbin/mysqld --user=mysql &

# Or use systemctl
sudo systemctl start mysql
sudo systemctl status mysql
```

### Port 8000 already in use
```bash
# Find and kill process on port 8000
lsof -ti:8000 | xargs kill -9

# Or use a different port
php -S 127.0.0.1:9000 -t .
```

### MySQL permission denied
If you get "access denied" errors:
```bash
# Try with sudo
sudo mysql -u root -e "SELECT 1;"

# Or check the MySQL version/setup
sudo mysql_secure_installation
```

### Database import fails
If SQL import fails, check:
```bash
# Verify DB files exist
ls -la /workspaces/MYCode/DB/

# Check file contents
head -20 /workspaces/MYCode/DB/booking.sql

# Try importing with verbose output
mysql -u root booking < /workspaces/MYCode/DB/booking.sql --verbose
```

---

## Architecture Overview

**NATMS** is a multi-module naval aviation training management system with:

1. **Facility Booking** - Schedule facilities (auditorium, exam halls)
2. **Classroom Monitoring** - Track instructor attendance
3. **Training Portal** - Distribute courses, PPTs, videos, Q&A banks
4. **Live Network Status** - Monitor device health
5. **NIAT Cloud** - File/document storage

**Stack:** PHP 8.0+ | MySQL/MariaDB | Tailwind CSS | Vanilla JavaScript

---

## Execution Steps Done So Far

✅ Analyzed project structure  
✅ Identified all required databases (5 SQL files)  
✅ Verified PHP 8.0.30 is available  
✅ Found database credentials (root / no password)  
✅ Created setup scripts  

⏳ **Next:** Run the setup commands above in your terminal

