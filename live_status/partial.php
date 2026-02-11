<?php
include_once 'helpers.php';

$typeFilter = $_GET['type'] ?? '';
$locationFilter = $_GET['location'] ?? '';
$search = $_GET['search'] ?? '';

$conn = new mysqli("localhost", "root", "", "live_network");

// Build WHERE clause
$where = [];
if (!empty($typeFilter)) {
    $where[] = "type = '" . $conn->real_escape_string($typeFilter) . "'";
}
if (!empty($locationFilter)) {
    $where[] = "location = '" . $conn->real_escape_string($locationFilter) . "'";
}
if (!empty($search)) {
    $where[] = "name LIKE '%" . $conn->real_escape_string($search) . "%'";
}

$sql = "SELECT * FROM devices";
if (!empty($where)) {
    $sql .= " WHERE " . implode(" AND ", $where);
}
$sql .= " ORDER BY type, name";

$result = $conn->query($sql);
$devices = [];
while ($row = $result->fetch_assoc()) {
    $devices[] = $row;
}
$conn->close();
?>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <?php foreach ($devices as $device): ?>
        <div class="<?= getColorClass($device['location']) ?> p-4 rounded-2xl shadow-md flex items-center justify-between">
            <div>
                <p class="text-lg font-semibold">
					<?= getDeviceEmoji($device['type']) ?> <?= htmlspecialchars($device['name']) ?>
				</p>
				<p class="text-sm text-gray-700"><?= $device['type'] ?> - <?= $device['location'] ?></p>

                
            </div>
            <div class="text-right">
                <span class="inline-block w-4 h-4 rounded-full <?= $device['status'] == 'up' ? 'bg-green-500' : 'bg-red-500' ?>" title="<?= $device['status'] ?>"></span>
            </div>
        </div>
    <?php endforeach; ?>
</div>
