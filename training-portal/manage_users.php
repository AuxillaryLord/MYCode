<?php

include 'db_connect.php';
include 'admin/session_check.php';

// Activate/deactivate user
if (isset($_GET['toggle_active'])) {
    $id = intval($_GET['toggle_active']);
    $stmt = $conn->prepare("UPDATE users SET is_active = NOT is_active WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: manage_users.php");
    exit;
}

// Update role
if (isset($_GET['update_role'], $_GET['role'])) {
    $id = intval($_GET['update_role']);
    $role = $_GET['role'];
    $validRoles = ['user','trainee','director','instructor','training','admin'];
    if (in_array($role, $validRoles)) {
        $stmt = $conn->prepare("UPDATE users SET role = ? WHERE id = ?");
        $stmt->bind_param("si", $role, $id);
        $stmt->execute();
        header("Location: manage_users.php");
        exit;
    }
}

// Add user
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_user'])) {
    $username = trim($_POST['username']);
    $password = password_hash(trim($_POST['password']), PASSWORD_BCRYPT);
    $role = trim($_POST['role']);
    $displayName = trim($_POST['display_name']);
    $isActive = 1;

    $stmt = $conn->prepare("INSERT INTO users (username, password, role, display_name, is_active) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssi", $username, $password, $role, $displayName, $isActive);
    $stmt->execute();

		if ($role === 'instructor') {
		$cconn = new mysqli("localhost", "root", "", "classroom_monitoring");

		// Check if instructor already exists by username
		$check = $cconn->prepare("SELECT id FROM instructors WHERE username = ?");
		$check->bind_param("s", $username);
		$check->execute();
		$result = $check->get_result();

		if ($result->num_rows === 0) {
			// Insert both name and username
			$insert = $cconn->prepare("INSERT INTO instructors (name, username) VALUES (?, ?)");
			$insert->bind_param("ss", $displayName, $username);
			$insert->execute();
		}
	}

    header("Location: manage_users.php?added=1");
    exit;
}

// Delete user
if (isset($_GET['delete_user'])) {
    $id = intval($_GET['delete_user']);

    // Fetch role and username before deleting
    $checkRole = $conn->prepare("SELECT role, username FROM users WHERE id = ?");
    $checkRole->bind_param("i", $id);
    $checkRole->execute();
    $result = $checkRole->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if ($user['role'] !== 'admin') {
            // If instructor, delete from instructors table first
            if ($user['role'] === 'instructor') {
                $cconn = new mysqli("localhost", "root", "", "classroom_monitoring");
                $deleteInstructor = $cconn->prepare("DELETE FROM instructors WHERE username = ?");
                $deleteInstructor->bind_param("s", $user['username']);
                $deleteInstructor->execute();
            }

            // Delete user from users table
            $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();

            header("Location: manage_users.php?deleted=1");
        } else {
            header("Location: manage_users.php?error=admin_delete_blocked");
        }
    } else {
        header("Location: manage_users.php?error=user_not_found");
    }
    exit;
}


// Edit User
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_user'])) {
    $id = intval($_POST['edit_user']);
    $username = $_POST['username'];
    $displayName = $_POST['display_name'];
    $role = $_POST['role'];
    $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_BCRYPT) : null;

    // Fetch existing display_name before updating
    $getOld = $conn->prepare("SELECT display_name FROM users WHERE id = ?");
    $getOld->bind_param("i", $id);
    $getOld->execute();
    $oldResult = $getOld->get_result();
    $oldData = $oldResult->fetch_assoc();
    $oldDisplayName = $oldData['display_name'];

    // Update users table
    if ($password) {
        $stmt = $conn->prepare("UPDATE users SET username = ?, password = ?, role = ?, display_name = ? WHERE id = ?");
        $stmt->bind_param("ssssi", $username, $password, $role, $displayName, $id);
    } else {
        $stmt = $conn->prepare("UPDATE users SET username = ?, role = ?, display_name = ? WHERE id = ?");
        $stmt->bind_param("sssi", $username, $role, $displayName, $id);
    }
    $stmt->execute();

    // If role is instructor, update instructors table
    // If role is instructor, update instructors table using username as key
	if ($role === 'instructor') {
		$cconn = new mysqli("localhost", "root", "", "classroom_monitoring");

		// Check if instructor with the given username already exists
		$check = $cconn->prepare("SELECT id FROM instructors WHERE username = ?");
		$check->bind_param("s", $username);
		$check->execute();
		$result = $check->get_result();

		if ($result->num_rows > 0) {
			// Instructor exists, update name
			$update = $cconn->prepare("UPDATE instructors SET name = ? WHERE username = ?");
			$update->bind_param("ss", $displayName, $username);
			$update->execute();
		} else {
			// Instructor does not exist, insert new
			$insert = $cconn->prepare("INSERT INTO instructors (name, username) VALUES (?, ?)");
			$insert->bind_param("ss", $displayName, $username);
			$insert->execute();
		}
	}


    header("Location: manage_users.php?edited=1");

    exit;
}

if (isset($_GET['edit_user'])) {
    $editId = intval($_GET['edit_user']);
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $editId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $userData = $result->fetch_assoc();
    }
}
?>

<?php if (isset($_GET['deleted']) && $_GET['deleted'] == 1): ?>
  <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
    ✅ User details successfully deleted.
  </div>
<?php endif; ?>



<?php if (isset($_GET['edited']) && $_GET['edited'] == 1): ?>
  <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
    ✅ User details successfully edited.
  </div>
<?php endif; ?>

<?php if (isset($_GET['added']) && $_GET['added'] == 1): ?>
  <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
    ✅ User details successfully added.
  </div>
<?php endif; ?>

<?php if (isset($_GET['error']) && $_GET['error'] === 'admin_delete_blocked'): ?>
  <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
    ❌ Admin users cannot be deleted.
  </div>
<?php endif; ?>


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

  <div class="max-w-5xl mx-auto bg-white p-6 rounded shadow">
    <h1 class="text-2xl font-bold text-blue-900 mb-4">User Management</h1>

    <!-- Add New User Form -->
    <!-- Add New User Form -->
	<?php if (!isset($userData)): ?>
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
		<div>
		  <label class="block mb-1 font-semibold">Display Name</label>
		  <input type="text" name="display_name" class="w-full p-2 border rounded" required>
		</div>
		<div>
		  <label class="block mb-1 font-semibold">Role</label>
		  <select name="role" class="w-full p-2 border rounded" required>
			<option value="trainee">Trainee</option>
			<option value="user">User</option>
			<option value="admin">Admin</option>
			<option value="director">Director</option>
			<option value="instructor">Instructor</option>
			<option value="training">TMC</option>
		  </select>
		</div>
	  </div>
	  <button type="submit" name="add_user" class="mt-4 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Add User</button>
	</form>
	<?php endif; ?>

	
	

	
    <!-- Edit User Form (only shown if edit_user query param is set) -->
    <!-- Edit User Form -->
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
		<div>
		  <label class="block mb-1 font-semibold">Display Name</label>
		  <input type="text" name="display_name" value="<?php echo $userData['display_name']; ?>" class="w-full p-2 border rounded" required>
		</div>
		<div>
		  <label class="block mb-1 font-semibold">Role</label>
		  <select name="role" class="w-full p-2 border rounded" required>
			<option value="admin" <?php echo ($userData['role'] == 'admin') ? 'selected' : ''; ?>>Admin</option>
			<option value="user" <?php echo ($userData['role'] == 'user') ? 'selected' : ''; ?>>User</option>
			<option value="trainee" <?php echo ($userData['role'] == 'trainee') ? 'selected' : ''; ?>>Trainee</option>
			<option value="director" <?php echo ($userData['role'] == 'director') ? 'selected' : ''; ?>>Director</option>
			<option value="instructor" <?php echo ($userData['role'] == 'instructor') ? 'selected' : ''; ?>>Instructor</option>
			<option value="training" <?php echo ($userData['role'] == 'training') ? 'selected' : ''; ?>>TMC</option>
		  </select>
		</div>
	  </div>
	  <input type="hidden" name="edit_user" value="<?php echo $userData['id']; ?>">
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
		  <th class="border px-4 py-2">Display Name</th>
          <th class="border px-4 py-2">Role</th>
          <th class="border px-4 py-2">Status</th>
          <th class="border px-4 py-2">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php
		$roleLabels = [
			'admin' => 'Admin',
			'user' => 'User',
			'trainee' => 'Trainee',
			'director' => 'Director',
			'instructor' => 'Instructor',
			'training' => 'TMC'
		];

		$roles = ['admin', 'user', 'trainee', 'director', 'instructor', 'training'];

		$user = $conn->query("SELECT * FROM users");
		while ($users = $user->fetch_assoc()) {
			$status = ($users['is_active'] == 1) ? 'Active' : 'Disabled';
			$statusClass = ($users['is_active'] == 1) ? 'bg-green-600' : 'bg-red-600';

			// Get label for role
			$roleDisplay = isset($roleLabels[$users['role']]) ? $roleLabels[$users['role']] : ucfirst($users['role']);

			// Calculate next role
			$currentIndex = array_search($users['role'], $roles);
			$nextIndex = ($currentIndex + 1) % count($roles);
			$nextRole = $roles[$nextIndex];

			// Now echo everything cleanly
			echo "<tr>
				<td class='border px-4 py-2'>{$users['id']}</td>
				<td class='border px-4 py-2'>{$users['username']}</td>
				<td class='border px-4 py-2'>{$users['display_name']}</td> <!-- Display Name added here -->
				<td class='border px-4 py-2'>{$roleDisplay}</td>
				<td class='border px-4 py-2 {$statusClass} text-white'>{$status}</td>
				<td class='border px-4 py-2 space-x-2'>
				  <a href='?toggle_active={$users['id']}' onclick='return confirm(\"Are you sure you want to change the status?\")' class='text-yellow-500 hover:underline'>Toggle Status</a> |
				  <a href='?update_role={$users['id']}&role={$nextRole}' class='text-blue-500 hover:underline'>Change Role</a> |
				  <a href='?edit_user={$users['id']}' class='text-blue-500 hover:underline'>Edit</a> |
				  <a href='?delete_user={$users['id']}' onclick='return confirm(\"Are you sure you want to delete this user?\")' class='text-red-500 hover:underline'>Delete</a>
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
