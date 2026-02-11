<?php
require_once '../../includes/db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $facility_id = $_POST['facility_id'];
    $block_date = $_POST['block_date'];
    $slot = $_POST['slot'];
    $reason = $_POST['reason'];

    list($start_time, $end_time) = explode('-', $slot);

    // Step 1: Check if any APPROVED bookings exist on that date for this facility
    $bookingCheck = $pdo->prepare("
        SELECT COUNT(*) FROM bookings
        WHERE facility_id = ?
        AND status = 'Approved'
        AND (
            (? BETWEEN start_date AND end_date)
            OR
            (? BETWEEN start_date AND end_date)
        )
    ");
    $bookingCheck->execute([$facility_id, $block_date, $block_date]);

    if ($bookingCheck->fetchColumn()) {
        echo "<script>
            alert('Cannot block slot: Approved bookings exist on this date for this facility.');
            window.location.href = '../admin_panel.php';
        </script>";
        exit();
    }

    // Step 2: Check for overlap with already blocked slots
    $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM blocked_slots 
        WHERE facility_id = ? 
        AND block_date = ?
        AND (
            (start_time < ? AND end_time > ?)
        )");
    $checkStmt->execute([
        $facility_id,
        $block_date,
        $end_time,
        $start_time
    ]);

    if ($checkStmt->fetchColumn()) {
        echo "<script>
            alert('Overlapping blocked slot already exists.');
            window.location.href = '../admin_panel.php';
        </script>";
        exit();
    }

    // Step 3: Insert new block
    $stmt = $pdo->prepare("INSERT INTO blocked_slots 
        (facility_id, block_date, start_time, end_time, slot, reason) 
        VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $facility_id,
        $block_date,
        $start_time,
        $end_time,
        $slot,
        $reason
    ]);

    echo "<script>
        alert('Slot blocked successfully.');
        window.location.href = '../admin_panel.php';
    </script>";
    exit();
}
?>
