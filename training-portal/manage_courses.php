<?php
// manage_courses.php





include 'db.php';
include 'admin/session_check.php'; // checks for admin session

// Handle course creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['course_name'])) {
    $name = trim($_POST['course_name']);
    if (!empty($name)) {
        $stmt = $conn->prepare("INSERT INTO courses (name) VALUES (?)");
        $stmt->bind_param("s", $name);
        $stmt->execute();
        $stmt->close();
    }
    header("Location: manage_courses.php");
    exit();
}

// Handle deletion
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM courses WHERE id = $id");
    header("Location: manage_courses.php");
    exit();
}

// Fetch courses
$result = $conn->query("SELECT * FROM courses ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Courses</title>
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
<body class="bg-gray-100 p-6">
<div class="mt-6 text-right">
            <a href="admin_manage.php" class="text-blue-700 underline">⬅ Back to Admin Management Panel </a>
        </div>

    <h1 class="text-3xl font-bold text-blue-800 mb-6">📘 Manage Courses</h1>

    <form method="POST" class="mb-6">
        <input type="text" name="course_name" placeholder="Enter new course name" required
               class="px-4 py-2 border rounded shadow w-80">
        <button type="submit"
                class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 ml-2">Add Course</button>
    </form>

    <div class="bg-white rounded shadow p-4">
        <h2 class="text-xl font-semibold mb-4">Course List</h2>
        <table class="table-auto w-full">
            <thead>
                <tr class="bg-gray-200 text-gray-700">
                    <th class="px-4 py-2 text-left">#</th>
                    <th class="px-4 py-2 text-left">Course Name</th>
                    <th class="px-4 py-2 text-left">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr class="border-t hover:bg-gray-50">
                    <td class="px-4 py-2"><?= $row['id'] ?></td>
                    <td class="px-4 py-2"><?= htmlspecialchars($row['name']) ?></td>
                    <td class="px-4 py-2">
                        <a href="?delete=<?= $row['id'] ?>"
                           class="text-red-600 hover:text-red-800"
                           onclick="return confirm('Are you sure you want to delete this course?');">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
		
    </div>
<!-- Footer -->
<footer>
    &copy; 2025 NATMS | NAVAL AVIATION TRAINING MANAGEMENT SYSTEM
</footer>
</body>
</html>
