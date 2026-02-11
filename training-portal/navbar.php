<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}
// Default URLs (override in individual pages if needed)
$courseLink = isset($course_id) ? "subjects.php?course_id=$course_id" : "main.php";
$subjectLink = isset($subject_id) ? "materials.php?subject_id=$subject_id" : "admin_manage.php";
$userLink = isset($subject_id) ? "materials.php?subject_id=$subject_id" : "manage_users.php";
$userRole = $_SESSION['role'];
?>

<nav class="bg-blue-900 text-white shadow-md">
  <div class="max-w-7xl mx-auto px-4 py-3 flex justify-between items-center">
    <div class="flex space-x-4">
      <a href="index.php" class="font-semibold hover:text-gray-200">🏠 Home</a>
      <a href="<?php echo $courseLink; ?>" class="hover:text-gray-200">📚 Main Menu</a>

      <!-- Show Manage Training for admin and user -->
      <?php if ($userRole === 'admin' || $userRole === 'user'): ?>
        <a href="<?php echo $subjectLink; ?>" class="hover:text-gray-200">📦 Manage Training</a>
      <?php endif; ?>

      <!-- Show Manage User only for admin -->
      <?php if ($userRole === 'admin'): ?>
        <a href="<?php echo $userLink; ?>" class="hover:text-gray-200">👤 Manage User</a>
      <?php endif; ?>
    </div>

    <div>
      <a href="../niatcloud/php/logout.php" class="hover:text-gray-200">🔁 Logout</a>
    </div>
  </div>
</nav>

