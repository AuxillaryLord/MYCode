<?php
// Include database connection
include '../db.php'; // This sets up $pdo (not $conn)

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $pno = $_POST['pno'];
    $name = $_POST['name'];
    $role = $_POST['role'];
    $status = $_POST['status'];
    $password = $_POST['password'];
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    try {
        $stmt = $pdo->prepare("INSERT INTO users (pno, name, role, status, password) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$pno, $name, $role, $status, $hashedPassword]);
        $message = "✅ User added successfully!";
    } catch (PDOException $e) {
        $message = "❌ Error adding user: " . $e->getMessage();
    }
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>Add User</title>
    <link href="tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-200 p-8">
    <div class="max-w-md mx-auto bg-white p-6 rounded shadow">
        <h2 class="text-2xl font-bold mb-4">Add New User</h2>

        <?php if ($message): ?>
            <p class="mb-4 text-blue-700 font-semibold"><?php echo $message; ?></p>
        <?php endif; ?>

        <form method="post" action="">
            <label class="block mb-2">PNO:</label>
            <input type="text" name="pno" required class="w-full mb-4 px-3 py-2 border rounded" />

            <label class="block mb-2">Name:</label>
            <input type="text" name="name" required class="w-full mb-4 px-3 py-2 border rounded" />

            <label class="block mb-2">Role:</label>
            <select name="role" required class="w-full mb-4 px-3 py-2 border rounded">
                <option value="admin">Admin</option>
                <option value="director">Director</option>
                <option value="training_faculty">TMC</option>
            </select>

            <label class="block mb-2">Status:</label>
            <select name="status" required class="w-full mb-4 px-3 py-2 border rounded">
                <option value="active">Active</option>
                <option value="disabled">Disabled</option>
            </select>

            <label class="block mb-2">Password:</label>
			<input type="password" name="password" required class="w-full mb-4 px-3 py-2 border rounded" /><br>
			
			<div class="flex justify-between  ">
				<button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Add User</button>
				<button type="reset" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-red-700">Reset</button>
			</div>
			<a href="admin_dashboard.php" class="ml-4 text-sm text-gray-700 underline">← Back</a>

        </form>
    </div>
</body>
</html>
