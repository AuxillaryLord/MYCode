<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// MODIFIED: Changed from 'localhost' to use MYSQL_HOST env var or 'mysql' service name
// Reason: Docker containers cannot connect to 'localhost' - must use service name from docker-compose.yml
// Original: $conn = new mysqli("localhost", "root", "", "live_network");
$conn = new mysqli(getenv('MYSQL_HOST') ?: 'mysql', "root", "", "live_network");

$id = intval($_POST['id']);
$role = $_POST['role'];

if (in_array($role, ['user', 'admin'])) {
    $stmt = $conn->prepare("UPDATE users SET role = ? WHERE id = ?");
    $stmt->bind_param("si", $role, $id);
    $stmt->execute();
    $stmt->close();
}

$conn->close();
header("Location: admin.php?status=updated_user");
	exit();
