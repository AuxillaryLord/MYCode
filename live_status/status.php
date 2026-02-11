<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "live_network");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch all devices
$result = $conn->query("SELECT id, ip_address FROM devices");

while ($device = $result->fetch_assoc()) {
    $ip = $device['ip_address'];

    // Windows ping command (1 ping, wait 1 second)
    $ping = exec("ping -n 1 -w 1000 $ip", $output, $statusCode);

    // statusCode 0 = success (host reachable)
    $newStatus = $statusCode === 0 ? 'up' : 'down';

    // Update the database
    $update = $conn->prepare("UPDATE devices SET status=? WHERE id=?");
    $update->bind_param("si", $newStatus, $device['id']);
    $update->execute();
    $update->close();
}

$conn->close();

echo json_encode(["success" => true, "message" => "Device statuses updated."]);
?>
