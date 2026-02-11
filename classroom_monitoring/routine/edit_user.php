<?php
include '../db.php';
$message = "";

// Step 1: Ensure ID is passed
if (!isset($_GET['id'])) {
    die("⚠️ ID not specified.");
}

$id = $_GET['id'];

// Step 2: Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST['name'];
    $role = $_POST['role'];
    $status = $_POST['status'];
    $password = $_POST['password'];

    try {
        if (!empty($password)) {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET name = ?, role = ?, status = ?, password = ? WHERE id = ?");
            $stmt->execute([$name, $role, $status, $hashedPassword, $id]);
        } else {
            $stmt = $pdo->prepare("UPDATE users SET name = ?, role = ?, status = ? WHERE id = ?");
            $stmt->execute([$name, $role, $status, $id]);
        }
        $message = "✅ User updated successfully!";
    } catch (PDOException $e) {
        $message = "❌ Error updating user: " . $e->getMessage();
    }
}

// Step 3: Fetch user details
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("❌ User not found.");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit User</title>
    <link href="tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 p-8">
<div class="max-w-md mx-auto bg-white p-6 rounded shadow">
    <h2 class="text-2xl font-bold mb-4">Edit User</h2>

    <?php if ($message): ?>
        <p class="mb-4 text-blue-700 font-semibold"><?php echo $message; ?></p>
    <?php endif; ?>

    <form method="post" action="">
        <label class="block mb-2">PNO (readonly):</label>
        <input type="text" value="<?php echo htmlspecialchars($user['pno']); ?>" readonly class="w-full mb-4 px-3 py-2 border rounded bg-gray-200" />

        <label class="block mb-2">Name:</label>
        <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required class="w-full mb-4 px-3 py-2 border rounded" />

        <label class="block mb-2">Role:</label>
        <select name="role" required class="w-full mb-4 px-3 py-2 border rounded">
            <option value="admin" <?php if ($user['role'] == 'admin') echo 'selected'; ?>>Admin</option>
            <option value="director" <?php if ($user['role'] == 'director') echo 'selected'; ?>>Director</option>
            <option value="training_faculty" <?php if ($user['role'] == 'training_faculty') echo 'selected'; ?>>TMC</option>
        </select>

        <label class="block mb-2">Status:</label>
        <select name="status" required class="w-full mb-4 px-3 py-2 border rounded">
            <option value="active" <?php if ($user['status'] == 'active') echo 'selected'; ?>>Active</option>
            <option value="disabled" <?php if ($user['status'] == 'disabled') echo 'selected'; ?>>Disabled</option>
        </select>

        <label class="block mb-2">New Password (leave blank to keep existing):</label>
        <input type="password" name="password" class="w-full mb-4 px-3 py-2 border rounded" />

        <div class="flex justify-between">
            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Update User</button>
            <a href="admin_dashboard.php" class="ml-4 text-sm text-gray-700 underline">← Back</a>
        </div>
    </form>
</div>
</body>
</html>
