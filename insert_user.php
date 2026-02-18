<?php
// MODIFIED: User insertion script for database testing
// Reason: Direct MySQL insertion via command line may fail due to escaping/formatting issues
// This PHP script provides reliable user insertion with proper error handling

echo "=== User Insertion Script ===\n\n";

// Database connection
$host = 'mysql'; // Docker service name
$user = 'root';
$password = '';
$dbname = 'nshare_lite_db';

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error . "\n");
}

echo "✅ Connected to database\n\n";

// Insert new user
$new_id = 102;
$new_username = 'testuser';
$new_display_name = 'Test User';
$new_password = password_hash('password', PASSWORD_BCRYPT); // Password: 'password'
$new_role = 'user';
$new_is_active = 1;

echo "Inserting user:\n";
echo "  ID: $new_id\n";
echo "  Username: $new_username\n";
echo "  Display Name: $new_display_name\n";
echo "  Role: $new_role\n";
echo "  Password hash: $new_password\n\n";

// Check if username already exists
$check_sql = "SELECT id FROM users WHERE username = ?";
$stmt = $conn->prepare($check_sql);
$stmt->bind_param("s", $new_username);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo "⚠️  Username '$new_username' already exists. Skipping insertion.\n";
    $stmt->close();
    $conn->close();
    exit;
}
$stmt->close();

// Insert the new user
$insert_sql = "INSERT INTO users (id, username, display_name, password, role, is_active) VALUES (?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($insert_sql);

if (!$stmt) {
    die("❌ Prepare failed: " . $conn->error . "\n");
}

$stmt->bind_param("issssi", $new_id, $new_username, $new_display_name, $new_password, $new_role, $new_is_active);

if ($stmt->execute()) {
    echo "✅ User inserted successfully!\n\n";
    echo "Login credentials:\n";
    echo "  Username: $new_username\n";
    echo "  Password: password\n";
} else {
    echo "❌ Insert failed: " . $stmt->error . "\n";
}

$stmt->close();

// Verify insertion
echo "\n--- Verifying insertion ---\n";
$verify_sql = "SELECT id, username, display_name, role FROM users WHERE username = ?";
$stmt = $conn->prepare($verify_sql);
$stmt->bind_param("s", $new_username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo "✅ User found in database:\n";
    echo "  ID: " . $row['id'] . "\n";
    echo "  Username: " . $row['username'] . "\n";
    echo "  Display Name: " . $row['display_name'] . "\n";
    echo "  Role: " . $row['role'] . "\n";
} else {
    echo "❌ User not found in database after insertion\n";
}

$stmt->close();
$conn->close();

echo "\n=== Done ===\n";
?>
