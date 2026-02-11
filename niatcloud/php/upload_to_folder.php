<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'user') {
    die("Unauthorized access.");
}

$baseDir = '../uploads/';
$folder = basename($_POST['folder'] ?? '');
$targetDir = $baseDir . $folder;

if (!is_dir($targetDir)) {
    die("Target folder not found.");
}

foreach ($_FILES['file']['tmp_name'] as $index => $tmpName) {
    $name = basename($_FILES['file']['name'][$index]);
    move_uploaded_file($tmpName, "$targetDir/$name");
}

echo "Files uploaded successfully.";
