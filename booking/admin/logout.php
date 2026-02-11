<?php
session_start();

// Destroy the session to log the admin out
session_destroy();

// Redirect to the index page
header("Location: ../index.php");
exit();
unset($_SESSION['filter_status']);

?>
