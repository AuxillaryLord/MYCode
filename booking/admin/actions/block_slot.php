<?php
require_once '../../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $facility_id = $_POST['facility_id'];
    $block_date = $_POST['block_date'];
    $slot = $_POST['slot'];  // Format: "08:00-11:00"
    $reason = $_POST['reason'];

    // Step 1: Extract start and end times from slot string
    list($start_time, $end_time) = explode('-', $slot);

    // Step 2: Check for overlap with existing blocked slots
    $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM blocked_slots 
        WHERE facility_id = ? 
        AND block_date = ?
        AND (
            (start_time < ? AND end_time > ?)  -- Overlap condition
        )");

    $checkStmt->execute([
        $facility_id,
        $block_date,
        $end_time, // New slot ends AFTER an existing slot starts
        $start_time // New slot starts BEFORE an existing slot ends
    ]);

    if ($checkStmt->fetchColumn()) {
        header("Location: ../admin_panel.php?message=Overlapping slot exists for this facility and date");
        exit();
    }

    // Step 3: Insert into table
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

    header("Location: ../admin_panel.php?message=Slot blocked successfully");
    exit();
}
?>
