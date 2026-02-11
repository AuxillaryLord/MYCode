<?php
include '../db.php';
$message = "";

if (!isset($_GET['id'])) {
    die("⚠️ Instructor ID not specified.");
}

$id = $_GET['id'];

// Handle update form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $designation = $_POST['designation'];
    $contact = $_POST['contact'];

    try {
        $stmt = $pdo->prepare("UPDATE instructors SET name = ?, email = ?, designation = ?, contact = ? WHERE id = ?");
        $stmt->execute([$name, $email, $designation, $contact, $id]);
        $message = "✅ Instructor updated successfully!";
    } catch (PDOException $e) {
        $message = "❌ Error updating instructor: " . $e->getMessage();
    }
}

// Fetch instructor details for form
$stmt = $pdo->prepare("SELECT * FROM instructors WHERE id = ?");
$stmt->execute([$id]);
$instructor = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$instructor) {
    die("❌ Instructor not found.");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Instructor</title>
    <link href="tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 p-8">
<div class="max-w-md mx-auto bg-white p-6 rounded shadow">
    <h2 class="text-2xl font-bold mb-4">Edit Instructor</h2>

    <?php if ($message): ?>
        <p class="mb-4 text-blue-700 font-semibold"><?php echo $message; ?></p>
    <?php endif; ?>

    <form method="post" action="">
        <label class="block mb-2">Name:</label>
        <input type="text" name="name" value="<?php echo htmlspecialchars($instructor['name']); ?>" required class="w-full mb-4 px-3 py-2 border rounded" />

        <label class="block mb-2">Personal No.:</label>
        <input type="text" name="email" value="<?php echo htmlspecialchars($instructor['email']); ?>" required class="w-full mb-4 px-3 py-2 border rounded" />

        <label class="block mb-2">Designation:</label>
        <input type="text" name="designation" value="<?php echo htmlspecialchars($instructor['designation']); ?>" required class="w-full mb-4 px-3 py-2 border rounded" />

        <label class="block mb-2">Contact:</label>
        <input type="text" name="contact" value="<?php echo htmlspecialchars($instructor['contact']); ?>" required class="w-full mb-4 px-3 py-2 border rounded" />

        <div class="flex justify-between">
            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Update</button>
			<button type="reset" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-red-700">Reset</button>
            
        </div>
		<a href="admin_dashboard.php" class="ml-4 text-sm text-gray-700 underline">← Back</a>
    </form>
</div>
</body>
</html>
