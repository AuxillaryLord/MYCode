<?php
require_once '../../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $roleName = trim($_POST['role_name']);
    $accessLevel = $_POST['access_level'];

    // Check if role already exists
    $stmt = $pdo->prepare("SELECT * FROM roles WHERE role_name = ?");
    $stmt->execute([$roleName]);
    $existingRole = $stmt->fetch();

    if ($existingRole) {
        // Update existing role
        $update = $pdo->prepare("UPDATE roles SET access_level = ? WHERE role_name = ?");
        $update->execute([$accessLevel, $roleName]);
    } else {
        // Add new role
        $insert = $pdo->prepare("INSERT INTO roles (role_name, access_level) VALUES (?, ?)");
        $insert->execute([$roleName, $accessLevel]);
    }

    header("Location: ../admin_panel.php");
    exit();
}
?>
