<?php
session_start();
$user_id = $_SESSION["user_id"] ?? null;
$conn = new mysqli("localhost", "root", "", "classroom_monitoring");
$name = "User";
if ($user_id) {
    $stmt = $conn->prepare("SELECT name FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($name);
    $stmt->fetch();
    $stmt->close();
}


date_default_timezone_set('Asia/Kolkata');
$today = date('l');
$now = date('H:i:s');
$current_date = date('Y-m-d');

// Step 1: Get all classrooms
$classrooms = [];
$res = $conn->query("SELECT id, code FROM classrooms ORDER BY code ASC");
while ($row = $res->fetch_assoc()) {
    $classrooms[$row['id']] = $row['code'];
}

// Step 2: Get today's sessions from weekly schedule
$stmt = $conn->prepare("
    SELECT ws.*, i.name AS instructor_name, c.code AS classroom_code 
    FROM weekly_schedule ws
    JOIN instructors i ON ws.instructor_id = i.id
    JOIN classrooms c ON ws.classroom_id = c.id
    WHERE ws.day_of_week = ?
");
$stmt->bind_param("s", $today);
$stmt->execute();
$sessions = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Step 3: Get today's check-ins
$checkins = [];
$res = $conn->prepare("
    SELECT * FROM checkins 
    WHERE date = ? AND status = 'manned'
");
$res->bind_param("s", $current_date);
$res->execute();
$ci_result = $res->get_result();
while ($row = $ci_result->fetch_assoc()) {
    $key = $row['classroom_id'] . '_' . $row['session_start'];
    $checkins[$key] = $row['checkin_time']; // store checkin time
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Director Dashboard - Real-Time</title>
    <link href="tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-blue-900 text-white p-6">
	<div class="flex justify-between items-center mb-4">
		<h1 class="text-2xl font-bold">Welcome, <?= $name ?></h1>
		<a href="logout.php" class="bg-red-600 hover:bg-red-800 text-white px-4 py-2 rounded">Logout</a>
	</div>
    <h1 class="text-3xl font-bold mb-4">Live Classroom Status - <?= $today ?> <?= date('H:i') ?></h1>

    <div class="grid grid-cols-3 gap-4">
        <?php foreach ($sessions as $s): 
            $key = $s['classroom_id'] . '_' . $s['start_time'];
            $status = isset($checkins[$key]) ? 'manned' : 'unmanned';
            $bg = $status === 'manned' ? 'bg-green-600' : 'bg-red-600';
        ?>
            <div class="p-4 <?= $bg ?> rounded shadow text-white">
                <h2 class="text-xl font-bold"><?= $s['classroom_code'] ?></h2>
                <p class="text-sm">Time: <?= substr($s['start_time'], 0, 5) ?> - <?= substr($s['end_time'], 0, 5) ?></p>
                <p class="text-sm">Instructor: <?= $s['instructor_name'] ?></p>
                <p class="text-sm">
					Status: <?= ucfirst($status) ?>
					<?php if ($status === 'manned'): ?>
						<br>Checked in at: <?= substr($checkins[$key], 0, 5) ?>
					<?php endif; ?>
				</p>

            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>
