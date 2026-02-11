<?php
// Database connection
$host = 'localhost';
$db   = 'booking';
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

    // Example of how you could fetch facilities conditionally
    // However, move this logic to where it's actually needed
    if (basename($_SERVER['PHP_SELF']) === 'index.php') {
        $stmt = $pdo->query("SELECT id, name FROM facilities ORDER BY name");
        $facilities = $stmt->fetchAll();
    }

} catch (\PDOException $e) {
    // Log error details (error message, time)
    error_log("DB error on " . date('Y-m-d H:i:s') . ": " . $e->getMessage(), 0);
    
    // Respond with a general error message
    http_response_code(500); // Internal Server Error
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed.']);
    exit;
}
