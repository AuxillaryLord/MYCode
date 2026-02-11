<?php
session_start();
if (!isset($_SESSION['user'])) {
    http_response_code(403);
    exit("Unauthorized");
}

$folder = $_POST['folder'] ?? '';
$oldName = $_POST['old_name'] ?? '';
$newName = $_POST['new_name'] ?? '';

$baseDir = '../uploads/';
$oldPath = realpath($baseDir . $folder . '/' . $oldName);
$newPath = $baseDir . $folder . '/' . $newName;

// Security: Prevent renaming outside base
if (strpos($oldPath, realpath($baseDir)) !== 0 || !$newName || !file_exists($oldPath)) {
    http_response_code(400);
    exit("Invalid operation.");
}

if (file_exists($newPath)) {
    http_response_code(409);
    exit("A file/folder with that name already exists.");
}

rename($oldPath, $newPath);
header("Location: ../folder_view.php?folder=" . urlencode($folder));
exit();
?>
