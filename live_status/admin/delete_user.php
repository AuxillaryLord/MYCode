<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = intval($_POST['id']);

    // MODIFIED: Changed from 'localhost' to use MYSQL_HOST env var or 'mysql' service name
    // Reason: Docker containers cannot connect to 'localhost' - must use service name from docker-compose.yml
    // Original: $conn = new mysqli("localhost", "root", "", "live_network");
    if ($_SESSION['user']['id'] !== $id) {
        $conn = new mysqli(getenv('MYSQL_HOST') ?: 'mysql', "root", "", "live_network");
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
        $conn->close();
    }

    header("Location: admin.php?status=user_deleted");
    exit;
}
