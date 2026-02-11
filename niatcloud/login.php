<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - NATMS</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
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
			color: #FFD700; /* Sky blue highlight */
			font-size: 26px;
			font-weight: 600;
			letter-spacing: 1px;
		}

	
		

		
		footer {
			background-color: #1B2A41; /* Same as navbar or dark navy */
			color: #EAEAEA;            /* Light gray text for readability */
			text-align: center;
			padding: 14px 0;
			font-size: 14px;
			position: fixed;
			bottom: 0;
			width: 100%;
		}


		@media (max-width: 480px) {
			.login-box {
				padding: 20px;
			}
		}
		

		

		
		

		.form-side {
			flex: 1;
			background-color: #f8f9fa;
			display: flex;
			justify-content: center;
			align-items: center;
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
			background-color: rgba(10, 25, 47, 0.85); /* darken overlay for readability */
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
		footer {
            background-color: #002147;
            color: white;
            text-align: center;
            padding: 10px 0;
            position: fixed;
            bottom: 0;
            width: 100%;
        }


	</style>

</head>
<body>

<!-- NAVBAR SECTION -->
<nav class="navbar">
    <div class="navbar-left">
        <img src="assets/unit-logo.png" alt="Unit Logo"> <!-- Replace with actual path -->
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
				<form action="php/login.php" method="post">
					<input type="text" name="username" placeholder="Username" required>
					<input type="password" name="password" placeholder="Password" required>
					<button type="submit">Login</button><br>
					
				</form><br>
				<!-- Guest Access Button -->
				<div class="mt-4 text-center">
					<a href="php/guest_login.php" class="bg-red-600 hover:bg-red-800 text-white px-4 py-2 rounded inline-block">Continue as Guest (for booking NIAT facilities)</a>
				</div>
			</div>
		</div>
	</div>





 <!-- Footer -->
<footer>
    &copy; 2025 NATMS | NAVAL AVIATION TRAINING MANAGEMENT SYSTEM
</footer>

</body>
</html>
