<?php
// Database connection
// MODIFIED: Changed from 'localhost' to use MYSQL_HOST env var or 'mysql' service name
// Reason: Docker containers cannot connect to 'localhost' - must use service name from docker-compose.yml
// Original: $host = 'localhost';
$host = getenv('MYSQL_HOST') ?: 'mysql';
$db   = 'classroom_monitoring';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

// DSN for PDO connection
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,  // Throw exceptions on errors
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,         // Return associative arrays
    PDO::ATTR_EMULATE_PREPARES   => false,                    // Use real prepared statements
];

try {
    // Attempt to create a PDO instance
    $pdo = new PDO($dsn, $user, $pass, $options);

    

} catch (\PDOException $e) {
    // Log error details (error message, time)
    error_log("DB error on " . date('Y-m-d H:i:s') . ": " . $e->getMessage(), 0);
    
    // Respond with a general error message
    throw new Exception('Database connection failed.');

}
