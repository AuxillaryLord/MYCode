<?php
session_start();
if (!isset($_SESSION['logged_in']) || !in_array($_SESSION['role'], ['admin', 'user'])) {
    header('Location: ../niatcloud/login.php');
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard - Training Portal</title>
  <link href="tailwind.min.css" rel="stylesheet">
  <style>
		footer {
            background-color: #002147;
            color: white;
            text-align: center;
            padding: 10px 10px;
            position: fixed;
            bottom: 0;
            width: 100%;
        }
	</style>
</head>
<body class="bg-gray-100 min-h-screen p-10">
	<div class="mt-6 text-right">
	  <a href="index.php" class="text-blue-700 underline block">⬅ Back to Home</a>
	</div>



  <div class="max-w-6xl mx-auto">
    <h1 class="text-4xl font-bold text-blue-800 mb-10">🛠 Admin Management Panel</h1>

    <div class="grid grid-cols-2 md:grid-cols-3 gap-8">

      <a href="manage_courses.php" class="bg-white p-6 rounded shadow hover:shadow-lg text-center hover:bg-blue-50">
        <div class="text-4xl mb-2">📘</div>
        <div class="font-semibold text-xl">Course Management</div>
      </a>

      <a href="manage_subjects.php" class="bg-white p-6 rounded shadow hover:shadow-lg text-center hover:bg-blue-50">
        <div class="text-4xl mb-2">📚</div>
        <div class="font-semibold text-xl">Subject Management</div>
      </a>

      <a href="manage_lesson_plans.php" class="bg-white p-6 rounded shadow hover:shadow-lg text-center hover:bg-blue-50">
        <div class="text-4xl mb-2">📄</div>
        <div class="font-semibold text-xl">Lesson Plans</div>
      </a>

      <a href="manage_training_videos.php" class="bg-white p-6 rounded shadow hover:shadow-lg text-center hover:bg-blue-50">
        <div class="text-4xl mb-2">🎥</div>
        <div class="font-semibold text-xl">Training Videos</div>
      </a>

      <a href="manage_tos.php" class="bg-white p-6 rounded shadow hover:shadow-lg text-center hover:bg-blue-50">
        <div class="text-4xl mb-2">📊</div>
        <div class="font-semibold text-xl">Table of Specs</div>
      </a>

      <a href="manage_question_banks.php" class="bg-white p-6 rounded shadow hover:shadow-lg text-center hover:bg-blue-50">
        <div class="text-4xl mb-2">❓</div>
        <div class="font-semibold text-xl">Question Bank</div>
      </a>

      <a href="manage_cbts.php" class="bg-white p-6 rounded shadow hover:shadow-lg text-center hover:bg-blue-50">
        <div class="text-4xl mb-2">🧠</div>
        <div class="font-semibold text-xl">CBTs</div>
      </a>

      <a href="manage_ppts.php" class="bg-white p-6 rounded shadow hover:shadow-lg text-center hover:bg-blue-50">
        <div class="text-4xl mb-2">📂</div>
        <div class="font-semibold text-xl">PPT Uploads</div>
      </a>

    </div>
	
  </div>
<footer>
    &copy; 2025 NATMS | NAVAL AVIATION TRAINING MANAGEMENT SYSTEM
</footer>


</body>
</html>
