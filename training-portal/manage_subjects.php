<?php
include 'db.php';
include 'admin/session_check.php'; // Admin session check



// Fetch all courses
$courses = $conn->query("SELECT * FROM courses");

// Handle Delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM subjects WHERE id = $id");
    header("Location: manage_subjects.php");
    exit();
}

// Handle Edit Load
$edit_subject = null;
if (isset($_GET['edit'])) {
    $edit_id = intval($_GET['edit']);
    $result = $conn->query("SELECT * FROM subjects WHERE id = $edit_id");
    if ($result && $result->num_rows > 0) {
        $edit_subject = $result->fetch_assoc();
    }
}

// Handle Edit Submit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_subject'])) {
    $subject_id = intval($_POST['subject_id']);
    $new_course_id = $_POST['course_id'];
    $new_name = trim($_POST['name']);

    if (!empty($new_name) && !empty($new_course_id)) {
        $stmt = $conn->prepare("UPDATE subjects SET course_id = ?, name = ? WHERE id = ?");
        $stmt->bind_param("isi", $new_course_id, $new_name, $subject_id);
        $stmt->execute();
        $success = "Subject updated successfully!";
        unset($edit_subject); // reset form
    } else {
        $error = "All fields are required for update.";
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_subject'])) {
    $course_id = $_POST['course_id'];
    $name = trim($_POST['name']);

    if (!empty($name) && !empty($course_id)) {
        $stmt = $conn->prepare("INSERT INTO subjects (course_id, name) VALUES (?, ?)");
        $stmt->bind_param("is", $course_id, $name);
        $stmt->execute();
        $success = "Subject added successfully!";
    } else {
        $error = "All fields are required.";
    }
}

// Fetch all subjects
$subjects = $conn->query("SELECT s.id, s.name, c.name AS course_name FROM subjects s LEFT JOIN courses c ON s.course_id = c.id");

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Subject Management</title>
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
<body class="bg-gray-50 p-8">
<div class="mt-6 text-right">
            <a href="admin_manage.php" class="text-blue-700 underline">⬅ Back to Admin Management Panel </a>
        </div>
	
  <div class="max-w-5xl mx-auto">
    <h1 class="text-3xl font-bold text-blue-800 mb-6">📚 Manage Subjects</h1>

    <?php if (isset($success)) echo "<div class='bg-green-100 text-green-800 p-3 mb-4 rounded'>$success</div>"; ?>
    <?php if (isset($error)) echo "<div class='bg-red-100 text-red-800 p-3 mb-4 rounded'>$error</div>"; ?>

    <!-- Add Subject Form -->
    <form method="POST" class="bg-white p-6 rounded shadow mb-10">
	  <h2 class="text-xl font-semibold mb-4">
		<?= $edit_subject ? "✏️ Edit Subject" : "➕ Add New Subject" ?>
	  </h2>
		
	  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
		<div>
		  <label class="block mb-1 font-medium">Select Course:</label>
		  <select name="course_id" class="w-full border p-2 rounded" required>
			<option value="">-- Select Course --</option>
			<?php
			$courses->data_seek(0); // rewind result pointer
			while ($row = $courses->fetch_assoc()):
			  $selected = ($edit_subject && $edit_subject['course_id'] == $row['id']) ? 'selected' : '';
			?>
			  <option value="<?= $row['id'] ?>" <?= $selected ?>><?= htmlspecialchars($row['name']) ?></option>
			<?php endwhile; ?>
		  </select>
		</div>
		<div>
		  <label class="block mb-1 font-medium">Subject Name:</label>
		  <input type="text" name="name" value="<?= $edit_subject ? htmlspecialchars($edit_subject['name']) : '' ?>" class="w-full border p-2 rounded" required>
		</div>
	  </div>

	  <?php if ($edit_subject): ?>
		<input type="hidden" name="id" value="<?= $edit_subject['id'] ?>">
		<button type="submit" name="update_subject" class="mt-4 bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600">Update Subject</button>
		<a href="manage_subjects.php" class="ml-3 text-gray-600 underline">Cancel</a>
	  <?php else: ?>
		<button type="submit" name="add_subject" class="mt-4 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Add Subject</button>
	  <?php endif; ?>
	</form>
	
<br>
    <!-- Subjects Table -->
    <div class="bg-white p-6 rounded shadow">
      <h2 class="text-xl font-semibold mb-4">📋 Existing Subjects</h2>
      <table class="w-full table-auto border-collapse">
        <thead>
          <tr class="bg-gray-200">
			  <th class="border px-4 py-2 text-left">ID</th>
			  <th class="border px-4 py-2 text-left">Course</th>
			  <th class="border px-4 py-2 text-left">Subject</th>
			  <th class="border px-4 py-2 text-left">Actions</th>
		    </tr>

        </thead>
        <tbody>
		  <?php while($sub = $subjects->fetch_assoc()): ?>
		  <tr class="border-b hover:bg-gray-50">
			<td class="px-4 py-2"><?= $sub['id'] ?></td>
			<td class="px-4 py-2"><?= htmlspecialchars($sub['course_name']) ?></td>
			<td class="px-4 py-2"><?= htmlspecialchars($sub['name']) ?></td>
			<td class="px-4 py-2">
			  <a href="?edit=<?= $sub['id'] ?>" class="text-yellow-600 hover:underline mr-3">Edit</a>
			  <a href="?delete=<?= $sub['id'] ?>" onclick="return confirm('Delete this subject?')" class="text-red-600 hover:underline">Delete</a>
			</td>
		  </tr>
		  <?php endwhile; ?>
		</tbody>

      </table>
    </div>

  </div>
   <!-- Footer -->
<footer>
    &copy; 2025 NATMS | NAVAL AVIATION TRAINING MANAGEMENT SYSTEM
</footer>
</body>
</html>
