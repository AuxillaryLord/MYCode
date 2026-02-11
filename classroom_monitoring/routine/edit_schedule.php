<?php
include '../db.php';
$message = "";

if (!isset($_GET['id'])) {
    die("⚠️ ID not provided.");
}

$id = $_GET['id'];

// Fetch instructors and classrooms
$instructors = $pdo->query("SELECT id, name FROM instructors")->fetchAll();
$classrooms = $pdo->query("SELECT id, code FROM classrooms")->fetchAll();
$courses = $pdo->query("SELECT id, name FROM courses")->fetchAll();




// Fetch existing data
$stmt = $pdo->prepare("SELECT * FROM weekly_schedule WHERE id = ?");
$stmt->execute([$id]);
$schedule = $stmt->fetch();

if (!$schedule) die("❌ Schedule not found.");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $classroom_id = $_POST['classroom_id'];
    $instructor_id = $_POST['instructor_id'];
    $day = $_POST['day_of_week'];
    $session = $_POST['session_number'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
	$course_id = $_POST['course_id'];




    try {
        $stmt = $pdo->prepare("UPDATE weekly_schedule SET classroom_id=?, instructor_id=?, course_id=?, day_of_week=?, session_number=?, start_time=?, end_time=? WHERE id=?");
		$stmt->execute([$classroom_id, $instructor_id, $course_id, $day, $session, $start_time, $end_time, $id]);
        $message = "✅ Schedule updated successfully!";
    } catch (PDOException $e) {
        $message = "❌ Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head><title>Edit Schedule</title>
<link href="tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 p-8">
<div class="max-w-lg mx-auto bg-white p-6 rounded shadow">
    <h2 class="text-xl font-bold mb-4">Edit Schedule</h2>
    <?php if ($message): ?><p class="text-green-600 mb-4"><?php echo $message; ?></p><?php endif; ?>
    <form method="post">
        <label>Classroom:</label>
        <select name="classroom_id" class="w-full mb-3 px-3 py-2 border rounded">
            <?php foreach ($classrooms as $c): ?>
                <option value="<?= $c['id'] ?>" <?= ($schedule['classroom_id'] == $c['id']) ? 'selected' : '' ?>>
                    <?= $c['code'] ?>
                </option>
            <?php endforeach; ?>
        </select>
		
		<label>Course:</label>
		<select name="course_id" class="w-full mb-3 px-3 py-2 border rounded">
			<?php foreach ($courses as $course): ?>
				<option value="<?= $course['id'] ?>" <?= ($schedule['course_id'] == $course['id']) ? 'selected' : '' ?>>
					<?= $course['name'] ?>
				</option>
			<?php endforeach; ?>
		</select>


        <label>Instructor:</label>
        <select name="instructor_id" class="w-full mb-3 px-3 py-2 border rounded">
            <?php foreach ($instructors as $i): ?>
                <option value="<?= $i['id'] ?>" <?= ($schedule['instructor_id'] == $i['id']) ? 'selected' : '' ?>>
                    <?= $i['name'] ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label>Day:</label>
        <input type="text" name="day_of_week" value="<?= $schedule['day_of_week'] ?>" class="w-full mb-3 px-3 py-2 border rounded">

        <label>Session Number:</label>
        <input type="number" name="session_number" value="<?= $schedule['session_number'] ?>" class="w-full mb-3 px-3 py-2 border rounded">

        <label>Start Time:</label>
        <input type="time" name="start_time" value="<?= $schedule['start_time'] ?>" class="w-full mb-3 px-3 py-2 border rounded">

        <label>End Time:</label>
        <input type="time" name="end_time" value="<?= $schedule['end_time'] ?>" class="w-full mb-4 px-3 py-2 border rounded">

         <div class="flex justify-between  ">

			<button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Edit Schedule</button>
			<button type="reset" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-red-700">Reset</button>
		</div><br>
        <a href="admin_dashboard.php" class="ml-4 text-sm text-gray-700 underline">← Back</a>
    </form>
</div>
</body>
</html>
