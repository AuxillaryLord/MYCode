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
// Block common navigation keys
document.addEventListener("keydown", function (e) {
    if ((e.altKey && (e.key === "ArrowLeft" || e.key === "ArrowRight")) ||
        (e.key === "Backspace" && e.target.tagName !== "INPUT" && e.target.tagName !== "TEXTAREA")) {
        e.preventDefault();
    }
});
</script>


<body class="bg-blue-50 flex items-center justify-center min-h-screen">
<!-- Top Bar with Logout -->
<div class="absolute top-4 right-6">
  <a href="../niatcloud/php/logout.php" class="text-blue-900 font-semibold hover:underline">
    🔁 Logout
  </a>
</div>

  <div class="text-center">
    <h1 class="text-4xl font-bold text-blue-900 mb-12">Welcome to <span class="text-blue-700">NIAT Cloud</span></h1>
    
    <div class="flex justify-center gap-20">
      <!-- Training Button -->
      <a href="index.php" class="zoom-circle bg-blue-600 text-white w-40 h-40 rounded-full flex items-center justify-center text-xl font-semibold shadow-lg hover:bg-blue-700">
        📘 Training
      </a>

      <?php if ($role === 'admin'): ?>
      <!-- Repository Button only for admin and user -->
      <a href="../niatcloud/index.php" class="zoom-circle bg-green-700 text-white w-40 h-40 rounded-full flex items-center justify-center text-xl font-semibold shadow-lg hover:bg-green-700">
        📂 Repository
      </a>
      <?php endif; ?>
    </div>
  </div>
<!-- Footer -->
<footer>
    &copy; 2025 NATMS | NAVAL AVIATION TRAINING MANAGEMENT SYSTEM
</footer>
</body>
</html>
