<?php
include '../db.php';

if (!isset($_GET['id'])) {
    die("⚠️ No schedule ID provided.");
}

$id = $_GET['id'];

try {
    $stmt = $pdo->prepare("DELETE FROM weekly_schedule WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: admin_dashboard.php?msg=Schedule+deleted");
    exit;
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage();
}
?>
