<?php
session_start();
$conn = new mysqli("localhost", "root", "", "classroom_monitoring");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $pno = $_POST["pno"];
    $password = $_POST["password"];

    $stmt = $conn->prepare("SELECT * FROM users WHERE pno = ? AND status = 'active'");
    $stmt->bind_param("s", $pno);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user["password"])) {
            $_SESSION["user_id"] = $user["id"];
            $_SESSION["pno"] = $user["pno"];
            $_SESSION["name"] = $user["name"];
            $_SESSION["role"] = $user["role"];

            // Redirect based on role
            if ($user["role"] === "training_faculty") {
				header("Location: index.php");
			} elseif ($user["role"] === "director") {
				header("Location: director_dashboard.php");
			} elseif ($user["role"] === "admin") {
				header("Location: admin_dashboard.php");
			} else {
				// Optional: Handle unexpected roles
				echo "Unauthorized role detected.";
			}
			exit;

        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "User not found or inactive.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Training Faculty Login</title>
    <link href="tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-blue-900 flex items-center justify-center min-h-screen text-white">
    <div class="bg-white text-black p-8 rounded-xl shadow-lg w-full max-w-md">
        <h1 class="text-2xl font-bold mb-6 text-center text-[#001F3F]">Login - Indian Navy</h1>

        <?php if (isset($error)): ?>
            <div class="bg-red-200 text-red-800 p-2 rounded mb-4"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST" class="space-y-4">
            <div>
                <label class="block font-semibold">User Name</label>
                <input type="text" name="pno" required class="w-full border p-2 rounded">
            </div>
            <div>
                <label class="block font-semibold">Password</label>
                <input type="password" name="password" required class="w-full border p-2 rounded">
            </div><br>
			<div class="flex justify-between gap-6">
				<button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-900 w-full">Login</button>
				<button type="reset" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-gray-500 w-full">Reset</button>
			</div>
			
			<div class="text-center mt-6">
				<a href="instructor_login.php" class="inline-block bg-blue-500 text-white px-6 py-2 rounded hover:bg-blue-900">
					Instructor Login
				</a>
			</div>
        </form>
    </div>
</body>
</html>
