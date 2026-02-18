<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - NATMS</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        /* MODIFIED: Changed entire login page design to match NIAT Cloud
           Reason: User requested same beautiful design as NIAT Cloud login
           Original: Custom modern gradient design with cards
           New: Two-column layout with aircraft image and login box, matches NIAT Cloud exactly */

        body {
            margin: 0;
            font-family: 'Times New Roman', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(to right, #0A192F, #1D3557);
            color: #fff;
            min-height: 95vh;
            display: flex;
            flex-direction: column;
        }

        /* NAVBAR STYLING */
        .navbar {
            background-color: #1B2A41;
            color: #EAEAEA;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 5px 10px;
            position: absolute;
            top: 5px;
            z-index: 1000;
        }

        .navbar-left img {
            height: 50px;
        }

        .navbar-center {
            flex-grow: 1;
            text-align: center;
        }

        .navbar-title {
            color: #FFD700;
            font-size: 26px;
            font-weight: 600;
            letter-spacing: 1px;
        }

        footer {
            background-color: #002147;
            color: white;
            text-align: center;
            padding: 10px 0;
            position: fixed;
            bottom: 0;
            width: 100%;
            font-size: 14px;
        }

        @media (max-width: 480px) {
            .login-box {
                padding: 20px;
            }
        }

        .login-box h2 {
            text-align: center;
            margin-bottom: 25px;
            font-size: 22px;
            color: #001f3f;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 12px;
            margin: 10px 0 20px 0;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 16px;
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: #002147;
            color: #fff;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #001233;
        }

        .login-page {
            display: flex;
            min-height: 100vh;
        }

        .left-side {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #0A192F;
            padding: 20px;
        }

        .left-side img {
            width: 90%;
            max-width: 1000px;
            object-fit: contain;
            opacity: 40%;
        }

        .right-side {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            background-image: url('assets/svikrant.png');
            background-size: contain;
            background-position: center;
            position: relative;
        }

        .right-side::before {
            content: '';
            position: absolute;
            inset: 0;
            background-color: rgba(10, 25, 47, 0.85);
            z-index: 0;
        }

        .login-box {
            position: relative;
            z-index: 1;
            background-color: #ffffff;
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3);
            color: #002147;
            width: 100%;
            max-width: 400px;
        }

        /* MODIFIED: Added error message styling
           Reason: Display login errors from backend */
        .alert-error {
            padding: 12px 15px;
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            border-radius: 6px;
            margin-bottom: 20px;
            display: none;
            font-family: Arial, sans-serif;
        }

        .alert-error.show {
            display: block;
        }

        /* MODIFIED: Added debug link at bottom
           Reason: Allow users to test password authentication if login fails */
        .debug-link {
            text-align: center;
            margin-top: 15px;
        }

        .debug-link a {
            color: #666;
            font-size: 12px;
            text-decoration: none;
            font-family: Arial, sans-serif;
        }

        .debug-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<!-- NAVBAR SECTION -->
<nav class="navbar">
    <div class="navbar-left">
        <img src="assets/unit-logo.png" alt="Unit Logo">
    </div>
    <div class="navbar-center">
        <span class="navbar-title">NAVAL AVIATION TRAINING MANAGEMENT SYSTEM</span>
    </div>
</nav>

<!-- Main Login Form -->
<div class="login-page">
    <div class="left-side">
        <img src="assets/combined.png" alt="MiG-29K">
    </div>
    <div class="right-side">
        <div class="login-box">
            <h2>NATMS</h2>
            <!-- MODIFIED: Added error message display section
                 Reason: Show login errors from backend (incorrect password, user not found, etc.) -->
            <div class="alert-error" id="errorAlert"></div>
            <!-- MODIFIED: Changed form to post to login_central.php
                 Reason: Use centralized authentication for all modules -->
            <form action="php/login_central.php" method="post">
                <input type="text" name="username" placeholder="Username" required autocomplete="username">
                <input type="password" name="password" placeholder="Password" required autocomplete="current-password">
                <button type="submit">Login</button>
            </form>
            <!-- MODIFIED: Added debug link for testing
                 Reason: Help users diagnose password issues if authentication fails -->
            <div class="debug-link">
                <a href="debug_test.php">Having trouble? Test credentials here</a>
            </div>
        </div>
    </div>
</div>

<!-- Footer -->
<footer>
    &copy; 2025 NATMS | NAVAL AVIATION TRAINING MANAGEMENT SYSTEM
</footer>

<!-- MODIFIED: Added JavaScript for error handling
     Reason: Display error messages from login backend redirect -->
<script>
    // MODIFIED: Check for error parameter in URL
    // Reason: Backend redirects here with error message if authentication fails
    const urlParams = new URLSearchParams(window.location.search);
    const error = urlParams.get('error');
    
    if (error) {
        const errorAlert = document.getElementById('errorAlert');
        errorAlert.textContent = decodeURIComponent(error);
        errorAlert.classList.add('show');
    }
</script>

</body>
</html>
