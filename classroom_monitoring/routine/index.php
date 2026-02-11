<?php
session_start();

// ✅ Use NIATCloud login session instead of local one
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: /niatcloud/login.php"); // Redirect to central login
    exit;
}

// ✅ Restrict roles allowed to access classroom monitoring
$allowed_roles = ['training'];
if (!in_array($_SESSION['role'], $allowed_roles)) {
    echo "Access Denied: You do not have permission to view this page.";
    exit;
}

// Connect to the classroom_monitoring database
$conn = new mysqli("localhost", "root", "", "classroom_monitoring");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch data for dropdowns
$classrooms = $conn->query("SELECT id, code FROM classrooms WHERE status = 'active'");
$instructors = $conn->query("SELECT id, name FROM instructors");
$courses = $conn->query("SELECT id, name FROM courses");
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Weekly Routine Manager</title>
    <link href="tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-blue-900 text-white min-h-screen flex items-center justify-center p-8">
    <div class="bg-white text-black rounded-2xl shadow-lg p-10 w-full max-w-3xl">
        <h1 class="text-2xl font-bold mb-6 text-center text-[#001F3F]">Indian Navy - Weekly Routine Manager</h1>
		<div class="flex justify-between items-center mb-6">
			<div class="text-[#001F3F] font-semibold">
				Welcome, <?= $_SESSION['username'] ?> 
			</div>

			<a href="logout_trg.php" class="bg-red-600 text-white px-4 py-1 rounded hover:bg-red-700">Logout</a>
		</div>
        <form action="weekly_schedule.php" method="POST" class="space-y-4">
            <div>
                <label class="block font-semibold">Classroom</label>
                <select name="classroom_id" required class="w-full border p-2 rounded">
                    <?php while ($row = $classrooms->fetch_assoc()): ?>
                        <option value="<?= $row['id'] ?>"><?= $row['code'] ?></option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div>
                <label class="block font-semibold">Instructor</label>
                <select name="instructor_id" required class="w-full border p-2 rounded">
                    <?php while ($row = $instructors->fetch_assoc()): ?>
                        <option value="<?= $row['id'] ?>"><?= $row['name'] ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
			
			<div>
                <label class="block font-semibold">Course</label>
                <select name="course_id" required class="w-full border p-2 rounded">
                    <?php while ($row = $courses->fetch_assoc()): ?>
                        <option value="<?= $row['id'] ?>"><?= $row['name'] ?></option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div>
                <label class="block font-semibold">Day of Week</label>
                <select name="day_of_week" required class="w-full border p-2 rounded">
                    <option value="Monday">Monday</option>
                    <option value="Tuesday">Tuesday</option>
                    <option value="Wednesday">Wednesday</option>
                    <option value="Thursday">Thursday</option>
                    <option value="Friday">Friday</option>
                    <option value="Saturday">Saturday</option>
                </select>
            </div>

            <div>
                <label class="block font-semibold">Session Number (1–4)</label>
                <select name="session_number" required class="w-full border p-2 rounded">
                    <option value="1">Session 1 (08:00 - 09:20)</option>
                    <option value="2">Session 2 (09:20 - 10:40)</option>
                    <option value="3">Session 3 (11:00 - 12:20)</option>
                    <option value="4">Session 4 (12:20 - 13:40)</option>
                </select>
            </div>

            <div class="flex space-x-4">
                <div class="w-1/2">
                    <label class="block font-semibold">Start Time</label>
                    <input type="time" name="start_time" required class="w-full border p-2 rounded">
                </div>
                <div class="w-1/2">
                    <label class="block font-semibold">End Time</label>
                    <input type="time" name="end_time" required class="w-full border p-2 rounded">
                </div>
            </div><br>

            <div class="flex justify-between">
				<button type="reset" class="bg-red-500 text-white px-6 py-2 rounded hover:bg-gray-700">Reset</button>
				<button type="submit" class="bg-blue-500 text-white px-6 py-2 rounded hover:bg-blue-900">Save Schedule</button>
			</div>
			
			


        </form>
    </div>
</body>
</html>
