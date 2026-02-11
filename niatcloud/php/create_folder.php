<?php
session_start();
if (!isset($_SESSION['user'])) {
    die("Access Denied");
}

if (isset($_POST['folder_name'])) {
    $folderName = preg_replace('/[^A-Za-z0-9_\-]/', '', $_POST['folder_name']); // Clean input

    $path = "../uploads/" . $folderName;
    if (!file_exists($path)) {
        mkdir($path, 0777, true);
        echo "Folder '$folderName' created successfully.";
    } else {
        echo "Folder already exists!";
    }
} else {
    echo "Folder name required!";
}
?>
