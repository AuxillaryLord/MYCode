<?php
include 'db.php';
include 'admin/session_check.php'; // Admin session check

// Handle deletion
if (isset($_GET['delete'])) {
    $idToDelete = intval($_GET['delete']);

    // Get file path before deleting
    $fileQuery = $conn->prepare("SELECT file_path FROM training_videos WHERE id = ?");
    $fileQuery->bind_param("i", $idToDelete);
    $fileQuery->execute();
    $fileResult = $fileQuery->get_result();

    if ($fileResult->num_rows > 0) {
        $fileRow = $fileResult->fetch_assoc();
        $fileToDelete = 'uploads/training_videos/' . $fileRow['file_path'];

        // Delete from DB
        $deleteQuery = $conn->prepare("DELETE FROM training_videos WHERE id = ?");
        $deleteQuery->bind_param("i", $idToDelete);
        if ($deleteQuery->execute()) {
            if (file_exists($fileToDelete)) {
                unlink($fileToDelete);
            }
            echo "<script>alert('Training Video deleted successfully.'); window.location.href='manage_training_videos.php';</script>";
        } else {
            echo "<script>alert('Error deleting Training Videos.');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Training Videos</title>
  <link href="tailwind.min.css" rel="stylesheet">
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
<body class="bg-gray-100 p-8">
<div class="mt-6 text-right">
      <a href="admin_manage.php" class="text-blue-700 underline">⬅ Back to Admin Management Panel</a>
    </div>
  <div class="max-w-4xl mx-auto bg-white p-6 rounded shadow">
    <h1 class="text-2xl font-bold text-blue-900 mb-4">Training Videos Management</h1>

    <!-- Upload Form -->
    <form action="admin/upload_training_video.php" method="POST" enctype="multipart/form-data" class="mb-6">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block mb-1 font-semibold">Select Subject</label>
          <select name="subject_id" class="w-full p-2 border rounded" required>
            <option value="">-- Select Subject --</option>
            <?php
            $subjects = $conn->query("SELECT s.id, s.name, c.name as course_name FROM subjects s JOIN courses c ON s.course_id = c.id");
            while ($row = $subjects->fetch_assoc()) {
              echo "<option value='{$row['id']}'>{$row['course_name']} - {$row['name']}</option>";
            }
            ?>
          </select>
        </div>
        <div>
          <label class="block mb-1 font-semibold">Training Videos Title</label>
          <input type="text" name="title" class="w-full p-2 border rounded" required>
        </div>
      </div>
      <div class="mt-4">
        <label class="block mb-1 font-semibold">Upload Training Videos</label>
        <input type="file" name="file" accept=".mp4, .mov, .avi, .wmv, .mkv, .flv" required>
      </div>
      <button type="submit" class="mt-4 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Upload Training Videos</button>
    </form>

    <!-- Display Existing PPTs -->
    <h2 class="text-xl font-semibold mb-2">Existing Training Videos</h2>
    <table class="w-full table-auto border-collapse border border-gray-300">
      <thead>
        <tr class="bg-gray-200">
          <th class="border px-4 py-2">ID</th>
          <th class="border px-4 py-2">Title</th>
          <th class="border px-4 py-2">Subject</th>
          <th class="border px-4 py-2">File</th>
          <th class="border px-4 py-2">Action</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $tvs = $conn->query("SELECT p.*, s.name as subject FROM training_videos p JOIN subjects s ON p.subject_id = s.id");
        while ($tv = $tvs->fetch_assoc()) {
          echo "<tr>
            <td class='border px-4 py-2'>{$tv['id']}</td>
            <td class='border px-4 py-2'>{$tv['title']}</td>
            <td class='border px-4 py-2'>{$tv['subject']}</td>
            <td class='border px-4 py-2'><a href='uploads/training_videos/{$tv['file_path']}' class='text-blue-600' target='_blank'>View</a></td>
            <td class='border px-4 py-2'><a href='?delete={$tv['id']}' onclick='return confirm(\"Are you sure?\")' class='text-red-500 hover:underline'>Delete</a></td>
          </tr>";
        }
        ?>
      </tbody>
    </table>

    
  </div>
 <!-- Footer -->
<footer>
    &copy; 2025 NATMS | NAVAL AVIATION TRAINING MANAGEMENT SYSTEM
</footer>
</body>
</html>
