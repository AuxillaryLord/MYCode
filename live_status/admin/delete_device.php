<?php
$conn = new mysqli("localhost", "root", "", "live_network");
$id = $_GET['id'];
$conn->query("DELETE FROM devices WHERE id=$id");
$conn->close();

header("Location: admin.php?status=deleted");
exit();

?>
