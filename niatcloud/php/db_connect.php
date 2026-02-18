<?php
// MODIFIED: Changed from 'localhost' to use MYSQL_HOST env var or 'mysql' service name
// Reason: Docker containers cannot connect to 'localhost' - must use service name from docker-compose.yml
// Original: $servername = "localhost";
$servername = getenv('MYSQL_HOST') ?: 'mysql';
$username = "root";
$password = ""; // Empty if you didn't set MySQL password
$dbname = "nshare_lite_db";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
