<?php
include '../db.php';
$message = "";

// Fetch instructors, classrooms, and courses for dropdowns
$instructors = $pdo->query("SELECT id, name FROM instructors")->fetchAll();
$classrooms = $pdo->query("SELECT id, code FROM classrooms")->fetchAll();
$courses = $pdo->query("SELECT id, name FROM courses")->fetchAll();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $classroom_id = $_POST['classroom_id'];
    $instructor_id = $_POST['instructor_id'];
    $course_id = $_POST['course_id'];
    $day = $_POST['day_of_week'];
    $session = $_POST['session_number'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];

    try {
        $stmt = $pdo->prepare("INSERT INTO weekly_schedule (classroom_id, instructor_id, course_id, day_of_week, session_number, start_time, end_time) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$classroom_id, $instructor_id, $course_id, $day, $session, $start_time, $end_time]);
        $message = "✅ Schedule added successfully!";
    } catch (PDOException $e) {
        $message = "❌ Error: " . $e->getMessage();
    }
}

?>

<!DOCTYPE html>
<html>
<head><title>Add Schedule</title>
<link href="tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 p-8">
<div class="max-w-lg mx-auto bg-white p-6 rounded shadow">
    <h2 class="text-xl font-bold mb-4">Add Schedule</h2>
    <?php if ($message): ?><p class="text-blue-600 mb-4"><?php echo $message; ?></p><?php endif; ?>
    <form method="post">
        <label>Classroom:</label>
        <select name="classroom_id" class="w-full mb-3 px-3 py-2 border rounded" required>
            <option value="">-- Select --</option>
            <?php foreach ($classrooms as $c): ?>
                <option value="<?= $c['id'] ?>"><?= $c['code'] ?></option>
            <?php endforeach; ?>
        </select>
		
		<label>Course:</label>
		<select name="course_id" class="w-full mb-3 px-3 py-2 border rounded" required>
			<option value="">-- Select --</option>
			<?php foreach ($courses as $course): ?>
				<option value="<?= $course['id'] ?>"><?= $course['name'] ?></option>
			<?php endforeach; ?>
		</select>


        <label>Instructor:</label>
        <select name="instructor_id" class="w-full mb-3 px-3 py-2 border rounded" required>
            <option value="">-- Select --</option>
            <?php foreach ($instructors as $i): ?>
                <option value="<?= $i['id'] ?>"><?= $i['name'] ?></option>
            <?php endforeach; ?>
        </select>

        <label>Day:</label>
        <input type="text" name="day_of_week" class="w-full mb-3 px-3 py-2 border rounded" required>

        <label>Session Number:</label>
        <input type="number" name="session_number" class="w-full mb-3 px-3 py-2 border rounded" required>

        <label>Start Time:</label>
        <input type="time" name="start_time" class="w-full mb-3 px-3 py-2 border rounded" required>

        <label>End Time:</label>
        <input type="time" name="end_time" class="w-full mb-4 px-3 py-2 border rounded" required>

        <div class="flex justify-between  ">

			<button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Add Schedule</button>
			<button type="reset" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-red-700">Reset</button>
		</div><br>
        <a href="admin_dashboard.php" class="ml-4 text-sm text-gray-700 underline">← Back</a>
    </form>
</div>
</body>
</html>
