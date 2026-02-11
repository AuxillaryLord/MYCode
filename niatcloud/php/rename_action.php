<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $folder = isset($_POST['folder']) ? basename($_POST['folder']) : '';
    $oldName = isset($_POST['old_name']) ? basename($_POST['old_name']) : '';
    $newName = isset($_POST['new_name']) ? basename($_POST['new_name']) : '';

    $folderPath = "../uploads/$folder/";
    $oldPath = $folderPath . $oldName;
    $newPath = $folderPath . $newName;

    if (!file_exists($oldPath)) {
        die("File not found.");
    }

    if (file_exists($newPath)) {
        die("A file with the new name already exists.");
    }

    if (rename($oldPath, $newPath)) {
        header("Location: ../browse.php?folder=" . urlencode($folder) . "&msg=renamed");
        exit();
    } else {
        die("Rename failed.");
    }
}
?>
