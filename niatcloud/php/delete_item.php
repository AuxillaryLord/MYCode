<?php
session_start();
if (!isset($_SESSION['user'])) {
    http_response_code(403);
    exit("Unauthorized");
}

$folder = $_GET['folder'] ?? '';
$item = $_GET['item'] ?? '';

$baseDir = '../uploads/';
$targetPath = realpath($baseDir . $folder . '/' . $item);

// Prevent directory traversal
if (strpos($targetPath, realpath($baseDir)) !== 0 || !file_exists($targetPath)) {
    http_response_code(400);
    exit("Invalid file/folder.");
}

// If it's a folder, delete recursively
function deleteFolder($path) {
    foreach (scandir($path) as $file) {
        if ($file === '.' || $file === '..') continue;
        $fullPath = $path . DIRECTORY_SEPARATOR . $file;
        if (is_dir($fullPath)) {
            deleteFolder($fullPath);
        } else {
            unlink($fullPath);
        }
    }
    rmdir($path);
}

if (is_dir($targetPath)) {
    deleteFolder($targetPath);
} else {
    unlink($targetPath);
}

header("Location: ../folder_view.php?folder=" . urlencode($folder));
exit();
?>
