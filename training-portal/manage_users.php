<?php
include 'db_connect.php';
include 'admin/session_check.php'; // Admin session check

// Handle users activation/deactivation
if (isset($_GET['toggle_active'])) {
    $idToToggle = intval($_GET['toggle_active']);
    $currentStatusQuery = $conn->prepare("SELECT is_active FROM users WHERE id = ?");
    $currentStatusQuery->bind_param("i", $idToToggle);
    $currentStatusQuery->execute();
    $result = $currentStatusQuery->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $newStatus = ($row['is_active'] == 1) ? 0 : 1; // Toggle status
        $updateStatusQuery = $conn->prepare("UPDATE users SET is_active = ? WHERE id = ?");
        $updateStatusQuery->bind_param("ii", $newStatus, $idToToggle);
        if ($updateStatusQuery->execute()) {
            echo "<script>alert('User status updated successfully.'); window.location.href='manage_users.php';</script>";
        } else {
            echo "<script>alert('Error updating status.');</script>";
        }
    }
}

// Handle users role update
if (isset($_GET['update_role'])) {
    $idToUpdate = intval($_GET['update_role']);
    $newRole = ($_GET['role'] == 'trainee') ? 'user' : 'trainee'; // Toggle role

    $updateRoleQuery = $conn->prepare("UPDATE users SET role = ? WHERE id = ?");
    $updateRoleQuery->bind_param("si", $newRole, $idToUpdate);
    if ($updateRoleQuery->execute()) {
        echo "<script>alert('User role updated to {$newRole} successfully.'); window.location.href='manage_users.php';</script>";
    } else {
        echo "<script>alert('Error updating role.');</script>";
    }
}

// Handle new users addition
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_user'])) {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $role = $_POST['role'];
    $isActive = 1; // Active by default

    // Insert new users
    $insertQuery = $conn->prepare("INSERT INTO users (username, password, role, is_active) VALUES (?, ?, ?, ?)");
    $insertQuery->bind_param("sssi", $username, $password, $role, $isActive);
    if ($insertQuery->execute()) {
        echo "<script>alert('New users added successfully.'); window.location.href='manage_users.php';</script>";
    } else {
        echo "<script>alert('Error adding new users.');</script>";
    }
}

// Handle users deletion
if (isset($_GET['delete_user'])) {
    $idToDelete = intval($_GET['delete_user']);
    
    $deleteQuery = $conn->prepare("DELETE FROM users WHERE id = ?");
    $deleteQuery->bind_param("i", $idToDelete);
    if ($deleteQuery->execute()) {
        echo "<script>alert('User deleted successfully.'); window.location.href='manage_users.php';</script>";
    } else {
        echo "<script>alert('Error deleting users.');</script>";
    }
}

// Handle users edit (show users details in a form)
if (isset($_GET['edit_user'])) {
    $idToEdit = intval($_GET['edit_user']);
    
    // Fetch users data to populate the form
    $userQuery = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $userQuery->bind_param("i", $idToEdit);
    $userQuery->execute();
    $userData = $userQuery->get_result()->fetch_assoc();
}

// Handle users edit (submit the edited user details)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_user'])) {
    $idToEdit = intval($_POST['edit_user']); // User ID from the hidden input
    $username = $_POST['username']; // New username
    $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_BCRYPT) : null; // New password (null if empty)
    $role = $_POST['role']; // New role

    // Update query
    if ($password) {
        // Update both username and password
        $updateQuery = $conn->prepare("UPDATE users SET username = ?, password = ?, role = ? WHERE id = ?");
        $updateQuery->bind_param("sssi", $username, $password, $role, $idToEdit);
    } else {
        // Update only username and role, keep the password unchanged
        $updateQuery = $conn->prepare("UPDATE users SET username = ?, role = ? WHERE id = ?");
        $updateQuery->bind_param("ssi", $username, $role, $idToEdit);
    }

    if ($updateQuery->execute()) {
        echo "<script>alert('User updated successfully.'); window.location.href='manage_users.php';</script>";
    } else {
        echo "<script>alert('Error updating user.');</script>";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Users</title>
  <link href="tailwind.min.css" rel="stylesheet">
  <style>
    
	footer {
		background-color: #002147;
		color: white;
		text-align: center;
		padding: 15px 0;
		position: fixed;
		bottom: 0;
		width: 100%;
    }

  </style>
</head>
<body class="bg-gray-100 p-8">
<div class="mt-6 text-right">
	  <a href="index.php" class="text-blue-700 underline block">⬅ Back to Home</a>
	</div>

  <div class="max-w-4xl mx-auto bg-white p-6 rounded shadow">
    <h1 class="text-2xl font-bold text-blue-900 mb-4">User Management</h1>

    <!-- Add New User Form -->
    <form action="manage_users.php" method="POST" class="mb-6">
      <h2 class="text-xl font-semibold mb-2">Add New User</h2>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block mb-1 font-semibold">Username</label>
          <input type="text" name="username" class="w-full p-2 border rounded" required>
        </div>
        <div>
          <label class="block mb-1 font-semibold">Password</label>
          <input type="password" name="password" class="w-full p-2 border rounded" required>
        </div>
        <div class="w-full">
          <label class="block mb-1 font-semibold">Role</label>
          <select name="role" class="w-full p-2 border rounded" required>
            <option value="trainee">Trainee</option>
            <option value="user">User</option>
          </select>
        </div>
      </div>
      <button type="submit" name="add_user" class="mt-4 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Add User</button>
    </form>
	
	

	<br>
    <!-- Edit User Form (only shown if edit_user query param is set) -->
    <?php if (isset($userData)): ?>
    <form action="manage_users.php" method="POST" class="mb-6">
	  <h2 class="text-xl font-semibold mb-2">Edit User</h2>
	  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
		<div>
		  <label class="block mb-1 font-semibold">Username</label>
		  <input type="text" name="username" value="<?php echo $userData['username']; ?>" class="w-full p-2 border rounded" required>
		</div>
		<div>
		  <label class="block mb-1 font-semibold">Password</label>
		  <input type="password" name="password" class="w-full p-2 border rounded">
		</div>
		<div class="w-full">
		  <label class="block mb-1 font-semibold">Role</label>
		  <select name="role" class="w-full p-2 border rounded" required>
			<option value="trainee" <?php echo ($userData['role'] == 'trainee') ? 'selected' : ''; ?>>Trainee</option>
			<option value="user" <?php echo ($userData['role'] == 'user') ? 'selected' : ''; ?>>User</option>
		  </select>
		</div>
	  </div>
	  <input type="hidden" name="edit_user" value="<?php echo $userData['id']; ?>"> <!-- Hidden field for user ID -->
	  <button type="submit" class="mt-4 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Update User</button>
	</form>

    <?php endif; ?>

    <!-- Display Existing Users -->
    <h2 class="text-xl font-semibold mb-2">Existing Users</h2>
    <table class="w-full table-auto border-collapse border border-gray-300">
      <thead>
        <tr class="bg-gray-200">
          <th class="border px-4 py-2">ID</th>
          <th class="border px-4 py-2">Username</th>
          <th class="border px-4 py-2">Role</th>
          <th class="border px-4 py-2">Status</th>
          <th class="border px-4 py-2">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $user = $conn->query("SELECT * FROM users");
        while ($users = $user->fetch_assoc()) {
            $status = ($users['is_active'] == 1) ? 'Active' : 'Disabled';
            $statusClass = ($users['is_active'] == 1) ? 'bg-green-600' : 'bg-red-600';
            echo "<tr>
                    <td class='border px-4 py-2'>{$users['id']}</td>
                    <td class='border px-4 py-2'>{$users['username']}</td>
                    <td class='border px-4 py-2'>{$users['role']}</td>
                    <td class='border px-4 py-2 {$statusClass} text-white'>{$status}</td>
                    <td class='border px-4 py-2'>
                      <a href='?toggle_active={$users['id']}' onclick='return confirm(\"Are you sure you want to change the status?\")' class='text-yellow-500 hover:underline'>Toggle Status</a> | 
                      <a href='?update_role={$users['id']}&role={$users['role']}' class='text-blue-500 hover:underline'>Change Role</a> | 
                      <a href='?edit_user={$users['id']}' class='text-blue-500 hover:underline'>Edit</a> | 
                      <a href='?delete_user={$users['id']}' onclick='return confirm(\"Are you sure you want to delete this users?\")' class='text-red-500 hover:underline'>Delete</a>
                    </td>
                  </tr>";
        }
        ?>
      </tbody>
    </table>
	
  </div>
  <!-- Footer -->
<footer>
    &copy; 2025 NATMS | NAVAL AVIATION TRAINING MANAGEMENT SYSTEM
</footer>

</body>
</html>
