<?php
session_start();
session_regenerate_id(true);

if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit();
}

if (isset($_FILES['fileToUpload']) && isset($_POST['folder'])) {
    $selectedFolder = basename($_POST['folder']); // avoid directory traversal
    $baseDir = "../uploads/";
    $targetDir = $baseDir . $selectedFolder . "/";

    if (!is_dir($targetDir)) {
        die("Invalid folder selected.");
    }

    $filename = basename($_FILES["fileToUpload"]["name"]);
    $targetFile = $targetDir . $filename;

    if (file_exists($targetFile)) {
        $filename = time() . "_" . $filename;
        $targetFile = $targetDir . $filename;
    }

    $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
    $allowedTypes = ['jpg', 'png', 'pdf', 'docx', 'xlsx', 'txt', 'zip', 'rar', 'pptx'];

    if (in_array($fileType, $allowedTypes)) {
        if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $targetFile)) {
            echo "File uploaded to folder: " . htmlspecialchars($selectedFolder);
        } else {
            echo "Error uploading file.";
        }
    } else {
        echo "File type not allowed.";
    }
} else {
    echo "No file or folder selected.";
}
?>
