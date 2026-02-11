<?php
session_start();

// Prevent browser from caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Redirect if not logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: /niatcloud/login.php");
    exit();
}

// Restrict to admin role only
if ($_SESSION['role'] !== 'admin') {
    echo "Access Denied: You do not have permission to view this page.";
    exit();
}

// Use session values directly
$username = $_SESSION['username'];
$role = $_SESSION['role'];

// Database connection
$conn = new mysqli("localhost", "root", "", "classroom_monitoring");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

?>


<?php if (isset($_GET['msg'])): ?>
    <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4">
        <?php echo htmlspecialchars($_GET['msg']); ?>
    </div>
<?php endif; ?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link href="tailwind.min.css" rel="stylesheet">
    <script>
        function showTab(id) {
            document.querySelectorAll('.tab-content').forEach(tab => tab.classList.add('hidden'));
            document.getElementById(id).classList.remove('hidden');
        }
    </script>
</head>
<body class="bg-gray-100 p-6">
    <h1 class="text-3xl font-bold mb-4 text-blue-800">Admin Dashboard</h1>
	 <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">Welcome, <?= htmlspecialchars($username) ?></h1>
        <a href="logout_trg.php" class="bg-red-600 hover:bg-red-800 text-white px-4 py-2 rounded">Logout</a>
    </div>
    <div class="mb-4">
        
        <button onclick="showTab('instructors')" class="px-4 py-2 bg-green-600 text-white rounded">Instructors</button>
        <button onclick="showTab('classrooms')" class="px-4 py-2 bg-purple-600 text-white rounded">Classrooms</button>
        <button onclick="showTab('schedule')" class="px-4 py-2 bg-orange-600 text-white rounded">Weekly Schedule</button>
        <button onclick="showTab('checkins')" class="px-4 py-2 bg-red-600 text-white rounded">Check-ins</button>
        
    </div>

    

    <div id="instructors" class="tab-content hidden">
        <h2 class="text-xl font-bold mb-2">Instructors Table</h2>
		<?php
			$result = $conn->query("SELECT * FROM instructors");
			if ($result->num_rows > 0) {
				
				echo "<table class='table-auto w-full border text-center'><tr><th>ID</th><th>Name</th></tr>";

				while ($row = $result->fetch_assoc()) {
					echo "<tr class='border'>
						<td>{$row['id']}</td>
						<td>{$row['name']}</td>
						
						
					  </tr>";

				}
				echo "</table>";
			} else {
				echo "No instructors found.";
			}
			?>

    </div>

    <div id="classrooms" class="tab-content hidden">
        <h2 class="text-xl font-bold mb-2">Classrooms Table</h2>
		<?php
			$result = $conn->query("SELECT * FROM classrooms");
			if ($result->num_rows > 0) {
				echo "<button onclick=\"location.href='add_classroom.php'\" class='bg-green-600 text-white px-4 py-1 rounded mb-2'>Add Classroom</button>";
				echo "<table class='table-auto w-full border text-center'><tr><th>ID</th><th>Class Name</th><th>Status</th><th>Actions</th></tr>";

				while ($row = $result->fetch_assoc()) {
					echo "<tr class='border'>
						<td>{$row['id']}</td>
						<td>{$row['code']}</td>
						<td>{$row['status']}</td>
						<td>
							<a href='edit_classroom.php?id={$row['id']}' class='bg-yellow-500 text-white px-2 py-1 rounded mr-1'>Edit</a>
							<a href='delete_classroom.php?id={$row['id']}' onclick=\"return confirm('Are you sure you want to delete this classroom?');\" class='bg-red-600 text-white px-2 py-1 rounded'>Delete</a>
						</td>
					  </tr>";

				}
				echo "</table>";
			} else {
				echo "No classrooms found.";
			}
			?>

    </div>

    <div id="schedule" class="tab-content hidden">
        <h2 class="text-xl font-bold mb-2">Weekly Schedule Table</h2>
		<?php 
			$query = "
				SELECT 
					ws.id,
					i.name AS instructor_name,
					cl.code AS classroom_code,
					c.name AS course_name,
					ws.day_of_week,
					ws.session_number,
					ws.start_time,
					ws.end_time
				FROM weekly_schedule ws
				LEFT JOIN instructors i ON ws.instructor_id = i.id
				LEFT JOIN classrooms cl ON ws.classroom_id = cl.id
				LEFT JOIN courses c ON ws.course_id = c.id
			";

			$result = $conn->query($query);

			if ($result->num_rows > 0) {
				echo "<button onclick=\"location.href='add_schedule.php'\" class='bg-green-600 text-white px-4 py-1 rounded mb-2'>Add Schedule</button>";
				echo "<table class='table-auto w-full border text-center'>
						<tr>
							<th>ID</th>
							<th>Classroom</th>
							<th>Instructor</th>
							<th>Course</th>
							<th>Day</th>
							<th>Session</th>
							<th>Start Time</th>
							<th>End Time</th>
							<th>Actions</th>
						</tr>";

				while ($row = $result->fetch_assoc()) {
					echo "<tr class='border'>
						<td>{$row['id']}</td>
						<td>{$row['classroom_code']}</td>
						<td>{$row['instructor_name']}</td>
						<td>{$row['course_name']}</td>
						<td>{$row['day_of_week']}</td>
						<td>{$row['session_number']}</td>
						<td>{$row['start_time']}</td>
						<td>{$row['end_time']}</td>
						<td>
							<a href='edit_schedule.php?id={$row['id']}' class='bg-yellow-500 text-white px-2 py-1 rounded mr-1'>Edit</a>
							<a href='delete_schedule.php?id={$row['id']}' onclick=\"return confirm('Are you sure you want to delete this schedule?');\" class='bg-red-600 text-white px-2 py-1 rounded'>Delete</a>
						</td>
					</tr>";
				}

				echo "</table>";
			} else {
				echo "No schedule data found.";
			}
			?>



    </div>

    <div id="checkins" class="tab-content hidden">
        <h2 class="text-xl font-bold mb-2">Check-ins Table</h2>
		
		<?php
			$query = "
				SELECT 
					c.id,
					i.name AS instructor_name,
					cl.code AS classroom_code,
					c.date,
					c.session_start,
					c.session_end,
					c.status,
					c.checkin_time,
					c.timestamp
				FROM checkins c
				LEFT JOIN instructors i ON c.instructor_id = i.id
				LEFT JOIN classrooms cl ON c.classroom_id = cl.id
			";

			$result = $conn->query($query);

			if ($result->num_rows > 0) {
				
				echo "<table class='table-auto w-full border text-center'><tr>
						<th>ID</th>
						<th>Instructor</th>
						<th>Classroom</th>
						<th>Date</th>
						<th>Start Time</th>
						<th>End Time</th>
						<th>Status</th>
						<th>Check In Time</th>
						<th>Time Stamp</th>
						
					  </tr>";

				while ($row = $result->fetch_assoc()) {
					echo "<tr class='border'>
						<td>{$row['id']}</td>
						<td>{$row['instructor_name']}</td>
						<td>{$row['classroom_code']}</td>
						<td>{$row['date']}</td>
						<td>{$row['session_start']}</td>
						<td>{$row['session_end']}</td>
						<td>{$row['status']}</td>
						<td>{$row['checkin_time']}</td>
						<td>{$row['timestamp']}</td>
						
					  </tr>";
				}

				echo "</table>";
			} else {
				echo "No check-ins found.";
			}
			?>


    </div>

    
</body>
</html>
