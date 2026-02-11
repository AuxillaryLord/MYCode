<?php
include '../db.php';
$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $code = $_POST['code'];
    $status = $_POST['status'];

    try {
        $stmt = $pdo->prepare("INSERT INTO classrooms (code, status) VALUES (?, ?)");
        $stmt->execute([$code, $status]);
        $message = "✅ Classroom added successfully!";
    } catch (PDOException $e) {
        $message = "❌ Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Classroom</title>
    <link href="tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 p-8">
<div class="max-w-md mx-auto bg-white p-6 rounded shadow">
    <h2 class="text-2xl font-bold mb-4">Add Classroom</h2>

    <?php if ($message): ?>
        <p class="mb-4 text-blue-700 font-semibold"><?php echo $message; ?></p>
    <?php endif; ?>

    <form method="post">
        <label class="block mb-2">Class Name:</label>
        <input type="text" name="code" required class="w-full mb-4 px-3 py-2 border rounded" />

        <label class="block mb-2">Status:</label>
        <select name="status" required class="w-full mb-4 px-3 py-2 border rounded">
            <option value="active">Active</option>
            <option value="inactive">InActive</option>
        </select>
		
		<div class="flex justify-between  ">

			<button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Add Classroom</button>
			<button type="reset" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-red-700">Reset</button>
		</div><br>
        <a href="admin_dashboard.php" class="ml-4 text-sm text-gray-700 underline">← Back</a>
    </form>
</div>
</body>
</html>
