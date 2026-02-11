<?php
session_start();
include 'db.php';

// Ensure admin is logged in
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject_id = $_POST['subject_id'];
    $title = trim($_POST['title']);

    $uploadDir = "../uploads/lesson_plans/";
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $fileName = basename($_FILES['file']['name']);
    $targetPath = $uploadDir . $fileName;

    // Check file type
    $fileType = strtolower(pathinfo($targetPath, PATHINFO_EXTENSION));
    $allowedTypes = ['pdf', 'doc', 'docx'];

    if (in_array($fileType, $allowedTypes)) {
        if (move_uploaded_file($_FILES['file']['tmp_name'], $targetPath)) {
            // Store in DB (just filename is enough, since path is known)
            $stmt = $conn->prepare("INSERT INTO lesson_plans (subject_id, title, file_path) VALUES (?, ?, ?)");
            $stmt->bind_param("iss", $subject_id, $title, $fileName);

            if ($stmt->execute()) {
                header("Location: manage_lesson_plans.php?success=1");
            } else {
                echo "<script>alert('Database insert failed!'); window.location.href='manage_lesson_plans.php';</script>";
            }
        } else {
            echo "<script>alert('File upload failed!'); window.location.href='manage_lesson_plans.php';</script>";
        }
    } else {
        echo "<script>alert('Invalid file type. Only PDF, DOC, DOCX allowed.'); window.location.href='manage_lesson_plans.php';</script>";
    }
}
?>
