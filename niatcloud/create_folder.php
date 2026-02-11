<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create New Folder</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<!-- Navigation Bar -->
<nav>
    <div class="container">
        <a href="index.php">Home</a>
        
        
        <a href="create_folder.php" class="active">Create Folder</a>
        <a href="upload.php">Upload</a>
        <a href="browse.php">Browse Files</a>
        <a href="php/logout.php">Logout</a>
    </div>
</nav>

<!-- Main Content -->
<div class="container">
    <div class="page-content">
        <h2>Create New Folder</h2>
        
        <!-- Folder Creation Form -->
        <form action="php/create_folder.php" method="post">
            <div class="form-group">
                <input type="text" name="folder_name" placeholder="Folder Name" required class="input-field">
            </div>
            <button type="submit" class="btn">Create Folder</button>
        </form>

        <br><a href="browse.php" class="btn">Back to Browse</a>
    </div>
</div>

<!-- Footer -->
<footer>
    <p>&copy; 2025 Your Organization | All rights reserved.</p>
</footer>

</body>
</html>
