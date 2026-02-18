<?php
// MODIFIED: Changed from 'localhost' to use MYSQL_HOST env var or 'mysql' service name
// Reason: Docker containers cannot connect to 'localhost' - must use service name from docker-compose.yml
// Original: $conn = new mysqli("localhost", "root", "", "live_network");
$conn = new mysqli(getenv('MYSQL_HOST') ?: 'mysql', "root", "", "live_network");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_location = trim($_POST['location']);

    if (!empty($new_location)) {
        // Check if location already exists
        $stmt = $conn->prepare("SELECT COUNT(*) FROM locations WHERE name = ?");
        $stmt->bind_param("s", $new_location);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();

        if ($count == 0) {
            // Insert new location
            $color_class = $_POST['color_class'];

			$stmt = $conn->prepare("INSERT INTO locations (name, color_class) VALUES (?, ?)");
			$stmt->bind_param("ss", $new_location, $color_class);

       
            $stmt->execute();
            $stmt->close();
            $success = "✅ Location added successfully!";
        } else {
            $error = "⚠️ Location already exists.";
        }
    } else {
        $error = "⚠️ Please enter a valid location name.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add New Location</title>
    <link href="../assets/css/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 p-6">

    <div class="max-w-md mx-auto bg-white p-6 rounded-xl shadow-md mt-10">
        <h1 class="text-xl font-bold text-gray-800 mb-4">➕ Add New Location</h1>

        <?php if (!empty($success)): ?>
            <div class="bg-green-100 text-green-800 px-4 py-2 mb-4 rounded-md border border-green-300">
                <?= $success ?>
            </div>
        <?php elseif (!empty($error)): ?>
            <div class="bg-red-100 text-red-800 px-4 py-2 mb-4 rounded-md border border-red-300">
                <?= $error ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-4">
            <div>
                <label for="location" class="block text-sm font-medium text-gray-700 mb-1">Location Name</label>
                <input type="text" id="location" name="location" placeholder="e.g. Office E"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none"
                       required>
            </div><br>
			
			<div>
				<label for="color_class" class="block text-sm font-medium text-gray-700 mb-1">Tailwind Color Class</label>
				<select name="color_class" id="color_class"
					class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none" required>
					<option value="bg-red-100">Red 100</option>
					<option value="bg-red-200">Red 200</option>
					<option value="bg-orange-100">Orange 100</option>
					<option value="bg-orange-200">Orange 200</option>
					<option value="bg-amber-100">Amber 100</option>
					<option value="bg-amber-200">Amber 200</option>
					<option value="bg-yellow-100">Yellow 100</option>
					<option value="bg-yellow-200">Yellow 200</option>
					<option value="bg-lime-100">Lime 100</option>
					<option value="bg-lime-200">Lime 200</option>
					<option value="bg-green-100">Green 100</option>
					<option value="bg-green-200">Green 200</option>
					<option value="bg-emerald-100">Emerald 100</option>
					<option value="bg-emerald-200">Emerald 200</option>
					<option value="bg-teal-100">Teal 100</option>
					<option value="bg-teal-200">Teal 200</option>
					<option value="bg-cyan-100">Cyan 100</option>
					<option value="bg-cyan-200">Cyan 200</option>
					<option value="bg-blue-100">Blue 100</option>
					<option value="bg-blue-200">Blue 200</option>
					<option value="bg-indigo-100">Indigo 100</option>
					<option value="bg-indigo-200">Indigo 200</option>
					<option value="bg-violet-100">Violet 100</option>
					<option value="bg-violet-200">Violet 200</option>
					<option value="bg-purple-100">Purple 100</option>
					<option value="bg-purple-200">Purple 200</option>
					<option value="bg-pink-100">Pink 100</option>
					<option value="bg-pink-200">Pink 200</option>
					<option value="bg-rose-100">Rose 100</option>
					<option value="bg-rose-200">Rose 200</option>

				</select>
			</div><br>


            <div class="flex justify-between items-center">
                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded-lg shadow">
                    ➕ Add Location
                </button>
                <a href="admin.php"
                   class="text-blue-600 hover:underline text-sm font-medium">
                    🔙 Back
                </a>
            </div>
        </form>
    </div>

</body>
</html>
