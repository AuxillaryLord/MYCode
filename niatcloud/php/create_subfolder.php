<?php
session_start();
if (!isset($_SESSION['user']) || !in_array($_SESSION['role'], ['user', 'admin'])) {
    header("Location: ../login.php");
    exit();
}

$baseDir = '../uploads/';
$folder = $_POST['folder'] ?? '';
$newFolderName = $_POST['new_folder'] ?? '';

// ✅ Security: prevent directory traversal
$folder = trim(str_replace(['..', '\\'], '', $folder), '/');
$newFolderName = trim(str_replace(['..', '/', '\\'], '', $newFolderName));  // block nested names in subfolder

$currentDir = $baseDir . $folder;

// 🔍 Check if base folder exists
if (!is_dir($currentDir)) {
    die("Base folder does not exist.");
}

// 🛠️ Create the new subfolder
$newSubfolderPath = $currentDir . '/' . $newFolderName;

if (!is_dir($newSubfolderPath)) {
    if (mkdir($newSubfolderPath, 0777, true)) {
        header("Location: ../folder_view.php?folder=" . urlencode($folder));
        exit();
    } else {
        die("Failed to create subfolder.");
    }
} else {
    die("Subfolder already exists.");
}
?>
