<?php
session_start();
if (!isset($_SESSION['username']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../niatcloud/login.php");
    exit;
}
$username = $_SESSION['username'];
$role = $_SESSION['role'];


include_once '../helpers.php';
$conn = new mysqli("localhost", "root", "", "live_network");

// Fetch devices
$deviceResult = $conn->query("SELECT * FROM devices ORDER BY type, name");
$devices = [];
while ($row = $deviceResult->fetch_assoc()) {
    $devices[] = $row;
}


$conn->close();
?>

<?php if (isset($_GET['status']) && $_GET['status'] === 'deleted'): ?>
    <div class="bg-green-100 text-green-800 px-4 py-2 rounded-xl shadow mb-4 border border-green-300">
        ✅ Device deleted successfully.
    </div>
<?php endif; ?>

<?php if (isset($_GET['status']) && $_GET['status'] === 'added'): ?>
    <div class="bg-green-100 text-green-800 px-4 py-2 rounded-xl shadow mb-4 border border-green-300">
        ✅ Device added successfully.
    </div>
<?php endif; ?>

<?php if (isset($_GET['status']) && $_GET['status'] === 'edited'): ?>
    <div class="bg-green-100 text-green-800 px-4 py-2 rounded-xl shadow mb-4 border border-green-300">
        ✅ Device updated successfully.
    </div>
<?php endif; ?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link href="../assets/css/tailwind.min.css" rel="stylesheet">
    <script>
        function showSection(sectionId) {
            document.getElementById('device-section').classList.add('hidden');
           
            document.getElementById(sectionId).classList.remove('hidden');
        }
    </script>
</head>
<body class="bg-gray-100 p-6 min-h-screen">

    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">🛠️ Admin Dashboard</h1>
        <a href="../../classroom_monitoring/routine/logout_trg.php" class="bg-red-600 text-white px-4 py-2 rounded-xl hover:bg-red-700">🚪 Logout</a>
    </div>

    <!-- Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div onclick="showSection('device-section')" class="bg-white shadow-xl rounded-xl p-6 cursor-pointer hover:bg-gray-100 transition">
            <h2 class="text-xl font-semibold">🖥️ Manage Devices</h2>
            <p class="text-sm text-gray-500">Add, edit, or delete IT assets</p>
        </div>
       
		
		<a href="admin_index.php" class="block">
			<div class="bg-white shadow-xl rounded-xl p-6 cursor-pointer hover:bg-gray-100 transition">
				<h2 class="text-xl font-semibold">👥 Network Status</h2>
				<p class="text-sm text-gray-500">Network details with IP Address</p>
			</div>
		</a>

    </div>

    <!-- Manage Devices Section -->
    <div id="device-section" class="hidden">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-bold text-blue-700">💻 IT Assets</h2>
            <a href="add_device.php" class="bg-blue-600 text-white px-4 py-2 rounded-xl hover:bg-blue-700">➕ Add Device</a>
        </div>

        <table class="w-full table-auto border-collapse bg-white shadow rounded-xl overflow-hidden">
            <thead class="bg-gray-200">
                <tr>
                    <th class="p-3 text-left">Name</th>
                    <th class="p-3 text-left">Type</th>
                    <th class="p-3 text-left">Location</th>
                    <th class="p-3 text-left">IP Address</th>
                    <th class="p-3 text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($devices as $device): ?>
                    <tr class="border-t hover:bg-gray-50">
                        <td class="p-3"><?= htmlspecialchars($device['name']) ?></td>
                        <td class="p-3"><?= htmlspecialchars($device['type']) ?></td>
                        <td class="p-3"><?= htmlspecialchars($device['location']) ?></td>
                        <td class="p-3"><?= htmlspecialchars($device['ip_address']) ?></td>
                        <td class="p-3 flex gap-3">
                            <a href="edit_device.php?id=<?= $device['id'] ?>" class="bg-blue-600 text-white px-4 py-2 rounded-xl hover:bg-blue-700">✏️ Edit</a>
                            <a href="delete_device.php?id=<?= $device['id'] ?>" class="bg-red-600 text-white px-4 py-2 rounded-xl hover:bg-red-700" onclick="return confirm('Are you sure?')">❌ Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    


        

</body>
</html>
