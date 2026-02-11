<?php
include 'db.php';
include 'navbar.php';

$subject_id = isset($_GET['subject_id']) ? intval($_GET['subject_id']) : 0;

// Get subject and course name
$subject_sql = "SELECT name, course_id FROM subjects WHERE id = $subject_id";
$subject_result = $conn->query($subject_sql);

if ($subject_result->num_rows == 0) {
  die("Invalid subject ID.");
}

$subject_data = $subject_result->fetch_assoc();
$subject_name = $subject_data['name'];
$course_id = $subject_data['course_id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Select Material Type - <?php echo htmlspecialchars($subject_name); ?></title>
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
  <div class="max-w-4xl mx-auto px-4 py-10">
    <h1 class="text-3xl font-bold text-blue-900 mb-8">📘 Select Material Type for: <span class="text-blue-700"><?php echo htmlspecialchars($subject_name); ?></span></h1>

    <div class="grid md:grid-cols-2 gap-6">
      <?php
        $materials = [
          "lesson_plans.php" => "📄 Lesson Plans",
          "ppts.php" => "📊 PPT Presentations",
          "question_banks.php" => "📝 Question Banks",
          "training_videos.php" => "🎞️ Training Videos",
          "cbts.php" => "💻 CBT Modules",
          "tos.php" => "📋 Table of Specifications"
        ];

        foreach ($materials as $file => $label) {
          echo "
            <a href='{$file}?course_id={$course_id}&subject_id={$subject_id}' class='block bg-white shadow-md rounded-lg p-6 hover:shadow-lg transition'>
              <h2 class='text-xl font-semibold text-blue-800'>{$label}</h2>
              <p class='text-sm text-gray-500 mt-2'>Click to view all {$label}</p>
            </a>
          ";
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
