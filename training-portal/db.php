<?php
// MODIFIED: Changed from 'localhost' to use MYSQL_HOST env var or 'mysql' service name
// Reason: Docker containers cannot connect to 'localhost' - must use service name from docker-compose.yml
// Original: $host = "localhost";
$host = getenv('MYSQL_HOST') ?: 'mysql';
$user = "root";
$password = "";
$dbname = "training_portal";

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
?>
