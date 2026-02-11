<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$baseDir = "uploads/";
$folders = array_filter(glob($baseDir . '*'), 'is_dir');

$selectedFolder = isset($_GET['folder']) ? basename($_GET['folder']) : '';
$folderPath = $baseDir . $selectedFolder . '/';

$files = [];

if ($selectedFolder && is_dir($folderPath)) {
    $items = array_diff(scandir($folderPath), ['.', '..']);
    foreach ($items as $item) {
        $files[] = $item; // only direct children (files or subfolders)
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Browse Folders - NShare Lite</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
		html, body {
			height: 100%;
			margin: 0;
			padding: 0;
		}

		.wrapper {
			display: flex;
			flex-direction: column;
			min-height: 92vh;
		}

		.container {
			flex: 1;
		}

		footer {
			text-align: center;
			padding: 15px;
			background-color: #001f3f;
			color: white;
			margin-top: auto;
		}

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f7fc;
            margin: 0;
            padding-top: 60px;
        }

        nav {
            position: fixed;
            top: 0;
            width: 100%;
            background-color: #001f3f;
            padding: 15px 0;
            z-index: 1000;
            box-shadow: 0 2px 4px rgba(0,0,0,0.3);
            text-align: center;
        }

        nav a {
            color: white;
            margin: 0 20px;
            font-weight: bold;
            text-decoration: none;
            font-size: 16px;
        }

        nav a:hover, nav .active {
            text-decoration: underline;
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
        }

        .page-content {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 6px 12px rgba(0,0,0,0.1);
        }

        h2, h3 {
            color: #002147;
            text-align: center;
        }

        form {
            text-align: center;
            margin-bottom: 20px;
        }

        select {
            padding: 10px;
            font-size: 16px;
            border-radius: 6px;
            border: 1px solid #ccc;
            width: 250px;
        }

        ul {
            list-style-type: none;
            padding: 0;
        }

        li {
            margin: 10px 0;
            background-color: #f1f9ff;
            padding: 12px;
            border-radius: 5px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        li a {
            margin: 0 5px;
            color: #001f3f;
            font-weight: bold;
        }

        li a:hover {
            text-decoration: underline;
        }

        
    </style>
</head>
<body>
<div class="wrapper">

    <!-- Navigation Bar -->
    <nav>
        <a href="index.php">Home</a>
        <a href="upload.php">Upload Files</a>
        <a href="create_folder.php">Create Folder</a>
        
        <a href="browse.php" class="active">Browse Files</a>
        <a href="php/logout.php">Logout</a>
    </nav>

    <!-- Page Content -->
    <div class="container">
        <div class="page-content">
            <h2>Browse Files in Folders</h2>

            <form method="GET" action="browse.php">
                <label for="folder">Select Folder:</label><br><br>
                <select name="folder" onchange="this.form.submit()">
                    <option value="">-- Select Folder --</option>
                    <?php foreach ($folders as $folder): 
                        $folderName = basename($folder);
                        $selected = ($folderName === $selectedFolder) ? 'selected' : '';
                    ?>
                        <option value="<?= htmlspecialchars($folderName) ?>" <?= $selected ?>>
                            <?= htmlspecialchars($folderName) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>

            <?php if ($selectedFolder): ?>
                <h3>Files in: <?= htmlspecialchars($selectedFolder) ?></h3>
                <ul>
				<?php foreach ($files as $file): ?>
					<?php $fullPath = $folderPath . $file; ?>
					<li>
						<span>
							<?php if (is_dir($fullPath)): ?>
								📁 <strong><?= htmlspecialchars($file) ?>/</strong>
							<?php else: ?>
								📄 <?= htmlspecialchars($file) ?>
							<?php endif; ?>
						</span>
						<span>
							<?php if (!is_dir($fullPath)): ?>
								<a href="<?= $fullPath ?>" download>Download</a> |
							<?php endif; ?>
							<a href="php/delete_file.php?folder=<?= urlencode($selectedFolder) ?>&file=<?= urlencode($file) ?>" onclick="return confirm('Are you sure?')">Delete</a> |
							<a href="php/rename.php?folder=<?= urlencode($selectedFolder) ?>&file=<?= urlencode($file) ?>">Rename</a>
						</span>
					</li>
				<?php endforeach; ?>
				</ul>

            <?php endif; ?>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <p>&copy; 2025 NIAT | NShare Lite</p>
    </footer>

</div> <!-- .wrapper -->
</body>

</html>
