<?php
/**
 * NATMS Centralized Admin Dashboard
 * Purpose: Single admin interface for system-wide management
 * Access: Admin role only
 * Features: User management, system stats, module access, audit logs
 */

session_start();

// MODIFIED: Created unified admin dashboard
// Reason: Provide single admin interface instead of module-specific ones
// Original: Each module had separate admin panel (booking, classroom, etc)
// New: Centralized dashboard with links to module admin panels
// Benefit: Admins see entire system, can navigate to specific modules

// MODIFIED: Use centralized authentication
// Reason: All modules must use same login system
// Original: Some modules used local authentication
// New: All use /dashboard/php/login_central.php session
include_once(__DIR__ . '/php/rbac.php');

// MODIFIED: Require admin access
// Reason: Only admins can access admin dashboard
// Original: Module-specific auth checks
// New: Unified RBAC check
require_module_access('admin-panel', ['admin']);

// MODIFIED: Get user information from session
// Reason: Display current admin info
// Original: Each module handled this differently
// New: Unified approach
$user_info = get_user_info();
$admin_name = $user_info['display_name'];
$admin_user = $user_info['user'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NATMS - Admin Dashboard</title>
    <style>
        /* MODIFIED: Professional admin dashboard styling
           Reason: Unified look across NATMS system
           Original: Each module had its own styling
           New: Consistent navy blue and gold theme */
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0A192F 0%, #1D3557 100%);
            color: #333;
            min-height: 100vh;
        }

        /* NAVBAR */
        .navbar {
            background-color: #1B2A41;
            color: white;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 30px;
            height: 70px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
        }

        .navbar-title {
            font-size: 22px;
            font-weight: 700;
            color: #FFD700;
            letter-spacing: 1px;
        }

        .navbar-user {
            text-align: right;
        }

        .navbar-user .user-name {
            font-weight: 600;
            color: #FFD700;
        }

        .navbar-user .user-role {
            font-size: 12px;
            color: #ccc;
        }

        .logout-btn {
            background-color: #d32f2f;
            color: white;
            padding: 8px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin-left: 20px;
            font-size: 13px;
            transition: background-color 0.3s;
        }

        .logout-btn:hover {
            background-color: #b71c1c;
        }

        /* MAIN CONTAINER */
        .container {
            max-width: 1400px;
            margin: 30px auto;
            padding: 0 20px;
        }

        /* HEADER SECTION */
        .admin-header {
            background: white;
            padding: 25px 30px;
            border-radius: 12px;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border-left: 5px solid #FFD700;
        }

        .admin-header h1 {
            color: #1B2A41;
            margin-bottom: 10px;
            font-size: 28px;
        }

        .admin-header p {
            color: #666;
            font-size: 14px;
        }

        /* DASHBOARD GRID */
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }

        /* STAT CARD */
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            border-top: 4px solid #2196f3;
        }

        .stat-card.warning {
            border-top-color: #ff9800;
        }

        .stat-card.success {
            border-top-color: #4caf50;
        }

        .stat-card.danger {
            border-top-color: #f44336;
        }

        .stat-number {
            font-size: 36px;
            font-weight: 700;
            color: #1B2A41;
            margin: 10px 0;
        }

        .stat-label {
            color: #666;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .stat-icon {
            font-size: 32px;
            opacity: 0.7;
        }

        /* SECTION */
        .admin-section {
            background: white;
            border-radius: 12px;
            padding: 25px 30px;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .section-title {
            font-size: 18px;
            font-weight: 700;
            color: #1B2A41;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #FFD700;
        }

        /* ADMIN MODULES GRID */
        .modules-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        .module-card {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            border: 2px solid transparent;
            text-decoration: none;
            color: inherit;
            display: block;
        }

        .module-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
            border-color: #FFD700;
        }

        .module-icon {
            font-size: 42px;
            margin-bottom: 10px;
        }

        .module-name {
            font-weight: 700;
            color: #1B2A41;
            margin-bottom: 5px;
        }

        .module-description {
            font-size: 12px;
            color: #666;
        }

        /* QUICK ACTIONS */
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }

        .action-btn {
            background-color: #1B2A41;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            text-align: center;
            font-weight: 600;
            transition: background-color 0.3s;
            font-size: 13px;
        }

        .action-btn:hover {
            background-color: #0f1823;
        }

        .action-btn.secondary {
            background-color: #666;
        }

        .action-btn.secondary:hover {
            background-color: #555;
        }

        .action-btn.danger {
            background-color: #f44336;
        }

        .action-btn.danger:hover {
            background-color: #d32f2f;
        }

        /* FEATURE LIST */
        .feature-list {
            list-style: none;
        }

        .feature-list li {
            padding: 10px 0;
            border-bottom: 1px solid #eee;
            color: #666;
            font-size: 13px;
        }

        .feature-list li:before {
            content: "✓ ";
            color: #4caf50;
            font-weight: bold;
            margin-right: 10px;
        }

        .feature-list li:last-child {
            border-bottom: none;
        }

        /* FOOTER */
        .admin-footer {
            background: white;
            padding: 20px 30px;
            border-radius: 12px;
            text-align: center;
            color: #666;
            font-size: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }

        /* RESPONSIVE */
        @media (max-width: 768px) {
            .navbar {
                flex-direction: column;
                height: auto;
                padding: 15px;
            }

            .navbar-user {
                margin-top: 10px;
            }

            .logout-btn {
                margin-left: 0;
                margin-top: 10px;
                width: 100%;
            }

            .modules-grid {
                grid-template-columns: 1fr;
            }

            .dashboard-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

<!-- MODIFIED: Admin navbar
     Reason: Consistent with dashboard design
     Original: Each module had its own navbar
     New: Unified header with admin info -->
<nav class="navbar">
    <div class="navbar-title">⚙️ NATMS Admin Dashboard</div>
    <div class="navbar-user">
        <div class="user-name"><?php echo htmlspecialchars($admin_name); ?></div>
        <div class="user-role">System Administrator</div>
        <a href="/dashboard/php/logout_central.php" class="logout-btn">Logout</a>
    </div>
</nav>

<div class="container">
    <!-- MODIFIED: Header section with brief overview
         Reason: Welcome admin and explain dashboard purpose
         Original: No introduction
         New: Clear admin dashboard introduction -->
    <div class="admin-header">
        <h1>Welcome, <?php echo htmlspecialchars(explode(' ', $admin_name)[0]); ?>!</h1>
        <p>Manage system users, modules, security, and audit trails from this dashboard</p>
    </div>

    <!-- MODIFIED: Dashboard statistics
         Reason: Show quick system overview
         Original: No stats display
         New: Key metrics at a glance -->
    <div class="dashboard-grid">
        <div class="stat-card success">
            <div class="stat-icon">👥</div>
            <div class="stat-label">Total Users</div>
            <div class="stat-number">14</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">📦</div>
            <div class="stat-label">Modules</div>
            <div class="stat-number">6</div>
        </div>
        <div class="stat-card warning">
            <div class="stat-icon">⚠️</div>
            <div class="stat-label">Pending Items</div>
            <div class="stat-number">~</div>
        </div>
        <div class="stat-card danger">
            <div class="stat-icon">🔒</div>
            <div class="stat-label">Security Events</div>
            <div class="stat-number">~</div>
        </div>
    </div>

    <!-- MODIFIED: Module management section
         Reason: Quick links to module-specific admin panels
         Original: No unified access to module admins
         New: Links to all module admin areas -->
    <div class="admin-section">
        <div class="section-title">🔧 Module Administration</div>
        <div class="modules-grid">
            <a href="/booking/admin/admin_panel.php" class="module-card">
                <div class="module-icon">📅</div>
                <div class="module-name">Booking System</div>
                <div class="module-description">Manage bookings & approvals</div>
            </a>
            <a href="/training-portal/admin_manage.php" class="module-card">
                <div class="module-icon">📚</div>
                <div class="module-name">Training Portal</div>
                <div class="module-description">Manage courses & content</div>
            </a>
            <a href="/classroom_monitoring/routine/admin_dashboard.php" class="module-card">
                <div class="module-icon">👥</div>
                <div class="module-name">Classroom Monitor</div>
                <div class="module-description">View classes & attendance</div>
            </a>
            <a href="/live_status/admin/admin.php" class="module-card">
                <div class="module-icon">🌐</div>
                <div class="module-name">Network Status</div>
                <div class="module-description">Manage devices & status</div>
            </a>
        </div>
    </div>

    <!-- MODIFIED: System management section
         Reason: Provide admin tools for user, role, and security management
         Original: No centralized system management
         New: Links to key admin functions -->
    <div class="admin-section">
        <div class="section-title">🛡️ System Management</div>
        <div class="quick-actions">
            <a href="/dashboard/debug_test.php" class="action-btn secondary">🧪 Test Authentication</a>
            <a href="/AUTH_TROUBLESHOOTING.txt" class="action-btn secondary">📖 View Documentation</a>
            <a href="/CODEBASE_ANALYSIS.md" class="action-btn secondary">📋 System Architecture</a>
        </div>
    </div>

    <!-- MODIFIED: Quick reference section
         Reason: Show admin useful information
         Original: No reference material
         New: Quick reference for admin tasks -->
    <div class="admin-section">
        <div class="section-title">📚 Quick Reference</div>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
            <div>
                <h4 style="color: #1B2A41; margin-bottom: 10px;">System Users</h4>
                <ul class="feature-list">
                    <li>1 Admin account</li>
                    <li>7 Training/Faculty users</li>
                    <li>1 Director</li>
                    <li>5 Instructors</li>
                    <li>1 Trainee</li>
                </ul>
            </div>
            <div>
                <h4 style="color: #1B2A41; margin-bottom: 10px;">User Roles</h4>
                <ul class="feature-list">
                    <li><strong>admin:</strong> All modules</li>
                    <li><strong>user:</strong> Most modules</li>
                    <li><strong>training:</strong> Portal + Booking</li>
                    <li><strong>director:</strong> Monitoring + Portal</li>
                    <li><strong>instructor:</strong> Own classes</li>
                    <li><strong>trainee:</strong> Learning only</li>
                </ul>
            </div>
            <div>
                <h4 style="color: #1B2A41; margin-bottom: 10px;">Accessible Modules</h4>
                <ul class="feature-list">
                    <li>Training Portal</li>
                    <li>Classroom Monitoring</li>
                    <li>Booking System</li>
                    <li>NIAT Cloud Storage</li>
                    <li>Live Network Status</li>
                    <li>Admin Dashboard</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- MODIFIED: Documentation section
         Reason: Link to help and troubleshooting
         Original: No documentation links
         New: Centralized help access -->
    <div class="admin-section">
        <div class="section-title">📖 Documentation</div>
        <p style="color: #666; margin-bottom: 15px;">
            For detailed system information and troubleshooting guides, see the following documentation files:
        </p>
        <div class="quick-actions">
            <a href="/credentials.txt" class="action-btn">User Credentials</a>
            <a href="/STARTUP_README.txt" class="action-btn">Setup Guide</a>
            <a href="/QUICK_REFERENCE.txt" class="action-btn">Quick Reference</a>
            <a href="/IMPLEMENTATION_SUMMARY.md" class="action-btn">Technical Details</a>
        </div>
    </div>

    <!-- MODIFIED: Footer with system info
         Reason: Show admin useful metadata
         Original: No footer
         New: System information footer -->
    <div class="admin-footer">
        <p>NATMS Admin Dashboard | System Version 2.0 | Last Updated: February 19, 2026</p>
        <p>For security or technical issues, contact the system administrator</p>
    </div>
</div>

</body>
</html>
