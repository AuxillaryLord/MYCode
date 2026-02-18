#!/bin/bash
set -e

echo "Creating NATMS databases..."

mysql -u root << EOSQL
CREATE DATABASE IF NOT EXISTS booking CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE DATABASE IF NOT EXISTS classroom_monitoring CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE DATABASE IF NOT EXISTS training_portal CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE DATABASE IF NOT EXISTS live_network CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE DATABASE IF NOT EXISTS nshare_lite_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EOSQL

echo "Importing database schemas..."

mysql -u root booking < /data/sql/booking.sql
mysql -u root classroom_monitoring < /data/sql/classroom_monitoring.sql
mysql -u root training_portal < /data/sql/training_portal.sql
mysql -u root live_network < /data/sql/live_network.sql
mysql -u root nshare_lite_db < /data/sql/nshare_lite_db.sql

echo "✓ All databases initialized successfully"
