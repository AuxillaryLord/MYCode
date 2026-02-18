<?php
include 'db.php';
include 'navbar.php';

$course_id = isset($_GET['course_id']) ? intval($_GET['course_id']) : 0;
$subject_id = isset($_GET['subject_id']) ? intval($_GET['subject_id']) : 0;

// Fetch subject name
$subject_sql = "SELECT name FROM subjects WHERE id = $subject_id";
$subject_result = $conn->query($subject_sql);
$subject_name = $subject_result->num_rows > 0 ? $subject_result->fetch_assoc()['name'] : "Unknown Subject";

// Fetch lesson plans
$lp_sql = "SELECT * FROM question_banks WHERE subject_id = $subject_id";
$lp_result = $conn->query($lp_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>QBs - <?php echo htmlspecialchars($subject_name); ?></title>
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
  <div class="max-w-5xl mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-blue-900 mb-6">📄 Question Banks - <?php echo htmlspecialchars($subject_name); ?></h1>

    <?php if ($lp_result->num_rows > 0): ?>
      <ul class="space-y-4">
        <?php while ($row = $lp_result->fetch_assoc()): ?>
          <li class="bg-white p-4 rounded shadow">
            <h2 class="text-xl font-semibold text-blue-800"><?php echo htmlspecialchars($row['title']); ?></h2>
            <!-- MODIFIED: Added 'uploads/question_banks/' prefix to file path. Reason: Database stores only filename; app needs full path to access files -->
            <a href="uploads/question_banks/<?php echo htmlspecialchars($row['file_path']); ?>" class="text-blue-600 hover:underline" target="_blank">View Question Bank</a>
          </li>
        <?php endwhile; ?>
      </ul>
    <?php else: ?>
      <p class="text-gray-600">No Question Bank available for this subject.</p>
    <?php endif; ?>
  </div>
 <!-- Footer -->
<footer>
    &copy; 2025 NATMS | NAVAL AVIATION TRAINING MANAGEMENT SYSTEM
</footer>
</body>
</html>
