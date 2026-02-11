<?php
include '../db.php';

if (!isset($_GET['id'])) {
    die("⚠️ Classroom ID not specified.");
}

$id = $_GET['id'];

try {
    $stmt = $pdo->prepare("DELETE FROM classrooms WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: admin_dashboard.php?msg=Classroom+deleted+successfully");
    exit;
} catch (PDOException $e) {
    echo "❌ Error deleting classroom: " . $e->getMessage();
}
?>
