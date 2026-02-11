<?php
// Start the session
session_start();

// If already logged in, redirect to the admin panel
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: admin_panel.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link href="css/tailwind.min.css" rel="stylesheet">
    <style>
        /* Navy blue theme styling */
        body {
            background: #002b45; /* Indian Navy Blue */
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .login-container {
            background-color: rgba(255, 255, 255, 0.85); /* Light background */
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            max-width: 400px;
            margin: 5% auto;
        }

        .login-header {
            font-size: 2rem;
            font-weight: 700;
            color: #003d5b; /* Dark Navy */
            text-align: center;
            margin-bottom: 20px;
        }

        .input-group {
            margin-bottom: 20px;
        }

        .input-group input {
            padding: 12px;
            width: 100%;
            border-radius: 8px;
            border: 1px solid #ddd;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            font-size: 1rem;
        }

        .input-group input:focus {
            border-color: #0073e6; /* Blue accent */
            outline: none;
        }

        .login-button {
            background-color: #0066cc;
            color: white;
            padding: 12px;
            width: 100%;
            border-radius: 8px;
            font-size: 1.2rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .login-button:hover {
            background-color: #004d99;
        }

        .footer {
            text-align: center;
            margin-top: 30px;
            color: #fff;
        }

        .footer a {
            color: #0073e6;
            text-decoration: none;
        }

        /* Animated background */
        .background {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, #003d5b, #004f72, #0066cc);
            animation: gradientMove 5s ease-in-out infinite;
            z-index: -1;
        }

        @keyframes gradientMove {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
    </style>
</head>
<body>

    <div class="background"></div> <!-- Animated background -->

    <div class="login-container">
        <h1 class="login-header">Admin Login</h1>

        <!-- Admin Login Form -->
        <form action="admin_login_backend.php" method="POST">
            <div class="input-group">
                <label for="username" class="text-sm text-gray-700">Username</label>
                <input type="text" id="username" name="username" required placeholder="Enter username" />
            </div>
            <div class="input-group">
                <label for="password" class="text-sm text-gray-700">Password</label>
                <input type="password" id="password" name="password" required placeholder="Enter password" />
            </div>
            <button type="submit" class="login-button">Login</button>
        </form>

        <!-- Footer -->
        <div class="footer">
            <p>Powered by <a href="#" target="_blank">NIAT</a></p>
        </div>
    </div>

</body>
</html>
