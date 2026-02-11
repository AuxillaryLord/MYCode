<?php
include 'db.php';
include 'navbar.php';

$course_id = isset($_GET['course_id']) ? intval($_GET['course_id']) : 0;

// Fetch course name
$course_sql = "SELECT name FROM courses WHERE id = $course_id";
$course_result = $conn->query($course_sql);
$course_name = $course_result->num_rows > 0 ? $course_result->fetch_assoc()['name'] : "Unknown Course";

// Fetch subjects
$subjects_sql = "SELECT * FROM subjects WHERE course_id = $course_id";
$subjects_result = $conn->query($subjects_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Subjects - <?php echo htmlspecialchars($course_name); ?></title>
  <link href="tailwind.min.css" rel="stylesheet" />
  <style>
		footer {
            background-color: #002147;
            color: white;
            text-align: center;
            padding: 10px 0;
            position: fixed;
            bottom: 0;
            width: 100%;
        }
	</style>
</head>
<body class="bg-gray-100 text-gray-800">
  <div class="max-w-6xl mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-blue-900 mb-6">📘 Subjects for: <?php echo htmlspecialchars($course_name); ?></h1>

    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
      <?php
        if ($subjects_result->num_rows > 0) {
          while ($subject = $subjects_result->fetch_assoc()) {
            echo '
            <a href="materials.php?subject_id=' . $subject['id'] . '" class="block bg-white shadow rounded-lg p-5 hover:shadow-lg transition">
              <h2 class="text-xl font-semibold text-blue-700">' . htmlspecialchars($subject['name']) . '</h2>
              <p class="text-sm text-gray-500">View training materials</p>
            </a>';
          }
        } else {
          echo '<p class="text-red-500">No subjects found for this course.</p>';
        }
      ?>
    </div>
  </div>
<!-- Footer -->
<footer>
    &copy; 2025 NATMS | NAVAL AVIATION TRAINING MANAGEMENT SYSTEM
</footer>
</body>
</html>
