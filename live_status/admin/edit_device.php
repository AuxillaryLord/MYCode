<?php
$conn = new mysqli("localhost", "root", "", "live_network");

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    die("Invalid device ID.");
}

// Fetch all locations
$locations = [];
$locationResult = $conn->query("SELECT name FROM locations ORDER BY name ASC");
while ($row = $locationResult->fetch_assoc()) {
    $locations[] = $row['name'];
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'] ?? '';
    $type = $_POST['type'] ?? '';
    $location = $_POST['location'] ?? '';
    $ip_address = $_POST['ip_address'] ?? '';

    $stmt = $conn->prepare("UPDATE devices SET name=?, type=?, location=?, ip_address=? WHERE id=?");
    $stmt->bind_param("ssssi", $name, $type, $location, $ip_address, $id);
    $stmt->execute();
    $stmt->close();
    $conn->close();

    header("Location: admin.php?status=edited");
    exit();
} else {
    $stmt = $conn->prepare("SELECT * FROM devices WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $device = $result->fetch_assoc();
    $stmt->close();

    if (!$device) {
        die("Device not found.");
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Device</title>
    <link href="../assets/css/tailwind.min.css" rel="stylesheet">
</head>
<body class="p-6 bg-gray-100">
    <h1 class="text-2xl font-bold mb-6">✏️ Edit Device</h1>

    <form method="POST" class="space-y-6 max-w-xl mx-auto bg-white p-6 rounded-xl shadow-xl">
        <!-- Device Name -->
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Device Name</label>
            <input type="text" name="name" id="name" required
                   value="<?= htmlspecialchars($device['name']) ?>"
                   class="w-full p-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        <!-- Device Type -->
        <div>
            <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Device Type</label>
            <select name="type" id="type" required
                    class="w-full p-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="PC" <?= $device['type'] == 'PC' ? 'selected' : '' ?>>PC</option>
                <option value="Printer" <?= $device['type'] == 'Printer' ? 'selected' : '' ?>>Printer</option>
                <option value="Switch" <?= $device['type'] == 'Switch' ? 'selected' : '' ?>>Switch</option>
            </select>
        </div>

        <!-- Location -->
        <div>
			<label for="location" class="block text-sm font-medium text-gray-700 mb-1">Location</label>
			<select name="location" id="location" required
					class="w-full p-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500">
				<?php foreach ($locations as $loc): ?>
					<option value="<?= htmlspecialchars($loc) ?>"
						<?= $device['location'] == $loc ? 'selected' : '' ?>>
						<?= htmlspecialchars($loc) ?>
					</option>
				<?php endforeach; ?>
			</select>
			<div class="text-right mt-2">
				<a href="add_location.php"
				   class="text-blue-600 hover:underline text-sm font-medium inline-flex items-center">
					➕ Add New Location
				</a>
			</div>
		</div>


        <!-- IP Address -->
        <div>
            <label for="ip_address" class="block text-sm font-medium text-gray-700 mb-1">IP Address</label>
            <input type="text" name="ip_address" id="ip_address" required
                   value="<?= htmlspecialchars($device['ip_address']) ?>"
                   class="w-full p-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        <!-- Buttons -->
        <div class="flex justify-end gap-4 pt-4">
            <button type="submit"
                    class="bg-green-600 text-white font-semibold px-6 py-2 rounded-xl shadow hover:bg-green-700 transition">
                ✅ Update Device
            </button>
            <a href="admin.php"
               class="bg-gray-600 text-white font-semibold px-6 py-2 rounded-xl shadow hover:bg-gray-700 transition text-center">
                🔙 Back
            </a>
        </div>
    </form>
</body>
</html>
