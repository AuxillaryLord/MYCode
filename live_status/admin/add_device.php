<?php
// MODIFIED: Changed from 'localhost' to use MYSQL_HOST env var or 'mysql' service name
// Reason: Docker containers cannot connect to 'localhost' - must use service name from docker-compose.yml
// Original: $conn = new mysqli("localhost", "root", "", "live_network");
$conn = new mysqli(getenv('MYSQL_HOST') ?: 'mysql', "root", "", "live_network");

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $conn->real_escape_string($_POST['name']);
    $type = $conn->real_escape_string($_POST['type']);
    $ip_address = $conn->real_escape_string($_POST['ip_address']);

    // Determine location
    if (!empty($_POST['new_location'])) {
        $new_location = $conn->real_escape_string($_POST['new_location']);

        // Insert new location into 'locations' table if it doesn't exist
        $check = $conn->query("SELECT * FROM locations WHERE name = '$new_location'");
        if ($check->num_rows == 0) {
            $conn->query("INSERT INTO locations (name) VALUES ('$new_location')");
        }
        $location = $new_location;
    } else {
        $location = $conn->real_escape_string($_POST['location']);
    }

    // Insert device
    $sql = "INSERT INTO devices (name, type, location, ip_address, status) 
            VALUES ('$name', '$type', '$location', '$ip_address', 'unknown')";
    $conn->query($sql);
    $conn->close();

    header("Location: admin.php?status=added");
    exit();
}

// Fetch location options
$locations = $conn->query("SELECT name FROM locations ORDER BY name ASC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Device</title>
    <link href="../assets/css/tailwind.min.css" rel="stylesheet">
</head>
<body class="p-6 bg-gray-100">
    <h1 class="text-2xl font-bold mb-4">➕ Add New Device</h1>
    
    <form method="POST" class="bg-white p-6 rounded-2xl shadow-xl max-w-xl mx-auto space-y-6">
        <h2 class="text-2xl font-bold text-gray-800">➕ Add New Device</h2>

        <!-- Device Name -->
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Device Name</label>
            <input type="text" name="name" id="name" required
                   class="w-full p-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div><br>

        <!-- Device Type -->
        <div>
            <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Device Type</label>
            <select name="type" id="type" required
                    class="w-full p-3 border border-gray-300 rounded-xl appearance-none focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="" disabled selected hidden>Select Type</option>
                <option value="PC">PC</option>
                <option value="Printer">Printer</option>
                <option value="Switch">Switch</option>
            </select>
        </div><br>

        <!-- Location (existing) -->
        <!-- Location -->
        <div>
			<label for="location" class="block text-sm font-medium text-gray-700 mb-1">Location</label>
			<select name="location" id="location" required
					class="w-full p-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500">
				<?php foreach ($locations as $loc): ?>
					<option value="<?= htmlspecialchars($loc['name']) ?>">
						<?= htmlspecialchars($loc['name']) ?>
					</option>
				<?php endforeach; ?>

			</select>
			<div class="text-right mt-2">
				<a href="add_location.php"
				   class="text-blue-600 hover:underline text-sm font-medium inline-flex items-center">
					➕ Add New Location
				</a>
			</div>
		</div><br>

        <!-- IP Address -->
        <div>
            <label for="ip_address" class="block text-sm font-medium text-gray-700 mb-1">IP Address</label>
            <input type="text" name="ip_address" id="ip_address" required
                   class="w-full p-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div><br>

        <!-- Buttons -->
        <div class="flex justify-end gap-4 pt-4">
            <button type="submit"
                    class="bg-blue-600 text-white font-semibold px-6 py-2 rounded-xl shadow hover:bg-blue-700 transition">
                💾 Save Device
            </button>
            <a href="admin.php"
               class="bg-gray-600 text-white font-semibold px-6 py-2 rounded-xl shadow hover:bg-gray-700 transition text-center">
                🔙 Back
            </a>
        </div>
    </form>
</body>
</html>
