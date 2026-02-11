<?php
function getColorClass($locationName) {
    $conn = new mysqli("localhost", "root", "", "live_network");

    if ($conn->connect_error) {
        return 'bg-gray-100'; // fallback
    }

    $stmt = $conn->prepare("SELECT color_class FROM locations WHERE name = ?");
    $stmt->bind_param("s", $locationName);
    $stmt->execute();
    $stmt->bind_result($color);
    $stmt->fetch();
    $stmt->close();
    $conn->close();

    return $color ?? 'bg-gray-100'; // return color or default
}


function getDeviceEmoji($type) {
    return match ($type) {
        'PC' => '💻',
        'Printer' => '🖨️',
        'Switch' => '🔀',
        default => '🧩'
    };
}
?>
