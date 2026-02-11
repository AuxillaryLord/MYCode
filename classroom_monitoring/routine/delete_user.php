<?php
include '../db.php';

if (!isset($_GET['id'])) {
    die("⚠️ ID not specified.");
}

$id = $_GET['id'];

try {
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$id]);

    // Redirect with success message
    header("Location: admin_dashboard.php?msg=User+deleted+successfully");
    exit;
} catch (PDOException $e) {
    echo "❌ Error deleting user: " . $e->getMessage();
}
?>
