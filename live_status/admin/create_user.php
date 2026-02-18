<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // MODIFIED: Changed from 'localhost' to use MYSQL_HOST env var or 'mysql' service name
    // Reason: Docker containers cannot connect to 'localhost' - must use service name from docker-compose.yml
    // Original: $conn = new mysqli("localhost", "root", "", "live_network");
    $conn = new mysqli(getenv('MYSQL_HOST') ?: 'mysql', "root", "", "live_network");
    $username = trim($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    if (!empty($username) && !empty($password) && in_array($role, ['admin', 'user'])) {
        $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $password, $role);
        $stmt->execute();
        $stmt->close();
    }

    $conn->close();
    header("Location: admin.php?status=user_created");
    exit;
}
