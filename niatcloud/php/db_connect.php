<?php
$servername = "localhost";
$username = "root";
$password = ""; // Empty if you didn't set MySQL password
$dbname = "nshare_lite_db";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
