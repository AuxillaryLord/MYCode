<?php
// Test script for MySQL connection debugging

echo "=== NATMS Database Connection Test ===\n\n";

// Test 1: Check environment variables
echo "1. Environment Variables:\n";
echo "   MYSQL_HOST = " . (getenv('MYSQL_HOST') ?: 'not set (will use default: mysql)') . "\n\n";

// Test 2: Try to connect with mysqli
echo "2. Testing mysqli connection to live_network database:\n";
$host = getenv('MYSQL_HOST') ?: 'mysql';
$user = 'root';
$password = '';
$dbname = 'live_network';

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    echo "   ❌ Connection failed!\n";
    echo "   Error: " . $conn->connect_error . "\n";
    echo "   Error Code: " . $conn->connect_errno . "\n";
} else {
    echo "   ✅ Connection successful!\n";
    echo "   Server info: " . $conn->server_info . "\n";
    
    // Test 3: Check if databases exist
    echo "\n3. Available databases:\n";
    $result = $conn->query("SHOW DATABASES;");
    if ($result) {
        while ($row = $result->fetch_array()) {
            echo "   - " . $row[0] . "\n";
        }
    }
    
    // Test 4: Check tables in live_network
    echo "\n4. Tables in 'live_network' database:\n";
    $result = $conn->query("SHOW TABLES;");
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_array()) {
            echo "   - " . $row[0] . "\n";
        }
    } else {
        echo "   (no tables or database empty)\n";
    }
    
    $conn->close();
}

// Test 5: Try PDO connection (for booking module)
echo "\n5. Testing PDO connection to booking database:\n";
try {
    $dsn = "mysql:host=$host;dbname=booking;charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
    echo "   ✅ PDO Connection successful!\n";
    $pdo = null;
} catch (PDOException $e) {
    echo "   ❌ PDO Connection failed!\n";
    echo "   Error: " . $e->getMessage() . "\n";
}

echo "\n=== End of Test ===\n";
?>
