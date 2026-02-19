<?php
/**
 * NATMS Unified RBAC & Routing Controller
 * Purpose: Centralized role-based access control and routing logic
 * Usage: Include at the top of any module that needs RBAC checks
 * Security: Validates session, checks roles, prevents unauthorized access
 * 
 * Example Usage:
 *   require_once('/app/dashboard/php/rbac.php');
 *   check_access(['admin', 'user']);  // Allow only admin or user
 */

// MODIFIED: Created unified RBAC system
// Reason: Prevent routing conflicts and ensure consistent access control
// Original: Each module had its own RBAC logic
// New: Single source of truth for all role-based access
// Benefit: Easy to audit, modify, and maintain security policies

// MODIFIED: Define all roles and their access permissions
// Reason: Centralize RBAC configuration
// Original: Each module defined roles differently
// New: Single RBAC matrix for all modules
// Format: role => [array of accessible modules]

const RBAC_MATRIX = [
    'admin' => [
        'dashboard',
        'training-portal',
        'classroom-monitoring',
        'booking',
        'niatcloud',
        'live-status',
        'admin-panel'
    ],
    'user' => [
        'dashboard',
        'training-portal',
        'booking',
        'niatcloud',
        'live-status'
    ],
    'training' => [
        'dashboard',
        'training-portal',
        'booking',
        'niatcloud'
    ],
    'director' => [
        'dashboard',
        'training-portal',
        'classroom-monitoring',
        'booking',
        'niatcloud'
    ],
    'instructor' => [
        'dashboard',
        'training-portal',
        'classroom-monitoring',
        'booking',
        'niatcloud'
    ],
    'trainee' => [
        'dashboard',
        'training-portal',
        'classroom-monitoring',
        'niatcloud'
    ]
];

// MODIFIED: Define module features by role
// Reason: Control what each role can DO within a module, not just access
// Original: Only module-level access control
// New: Fine-grained action-level access control
// Format: module => [role => [array of allowed actions]]

const MODULE_PERMISSIONS = [
    'training-portal' => [
        'admin' => ['view', 'create', 'edit', 'delete', 'approve'],
        'user' => ['view', 'create', 'edit'],
        'training' => ['view', 'create', 'edit'],
        'director' => ['view', 'approve'],
        'instructor' => ['view'],
        'trainee' => ['view'],  // read-only
    ],
    'classroom-monitoring' => [
        'admin' => ['view', 'create', 'edit', 'delete'],
        'director' => ['view'],
        'instructor' => ['view'],  // only own classes
        'trainee' => ['view'],       // only own class
    ],
    'booking' => [
        'admin' => ['view', 'create', 'edit', 'delete', 'approve', 'reject'],
        'user' => ['view', 'create'],
        'training' => ['view', 'create'],
        'director' => ['view', 'create'],
    ],
    'niatcloud' => [
        'admin' => ['view', 'upload', 'delete', 'share'],
        'user' => ['view', 'upload', 'delete', 'share'],
        'training' => ['view', 'upload', 'delete', 'share'],
        'director' => ['view', 'upload', 'delete', 'share'],
        'instructor' => ['view', 'upload', 'delete', 'share'],
        'trainee' => ['view', 'upload', 'share'],
    ],
    'live-status' => [
        'admin' => ['view', 'create', 'edit', 'delete'],
        'user' => ['view'],
    ],
    'admin-panel' => [
        'admin' => ['view', 'manage_users', 'manage_roles', 'view_logs', 'system_config'],
    ]
];

/**
 * MODIFIED: Check if user is authenticated
 * Reason: Prevent access to protected resources without login
 * Original: Each module had its own session check
 * New: Unified authentication check
 * Returns: boolean - true if logged in, false otherwise
 */
function is_authenticated() {
    // MODIFIED: Check for required session variables
    // Reason: Ensure session is valid and complete
    // Original: Only checked logged_in flag
    // New: Check all required session variables exist and are valid
    
    return isset($_SESSION['logged_in']) 
        && $_SESSION['logged_in'] === true
        && isset($_SESSION['user'])
        && isset($_SESSION['user_id'])
        && isset($_SESSION['role'])
        && !empty($_SESSION['user'])
        && !empty($_SESSION['role']);
}

/**
 * MODIFIED: Check if user has access to a module
 * Reason: Prevent unauthorized access to features
 * Original: No centralized module access check
 * New: Single function to verify module access
 * Params: $module = module name (string), $role = user role (string or null)
 * Returns: boolean - true if user can access module
 */
function has_module_access($module, $role = null) {
    // MODIFIED: Use provided role or get from session
    // Reason: Allow checking other roles or session role
    // Original: Only worked with session
    // New: Flexible role checking
    
    if ($role === null) {
        if (!is_authenticated()) {
            return false;
        }
        $role = $_SESSION['role'];
    }

    // MODIFIED: Check if role has access to module
    // Reason: Enforce RBAC matrix
    // Original: No centralized check
    // New: Single source of truth for access
    
    if (!isset(RBAC_MATRIX[$role])) {
        return false;  // Unknown role
    }

    return in_array($module, RBAC_MATRIX[$role], true);
}

/**
 * MODIFIED: Check if user can perform an action in a module
 * Reason: Fine-grained access control at action level
 * Original: Only module-level access
 * New: Can restrict specific operations
 * Params: $module = module name (string), $action = action name (string), $role = user role (string or null)
 * Returns: boolean - true if user can perform action
 */
function can_perform_action($module, $action, $role = null) {
    // MODIFIED: Use provided role or get from session
    // Reason: Allow checking other roles or session role
    // Original: Only worked with session
    // New: Flexible role checking
    
    if ($role === null) {
        if (!is_authenticated()) {
            return false;
        }
        $role = $_SESSION['role'];
    }

    // MODIFIED: Check if module exists and has permissions defined
    // Reason: Safety check before accessing array
    // Original: Would throw error on missing module
    // New: Graceful handling
    
    if (!isset(MODULE_PERMISSIONS[$module])) {
        return has_module_access($module, $role);  // Fall back to module access
    }

    if (!isset(MODULE_PERMISSIONS[$module][$role])) {
        return false;  // Role not defined for this module
    }

    // MODIFIED: Check if role has this action
    // Reason: Enforce action-level permissions
    // Original: No action-level control
    // New: Fine-grained control
    
    return in_array($action, MODULE_PERMISSIONS[$module][$role], true);
}

/**
 * MODIFIED: Require authentication - redirect if not logged in
 * Reason: Protect pages that require login
 * Original: Each module did its own redirect
 * New: Unified function for consistency
 * Returns: void (exits on failure)
 */
function require_auth() {
    // MODIFIED: Check if user is authenticated
    // Reason: Prevent access without login
    // Original: Each module had its own check
    // New: Unified approach
    
    if (!is_authenticated()) {
        // MODIFIED: Redirect to login with error message
        // Reason: Tell user why they're being redirected
        // Original: Silent redirect to login
        // New: User-friendly error page
        
        header('Location: /dashboard/error.php?code=401&type=auth&message=' . 
               urlencode('Please log in to continue') . 
               '&redirect=/dashboard/login.php&redirect_text=Go to Login');
        exit();
    }
}

/**
 * MODIFIED: Require access to a module - redirect if unauthorized
 * Reason: Enforce RBAC at module entry point
 * Original: Some modules checked, some didn't
 * New: Unified function for consistency
 * Params: $module = module name (string), $allowed_roles = array of allowed roles (optional)
 * Returns: void (exits on failure)
 */
function require_module_access($module, $allowed_roles = []) {
    // MODIFIED: First check authentication
    // Reason: Can't check role if not logged in
    // Original: Skipped some checks
    // New: Full validation chain
    
    require_auth();

    $user_role = $_SESSION['role'];

    // MODIFIED: Check if specific roles were provided
    // Reason: Allow module to override RBAC matrix (for testing/special cases)
    // Original: Always used RBAC matrix
    // New: Flexible approach
    
    if (!empty($allowed_roles)) {
        if (!in_array($user_role, $allowed_roles, true)) {
            // MODIFIED: Redirect to error page
            // Reason: Inform user why access was denied
            // Original: No feedback
            // New: Clear error message
            
            header('Location: /dashboard/error.php?code=403&type=permission&message=' . 
                   urlencode('You do not have permission to access this module') . 
                   '&details=' . urlencode('Required roles: ' . implode(', ', $allowed_roles) . ' | Your role: ' . $user_role) .
                   '&redirect=/dashboard/portals.php&redirect_text=Return to Dashboard');
            exit();
        }
    } else {
        if (!has_module_access($module, $user_role)) {
            // MODIFIED: Redirect to error page
            // Reason: Inform user why access was denied
            // Original: No feedback
            // New: Clear error message
            
            header('Location: /dashboard/error.php?code=403&type=permission&message=' . 
                   urlencode('Your role (' . htmlspecialchars($user_role) . ') does not have access to this module') . 
                   '&redirect=/dashboard/portals.php&redirect_text=Return to Dashboard');
            exit();
        }
    }
}

/**
 * MODIFIED: Require specific action permission - redirect if unauthorized
 * Reason: Enforce action-level access control
 * Original: No action-level control
 * New: Can restrict specific operations
 * Params: $module = module name (string), $action = action name (string)
 * Returns: void (exits on failure)
 */
function require_action_permission($module, $action) {
    // MODIFIED: First check authentication
    // Reason: Can't check permissions if not logged in
    // Original: Skipped some checks
    // New: Full validation chain
    
    require_auth();

    $user_role = $_SESSION['role'];

    // MODIFIED: Check if user can perform this action
    // Reason: Enforce action-level access control
    // Original: No action-level control
    // New: Fine-grained permissions
    
    if (!can_perform_action($module, $action, $user_role)) {
        // MODIFIED: Redirect to error page
        // Reason: Inform user why action is denied
        // Original: No feedback
        // New: Clear error message
        
        header('Location: /dashboard/error.php?code=403&type=permission&message=' . 
               urlencode('You do not have permission to perform this action') . 
               '&details=' . urlencode('Action: ' . $action . ' | Module: ' . $module . ' | Your role: ' . $user_role) .
               '&redirect=/dashboard/portals.php&redirect_text=Return to Dashboard');
        exit();
    }
}

/**
 * MODIFIED: Get user display information
 * Reason: Standardize how user info is retrieved from session
 * Original: Each module accessed session directly
 * New: Single function for consistency
 * Returns: array with user data or empty array if not authenticated
 */
function get_user_info() {
    // MODIFIED: Check if authenticated
    // Reason: Return safe data only if user is authenticated
    // Original: No safety check
    // New: Safe data retrieval
    
    if (!is_authenticated()) {
        return [];
    }

    // MODIFIED: Return standardized user info
    // Reason: Provide consistent data structure
    // Original: Each module created different structures
    // New: Single format for all modules
    
    return [
        'user' => $_SESSION['user'],
        'user_id' => $_SESSION['user_id'],
        'role' => $_SESSION['role'],
        'display_name' => $_SESSION['display_name'] ?? $_SESSION['user'],
        'authenticated' => true,
        'login_time' => $_SESSION['login_time'] ?? time(),
    ];
}

/**
 * MODIFIED: Log security event
 * Reason: Track access attempts for audit trail
 * Original: No logging
 * New: Security audit logging
 * Params: $event = event description (string), $severity = low|medium|high
 * Returns: void
 */
function log_security_event($event, $severity = 'low') {
    // MODIFIED: Create audit log entry
    // Reason: Track security events for compliance
    // Original: No logging at all
    // New: Basic audit trail
    
    $log_entry = [
        'timestamp' => date('Y-m-d H:i:s'),
        'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN',
        'user' => $_SESSION['user'] ?? 'UNKNOWN',
        'event' => $event,
        'severity' => $severity,
    ];

    // MODIFIED: Try to log to file
    // Reason: Keep security audit trail
    // Original: No logging
    // New: JSON log file for audit
    
    $log_file = '/workspaces/MYCode/logs/security.log';
    $log_dir = dirname($log_file);
    
    // MODIFIED: Create log directory if it doesn't exist
    // Reason: Prevent errors if directory missing
    // Original: No directory creation
    // New: Graceful directory creation
    
    if (!file_exists($log_dir)) {
        @mkdir($log_dir, 0755, true);
    }

    // MODIFIED: Append to log file
    // Reason: Keep historical record of events
    // Original: No recording
    // New: Persistent audit trail
    
    @file_put_contents(
        $log_file,
        json_encode($log_entry) . PHP_EOL,
        FILE_APPEND
    );
}

/**
 * MODIFIED: Check session timeout
 * Reason: Implement session idle timeout for security
 * Original: No timeout checking
 * New: Automatic logout after inactivity
 * Params: $timeout_minutes = timeout duration in minutes (default 30)
 * Returns: boolean - true if session is valid, false if timed out
 */
function check_session_timeout($timeout_minutes = 30) {
    // MODIFIED: Check if login time is set
    // Reason: Can't check timeout if login time unknown
    // Original: No timeout checking
    // New: Timeout support
    
    if (!isset($_SESSION['login_time'])) {
        $_SESSION['login_time'] = time();
        return true;
    }

    // MODIFIED: Check if timeout exceeded
    // Reason: Auto-logout after inactivity
    // Original: Session persists indefinitely
    // New: Security timeout feature
    
    $timeout = $timeout_minutes * 60;  // Convert minutes to seconds
    if (time() - $_SESSION['login_time'] > $timeout) {
        // MODIFIED: Timeout exceeded - destroy session
        // Reason: Force re-authentication after inactivity
        // Original: No timeout
        // New: Automatic session cleanup
        
        log_security_event('Session timeout for user: ' . $_SESSION['user'], 'low');
        session_destroy();
        return false;
    }

    // MODIFIED: Update last activity time
    // Reason: Reset idle timer on each page load
    // Original: Fixed timeout
    // New: Activity-based timeout
    
    $_SESSION['login_time'] = time();
    return true;
}

?>
