<?php
session_start();
if (isset($_SESSION['user'])) {
    header("Location: ../index.php");
    exit;
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = new mysqli("localhost", "root", "", "live_network");
    $username = $conn->real_escape_string($_POST['username']);
    $password = $_POST['password'];

    $query = $conn->query("SELECT * FROM users WHERE username = '$username' AND status = 'active'");

    if ($query->num_rows === 1) {
        $user = $query->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user'] = [
                'id' => $user['id'],
                'username' => $user['username'],
                'role' => $user['role']
            ];
            header("Location: admin.php");
            exit;
        } else {
            $message = "❌ Invalid credentials.";
        }
    } else {
        $message = "❌ User not found or disabled.";
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login</title>
    <link href="../assets/css/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 h-screen flex items-center justify-center">
    <div class="bg-white p-8 rounded-2xl shadow-md w-full max-w-sm">
        <h2 class="text-2xl font-bold mb-6 text-center text-blue-800">🔐 Admin Login</h2>

        <?php if ($message): ?>
            <p class="mb-4 text-red-600 text-center"><?= $message ?></p>
        <?php endif; ?>

        <form method="POST">
            <input type="text" name="username" placeholder="Username" required class="w-full mb-4 p-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500">
            <input type="password" name="password" placeholder="Password" required class="w-full mb-4 p-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500">
            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-xl hover:bg-blue-700">Login</button>
        </form>
    </div>
</body>
</html>
