<?php
$host = "localhost";
$user = "root";
$password = "";
$dbname = "training_portal";

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
?>
