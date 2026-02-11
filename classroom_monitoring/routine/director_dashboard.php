<?php
session_start();
$user_id = $_SESSION["user_id"] ?? null;
$conn = new mysqli("localhost", "root", "", "classroom_monitoring");

$username = $_SESSION['username'];
$role = $_SESSION['role'];

date_default_timezone_set('Asia/Kolkata');
$today = date('l');
$current_date = date('Y-m-d');

// Get classrooms
$classrooms = [];
$res = $conn->query("SELECT id, code FROM classrooms ORDER BY code ASC");
while ($row = $res->fetch_assoc()) {
    $classrooms[$row['id']] = $row['code'];
}

// Get today's sessions
$stmt = $conn->prepare("SELECT ws.*, i.name AS instructor_name, c.code AS classroom_code, cr.name AS course_name 
    FROM weekly_schedule ws
    JOIN instructors i ON ws.instructor_id = i.id
    JOIN classrooms c ON ws.classroom_id = c.id
    JOIN courses cr ON ws.course_id = cr.id
    WHERE ws.day_of_week = ?");
$stmt->bind_param("s", $today);
$stmt->execute();
$sessions = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Get check-ins
$checkins = [];
$res = $conn->prepare("SELECT * FROM checkins WHERE date = ? AND status = 'manned'");
$res->bind_param("s", $current_date);
$res->execute();
$ci_result = $res->get_result();
while ($row = $ci_result->fetch_assoc()) {
    $keyTime = date('H:i', strtotime($row['session_start']));
    $key = $row['classroom_id'] . '_' . $keyTime;
    $checkins[$key] = $row['checkin_time'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Director Dashboard</title>
    <link href="tailwind.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="bg-blue-900 text-white min-h-screen p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">Welcome, <?= htmlspecialchars($username) ?></h1>
        <a href="logout_trg.php" class="bg-red-600 hover:bg-red-800 px-4 py-2 rounded">Logout</a>
    </div>

    <h2 class="text-xl mb-4">Live Classroom Status - <?= $today ?> <?= date('H:i') ?></h2>

    <div class="mb-6">
        <label for="filter-status" class="mr-2">Filter by Status:</label>
        <select id="filter-status" class="bg-blue-800 text-white p-2 rounded">
            <option value="all">All</option>
            <option value="manned">Manned</option>
            <option value="unmanned">Unmanned</option>
        </select>
    </div>

    <div id="sessions-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <?php
        $groupedSessions = [];
        foreach ($sessions as $s) {
            $keyTime = date('H:i', strtotime($s['start_time']));
            $key = $s['classroom_id'] . '_' . $keyTime;
            $statusKey = isset($checkins[$key]) ? 'manned' : 'unmanned';
            $groupedSessions[$statusKey][] = [
                'course' => $s['course_name'],
                'classroom' => $s['classroom_code'],
                'start' => substr($s['start_time'], 0, 5),
                'end' => substr($s['end_time'], 0, 5),
                'instructor' => $s['instructor_name'],
                'checkin' => $checkins[$key] ?? null
            ];
        }

        foreach ($groupedSessions as $status => $sessionList) {
            foreach ($sessionList as $s) {
                $bg = $status === 'manned' ? 'bg-green-600' : 'bg-red-600';
                echo "<div class='p-4 $bg rounded-lg shadow space-y-1'>";
                echo "<h3 class='text-lg font-semibold'>{$s['course']} ({$s['classroom']})</h3>";
                echo "<p class='text-sm'>Time: {$s['start']} - {$s['end']}</p>";
                echo "<p class='text-sm'>Instructor: {$s['instructor']}</p>";
                echo "<p class='text-sm'>Status: " . ucfirst($status);
                if ($status === 'manned') {
                    echo "<br>Checked in at: " . substr($s['checkin'], 0, 5);
                }
                echo "</p></div>";
            }
        }
        ?>
    </div>

    <script>
        $('#filter-status').on('change', function() {
            const value = $(this).val();
            $('#sessions-container > div').each(function() {
                if (value === 'all') {
                    $(this).show();
                } else if ($(this).hasClass('bg-' + (value === 'manned' ? 'green' : 'red') + '-600')) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });

        function updateSessions() {
            $.ajax({
                url: "get_sessions.php",
                method: "GET",
                success: function(response) {
                    $('#sessions-container').html(response);
                }
            });
        }
        setInterval(updateSessions, 60000);
    </script>
</body>
</html>