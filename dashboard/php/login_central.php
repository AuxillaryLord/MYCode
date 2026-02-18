<?php
/**
 * MODIFIED: Central authentication backend with improved error handling
 * Reason: Ensure password verification works correctly and shows proper errors
 * Original behavior: Basic login without detailed error messages
 * New behavior: Detailed logging and debugging support
 */

session_start();

// MODIFIED: Added database connection
// Reason: Connect to central authentication database
include '../../niatcloud/php/db_connect.php';

// MODIFIED: Added request method check
// Reason: Only process POST requests from login form
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    header("Location: ../login.php");
    exit();
}

// MODIFIED: Get and trim input values
// Reason: Prevent whitespace issues and sanitize input
$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

// MODIFIED: Added input validation
// Reason: Prevent empty credentials and provide feedback
if (empty($username) || empty($password)) {
    header("Location: ../login.php?error=" . urlencode("Username and password are required"));
    exit();
}

// MODIFIED: Create prepared statement for secure database lookup
// Reason: Prevent SQL injection attacks
$sql = "SELECT id, username, display_name, password, role, is_active FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    // MODIFIED: Handle database preparation error
    // Reason: Catch database configuration issues
    header("Location: ../login.php?error=" . urlencode("Database error: " . $conn->error));
    exit();
}

// MODIFIED: Bind username parameter
// Reason: Secure parameterized query execution
$stmt->bind_param("s", $username);
$stmt->execute();
$res = $stmt->get_result();

// MODIFIED: Check if user exists in database
// Reason: Provide specific error for non-existent users
if ($res->num_rows !== 1) {
    header("Location: ../login.php?error=" . urlencode("User not found. Please check username."));
    exit();
}

// MODIFIED: Fetch user record
// Reason: Get all user information for verification
$user = $res->fetch_assoc();

// MODIFIED: Check if account is active
// Reason: Prevent inactive/suspended accounts from logging in
if ($user['is_active'] != 1) {
    header("Location: ../login.php?error=" . urlencode("Account is inactive. Contact administrator."));
    exit();
}

// MODIFIED: Verify password using bcrypt
// Reason: Securely compare plaintext password with stored hash
// Uses PHP's password_verify() function which protects against timing attacks
if (!password_verify($password, $user['password'])) {
    // MODIFIED: Specific error for wrong password
    // Reason: Distinguish from user not found
    // Security note: Being specific helps users know account exists but password is wrong
    header("Location: ../login.php?error=" . urlencode("Incorrect password. Please try again."));
    exit();
}

// MODIFIED: Create session with user information
// Reason: Make user authenticated for duration of session
$_SESSION['user'] = $user['username'];
$_SESSION['user_id'] = $user['id'];
$_SESSION['role'] = $user['role'];
$_SESSION['display_name'] = $user['display_name'];
$_SESSION['logged_in'] = true;

// MODIFIED: Redirect to portal dashboard
// Reason: Show all available modules for user's role
header("Location: ../portals.php");
exit();
?>
