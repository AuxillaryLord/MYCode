<?php include 'db.php';  include 'navbar.php';
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Training Portal</title>
  <link href="tailwind.min.css" rel="stylesheet" />
  <style>
    
	footer {
		background-color: #002147;
		color: white;
		text-align: center;
		padding: 15px 0;
		position: fixed;
		bottom: 0;
		width: 100%;
    }

  </style>

</head>
<body class="bg-gray-100 text-gray-800">
  <div class="max-w-6xl mx-auto px-4 py-8">
    <h1 class="text-4xl font-bold text-blue-900 mb-6">📚 Select Courses</h1>

    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
      <?php
        $sql = "SELECT * FROM courses";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
          while ($row = $result->fetch_assoc()) {
            echo '
            <a href="subjects.php?course_id=' . $row['id'] . '" class="block bg-white shadow-md rounded-lg p-5 hover:shadow-lg transition">
              <h2 class="text-xl font-semibold text-blue-800 mb-2">' . $row['name'] . '</h2>
              <p class="text-sm text-gray-500">View subjects and materials</p>
            </a>';
          }
        } else {
          echo '<p>No courses available.</p>';
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
