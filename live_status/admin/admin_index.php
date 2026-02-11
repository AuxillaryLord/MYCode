<?php
session_start();
include_once '../helpers.php';
$conn = new mysqli("localhost", "root", "", "live_network");
// Read filter values from query string
$typeFilter = $_GET['type'] ?? '';
$locationFilter = $_GET['location'] ?? '';
$search = $_GET['search'] ?? '';

// ✅ Return only partial content if requested via AJAX
if (isset($_GET['partial']) && $_GET['partial'] == 1) {
    include 'admin_partial.php';
    exit;
}
// Fetch location options
$locations = $conn->query("SELECT name FROM locations ORDER BY name ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Live Network Status</title>
    <link href="../assets/css/tailwind.min.css" rel="stylesheet">
    <script>
        function fetchAndUpdateStatus() {
            // Ping devices
            fetch('status.php')
                .then(res => res.json())
                .then(() => {
                    // Refresh device list
                    fetch('admin_index.php?partial=1')
                        .then(res => res.text())
                        .then(html => {
                            document.getElementById('deviceContainer').innerHTML = html;
                            document.getElementById('lastUpdated').textContent = new Date().toLocaleTimeString();
                        });
                });
        }

        setInterval(fetchAndUpdateStatus, 30000); // every 30 seconds
        window.onload = fetchAndUpdateStatus;
    </script>
</head>
<body class="bg-gray-100 p-6">
	<div class="flex justify-end mb-4">
		
			<a href="admin.php" class="bg-blue-600 text-white px-4 py-2 rounded-xl hover:bg-blue-700">🔑 Admin Dashboard</a>
		
		
	</div>

    <h1 class="text-3xl font-bold text-center mb-2">🖥️ Live Network Status</h1>
    <p class="text-center text-sm text-gray-600 mb-6">
        Last Updated: <span id="lastUpdated">Loading...</span>

    </p>
    <form method="GET" class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">
        <select name="type" class="p-2 rounded-xl border border-gray-300">
            <option value="">All Types</option>
            <option value="PC" <?= $typeFilter == 'PC' ? 'selected' : '' ?>>PC</option>
            <option value="Printer" <?= $typeFilter == 'Printer' ? 'selected' : '' ?>>Printer</option>
            <option value="Switch" <?= $typeFilter == 'Switch' ? 'selected' : '' ?>>Switch</option>
        </select>

        <select name="location" class="p-2 rounded-xl border border-gray-300">
					<option value="">All Locations</option>
            <?php foreach ($locations as $loc): ?>
					
					<option value="<?= htmlspecialchars($loc['name']) ?>">
						<?= htmlspecialchars($loc['name']) ?>
					</option>
				<?php endforeach; ?>
        </select>

        <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search by name..." class="p-2 rounded-xl border border-gray-300" />

        <button type="submit" class="bg-blue-600 text-white p-2 rounded-xl hover:bg-blue-700">Apply Filters</button>
    </form>

    <div id="deviceContainer">
        <?php include 'admin_partial.php'; ?>
    </div>

</body>
</html>
