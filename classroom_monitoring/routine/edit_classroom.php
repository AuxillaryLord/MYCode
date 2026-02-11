<?php
include '../db.php';
$message = "";

if (!isset($_GET['id'])) {
    die("⚠️ Classroom ID not specified.");
}

$id = $_GET['id'];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $code = $_POST['code'];
    $status = $_POST['status'];

    try {
        $stmt = $pdo->prepare("UPDATE classrooms SET code = ?, status = ? WHERE id = ?");
        $stmt->execute([$code, $status, $id]);
        $message = "✅ Classroom updated successfully!";
    } catch (PDOException $e) {
        $message = "❌ Error updating classroom: " . $e->getMessage();
    }
}

// Fetch classroom data
$stmt = $pdo->prepare("SELECT * FROM classrooms WHERE id = ?");
$stmt->execute([$id]);
$classroom = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$classroom) {
    die("❌ Classroom not found.");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Classroom</title>
    <link href="tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 p-8">
<div class="max-w-md mx-auto bg-white p-6 rounded shadow">
    <h2 class="text-2xl font-bold mb-4">Edit Classroom</h2>

    <?php if ($message): ?>
        <p class="mb-4 text-blue-700 font-semibold"><?php echo $message; ?></p>
    <?php endif; ?>

    <form method="post">
        <label class="block mb-2">Code:</label>
        <input type="text" name="code" value="<?php echo htmlspecialchars($classroom['code']); ?>" required class="w-full mb-4 px-3 py-2 border rounded" />

        <label class="block mb-2">Status:</label>
        <select name="status" required class="w-full mb-4 px-3 py-2 border rounded">
            <option value="active" <?php if ($classroom['status'] === 'active') echo 'selected'; ?>>Active</option>
            <option value="inactive" <?php if ($classroom['status'] === 'inactive') echo 'selected'; ?>>Inactive</option>
        </select>

        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Update</button>
		<a href="admin_dashboard.php" class="ml-4 text-sm text-gray-700 underline">← Back</a>
    </form>
</div>
</body>
</html>
