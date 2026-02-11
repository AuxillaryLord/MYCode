<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$uploadDir = 'uploads/';
$baseDir = 'uploads/';
$folders = array_filter(scandir($baseDir), function ($item) use ($baseDir) {
    return is_dir($baseDir . $item) && !in_array($item, ['.', '..']);
});

$orderFile = $baseDir . 'folder_order.json';
$orderedFolders = [];

if (file_exists($orderFile)) {
    $customOrder = json_decode(file_get_contents($orderFile), true);

    // Add folders in the defined order
    foreach ($customOrder as $folderName) {
        if (in_array($folderName, $folders)) {
            $orderedFolders[] = $folderName;
        }
    }

    // Add any remaining folders not in the JSON
    foreach ($folders as $f) {
        if (!in_array($f, $orderedFolders)) {
            $orderedFolders[] = $f;
        }
    }
} else {
    $orderedFolders = $folders; // fallback if no folder_order.json
}

$userRole = $_SESSION['role'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>NATMS</title>
    <link rel="stylesheet" href="css/style.css?v=1.0">
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #e8f0fe;
        }

        nav {
            background-color: #002147; /* Navy blue */
            padding: 12px 0;
            text-align: center;
        }

        nav a {
            color: white;
            text-decoration: none;
            margin: 0 12px;
            font-weight: bold;
        }

        nav a:hover {
            text-decoration: underline;
        }
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
        .container {
            width: 90%;
            max-width: 1200px;
            margin: auto;
            padding: 20px;
        }

        .page-content {
            margin-top: 20px;
        }

        h2, h3 {
            color: #002147;
        }

        .folder-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .folder-item {
            text-align: center;
            background: #fff;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .folder-item img {
            width: 64px;
            cursor: pointer;
        }

        .folder-item a {
            display: block;
            margin-top: 10px;
            font-weight: bold;
            color: #002147;
            text-decoration: none;
        }

        .folder-item a:hover {
            text-decoration: underline;
        }

        footer {
            background-color: #002147;
            color: white;
            text-align: center;
            padding: 15px 0;
            position: fixed;
            bottom: 0;
            width: 100%;
        }

        .admin-controls {
            margin-top: 30px;
            padding: 15px;
            background: #fff3cd;
            border: 1px solid #ffeeba;
            border-radius: 8px;
        }
		.navbar-left img {
			height: 50px;
		}
    </style>
</head>
<body>

<!-- Navigation Bar -->
<nav class="navbar">
	<div class="navbar-left">
        <img src="assets/unit-logo.png" alt="Unit Logo"> <!-- Replace with actual path -->
    
		<a href="/training-portal/main.php">Home</a>
		<?php if ($userRole === 'admin'): ?>
			<a href="create_folder.php">Create Folder</a>
		
		<a href="upload.php">Upload</a>
		<a href="browse.php">Browse Files</a>
		<?php endif; ?>
		<a href="php/logout.php">Logout</a>
	</div>
</nav>

<!-- Main Content -->
<div class="container">
    <div class="page-content">
        <h2>Welcome, <?= htmlspecialchars($_SESSION['user']) ?>!</h2>
        <hr>
        <h3>Available Folders</h3>

        <div class="folder-grid">
            <?php foreach ($orderedFolders as $folder): ?>
				<?php $folderPath = $uploadDir . $folder; ?>

                <?php 
                    $folder = basename($folderPath); 
                    if ($folder[0] === '.') continue;
                ?>
                <div class="folder-item">
                    <a href="folder_view.php?folder=<?= urlencode($folder) ?>">
                        <img src="assets/folder-icon.png" alt="Folder Icon">
                    </a>
                    <a href="folder_view.php?folder=<?= urlencode($folder) ?>"><?= htmlspecialchars($folder) ?></a>
                    
                    <?php if ($userRole === 'admin'): ?>
                        <a href="php/delete_folder.php?folder=<?= urlencode($folder) ?>" onclick="return confirm('Delete this folder? It must be empty.');" style="color: red; font-size: 0.9em;">[Delete]</a>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if ($userRole === 'admin'): ?>
            <div class="admin-controls">
                <p><strong>Admin Notice:</strong> You can create or delete folders from the navigation menu or above.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Footer -->
<footer>
    &copy; 2025 NIAT | NIAT CLOUD - Secure File Sharing
</footer>

</body>
</html>
