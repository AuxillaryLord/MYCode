<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit();
}

$folder = isset($_GET['folder']) ? basename($_GET['folder']) : '';
$file = isset($_GET['file']) ? basename($_GET['file']) : '';
$folderPath = "../uploads/$folder/";
$filePath = $folderPath . $file;

if (!file_exists($filePath)) {
    die("File not found.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Rename File</title>
</head>
<body>
    <h2>Rename File: <?= htmlspecialchars($file) ?></h2>
    <form action="rename_action.php" method="post">
        <input type="hidden" name="folder" value="<?= htmlspecialchars($folder) ?>">
        <input type="hidden" name="old_name" value="<?= htmlspecialchars($file) ?>">
        <label for="new_name">New Name:</label>
        <input type="text" name="new_name" id="new_name" required value="<?= htmlspecialchars($file) ?>">
        <button type="submit">Rename</button>
    </form>
    <br><a href="browse.php?folder=<?= urlencode($folder) ?>">Cancel</a>
</body>
</html>
