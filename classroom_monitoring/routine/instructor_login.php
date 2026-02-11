<?php
session_start();
$conn = new mysqli("localhost", "root", "", "classroom_monitoring");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];

    $stmt = $conn->prepare("SELECT id, name FROM instructors WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        $_SESSION['instructor_id'] = $user['id'];
        $_SESSION['instructor_name'] = $user['name'];
        header("Location: instructor_dashboard.php");
        exit();
    } else {
        $error = "Instructor not found.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Instructor Login</title>
    <link href="tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-blue-900 text-white flex items-center justify-center h-screen">
    <form method="POST" class="bg-white text-black p-8 rounded shadow w-96">
        <h1 class="text-2xl font-bold mb-4 text-center">Instructor Login</h1>
        <input type="text" name="email" required placeholder="Enter email" class="w-full mb-4 p-2 border rounded" />
        <button type="submit" class="w-full bg-blue-700 hover:bg-blue-900 text-white py-2 rounded">Login</button>
        <?php if (isset($error)): ?>
            <p class="mt-4 text-red-600"><?= $error ?></p>
        <?php endif; ?>
    </form>
</body>
</html>
