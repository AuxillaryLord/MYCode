<?php
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    // Not logged in
    header("Location: ../login.php");
    exit();
}

if (!in_array($_SESSION['role'], ['admin', 'user'])) {
    echo "<script>alert('Access denied: Admins and Users only!'); window.location.href = '../index.php';</script>";
    exit();
}

