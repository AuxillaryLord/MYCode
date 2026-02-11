<?php
session_start();
date_default_timezone_set('Asia/Kolkata'); // ✅ Use local time
// Prevent browser from caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

$conn = new mysqli("localhost", "root", "", "classroom_monitoring");

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: /niatcloud/login.php");
    exit();
}

// Restrict to instructor role only
if ($_SESSION['role'] !== 'instructor') {
    echo "Access Denied: You do not have permission to view this page.";
    exit();
}

// Use session values directly
$username = $_SESSION['username'];
$role = $_SESSION['role'];

$today = date('l');
$now = date('H:i:s');
$date = date('Y-m-d');

// Fetch all sessions for today (no filtering on end time)
// ✅ Step 1: Get instructor ID from username
$getInstructor = $conn->prepare("SELECT id FROM instructors WHERE username = ?");
$getInstructor->bind_param("s", $username);
$getInstructor->execute();
$getInstructorResult = $getInstructor->get_result();

if ($getInstructorResult->num_rows === 0) {
    echo "Instructor not found in the monitoring database.";
    exit();
}
$instructor = $getInstructorResult->fetch_assoc();
$instructor_id = $instructor['id'];

// ✅ Step 2: Fetch today's sessions for this instructor
$stmt = $conn->prepare("SELECT ws.id AS ws_id, c.code, c.id AS classroom_id, ws.start_time, ws.end_time 
                        FROM weekly_schedule ws
                        JOIN classrooms c ON ws.classroom_id = c.id
                        WHERE ws.instructor_id = ? AND ws.day_of_week = ?");
$stmt->bind_param("is", $instructor_id, $today);

$stmt->execute();
$result = $stmt->get_result();

$sessions = [];
while ($row = $result->fetch_assoc()) {
    $sessions[] = $row; // Include all sessions for today
}

// Handle check-in
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $classroom_id = $_POST['classroom_id'];
    $session_start = $_POST['start_time'];
    $end = $_POST['end_time'];
    $current_date = $date;

    // Check if session already manned
    $check = $conn->prepare("SELECT id FROM checkins WHERE classroom_id = ? AND session_start = ? AND date = ?");
    $check->bind_param("iss", $classroom_id, $session_start, $current_date);
    $check->execute();
    $checkResult = $check->get_result();

    if ($checkResult->num_rows === 0) {
        $checkin_time = date('H:i:s');
        $insert = $conn->prepare("INSERT INTO checkins (instructor_id, classroom_id, session_start, session_end, date, status, checkin_time) 
                                  VALUES (?, ?, ?, ?, ?, 'manned', ?)");
        $insert->bind_param("iissss", $instructor_id, $classroom_id, $session_start, $end, $current_date, $checkin_time);
        $insert->execute();

        $msg = "✅ Check-in marked successfully at $checkin_time.";
    } else {
        $msg = "⚠️ Already checked in for this session.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Instructor Dashboard</title>
    <link href="tailwind.min.css" rel="stylesheet">
    <style>
        .fade { animation: fadeIn 0.5s ease-in-out; }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
    </style>
</head>
<body class="bg-blue-900 text-white min-h-screen p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">Welcome, <?= htmlspecialchars($username) ?></h1>
        <a href="logout_trg.php" class="bg-red-600 hover:bg-red-800 text-white px-4 py-2 rounded">Logout</a>
    </div>

    <h2 class="mb-4 text-xl">
        Today is <?= $today ?> | 
        <span id="live-time" class="font-mono"></span>
    </h2>

    <?php if (isset($msg)): ?>
        <div class="mb-4 bg-green-600 text-white p-3 rounded fade">
            <?= $msg ?>
        </div>
    <?php endif; ?>

    <div class="grid gap-4">
        <?php if (empty($sessions)): ?>
            <p class="text-gray-300">No more sessions scheduled for today.</p>
        <?php else: ?>
            <?php foreach ($sessions as $s): ?>
                <?php
                    $sessionEnded = $now > $s['end_time'];
                ?>
                <form method="POST" class="bg-white text-black p-4 rounded shadow fade">
                    <input type="hidden" name="classroom_id" value="<?= $s['classroom_id'] ?>">
                    <input type="hidden" name="start_time" value="<?= $s['start_time'] ?>">
                    <input type="hidden" name="end_time" value="<?= $s['end_time'] ?>">

                    <h3 class="text-xl font-semibold mb-1">Classroom: <?= htmlspecialchars($s['code']) ?></h3>
                    <p class="mb-2">Session: <?= substr($s['start_time'], 0, 5) ?> - <?= substr($s['end_time'], 0, 5) ?></p>
                    <p class="text-sm text-gray-700 mb-3">Current Time: <?= substr($now, 0, 5) ?></p>

                    <button type="submit"
                            class="w-full font-semibold px-4 py-2 rounded 
                            <?= $sessionEnded ? 'bg-gray-500 cursor-not-allowed' : 'bg-blue-700 hover:bg-blue-900 text-white' ?>"
                            <?= $sessionEnded ? 'disabled' : '' ?>>
                        <?= $sessionEnded ? 'Session Ended' : 'Mark as Manned' ?>
                    </button>
                </form>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Live Time Script -->
    <script>
        function updateTime() {
            const now = new Date();
            const timeStr = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: false });
            document.getElementById('live-time').textContent = timeStr;
        }
        updateTime();
        setInterval(updateTime, 1000);
    </script>
</body>
</html>
