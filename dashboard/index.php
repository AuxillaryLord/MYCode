<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NATMS - Testing & Debugging</title>
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
            max-width: 1000px;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            margin-bottom: 40px;
            padding: 30px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            border: 2px solid #FFD700;
        }

        .header h1 {
            color: #FFD700;
            font-size: 32px;
            margin-bottom: 10px;
        }

        .header p {
            color: #ccc;
            font-size: 16px;
        }

        .cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }

        .card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 12px;
            padding: 25px;
            color: #001f3f;
            transition: all 0.3s ease;
            border-left: 5px solid #FFD700;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.3);
        }

        .card h2 {
            color: #002147;
            margin-bottom: 15px;
            font-size: 20px;
        }

        .card p {
            color: #555;
            line-height: 1.6;
            margin-bottom: 15px;
            font-size: 14px;
        }

        .card ul {
            list-style: none;
            margin-bottom: 15px;
        }

        .card li {
            padding: 5px 0;
            color: #666;
            font-size: 14px;
        }

        .card li:before {
            content: "✓ ";
            color: #4CAF50;
            font-weight: bold;
            margin-right: 8px;
        }

        .btn {
            display: inline-block;
            padding: 12px 24px;
            background-color: #002147;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            transition: background-color 0.3s;
            border: none;
            cursor: pointer;
            font-size: 14px;
        }

        .btn:hover {
            background-color: #001233;
        }

        .btn.secondary {
            background-color: #666;
            color: white;
        }

        .btn.secondary:hover {
            background-color: #555;
        }

        .btn-group {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .quick-links {
            background: rgba(255, 255, 255, 0.1);
            padding: 25px;
            border-radius: 12px;
            margin-bottom: 30px;
            border: 2px solid #FFD700;
        }

        .quick-links h3 {
            color: #FFD700;
            margin-bottom: 20px;
            font-size: 18px;
        }

        .quick-links-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
        }

        .quick-link {
            background: rgba(255, 255, 255, 0.95);
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            color: #002147;
        }

        .quick-link a {
            color: #002147;
            text-decoration: none;
            font-weight: 600;
            display: block;
            margin-bottom: 8px;
        }

        .quick-link a:hover {
            color: #FFD700;
        }

        .quick-link p {
            font-size: 12px;
            color: #666;
            margin: 0;
        }

        .code-block {
            background: #1a1a1a;
            padding: 15px;
            border-radius: 6px;
            font-family: 'Courier New', monospace;
            overflow-x: auto;
            margin: 10px 0;
            font-size: 12px;
            color: #4CAF50;
            border-left: 3px solid #FFD700;
        }

        .status-section {
            background: rgba(255, 255, 255, 0.1);
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 30px;
            border: 2px solid #4CAF50;
        }

        .status-section h3 {
            color: #FFD700;
            margin-bottom: 15px;
        }

        .status-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }

        .status-item {
            background: rgba(255, 255, 255, 0.15);
            padding: 15px;
            border-radius: 8px;
            border-left: 3px solid #4CAF50;
        }

        .status-item p {
            margin: 5px 0;
            font-size: 14px;
        }

        .status-item strong {
            color: #FFD700;
        }

        footer {
            background: rgba(255, 255, 255, 0.1);
            padding: 20px;
            text-align: center;
            border-radius: 12px;
            margin-top: 30px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .error-alert {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #f44336;
        }

        .success-alert {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #4CAF50;
        }

        @media (max-width: 768px) {
            .header h1 {
                font-size: 24px;
            }

            .cards-grid {
                grid-template-columns: 1fr;
            }

            .btn-group {
                flex-direction: column;
            }

            .btn {
                width: 100%;
                text-align: center;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <!-- Header -->
    <div class="header">
        <h1>🔧 NATMS Authentication Testing Center</h1>
        <p>Debug and test your login system</p>
    </div>

    <!-- Quick Links Section -->
    <div class="quick-links">
        <h3>⚡ Quick Access - Click to Test</h3>
        <div class="quick-links-grid">
            <div class="quick-link">
                <a href="login.php">🔐 Login Page</a>
                <p>Main login interface</p>
            </div>
            <div class="quick-link">
                <a href="debug_test.php">🧪 Debug Test Page</a>
                <p>Test passwords in real-time</p>
            </div>
            <div class="quick-link">
                <a href="portals.php">🎯 Portal Dashboard</a>
                <p>View after successful login</p>
            </div>
            <div class="quick-link">
                <a href="/niatcloud/login.php">☁️ NIAT Cloud</a>
                <p>Alternative login</p>
            </div>
        </div>
    </div>

    <!-- What to Do Section -->
    <div class="cards-grid">
        <!-- Main Login -->
        <div class="card">
            <h2>1️⃣ Try Main Login</h2>
            <p>Click the button below to access the main login page:</p>
            <div class="btn-group">
                <a href="login.php" class="btn">Go to Login</a>
            </div>
            <p style="margin-top: 15px; font-size: 13px; color: #888;">
                <strong>Try with:</strong><br>
                Username: <code style="background: #f0f0f0; color: #333; padding: 2px 6px;">admin</code><br>
                Password: <code style="background: #f0f0f0; color: #333; padding: 2px 6px;">admin123</code>
            </p>
        </div>

        <!-- Debug Test Page -->
        <div class="card">
            <h2>2️⃣ Test with Debug Page</h2>
            <p>If login fails, use the debug page to test passwords:</p>
            <div class="btn-group">
                <a href="debug_test.php" class="btn">Open Debug Page</a>
            </div>
            <p style="margin-top: 15px;">On the debug page:</p>
            <ul>
                <li>Click quick test buttons</li>
                <li>View all users</li>
                <li>Check password hashes</li>
                <li>Verify authentication</li>
            </ul>
        </div>

        <!-- Password Reset -->
        <div class="card">
            <h2>3️⃣ If Password is Wrong</h2>
            <p>Follow the troubleshooting guide to reset passwords:</p>
            <div class="btn-group">
                <a href="/AUTH_TROUBLESHOOTING.txt" class="btn">Read Guide</a>
            </div>
            <p style="margin-top: 15px;">Quick fix command:</p>
            <div class="code-block">
docker exec natms_db mysql -u root nshare_lite_db &lt; DB/setup_users.sql
            </div>
        </div>
    </div>

    <!-- Test Credentials -->
    <div class="status-section">
        <h3>📝 Quick Reference - Test Credentials</h3>
        <div class="status-grid">
            <div class="status-item">
                <p><strong>Admin:</strong></p>
                <p>Username: <code style="background: rgba(0,0,0,0.3); padding: 2px 6px;">admin</code></p>
                <p>Password: <code style="background: rgba(0,0,0,0.3); padding: 2px 6px;">admin123</code></p>
            </div>
            <div class="status-item">
                <p><strong>Training:</strong></p>
                <p>Username: <code style="background: rgba(0,0,0,0.3); padding: 2px 6px;">training</code></p>
                <p>Password: <code style="background: rgba(0,0,0,0.3); padding: 2px 6px;">training123</code></p>
            </div>
            <div class="status-item">
                <p><strong>Instructor:</strong></p>
                <p>Username: <code style="background: rgba(0,0,0,0.3); padding: 2px 6px;">500-504</code></p>
                <p>Password: <code style="background: rgba(0,0,0,0.3); padding: 2px 6px;">instructor123</code></p>
            </div>
            <div class="status-item">
                <p><strong>Trainee:</strong></p>
                <p>Username: <code style="background: rgba(0,0,0,0.3); padding: 2px 6px;">trg01</code></p>
                <p>Password: <code style="background: rgba(0,0,0,0.3); padding: 2px 6px;">password123</code></p>
            </div>
        </div>
    </div>

    <!-- Verification Steps -->
    <div class="status-section" style="border-color: #2196F3;">
        <h3>✅ System Verification Checklist</h3>
        <div style="background: rgba(255,255,255,0.15); padding: 20px; border-radius: 8px;">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px;">
                <div>
                    <p><strong>1. Docker Containers Running?</strong></p>
                    <p style="font-size: 13px; margin-top: 5px;">Run: <code style="background: rgba(0,0,0,0.3); padding: 2px 4px;">docker ps</code></p>
                    <p style="font-size: 13px; color: #aaa;">Look for: natms_web, natms_db</p>
                </div>
                <div>
                    <p><strong>2. Database Users Exist?</strong></p>
                    <p style="font-size: 13px; margin-top: 5px;">Run: <code style="background: rgba(0,0,0,0.3); padding: 2px 4px;">bash verify_auth.sh</code></p>
                    <p style="font-size: 13px; color: #aaa;">Should show test users</p>
                </div>
                <div>
                    <p><strong>3. Password Hashes Correct?</strong></p>
                    <p style="font-size: 13px; margin-top: 5px;">Open: <code style="background: rgba(0,0,0,0.3); padding: 2px 4px;">debug_test.php</code></p>
                    <p style="font-size: 13px; color: #aaa;">Click test buttons</p>
                </div>
                <div>
                    <p><strong>4. Login Working?</strong></p>
                    <p style="font-size: 13px; margin-top: 5px;">Try: <code style="background: rgba(0,0,0,0.3); padding: 2px 4px;">login.php</code></p>
                    <p style="font-size: 13px; color: #aaa;">Use test credentials</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Troubleshooting -->
    <div class="cards-grid">
        <div class="card" style="border-left-color: #f44336;">
            <h2>❌ Login Shows "Incorrect Password"</h2>
            <p><strong>Most common issue:</strong> Password hash in database doesn't match</p>
            <ol style="padding-left: 20px; color: #666; font-size: 14px;">
                <li>Open <a href="debug_test.php" style="color: #002147; font-weight: bold;">debug_test.php</a></li>
                <li>Click quick test for your user</li>
                <li>If test fails, password hash is wrong</li>
                <li>Use guide to reset: <a href="/AUTH_TROUBLESHOOTING.txt" style="color: #002147; font-weight: bold;">AUTH_TROUBLESHOOTING.txt</a></li>
            </ol>
        </div>

        <div class="card" style="border-left-color: #f44336;">
            <h2>❌ Login Shows "User Not Found"</h2>
            <p><strong>Cause:</strong> User doesn't exist in database</p>
            <ol style="padding-left: 20px; color: #666; font-size: 14px;">
                <li>Open <a href="debug_test.php" style="color: #002147; font-weight: bold;">debug_test.php</a></li>
                <li>Scroll down to "All Test User Accounts" table</li>
                <li>If no users: Run setup script</li>
                <li>Check username spelling (case-sensitive)</li>
            </ol>
        </div>

        <div class="card" style="border-left-color: #f44336;">
            <h2>❌ Cannot Access Debug Page</h2>
            <p><strong>Cause:</strong> Web server not running or page not found</p>
            <ol style="padding-left: 20px; color: #666; font-size: 14px;">
                <li>Check Docker: <code style="background: rgba(0,0,0,0.3); padding: 2px 4px;">docker ps</code></li>
                <li>Start if needed: <code style="background: rgba(0,0,0,0.3); padding: 2px 4px;">docker-compose up -d</code></li>
                <li>Wait 10 seconds</li>
                <li>Try URL again</li>
            </ol>
        </div>

        <div class="card" style="border-left-color: #4CAF50;">
            <h2>✅ Everything Works!</h2>
            <p><strong>Congratulations!</strong> Your authentication system is ready.</p>
            <ol style="padding-left: 20px; color: #666; font-size: 14px;">
                <li>Login with test credentials</li>
                <li>You'll see portal dashboard</li>
                <li>Click on modules to access them</li>
                <li>Change passwords for production</li>
            </ol>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <p style="margin-bottom: 10px;">
            <strong>📚 Documentation:</strong>
            See <code>credentials.txt</code>, <code>IMPLEMENTATION_SUMMARY.md</code>, and <code>AUTH_TROUBLESHOOTING.txt</code>
        </p>
        <p>
            <strong>🔗 Key Links:</strong>
            <a href="login.php" style="color: #FFD700; text-decoration: none; margin: 0 10px;">Login</a> |
            <a href="debug_test.php" style="color: #FFD700; text-decoration: none; margin: 0 10px;">Debug</a> |
            <a href="/niatcloud/login.php" style="color: #FFD700; text-decoration: none; margin: 0 10px;">NIAT Cloud</a>
        </p>
    </footer>

</div>

</body>
</html>
