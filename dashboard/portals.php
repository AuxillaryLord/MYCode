<?php
/**
 * MODIFIED: Moved session_start() to file beginning
 * Reason: PHP requires session_start() before any output sent to browser
 * Original location: Line 308 (after HTML output)
 * New location: Line 1 (before any output)
 * Fix: Prevents "headers already sent" error
 */

session_start();

// MODIFIED: Moved authentication check before HTML output
// Reason: Must validate user before outputting any content
// Original location: Line 313
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

// MODIFIED: Extract user information from session
// Reason: Display user context and customize portal access
$username = $_SESSION['user'];
$display_name = $_SESSION['display_name'] ?: $_SESSION['user'];
$role = $_SESSION['role'];

// MODIFIED: Define portal access based on user role
// Reason: Implement role-based access control (RBAC)
// Original behavior: No centralized access control
$portals = [];

// MODIFIED: All roles have access to NIAT Cloud (central file storage)
// Reason: Common workspace for all users
$portals[] = [
    'title' => 'NIAT Cloud',
    'description' => 'Central file storage, collaboration & workspace',
    'icon' => '☁️',
    'url' => '/niatcloud/',
    'class' => 'portal-cloud',
    'roles' => ['admin', 'user', 'trainee', 'director', 'instructor', 'training']
];

// MODIFIED: Training Portal access for training roles
// Reason: Restrict content management to authorized personnel
if (in_array($role, ['admin', 'user', 'training', 'trainee', 'instructor'])) {
    $portals[] = [
        'title' => 'Training Portal',
        'description' => 'Courses, materials, assessments & training content',
        'icon' => '📚',
        'url' => '/training-portal/main.php',
        'class' => 'portal-training',
        'roles' => ['admin', 'user', 'training', 'trainee', 'instructor']
    ];
}

// MODIFIED: Classroom Monitoring for directors and instructors
// Reason: Separate classroom management from other modules
if (in_array($role, ['admin', 'director', 'instructor'])) {
    $portals[] = [
        'title' => 'Classroom Monitoring',
        'description' => 'Monitor classrooms, attendance & instructor performance',
        'icon' => '👥',
        'url' => '/classroom_monitoring/routine/index.php',
        'class' => 'portal-classroom',
        'roles' => ['admin', 'director', 'instructor']
    ];
}

// MODIFIED: Booking System for all users
// Reason: Allow resource booking across all user types
if (in_array($role, ['admin', 'user', 'training', 'director', 'instructor'])) {
    $portals[] = [
        'title' => 'Booking System',
        'description' => 'Book training slots, facilities & resources',
        'icon' => '📅',
        'url' => '/booking/',
        'class' => 'portal-booking',
        'roles' => ['admin', 'user', 'training', 'director', 'instructor']
    ];
}

// MODIFIED: Live Network Status for network monitoring
// Reason: Provide system status visibility to authorized users
if (in_array($role, ['admin', 'user'])) {
    $portals[] = [
        'title' => 'Network Status',
        'description' => 'View live status of network devices & systems',
        'icon' => '🌐',
        'url' => '/live_status/',
        'class' => 'portal-network',
        'roles' => ['admin', 'user']
    ];
}

// MODIFIED: Admin Panel (only for admin accounts)
// Reason: Restrict system administration to authorized personnel
if ($role === 'admin') {
    $portals[] = [
        'title' => 'Admin Panel',
        'description' => 'System configuration, user management & analytics',
        'icon' => '⚙️',
        'url' => '/training-portal/admin/admin_panel.php',
        'class' => 'portal-admin',
        'roles' => ['admin']
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NATMS - Portal Dashboard</title>
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
            min-height: 100vh;
        }

        /* NAVBAR */
        .navbar {
            background-color: #1B2A41;
            color: #EAEAEA;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 15px 30px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .navbar-left {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .navbar-left img {
            height: 50px;
        }

        .navbar-title {
            color: #FFD700;
            font-size: 22px;
            font-weight: 600;
            letter-spacing: 1px;
        }

        .navbar-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .user-info {
            text-align: right;
        }

        .user-name {
            font-weight: 600;
            font-size: 14px;
        }

        .user-role {
            font-size: 12px;
            color: #aaa;
            text-transform: capitalize;
        }

        .logout-btn {
            background-color: #d32f2f;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: background-color 0.3s;
            text-decoration: none;
        }

        .logout-btn:hover {
            background-color: #b71c1c;
        }

        /* MAIN CONTAINER */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        .welcome-section {
            text-align: center;
            margin-bottom: 50px;
        }

        .welcome-section h1 {
            font-size: 36px;
            margin-bottom: 10px;
            color: #FFD700;
        }

        .welcome-section p {
            font-size: 16px;
            color: #ccc;
        }

        /* PORTALS GRID */
        .portals-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            margin-bottom: 40px;
        }

        /* PORTAL BUBBLE */
        .portal-bubble {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 2px solid rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            padding: 40px 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            color: white;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 250px;
            position: relative;
            overflow: hidden;
        }

        /* MODIFIED: Added gradient backgrounds for visual distinction
             Reason: Make different portals easily identifiable by color */
        .portal-bubble::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: inherit;
            transition: all 0.3s ease;
            z-index: -1;
        }

        .portal-bubble:hover {
            transform: translateY(-10px) scale(1.05);
            border-color: rgba(255, 255, 255, 0.4);
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.4);
            background: rgba(255, 255, 255, 0.15);
        }

        .portal-bubble:active {
            transform: translateY(-5px) scale(1.02);
        }

        .portal-icon {
            font-size: 60px;
            margin-bottom: 15px;
            display: block;
        }

        .portal-name {
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 10px;
            color: #FFD700;
        }

        .portal-desc {
            font-size: 14px;
            color: #ddd;
            line-height: 1.5;
        }

        /* PORTAL SPECIFIC COLORS */
        .portal-training {
            border-color: rgba(76, 175, 80, 0.5);
        }

        .portal-training:hover {
            background: rgba(76, 175, 80, 0.2);
            box-shadow: 0 10px 40px rgba(76, 175, 80, 0.3);
        }

        .portal-booking {
            border-color: rgba(33, 150, 243, 0.5);
        }

        .portal-booking:hover {
            background: rgba(33, 150, 243, 0.2);
            box-shadow: 0 10px 40px rgba(33, 150, 243, 0.3);
        }

        .portal-classroom {
            border-color: rgba(255, 152, 0, 0.5);
        }

        .portal-classroom:hover {
            background: rgba(255, 152, 0, 0.2);
            box-shadow: 0 10px 40px rgba(255, 152, 0, 0.3);
        }

        .portal-network {
            border-color: rgba(156, 39, 176, 0.5);
        }

        .portal-network:hover {
            background: rgba(156, 39, 176, 0.2);
            box-shadow: 0 10px 40px rgba(156, 39, 176, 0.3);
        }

        .portal-cloud {
            border-color: rgba(244, 67, 54, 0.5);
        }

        .portal-cloud:hover {
            background: rgba(244, 67, 54, 0.2);
            box-shadow: 0 10px 40px rgba(244, 67, 54, 0.3);
        }

        .portal-admin {
            border-color: rgba(233, 30, 99, 0.5);
        }

        .portal-admin:hover {
            background: rgba(233, 30, 99, 0.2);
            box-shadow: 0 10px 40px rgba(233, 30, 99, 0.3);
        }

        /* SECTION HEADERS */
        .section-header {
            font-size: 24px;
            font-weight: 700;
            margin-top: 40px;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 2px solid rgba(255, 255, 255, 0.2);
            color: #FFD700;
        }

        /* EMPTY STATE */
        .empty-state {
            text-align: center;
            padding: 40px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            color: #ccc;
        }

        /* FOOTER */
        footer {
            background-color: #1B2A41;
            color: #EAEAEA;
            text-align: center;
            padding: 20px 0;
            font-size: 13px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            margin-top: 40px;
        }

        /* RESPONSIVE */
        @media (max-width: 768px) {
            .portals-grid {
                grid-template-columns: 1fr;
            }

            .navbar {
                flex-direction: column;
                gap: 15px;
            }

            .navbar-right {
                width: 100%;
                justify-content: space-between;
            }

            .welcome-section h1 {
                font-size: 28px;
            }

            .container {
                padding: 20px 10px;
            }
        }
    </style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar">
    <div class="navbar-left">
        <img src="/dashboard/images/favicon.png" alt="Logo" style="filter: brightness(0) invert(1);">
        <span class="navbar-title">NATMS Dashboard</span>
    </div>
    <div class="navbar-right">
        <div class="user-info">
            <div class="user-name"><?php echo htmlspecialchars($display_name); ?></div>
            <div class="user-role"><?php echo htmlspecialchars($role); ?></div>
        </div>
        <!-- MODIFIED: Added logout functionality
             Reason: Allow users to securely exit the system -->
        <a href="php/logout_central.php" class="logout-btn">Logout</a>
    </div>
</nav>

<!-- MAIN CONTENT -->
<div class="container">
    <!-- WELCOME SECTION -->
    <div class="welcome-section">
        <h1>Welcome, <?php echo htmlspecialchars(explode(' ', $display_name)[0]); ?>!</h1>
        <p>Select a module below to get started</p>
    </div>

    <!-- PORTALS GRID -->
    <div class="portals-grid">
        <?php
        // MODIFIED: Generate portal bubbles based on user role
        // Reason: Show only accessible modules to each user
        foreach ($portals as $portal) {
            if (in_array($role, $portal['roles'])) {
                echo '
                <a href="' . htmlspecialchars($portal['url']) . '" class="portal-bubble ' . htmlspecialchars($portal['class']) . '">
                    <span class="portal-icon">' . $portal['icon'] . '</span>
                    <div class="portal-name">' . htmlspecialchars($portal['title']) . '</div>
                    <div class="portal-desc">' . htmlspecialchars($portal['description']) . '</div>
                </a>
                ';
            }
        }
        ?>
    </div>

    <!-- EMPTY STATE CHECK -->
    <?php
    $accessible_portals = array_filter($portals, function($p) use ($role) {
        return in_array($role, $p['roles']);
    });
    
    if (empty($accessible_portals)) {
        echo '<div class="empty-state">';
        echo '<p>No modules available for your account. Contact administrator.</p>';
        echo '</div>';
    }
    ?>
</div>

<!-- FOOTER -->
<footer>
    &copy; 2025 Naval Aviation Training Management System (NATMS) | All Rights Reserved
</footer>

</body>
</html>
