<?php
header('Content-Type: application/json');
require 'db.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$facility_id = $_GET['facility_id'] ?? '';
$date = $_GET['date'] ?? '';

if (!$facility_id || !$date) {
    echo json_encode([]);
    exit;
}

try {
    $standardSlots = [
        "08:00-11:00",
        "11:00-14:00",
        "14:00-16:00",
        "16:00-18:00",
        "08:00-18:00"
    ];

    $slotStatus = [];

    // 1. Fetch approved bookings
    $stmt = $pdo->prepare("SELECT start_date, end_date, start_time, end_time FROM bookings WHERE facility_id = ? AND ? BETWEEN start_date AND end_date AND status = 'approved'");
    $stmt->execute([$facility_id, $date]);

    function timesOverlap($start1, $end1, $start2, $end2) {
		return max($start1, $start2) < min($end1, $end2);
	}

	$customBookedSlots = [];
	$bookedIndividualSlots = [];


	while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
		$bookingStart = date('H:i', strtotime($row['start_time']));
		$bookingEnd = date('H:i', strtotime($row['end_time']));

		$matchedStandard = false;

		foreach ($standardSlots as $slot) {
			list($slotStart, $slotEnd) = explode('-', $slot);

			if ($bookingStart === $slotStart && $bookingEnd === $slotEnd) {
				$slotStatus[$slot] = 'booked';
				$matchedStandard = true;
				if ($slot !== "08:00-18:00") {
					$bookedIndividualSlots[] = $slot;
				}
			} elseif (timesOverlap($bookingStart, $bookingEnd, $slotStart, $slotEnd)) {
				if (!isset($slotStatus[$slot])) {
					$slotStatus[$slot] = 'partial';
				}
			}
		}

		if (!$matchedStandard) {
			// Store custom bookings to return to frontend
			$customBookedSlots[] = "$bookingStart-$bookingEnd";
		}
	}

	// After loop: check if all standard slots are booked, then mark full day
	$individualSlots = ["08:00-11:00", "11:00-14:00", "14:00-16:00", "16:00-18:00"];
	if (!array_diff($individualSlots, $bookedIndividualSlots)) {
		$slotStatus["08:00-18:00"] = 'booked';
	}

// Admin blocked slots (no change)





    // 2. Fetch admin-blocked slots
    $stmt = $pdo->prepare("SELECT slot FROM blocked_slots WHERE facility_id = ? AND block_date = ?");
    $stmt->execute([$facility_id, $date]);
    $blockedSlots = $stmt->fetchAll(PDO::FETCH_COLUMN);

    foreach ($blockedSlots as $blocked) {
        // If already marked as booked, don't overwrite
        if (!isset($slotStatus[$blocked])) {
            $slotStatus[$blocked] = 'blocked';
        }
    }

    // Build output format
    $output = [];
    foreach ($slotStatus as $slot => $status) {
        $output[] = [
            'slot' => $slot,
            'status' => $status
        ];
    }
	
	// Add custom booked slots separately
	foreach ($customBookedSlots as $customSlot) {
		$output[] = [
			'slot' => $customSlot,
			'status' => 'custom'
		];
	}

    echo json_encode($output);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error']);
}
