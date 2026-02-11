<?php
session_start();
session_regenerate_id(true);

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Upload Files - NShare Lite</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        /* Container for the upload form */
        .container {
            width: 90%;
            max-width: 500px;
            margin: auto;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-top: 50px;
        }

        /* Heading Style */
        h2 {
            text-align: center;
            color: #333;
        }

        /* Input and Button Styling */
        select, input[type="file"], button {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button {
            background-color: #333;
            color: white;
            border: none;
            cursor: pointer;
        }

        button:hover {
            background-color: #555;
        }

        /* Navigation Styling */
        nav {
            background-color: #333;
            padding: 10px 0;
            text-align: center;
        }

        nav a {
            color: #fff;
            text-decoration: none;
            padding: 10px 20px;
            font-size: 16px;
        }

        nav a:hover {
            background-color: #555;
        }
    </style>
</head>
<body>

<!-- Navigation Bar -->
<nav>
    <a href="index.php">Home</a>
    
    <a href="upload.php">Upload Files</a>
    <a href="browse.php">Browse Files</a>
    <a href="create_folder.php">Create Folder</a>
    <a href="php/logout.php">Logout</a>
</nav>

<!-- Upload Form Container -->
<div class="container">
    <h2>Upload a File</h2>
    <form action="php/upload.php" method="post" enctype="multipart/form-data">
        <label for="folder">Select Folder:</label>
        <select name="folder" required>
            <option value="">-- Select Folder --</option>
            <?php
            $uploadBase = 'uploads/';
            $dirs = array_filter(glob($uploadBase . '*'), 'is_dir');
            foreach ($dirs as $dir) {
                $folderName = basename($dir);
                echo "<option value=\"$folderName\">$folderName</option>";
            }
            ?>
        </select>
        <br><br>
        <input type="file" name="fileToUpload" required><br><br>
        <button type="submit" name="submit">Upload</button>
    </form>
</div>

</body>
</html>

