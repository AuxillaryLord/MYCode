<?php
include '../db.php';

if (!isset($_GET['id'])) {
    die("⚠️ Instructor ID not specified.");
}

$id = $_GET['id'];

try {
    $stmt = $pdo->prepare("DELETE FROM instructors WHERE id = ?");
    $stmt->execute([$id]);

    header("Location: admin_dashboard.php?msg=Instructor+deleted+successfully");
    exit;
} catch (PDOException $e) {
    echo "❌ Error deleting instructor: " . $e->getMessage();
}
?>
