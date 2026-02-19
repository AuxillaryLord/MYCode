<?php
/**
 * MODIFIED: Fixed niatcloud/index.php to handle missing uploads directory
 * Reason: Original code failed when uploads/ directory didn't exist
 * Original behavior: scandir() would fail with fatal error
 * New behavior: Create directory if missing, handle gracefully
 * Fix: Added error handling and directory creation
 */

session_start();

// MODIFIED: Use centralized authentication from dashboard
// Reason: All modules should check same login system
// Original: Checked only for $_SESSION['user']
// New: Use unified RBAC system
// Note: Already checked by portal.php before redirecting here
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: /dashboard/login.php?error=" . urlencode("Please log in first"));
    exit();
}

// MODIFIED: Use session variable from centralized login
// Reason: Consistent across all modules
// Original: Used $_SESSION['user'] (inconsistent naming)
// New: Using $_SESSION['user'] but with validation
$username = $_SESSION['user'] ?? '';
$display_name = $_SESSION['display_name'] ?? $username;
$userRole = $_SESSION['role'] ?? 'guest';

// MODIFIED: Define uploads directory path
// Reason: Use relative path from current location
// Original: Simple string 'uploads/'
// New: Full path handling with proper directory checks
// Security: Validate directory exists and is readable
$uploadDir = __DIR__ . '/uploads/';
$baseDir = __DIR__ . '/uploads/';

// MODIFIED: Create uploads directory if it doesn't exist
// Reason: First access would fail without this directory
// Original: Assumed directory always existed
// New: Auto-create with proper permissions
// Benefit: Prevents fatal errors on first page load
if (!file_exists($baseDir)) {
    // MODIFIED: Create directory with error handling
    // Reason: Gracefully handle creation failure
    // Original: No directory creation
    // New: Try to create, show error if fails
    if (!@mkdir($baseDir, 0755, true)) {
        // MODIFIED: Redirect to error page instead of fatal error
        // Reason: User-friendly error display
        // Original: Fatal error shown to user
        // New: Professional error page
        header('Location: /dashboard/error.php?code=500&type=error&message=' . 
               urlencode('Cloud storage not accessible') . 
               '&details=' . urlencode('Failed to create uploads directory') .
               '&redirect=/dashboard/portals.php&redirect_text=Return to Dashboard');
        exit();
    }
}

// MODIFIED: Verify directory is readable/writable
// Reason: Check permissions before proceeding
// Original: No permission check
// New: Validate directory permissions
if (!is_readable($baseDir) || !is_writable($baseDir)) {
    // MODIFIED: Redirect to error page
    // Reason: Inform admin about permission issue
    // Original: Silent failure or confusing error
    // New: Clear permission error message
    header('Location: /dashboard/error.php?code=500&type=error&message=' . 
           urlencode('Cloud storage has permission issues') . 
           '&details=' . urlencode('Uploads directory exists but is not readable/writable') .
           '&redirect=/dashboard/portals.php&redirect_text=Return to Dashboard');
    exit();
}

// MODIFIED: Safely scan directory with error handling
// Reason: scandir() can fail if directory has permission issues
// Original: Direct scandir() call without error handling
// New: Wrapped in error handling with fallback
// Benefit: Prevents fatal errors
$scan_result = @scandir($baseDir);
if ($scan_result === false) {
    // MODIFIED: Handle scandir() failure gracefully
    // Reason: Directory might be locked or have issues
    // Original: Causes fatal error
    // New: Show user-friendly message
    header('Location: /dashboard/error.php?code=500&type=error&message=' . 
           urlencode('Could not read cloud storage') . 
           '&details=' . urlencode('Failed to list directory contents') .
           '&redirect=/dashboard/portals.php&redirect_text=Return to Dashboard');
    exit();
}

// MODIFIED: Filter and process directory listing
// Reason: Remove . and .. entries, only get directories
// Original: Used array_filter with callback
// New: Same logic, now with proper $scan_result validation
$folders = array_filter($scan_result, function ($item) use ($baseDir) {
    // MODIFIED: Validate each item is a directory
    // Reason: Exclude files from folder listing
    // Original: Same logic
    // New: Now working with validated scandir() result
    return is_dir($baseDir . $item) && !in_array($item, ['.', '..', '.git', '.gitignore']);
});

// MODIFIED: Load custom folder ordering if exists
// Reason: Allow admin to customize folder display order
// Original: Used file_exists() and json_decode
// New: Same logic with better error handling
$orderFile = $baseDir . 'folder_order.json';
$orderedFolders = [];

if (file_exists($orderFile)) {
    // MODIFIED: Read and parse folder order file
    // Reason: Allow custom ordering
    // Original: Direct file read and decode
    // New: Same logic
    $customOrder = json_decode(file_get_contents($orderFile), true);

    if (is_array($customOrder)) {
        // MODIFIED: Add folders in custom order
        // Reason: Respect admin's folder ordering
        // Original: Same logic
        // New: With null check for customOrder
        foreach ($customOrder as $folderName) {
            if (in_array($folderName, $folders)) {
                $orderedFolders[] = $folderName;
            }
        }

        // MODIFIED: Add any remaining folders not explicitly ordered
        // Reason: Show new folders created after ordering was set
        // Original: Same logic
        // New: Same logic
        foreach ($folders as $f) {
            if (!in_array($f, $orderedFolders)) {
                $orderedFolders[] = $f;
            }
        }
    } else {
        // MODIFIED: Fall back to unordered folders if JSON is invalid
        // Reason: Handle corrupted folder_order.json gracefully
        // Original: Would still use alphabetical order
        // New: More explicit fallback
        $orderedFolders = $folders;
    }
} else {
    // MODIFIED: Use alphabetical order if no custom ordering file
    // Reason: Default behavior when no preferences set
    // Original: Same logic
    // New: Same logic
    $orderedFolders = $folders;
}
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
