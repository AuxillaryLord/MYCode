<?php
session_start();

// Include database connection
require_once '../includes/db.php'; // Make sure this file connects to your MySQL database

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get username and password from POST request
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Validate input fields
    if (empty($username) || empty($password)) {
        echo "<script>alert('Please fill both username and password fields.'); window.location.href = 'admin_login.php';</script>";
        exit();
    }

    // Query the database for the admin with the given username
    $query = "SELECT * FROM admins WHERE username = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$username]);

    $admin = $stmt->fetch();

    // Check if the admin exists and if the password is correct
    if ($admin && password_verify($password, $admin['password_hash'])) {
        // Check if the admin is active
        if ($admin['is_active'] == 1) {
            // Set session variables for the logged-in admin
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];

            // Redirect to the admin panel
            header("Location: admin_panel.php");
            exit();
        } else {
            echo "<script>alert('Your account is inactive. Please contact support.'); window.location.href = 'admin_login.php';</script>";
        }
    } else {
        // Invalid credentials
        echo "<script>alert('Invalid username or password. Please try again.'); window.location.href = 'admin_login.php';</script>";
    }
}
?>
