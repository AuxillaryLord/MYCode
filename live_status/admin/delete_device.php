<?php
// MODIFIED: Changed from 'localhost' to use MYSQL_HOST env var or 'mysql' service name
// Reason: Docker containers cannot connect to 'localhost' - must use service name from docker-compose.yml
// Original: $conn = new mysqli("localhost", "root", "", "live_network");
$conn = new mysqli(getenv('MYSQL_HOST') ?: 'mysql', "root", "", "live_network");
$id = $_GET['id'];
$conn->query("DELETE FROM devices WHERE id=$id");
$conn->close();

header("Location: admin.php?status=deleted");
exit();

?>
