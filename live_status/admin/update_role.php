<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "live_network");

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
