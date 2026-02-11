<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit();
}

$folder = isset($_GET['folder']) ? basename($_GET['folder']) : '';
$file = isset($_GET['file']) ? basename($_GET['file']) : '';

$path = "../uploads/$folder/$file";

if (file_exists($path)) {
    if (unlink($path)) {
        header("Location: ../browse.php?folder=" . urlencode($folder));
        exit();
    } else {
        echo "Failed to delete file.";
    }
} else {
    echo "File not found.";
}
?>
