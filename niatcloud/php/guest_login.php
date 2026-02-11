<?php
session_start();

// Set guest session
$_SESSION['user_id'] = 'guest';
$_SESSION['username'] = 'Guest User';
$_SESSION['role'] = 'guest';

header("Location: ../../booking/index.php");
exit();
?>
