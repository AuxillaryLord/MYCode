<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = intval($_POST['id']);

    // Prevent self-deletion
    if ($_SESSION['user']['id'] !== $id) {
        $conn = new mysqli("localhost", "root", "", "live_network");
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
        $conn->close();
    }

    header("Location: admin.php?status=user_deleted");
    exit;
}
