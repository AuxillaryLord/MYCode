<?php
/**
 * MODIFIED: Created logout script for central authentication
 * Reason: Safely clear session and redirect to login
 * Original behavior: No centralized logout mechanism
 * New behavior: Destroy session and redirect to login page
 */

session_start();

// MODIFIED: Clear all session variables
// Reason: Ensure complete user session cleanup
session_unset();

// MODIFIED: Destroy entire session
// Reason: Remove all session data from server
session_destroy();

// MODIFIED: Redirect to login page with logout confirmation
// Reason: Give user feedback that logout was successful
?>

<!DOCTYPE html>
<html>
<head>
    <title>Logged Out - NATMS</title>
    <meta http-equiv="refresh" content="3; url=../login.php">
</head>
<body style="background: #0A192F; color: white; display: flex; align-items: center; justify-content: center; height: 100vh; font-family: Arial;">
    <div style="text-align: center;">
        <h2>You have been logged out successfully</h2>
        <p>Redirecting to login page in 3 seconds...</p>
        <a href="../login.php" style="color: #FFD700; text-decoration: none;">Click here if not redirected</a>
    </div>
</body>
</html>
