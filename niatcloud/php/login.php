<?php
session_start();
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows === 1) {
        $user = $res->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            // ✅ Set all necessary session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['display_name'];
            $_SESSION['role'] = $user['role'];
			$_SESSION['user'] = $user['username'];
            $_SESSION['logged_in'] = true;

            // ✅ Redirect to centralized main menu
            header("Location: /training-portal/main.php");
            exit();
        } else {
            echo "<script>alert('Incorrect password!'); window.location.href = '../login.php';</script>";
        }
    } else {
        echo "<script>alert('User not found!'); window.location.href = '../login.php';</script>";
    }
}
?>
