<?php
/**
 * MODIFIED: Created password debug and testing page
 * Reason: Diagnose "incorrect password" authentication errors
 * Original behavior: No way to debug password hash mismatches
 * New behavior: Test page to verify passwords, hash values, and authentication
 */

session_start();
include '../niatcloud/php/db_connect.php';

// MODIFIED: Check database connection
// Reason: Verify database is accessible before testing passwords
$db_status = $conn ? "✅ Connected" : "❌ Failed";

// MODIFIED: Retrieve all users for testing
// Reason: Show available test accounts and their hash formats
$users = [];
$sql = "SELECT id, username, display_name, password, role, is_active FROM users ORDER BY role";
$result = $conn->query($sql);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}

// MODIFIED: Test specific username/password combination
// Reason: Allow manual testing of authentication
$test_results = [];
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $test_username = trim($_POST['test_username'] ?? '');
    $test_password = $_POST['test_password'] ?? '';
    
    if ($test_username && $test_password) {
        // Find user in database
        $sql = "SELECT * FROM users WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $test_username);
        $stmt->execute();
        $res = $stmt->get_result();
        
        if ($res->num_rows === 1) {
            $user = $res->fetch_assoc();
            $hash = $user['password'];
            $password_match = password_verify($test_password, $hash);
            
            // MODIFIED: Detailed password verification test
            // Reason: Show exactly what's happening in authentication
            $test_results = [
                'found' => true,
                'username' => $user['username'],
                'display_name' => $user['display_name'],
                'role' => $user['role'],
                'is_active' => $user['is_active'],
                'password_hash' => $hash,
                'password_match' => $password_match,
                'message' => $password_match ? '✅ Password CORRECT' : '❌ Password INCORRECT'
            ];
        } else {
            $test_results = [
                'found' => false,
                'username' => $test_username,
                'message' => '❌ User not found in database'
            ];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NATMS - Password Debug Page</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0A192F 0%, #1D3557 100%);
            color: #fff;
            padding: 20px;
            min-height: 100vh;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            padding: 20px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            border: 2px solid #FFD700;
        }

        .header h1 {
            color: #FFD700;
            font-size: 28px;
            margin-bottom: 10px;
        }

        .status-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
            margin-bottom: 30px;
        }

        .status-card {
            background: rgba(255, 255, 255, 0.1);
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #FFD700;
        }

        .status-card h3 {
            color: #FFD700;
            font-size: 14px;
            margin-bottom: 8px;
        }

        .status-card p {
            font-size: 16px;
            color: #fff;
        }

        .test-form {
            background: rgba(255, 255, 255, 0.95);
            padding: 25px;
            border-radius: 10px;
            margin-bottom: 30px;
            color: #001f3f;
        }

        .test-form h2 {
            color: #001f3f;
            margin-bottom: 20px;
            font-size: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #001f3f;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 2px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
            font-family: monospace;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #002147;
            box-shadow: 0 0 5px rgba(0, 33, 71, 0.3);
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .submit-btn {
            background-color: #002147;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .submit-btn:hover {
            background-color: #001233;
        }

        .test-result {
            background: rgba(255, 255, 255, 0.95);
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
            color: #001f3f;
            border-left: 5px solid #4CAF50;
        }

        .test-result.error {
            border-left-color: #f44336;
        }

        .test-result h3 {
            color: #002147;
            margin-bottom: 15px;
            font-size: 18px;
        }

        .result-item {
            display: grid;
            grid-template-columns: 150px 1fr;
            gap: 15px;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
            font-family: monospace;
            font-size: 13px;
        }

        .result-item:last-child {
            border-bottom: none;
        }

        .result-label {
            font-weight: 600;
            color: #002147;
        }

        .result-value {
            word-break: break-all;
            color: #555;
        }

        .users-table {
            background: rgba(255, 255, 255, 0.95);
            padding: 20px;
            border-radius: 10px;
            margin-top: 30px;
            color: #001f3f;
            overflow-x: auto;
        }

        .users-table h2 {
            color: #001f3f;
            margin-bottom: 15px;
            font-size: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        table thead {
            background-color: #002147;
            color: white;
        }

        table th {
            padding: 12px;
            text-align: left;
            font-weight: 600;
        }

        table td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
        }

        table tbody tr:hover {
            background-color: #f5f5f5;
        }

        .status-active {
            color: #4CAF50;
            font-weight: 600;
        }

        .status-inactive {
            color: #f44336;
            font-weight: 600;
        }

        .quick-test {
            background: #fff3cd;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            color: #856404;
            border-left: 4px solid #ffc107;
        }

        .quick-test h4 {
            margin-bottom: 10px;
        }

        .quick-test button {
            background: #ffc107;
            color: #000;
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 600;
            margin: 5px 5px 5px 0;
        }

        .quick-test button:hover {
            background: #e0a800;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h1>🔧 NATMS Password Debug & Test Page</h1>
        <p>Diagnose authentication issues and test user accounts</p>
    </div>

    <!-- Status Grid -->
    <div class="status-grid">
        <div class="status-card">
            <h3>Database Status</h3>
            <p><?php echo $db_status; ?></p>
        </div>
        <div class="status-card">
            <h3>Total Users</h3>
            <p><?php echo count($users); ?> accounts found</p>
        </div>
        <div class="status-card">
            <h3>Active Users</h3>
            <p><?php echo count(array_filter($users, fn($u) => $u['is_active'])); ?> active</p>
        </div>
        <div class="status-card">
            <h3>Test Instructions</h3>
            <p>Use any username below with its password</p>
        </div>
    </div>

    <!-- Test Form -->
    <div class="test-form">
        <h2>🔐 Test Password Authentication</h2>
        
        <div class="quick-test">
            <h4>⚡ Quick Test Options:</h4>
            <form method="POST" style="display: inline;">
                <input type="hidden" name="test_username" value="admin">
                <input type="hidden" name="test_password" value="admin123">
                <button type="submit">Test: admin / admin123</button>
            </form>
            <form method="POST" style="display: inline;">
                <input type="hidden" name="test_username" value="training">
                <input type="hidden" name="test_password" value="training123">
                <button type="submit">Test: training / training123</button>
            </form>
            <form method="POST" style="display: inline;">
                <input type="hidden" name="test_username" value="500">
                <input type="hidden" name="test_password" value="instructor123">
                <button type="submit">Test: 500 / instructor123</button>
            </form>
            <form method="POST" style="display: inline;">
                <input type="hidden" name="test_username" value="trg01">
                <input type="hidden" name="test_password" value="password123">
                <button type="submit">Test: trg01 / password123</button>
            </form>
        </div>

        <form method="POST">
            <div class="form-row">
                <div class="form-group">
                    <label for="test_username">Username</label>
                    <input type="text" id="test_username" name="test_username" placeholder="Enter username" value="<?php echo htmlspecialchars($_POST['test_username'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="test_password">Password</label>
                    <input type="password" id="test_password" name="test_password" placeholder="Enter password">
                </div>
            </div>
            <button type="submit" class="submit-btn">🔍 Test Password</button>
        </form>
    </div>

    <!-- Test Results -->
    <?php if (!empty($test_results)): ?>
    <div class="test-result <?php echo !$test_results['found'] || ($test_results['found'] && !$test_results['password_match']) ? 'error' : ''; ?>">
        <h3><?php echo htmlspecialchars($test_results['message']); ?></h3>
        
        <?php if ($test_results['found']): ?>
            <div class="result-item">
                <div class="result-label">Username:</div>
                <div class="result-value"><?php echo htmlspecialchars($test_results['username']); ?></div>
            </div>
            <div class="result-item">
                <div class="result-label">Display Name:</div>
                <div class="result-value"><?php echo htmlspecialchars($test_results['display_name'] ?: 'N/A'); ?></div>
            </div>
            <div class="result-item">
                <div class="result-label">Role:</div>
                <div class="result-value"><?php echo htmlspecialchars($test_results['role']); ?></div>
            </div>
            <div class="result-item">
                <div class="result-label">Active Status:</div>
                <div class="result-value <?php echo $test_results['is_active'] ? 'status-active' : 'status-inactive'; ?>">
                    <?php echo $test_results['is_active'] ? '✅ Active' : '❌ Inactive'; ?>
                </div>
            </div>
            <div class="result-item">
                <div class="result-label">Password Hash:</div>
                <div class="result-value"><?php echo htmlspecialchars(substr($test_results['password_hash'], 0, 40)); ?>...</div>
            </div>
            <div class="result-item">
                <div class="result-label">Verification:</div>
                <div class="result-value <?php echo $test_results['password_match'] ? 'status-active' : 'status-inactive'; ?>">
                    <?php echo $test_results['password_match'] ? '✅ Hash matches password' : '❌ Hash does NOT match password'; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- All Users Table -->
    <div class="users-table">
        <h2>📋 All Test User Accounts</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Display Name</th>
                    <th>Role</th>
                    <th>Active</th>
                    <th>Password Hash (First 50 chars)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                <tr>
                    <td><?php echo $user['id']; ?></td>
                    <td style="font-weight: 600; font-family: monospace;"><?php echo htmlspecialchars($user['username']); ?></td>
                    <td><?php echo htmlspecialchars($user['display_name'] ?: 'N/A'); ?></td>
                    <td><?php echo htmlspecialchars($user['role']); ?></td>
                    <td <?php echo $user['is_active'] ? 'class="status-active"' : 'class="status-inactive"'; ?>>
                        <?php echo $user['is_active'] ? '✅ Yes' : '❌ No'; ?>
                    </td>
                    <td style="font-family: monospace; font-size: 11px;">
                        <?php echo htmlspecialchars(substr($user['password'], 0, 50)); ?>...
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Debug Info -->
    <div style="background: rgba(255, 255, 255, 0.1); padding: 20px; border-radius: 10px; margin-top: 30px; border: 2px solid #FFD700;">
        <h3 style="color: #FFD700; margin-bottom: 15px;">📖 How Authentication Works</h3>
        <ol style="line-height: 1.8;">
            <li><strong>User submits</strong> username + plaintext password via login form</li>
            <li><strong>Database lookup</strong> finds user by username</li>
            <li><strong>Hash verification</strong> uses password_verify(submitted_password, stored_hash)</li>
            <li><strong>If match succestrong> creates session and redirects to portals.php</li>
            <li><strong>If no match:</strong> shows "Incorrect password" error</li>
        </ol>

        <h3 style="color: #FFD700; margin-bottom: 15px; margin-top: 20px;">🔍 What This Debug Page Does</h3>
        <ul style="line-height: 1.8;">
            <li>Shows all users in database with their roles and active status</li>
            <li>Tests password verification in real time</li>
            <li>Shows the exact hash value for the user</li>
            <li>Confirms if password matches hash</li>
            <li>Helps identify if issue is in DB, hash, or verification logic</li>
        </ul>

        <h3 style="color: #FFD700; margin-bottom: 15px; margin-top: 20px;">💡 Troubleshooting</h3>
        <ul style="line-height: 1.8;">
            <li><strong>User not found?</strong> Check username spelling in the database</li>
            <li><strong>Password incorrect?</strong> Verify the exact hashed password is in DB</li>
            <li><strong>Account inactive?</strong> Check is_active column = 0</li>
            <li><strong>Still failing?</strong> Check your login form is POSTing username and password correctly</li>
        </ul>
    </div>

</div>

</body>
</html>
