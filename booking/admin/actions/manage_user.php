<?php
require_once '../../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $role = $_POST['role'];
    $status = $_POST['status'];

    // Check if user exists
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $existingUser = $stmt->fetch();

    if ($existingUser) {
        // Update existing user
        $update = $pdo->prepare("UPDATE users SET username = ?, role = ?, status = ? WHERE email = ?");
        $update->execute([$username, $role, $status, $email]);
    } else {
        // Add new user
        $insert = $pdo->prepare("INSERT INTO users (username, email, role, status) VALUES (?, ?, ?, ?)");
        $insert->execute([$username, $email, $role, $status]);
    }

    header("Location: ../admin_panel.php");
    exit();
}
?>
