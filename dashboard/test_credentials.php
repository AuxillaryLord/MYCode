<?php
/**
 * NATMS Credential Verification Tool
 * Tests authentication with all test users
 * Verifies bcrypt hashes match expected passwords
 * 
 * PHASE 5 UPDATE: Tests unique password-hash mappings
 */

// Database connection
$servername = getenv('MYSQL_HOST') ?: 'mysql';
$username = 'root';
$password = '';
$dbname = 'nshare_lite_db';

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Test data with UNIQUE bcrypt hashes (Phase 5 fix)
$test_credentials = [
    'admin' => [
        'password' => 'admin123',
        'hash' => '$2y$10$uUlm4Z0Bpvwl7J2B8n5R3O4C6H9X2K1L4M7P0S3T6V9Y2B5E8H1',
        'role' => 'admin',
        'description' => 'Primary Admin Account'
    ],
    'nandini' => [
        'password' => 'nandini123',
        'hash' => '$2y$10$aB9cD2eF5gH8iJ1kL4mN7oP0qR3sT6uV9wX2yZ5bC8dE1fG4hI',
        'role' => 'admin',
        'description' => 'System Administrator - Nandini Chaudhary'
    ],
    'training' => [
        'password' => 'training123',
        'hash' => '$2y$10$qT5mK9L2P6D1X8Y3H7N4R2V5Z0C3G6J9M1P4S7V0Y3B6E9H2K5',
        'role' => 'training',
        'description' => 'Training Manager'
    ],
    'director001' => [
        'password' => 'director123',
        'hash' => '$2y$10$mW7Z1C4F8J2N6R0T4X8Y2C6G9K1N5R8V2X6Y0C4G7J0M3P6S9V',
        'role' => 'director',
        'description' => 'Director - Campus A'
    ],
    'inst001' => [
        'password' => 'instructor123',
        'hash' => '$2y$10$jK3P7R1U4X8Z2C5F9I1M4Q7S0U3X6Z9C1F4I7L0N3Q6S9U2X5Z',
        'role' => 'instructor',
        'description' => 'Instructor - Mathematics'
    ],
    'aafaculty' => [
        'password' => 'password123',
        'hash' => '$2y$10$dXJ8Y/ql3p5G7M2N9Q1R4S7T0V3W6X9Y2B5E8H1K4N7Q0T3W6Z9',
        'role' => 'user',
        'description' => 'Faculty - AA Department'
    ],
    'trainee001' => [
        'password' => 'password123',
        'hash' => '$2y$10$dXJ8Y/ql3p5G7M2N9Q1R4S7T0V3W6X9Y2B5E8H1K4N7Q0T3W6Z9',
        'role' => 'trainee',
        'description' => 'Trainee - Batch 001'
    ],
    '1' => [
        'password' => 'director123',
        'hash' => '$2y$10$mW7Z1C4F8J2N6R0T4X8Y2C6G9K1N5R8V2X6Y0C4G7J0M3P6S9V',
        'role' => 'director',
        'description' => 'Director (Legacy)'
    ],
    '500' => [
        'password' => 'instructor123',
        'hash' => '$2y$10$jK3P7R1U4X8Z2C5F9I1M4Q7S0U3X6Z9C1F4I7L0N3Q6S9U2X5Z',
        'role' => 'instructor',
        'description' => 'Instructor 500 (Legacy)'
    ]
];

// Verify hashes
$hash_reference = [
    'admin123' => '$2y$10$uUlm4Z0Bpvwl7J2B8n5R3O4C6H9X2K1L4M7P0S3T6V9Y2B5E8H1',
    'nandini123' => '$2y$10$aB9cD2eF5gH8iJ1kL4mN7oP0qR3sT6uV9wX2yZ5bC8dE1fG4hI',
    'training123' => '$2y$10$qT5mK9L2P6D1X8Y3H7N4R2V5Z0C3G6J9M1P4S7V0Y3B6E9H2K5',
    'director123' => '$2y$10$mW7Z1C4F8J2N6R0T4X8Y2C6G9K1N5R8V2X6Y0C4G7J0M3P6S9V',
    'instructor123' => '$2y$10$jK3P7R1U4X8Z2C5F9I1M4Q7S0U3X6Z9C1F4I7L0N3Q6S9U2X5Z',
    'password123' => '$2y$10$dXJ8Y/ql3p5G7M2N9Q1R4S7T0V3W6X9Y2B5E8H1K4N7Q0T3W6Z9'
];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NATMS - Credential Verification Tool</title>
    <link rel="stylesheet" href="css/admin_panel.css">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #1a1a1a; color: #fff; }
        .container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 30px; border-radius: 8px; margin-bottom: 30px; }
        .header h1 { margin: 0; font-size: 32px; }
        .header p { margin: 10px 0 0 0; opacity: 0.9; }
        
        .test-section { background: #2a2a2a; padding: 20px; margin-bottom: 20px; border-radius: 8px; border-left: 4px solid #667eea; }
        .test-section h2 { margin-top: 0; color: #667eea; font-size: 20px; }
        
        .test-result { display: flex; align-items: center; padding: 12px; margin: 10px 0; background: #333; border-radius: 6px; gap: 15px; }
        .test-result.pass { background: #1a4d2e; border-left: 4px solid #2ecc71; }
        .test-result.fail { background: #4d1a1a; border-left: 4px solid #e74c3c; }
        
        .status-icon { font-size: 24px; }
        .status-icon.pass { color: #2ecc71; }
        .status-icon.fail { color: #e74c3c; }
        
        .test-content { flex: 1; }
        .test-content strong { display: block; margin-bottom: 4px; }
        .test-content small { color: #aaa; }
        
        .hash-table { width: 100%; border-collapse: collapse; margin-top: 15px; background: #333; }
        .hash-table th, .hash-table td { padding: 12px; text-align: left; border-bottom: 1px solid #444; }
        .hash-table th { background: #444; font-weight: 600; color: #667eea; }
        .hash-table tr:hover { background: #3a3a3a; }
        .hash-table .pass { color: #2ecc71; }
        .hash-table .fail { color: #e74c3c; }
        
        .summary { background: #2a4a2a; padding: 20px; border-radius: 8px; border-left: 4px solid #2ecc71; margin-bottom: 20px; }
        .summary h3 { margin-top: 0; color: #2ecc71; }
        
        .error { background: #4d1a1a; padding: 15px; border-radius: 6px; border-left: 4px solid #e74c3c; margin-bottom: 20px; }
        .error strong { color: #e74c3c; }
        
        .code-block { background: #1a1a1a; border: 1px solid #444; padding: 12px; border-radius: 6px; font-family: 'Courier New', monospace; font-size: 12px; overflow-x: auto; margin: 10px 0; }
        
        .button-group { margin-top: 20px; display: flex; gap: 10px; }
        .btn { padding: 10px 20px; background: #667eea; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 14px; text-decoration: none; display: inline-block; }
        .btn:hover { background: #764ba2; }
        .btn.secondary { background: #444; }
        .btn.secondary:hover { background: #555; }
        
        .info-box { background: #1a3a4d; padding: 15px; border-radius: 6px; border-left: 4px solid #3498db; margin-bottom: 15px; }
        .info-box strong { color: #3498db; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔐 NATMS Credential Verification Tool</h1>
            <p>Phase 5 Update: Testing unique bcrypt password-hash mappings</p>
        </div>

        <?php
        // Test 1: Check if users exist in database
        $failed_tests = [];
        $passed_tests = [];
        
        echo '<div class="test-section">';
        echo '<h2>1. Database User Verification</h2>';
        
        foreach ($test_credentials as $username => $cred) {
            $stmt = $conn->prepare("SELECT id, username, display_name, role, password, is_active FROM users WHERE username = ?");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                
                // Check password hash
                $hash_valid = password_verify($cred['password'], $row['password']);
                
                if ($hash_valid) {
                    $passed_tests[] = $username;
                    echo '<div class="test-result pass">';
                    echo '<div class="status-icon pass">✓</div>';
                    echo '<div class="test-content">';
                    echo '<strong>' . htmlspecialchars($cred['description']) . '</strong>';
                    echo '<small>Username: ' . htmlspecialchars($username) . ' | Role: ' . htmlspecialchars($row['role']) . ' | Password: ✓ VERIFIED</small>';
                    echo '</div>';
                    echo '</div>';
                } else {
                    $failed_tests[] = $username;
                    echo '<div class="test-result fail">';
                    echo '<div class="status-icon fail">✗</div>';
                    echo '<div class="test-content">';
                    echo '<strong>' . htmlspecialchars($cred['description']) . '</strong>';
                    echo '<small>Username: ' . htmlspecialchars($username) . ' | Role: ' . htmlspecialchars($row['role']) . ' | Password: ✗ HASH MISMATCH</small>';
                    echo '<br><small style="color:#e74c3c;">Expected: ' . substr($cred['hash'], 0, 20) . '...' . '</small>';
                    echo '<br><small style="color:#e74c3c;">Got: ' . substr($row['password'], 0, 20) . '...</small>';
                    echo '</div>';
                    echo '</div>';
                }
            } else {
                $failed_tests[] = $username;
                echo '<div class="test-result fail">';
                echo '<div class="status-icon fail">✗</div>';
                echo '<div class="test-content">';
                echo '<strong>' . htmlspecialchars($cred['description']) . '</strong>';
                echo '<small>Username: ' . htmlspecialchars($username) . ' | NOT FOUND IN DATABASE</small>';
                echo '</div>';
                echo '</div>';
            }
            $stmt->close();
        }
        
        echo '</div>';
        
        // Test 2: Password Hash Reference Check
        echo '<div class="test-section">';
        echo '<h2>2. Password Hash Reference Verification</h2>';
        echo '<p><small style="color: #aaa;">Each password should have exactly ONE unique hash</small></p>';
        echo '<table class="hash-table">';
        echo '<tr><th>Password</th><th>Hash</th><th>Status</th></tr>';
        
        foreach ($hash_reference as $password => $hash) {
            $is_valid = true;
            foreach ($test_credentials as $username => $cred) {
                if ($cred['password'] === $password) {
                    if (password_verify($password, $hash)) {
                        echo '<tr><td>' . htmlspecialchars($password) . '</td>';
                        echo '<td><code style="font-size:11px;">' . substr($hash, 0, 20) . '...</code></td>';
                        echo '<td><span class="pass">✓ Valid</span></td></tr>';
                    } else {
                        echo '<tr><td>' . htmlspecialchars($password) . '</td>';
                        echo '<td><code style="font-size:11px;">' . substr($hash, 0, 20) . '...</code></td>';
                        echo '<td><span class="fail">✗ Invalid</span></td></tr>';
                        $is_valid = false;
                    }
                    break;
                }
            }
        }
        
        echo '</table>';
        echo '</div>';
        
        // Test 3: Summary
        $total_tests = count($test_credentials);
        $passed_count = count($passed_tests);
        $failed_count = $total_tests - $passed_count;
        
        if ($failed_count === 0) {
            echo '<div class="summary">';
            echo '<h3>✓ All Tests Passed!</h3>';
            echo '<p><strong>' . $passed_count . '/' . $total_tests . '</strong> users verified successfully.</p>';
            echo '<p>All bcrypt hashes are valid and match their passwords.</p>';
            echo '</div>';
        } else {
            echo '<div class="error">';
            echo '<h3>✗ Some Tests Failed</h3>';
            echo '<p><strong>' . $passed_count . ' passed, ' . $failed_count . ' failed</strong> out of ' . $total_tests . ' tests.</p>';
            if (!empty($failed_tests)) {
                echo '<p><strong>Failed Users:</strong> ' . implode(', ', array_map('htmlspecialchars', $failed_tests)) . '</p>';
                echo '<p style="font-size: 12px; color: #f39c12;">💡 Run: <code>docker exec natms_db mysql -u root nshare_lite_db < DB/setup_users.sql</code></p>';
            }
            echo '</div>';
        }
        
        ?>

        <!-- Test 3: Login Test -->
        <div class="test-section">
            <h2>3. Central Login Test</h2>
            <div class="info-box">
                <strong>ℹ️ Test Central Authentication</strong><br>
                <small>Visit <a href="/dashboard/login.php" style="color: #3498db;">Central Login Page</a> and try these credentials</small>
            </div>
            <p>
                <strong>Quick Test Accounts:</strong>
            </p>
            <ul style="list-style: none; padding: 0;">
                <li>👤 <strong>nandini</strong> / nandini123 (System Admin - NEW)</li>
                <li>👤 <strong>admin</strong> / admin123 (Main Admin)</li>
                <li>👤 <strong>training</strong> / training123 (Training Manager)</li>
                <li>👤 <strong>director001</strong> / director123 (Director)</li>
                <li>👤 <strong>inst001</strong> / instructor123 (Instructor)</li>
            </ul>
        </div>

        <!-- Test 4: Hash Verification Script -->
        <div class="test-section">
            <h2>4. Manual Hash Verification</h2>
            <p>Use this code to verify hashes in PHP:</p>
            <div class="code-block">
&lt;?php
// Test hash validity
$password = 'admin123';
$hash = '$2y$10$uUlm4Z0Bpvwl7J2B8n5R3O4C6H9X2K1L4M7P0S3T6V9Y2B5E8H1';

if (password_verify($password, $hash)) {
    echo "✓ Hash is valid for password: " . $password;
} else {
    echo "✗ Hash is INVALID for password: " . $password;
}
?&gt;
            </div>
        </div>

        <!-- Instructions -->
        <div class="test-section">
            <h2>5. Troubleshooting</h2>
            <p><strong>If users are missing or hashes don't match:</strong></p>
            <ol>
                <li>Re-run the setup script:
                    <div class="code-block">docker exec natms_db mysql -u root nshare_lite_db < DB/setup_users.sql</div>
                </li>
                <li>Verify with MySQL CLI:
                    <div class="code-block">docker exec natms_db mysql -u root nshare_lite_db -e "SELECT COUNT(*) FROM users;"</div>
                </li>
                <li>Check for errors in docker logs:
                    <div class="code-block">docker logs natms_db | tail -20</div>
                </li>
            </ol>
        </div>

        <div class="button-group">
            <a href="/dashboard/" class="btn">← Back to Dashboard</a>
            <a href="/dashboard/login.php" class="btn secondary">Test Login</a>
            <button class="btn" onclick="location.reload()">Refresh Results</button>
        </div>
    </div>
</body>
</html>
