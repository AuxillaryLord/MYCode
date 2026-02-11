<?php
require 'db.php';

$facility_id = $_POST['facility_id'] ?? '';
$start_date = $_POST['start_date'] ?? '';
$end_date = $_POST['end_date'] ?? '';
$slot = $_POST['slot'] ?? 'custom';  // Get slot value
$name = trim($_POST['name'] ?? '');
$unit = trim($_POST['unit'] ?? '');

file_put_contents('debug_post.txt', print_r($_POST, true));

// Determine start_time and end_time based on slot
if ($slot !== 'custom' && strpos($slot, '-') !== false) {
    [$start_time, $end_time] = explode('-', $slot);
    $start_time = trim($start_time ?? '');
    $end_time = trim($end_time ?? '');
} else {
    $start_time = $_POST['start_time'] ?? '';
    $end_time = $_POST['end_time'] ?? '';
}

// Validate required fields
if (!$facility_id || !$start_date || !$end_date || !$start_time || !$end_time || !$name || !$unit) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
    exit;
}

// Validate date format
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $start_date) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $end_date)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid date format.']);
    exit;
}

try {
    // Check if the slot is already booked (pending or approved)
	$stmt = $pdo->prepare("SELECT COUNT(*) FROM bookings 
		WHERE facility_id = ? 
		AND status IN ('pending', 'approved') 
		AND (
			start_date <= ? AND end_date >= ?
		)
		AND (
			start_time < ? AND end_time > ?
		)");
	$stmt->execute([
		$facility_id,
		$end_date, $start_date,
		$end_time, $start_time
	]);

	if ($stmt->fetchColumn()) {
		echo json_encode(['status' => 'error', 'message' => 'Booking overlaps with an existing request.']);
		exit;
	}

	// ✅ Check if the slot is blocked
	$stmt = $pdo->prepare("SELECT COUNT(*) FROM blocked_slots 
		WHERE facility_id = ? 
		AND block_date BETWEEN ? AND ?
		AND (
			start_time < ? AND end_time > ?
		)");

	$stmt->execute([
		$facility_id,
		$start_date,
		$end_date,
		$end_time, // from user input
		$start_time // from user input
	]);

	if ($stmt->fetchColumn()) {
		echo json_encode(['status' => 'error', 'message' => 'This slot is blocked and cannot be booked.']);
		exit;
	}



    $stmt = $pdo->prepare("INSERT INTO bookings 
        (facility_id, start_date, end_date, start_time, end_time, slot, name, unit, status) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Pending')");

    $success = $stmt->execute([
        $facility_id,
        $start_date,
        $end_date,
        $start_time,
        $end_time,
        $slot,
        $name,
        $unit
    ]);

    echo json_encode([
        'status' => $success ? 'success' : 'error',
        'message' => $success ? 'Booking request submitted for approval.' : 'Booking failed. Please try again.'
    ]);

} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}
