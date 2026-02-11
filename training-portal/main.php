<?php
session_start();

// Prevent browser from caching this page
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Redirect to login if not logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: ../niatcloud/login.php");
    exit();
}

$username = $_SESSION['username'];
$role = $_SESSION['role'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>NIATCloud - Main Menu</title>
  <link href="tailwind.min.css" rel="stylesheet">
  <style>
    .zoom-circle {
      transition: transform 0.3s ease;
    }
    .zoom-circle:hover {
      transform: scale(1.1);
    }
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
<script>
document.addEventListener("keydown", function (e) {
    if ((e.altKey && (e.key === "ArrowLeft" || e.key === "ArrowRight")) ||
        (e.key === "Backspace" && e.target.tagName !== "INPUT" && e.target.tagName !== "TEXTAREA")) {
        e.preventDefault();
    }
});
</script>

<body class="bg-blue-50 flex items-center justify-center min-h-screen">
<!-- Top Bar -->
<div class="absolute top-4 right-6">
  <span class="text-blue-800 font-medium mr-4">👤 <?= htmlspecialchars($username); ?> (<?= $role ?>)</span>
  <a href="../niatcloud/php/logout.php" class="text-blue-900 font-semibold hover:underline">
    🔁 Logout
  </a>
</div>

<div class="text-center">
  <h1 class="text-4xl font-bold text-blue-900 mb-12">
    Welcome to <span class="text-blue-700">NIAT Training Management System</span>
  </h1>

  <div class="flex justify-center items-center flex-wrap gap-10">

    <!-- TRAINING PORTAL -->
	<?php if ($role === 'admin' || $role === 'user' || $role === 'trainee'): ?>
    <a href="/training-portal/index.php"
      class="zoom-circle bg-blue-600 text-white w-40 h-40 rounded-full flex items-center justify-center text-center text-xl font-semibold shadow-lg hover:bg-blue-700">
      📘 Training<br>Portal
    </a>
	<?php endif; ?>
    <!-- BOOKING SYSTEM -->
    <?php if ($role === 'admin'): ?>
    <a href="/booking/admin/admin_panel.php"
      class="zoom-circle bg-yellow-600 text-white w-40 h-40 rounded-full flex items-center justify-center text-center text-xl font-semibold shadow-lg hover:bg-yellow-700">
      🏛️ Facility<br>Booking<br>System
    </a>
    <?php endif; ?>

    <!-- CLASSROOM MONITORING -->
    <?php
      $monitoring_links = [
        'admin'      => '/classroom_monitoring/routine/admin_dashboard.php',
        'director'   => '/classroom_monitoring/routine/director_dashboard.php',
        'instructor' => '/classroom_monitoring/routine/instructor_dashboard.php',
        'training'   => '/classroom_monitoring/routine/index.php'
      ];

      if (array_key_exists($role, $monitoring_links)) :
    ?>
    <a href="<?= $monitoring_links[$role]; ?>"
      class="zoom-circle bg-indigo-700 text-white w-40 h-40 rounded-full flex items-center justify-center text-center text-xl font-semibold shadow-lg hover:bg-indigo-800">
      🧑‍🏫 Classroom<br>Monitoring<br>System
    </a>
    <?php endif; ?>

    <!-- LIVE STATUS (IT Monitoring) -->
    <?php if ($role === 'admin'): ?>
    <a href="/live_status/index.php"
      class="zoom-circle bg-red-700 text-white w-40 h-40 rounded-full flex items-center justify-center text-center text-xl font-semibold shadow-lg hover:bg-red-800">
      🖥️ Live<br>Network<br>Status
    </a>
    <?php endif; ?>

    <!-- REPOSITORY (NIATCloud) -->
    <?php if ($role === 'admin'): ?>
    <a href="/niatcloud/index.php"
      class="zoom-circle bg-green-700 text-white w-40 h-40 rounded-full flex items-center justify-center text-center text-xl font-semibold shadow-lg hover:bg-green-800">
      📂 Data<br>Repository
    </a>
    <?php endif; ?>

  </div>
</div>


<footer>
    &copy; 2025 NATMS | NAVAL AVIATION TRAINING MANAGEMENT SYSTEM
</footer>
</body>
</html>
