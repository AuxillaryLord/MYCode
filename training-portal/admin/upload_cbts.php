<?php
include '../db.php';
include 'session_check.php'; // Check admin session

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject_id = intval($_POST['subject_id']);
    $title = trim($_POST['title']);

    // Check if file is uploaded
    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../uploads/cbts/';
        $fileName = basename($_FILES['file']['name']);
        $fileTmp = $_FILES['file']['tmp_name'];

        // Ensure unique filename
        $fileExt = pathinfo($fileName, PATHINFO_EXTENSION);
        $safeFileName = uniqid('cbt_') . '.' . $fileExt;
        $destination = $uploadDir . $safeFileName;

        if (move_uploaded_file($fileTmp, $destination)) {
            // Insert record
            $stmt = $conn->prepare("INSERT INTO cbts (subject_id, title, file_path) VALUES (?, ?, ?)");
            $stmt->bind_param("iss", $subject_id, $title, $safeFileName);
            if ($stmt->execute()) {
                echo "<script>alert('CBT uploaded successfully.'); window.location.href='../manage_cbts.php';</script>";
            } else {
                echo "<script>alert('Database error.'); window.location.href='../manage_cbts.php';</script>";
            }
        } else {
            echo "<script>alert('Failed to move uploaded file.'); window.location.href='../manage_cbts.php';</script>";
        }
    } else {
        echo "<script>alert('No file uploaded or upload error.'); window.location.href='../manage_cbts.php';</script>";
    }
}
?>
