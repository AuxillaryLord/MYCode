# 🚀 NATMS System - Quick Start

## Status: Ready to Run ✅

Your NATMS (Naval Aviation Training Management System) is ready. Follow the commands below to launch it.

---

## 🎯 The Basics

**What you have:**
- ✅ PHP 8.0.30 (installed)
- ✅ 5 SQL schema files (DB/*.sql)
- ✅ Multi-module web app (5 modules)
- ✅ All source code (ready to go)

**What you need to do:**
1. Start MySQL (likely already running)
2. Import 5 databases
3. Start PHP web server
4. Open browser

**Time to running:** ~30 seconds

---

## ⚡ Run This (Copy & Paste into Terminal)

```bash
cd /workspaces/MYCode && bash -c '
# Set up all 5 databases
for db in booking classroom_monitoring training_portal live_network nshare_lite_db; do
  mysql -u root -e "DROP DATABASE IF EXISTS \`$db\`;" 2>/dev/null
  mysql -u root -e "CREATE DATABASE \`$db\` CHARACTER SET utf8mb4;"
  mysql -u root "$db" < DB/${db}.sql && echo "✓ $db"
done

# Start PHP server (kill existing first)
lsof -ti:8000 2>/dev/null | xargs kill -9 2>/dev/null || true
sleep 1
nohup php -S 127.0.0.1:8000 -t . > /tmp/php.log 2>&1 &
sleep 2

# Verify
if curl -s http://127.0.0.1:8000 >/dev/null 2>&1; then
  echo ""
  echo "✅ NATMS is RUNNING!"
  echo "🌐 Open: http://127.0.0.1:8000"
  echo "📊 Dashboard: http://127.0.0.1:8000/dashboard/"
else
  echo "❌ Server failed. Check: tail -f /tmp/php.log"
fi
'
```

---

## 📱 Access the App

Once running, open in your browser:

| Module | URL |
|--------|-----|
| **Home/Dashboard** | http://127.0.0.1:8000/dashboard/ |
| **Facility Booking** | http://127.0.0.1:8000/booking/ |
| **Training Portal** | http://127.0.0.1:8000/training-portal/ |
| **Classroom Mon.** | http://127.0.0.1:8000/classroom_monitoring/ |
| **Live Network** | http://127.0.0.1:8000/live_status/ |
| **NIAT Cloud** | http://127.0.0.1:8000/niatcloud/ |

---

## 🛠️ Common Commands

```bash
# View log
tail -f /tmp/php.log

# Stop server (if running in background)
pkill -f "php -S"

# Restart PHP on different port
php -S 127.0.0.1:9000 -t .

# Check MySQL
mysql -u root -e "SHOW DATABASES;"

# Manually start/stop MySQL
sudo systemctl start mysql
sudo systemctl stop mysql
```

---

## 📋 What Is NATMS?

- **Naval Aviation Training Management System**
- Multi-module platform for managing training, facilities, classrooms, documents
- 5 independent modules + shared authentication (NIAT Cloud)
- Used by facility managers, instructors, trainees

## 🏗️ Tech Stack
| Component | Version |
|-----------|---------|
| PHP | 8.0+ |
| MySQL | 10.4+ (MariaDB) |
| Frontend | Tailwind CSS + Vanilla JS |
| Database | PDO / MySQLi |

---

## ❓ Issues?

### MySQL not found/running?
```bash
sudo systemctl start mysql    # or mariadb
mysql -u root -e "SELECT 1;"  # verify it works
```

### Port 8000 in use?
```bash
lsof -ti:8000 | xargs kill -9
# then retry PHP command with port 9000
```

### Can't import databases?
```bash
# Try verbose mode
mysql -u root booking < DB/booking.sql --verbose
# Or check MySQL user/password
mysql -u root -p  # (try empty password)
```

---

## 📚 More Details

See [SETUP_GUIDE.md](SETUP_GUIDE.md) for step-by-step instructions and complete troubleshooting.

