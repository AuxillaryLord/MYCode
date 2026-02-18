#!/bin/bash
# Initialize databases for NATMS

echo "Creating databases..."

mysql -u root << EOF
CREATE DATABASE IF NOT EXISTS booking CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE DATABASE IF NOT EXISTS classroom_monitoring CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE DATABASE IF NOT EXISTS training_portal CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE DATABASE IF NOT EXISTS live_network CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE DATABASE IF NOT EXISTS nshare_lite_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EOF

echo "Importing schemas..."

mysql -u root booking < /docker-entrypoint-initdb.d/DB/booking.sql
mysql -u root classroom_monitoring < /docker-entrypoint-initdb.d/DB/classroom_monitoring.sql
mysql -u root training_portal < /docker-entrypoint-initdb.d/DB/training_portal.sql
mysql -u root live_network < /docker-entrypoint-initdb.d/DB/live_network.sql
mysql -u root nshare_lite_db < /docker-entrypoint-initdb.d/DB/nshare_lite_db.sql

echo "✓ Databases initialized"
