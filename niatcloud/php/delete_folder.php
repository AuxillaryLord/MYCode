<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit();
}

$folder = isset($_GET['folder']) ? basename($_GET['folder']) : '';
$folderPath = "../uploads/$folder";

if (is_dir($folderPath)) {
    // Check if the folder is empty (only contains . and ..)
    if (count(scandir($folderPath)) == 2) {
        if (rmdir($folderPath)) {
            header("Location: ../index.php?msg=folder_deleted");
            exit();
        } else {
            die("Failed to delete folder.");
        }
    } else {
        die("Folder is not empty. Please delete all files inside first.");
    }
} else {
    die("Folder not found.");
}
?>
