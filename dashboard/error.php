<?php
/**
 * NATMS Global Error Handler
 * Purpose: Provide unified error display across all modules
 * Usage: Included by all modules to display authentication/authorization errors
 * Security: Sanitizes all error messages to prevent XSS
 * 
 * Error Types Handled:
 * - Authentication failures (401)
 * - Authorization failures (403)
 * - Resource not found (404)
 * - Database errors (500)
 * - General application errors (500)
 */

// MODIFIED: Created new error handler
// Reason: Centralize error display across all modules
// Original: Each module had its own error handling
// New: Single error page with consistent styling
// Benefit: Professional appearance, consistent user experience

session_start();

// MODIFIED: Get error details from query parameters or session
// Reason: Allow different modules to pass error information
$error_code = isset($_GET['code']) ? intval($_GET['code']) : 500;
$error_message = isset($_GET['message']) ? $_GET['message'] : 'An unexpected error occurred';
$error_details = isset($_GET['details']) ? $_GET['details'] : '';
$error_type = isset($_GET['type']) ? $_GET['type'] : 'error';
$redirect_url = isset($_GET['redirect']) ? $_GET['redirect'] : '/dashboard/login.php';
$redirect_text = isset($_GET['redirect_text']) ? $_GET['redirect_text'] : 'Return to Login';

// MODIFIED: Sanitize all error output to prevent XSS
// Reason: User input is never trusted
$error_message = htmlspecialchars($error_message, ENT_QUOTES, 'UTF-8');
$error_details = htmlspecialchars($error_details, ENT_QUOTES, 'UTF-8');
$redirect_url = htmlspecialchars($redirect_url, ENT_QUOTES, 'UTF-8');
$redirect_text = htmlspecialchars($redirect_text, ENT_QUOTES, 'UTF-8');

// MODIFIED: Map error codes to user-friendly titles
// Reason: Provide context without exposing technical details
$error_titles = [
    401 => 'Authentication Failed',
    403 => 'Access Denied',
    404 => 'Resource Not Found',
    500 => 'Server Error',
    503 => 'Service Unavailable',
];

$error_title = $error_titles[$error_code] ?? 'Error';

// MODIFIED: Define colors based on error type
// Reason: Visual feedback helps users understand error severity
$icon_colors = [
    'auth' => '#f44336',      // Red for authentication issues
    'permission' => '#ff9800', // Orange for authorization issues
    'notfound' => '#2196f3',   // Blue for missing resources
    'error' => '#f44336',      // Red for general errors
];

$color = $icon_colors[$error_type] ?? '#f44336';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $error_code . ' - ' . htmlspecialchars($error_title); ?></title>
    <style>
        /* MODIFIED: Professional error page styling
           Reason: Match NATMS design language (navy blue and gold)
           Original: No unified error styling
           New: Consistent with NIAT Cloud and dashboard design */
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0A192F 0%, #1D3557 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .error-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 600px;
            width: 100%;
            overflow: hidden;
        }

        .error-header {
            background: linear-gradient(135deg, #1B2A41 0%, #2C3E50 100%);
            padding: 40px 30px;
            text-align: center;
            border-bottom: 4px solid #FFD700;
        }

        .error-icon {
            font-size: 64px;
            margin-bottom: 20px;
            animation: bounce 2s infinite;
        }

        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        .error-code {
            font-size: 48px;
            font-weight: 700;
            color: #FFD700;
            margin-bottom: 10px;
        }

        .error-title {
            font-size: 24px;
            font-weight: 600;
            color: white;
            margin-bottom: 5px;
        }

        .error-body {
            padding: 40px 30px;
        }

        .error-message {
            font-size: 16px;
            color: #333;
            line-height: 1.6;
            margin-bottom: 20px;
        }

        .error-details {
            background: #f5f5f5;
            border-left: 4px solid #ff9800;
            padding: 15px;
            border-radius: 4px;
            font-size: 13px;
            color: #666;
            font-family: 'Courier New', monospace;
            word-break: break-word;
            max-height: 200px;
            overflow-y: auto;
            display: none;
        }

        .error-details.show {
            display: block;
        }

        .error-details.show + .toggle-details {
            margin-top: 10px;
        }

        .toggle-details {
            display: inline-block;
            color: #2196f3;
            cursor: pointer;
            font-size: 12px;
            text-decoration: underline;
            margin-top: 10px;
        }

        .toggle-details:hover {
            color: #1976d2;
        }

        .error-suggestions {
            background: #e8f5e9;
            border-left: 4px solid #4caf50;
            padding: 15px;
            border-radius: 4px;
            margin: 20px 0;
        }

        .error-suggestions h4 {
            color: #2e7d32;
            margin-bottom: 10px;
            font-size: 14px;
        }

        .error-suggestions ul {
            list-style: none;
            padding-left: 20px;
        }

        .error-suggestions li {
            color: #333;
            margin: 5px 0;
            font-size: 13px;
        }

        .error-suggestions li:before {
            content: "✓ ";
            color: #4caf50;
            font-weight: bold;
            margin-right: 8px;
        }

        .error-footer {
            padding: 25px 30px;
            background: #f9f9f9;
            border-top: 1px solid #eee;
            text-align: center;
        }

        .btn-redirect {
            display: inline-block;
            padding: 12px 30px;
            background-color: #1B2A41;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            transition: background-color 0.3s;
            border: 2px solid #1B2A41;
            cursor: pointer;
            font-size: 14px;
        }

        .btn-redirect:hover {
            background-color: #0f1823;
            border-color: #0f1823;
        }

        .btn-secondary {
            display: inline-block;
            padding: 10px 20px;
            background-color: transparent;
            color: #2196f3;
            text-decoration: underline;
            font-weight: 600;
            margin-left: 10px;
            font-size: 13px;
            cursor: pointer;
            border: none;
        }

        .btn-secondary:hover {
            color: #1976d2;
        }

        .error-timestamp {
            color: #999;
            font-size: 12px;
            margin-top: 15px;
        }

        /* Responsive */
        @media (max-width: 600px) {
            .error-header {
                padding: 30px 20px;
            }

            .error-code {
                font-size: 36px;
            }

            .error-title {
                font-size: 18px;
            }

            .error-body, .error-footer {
                padding: 25px 20px;
            }

            .btn-secondary {
                display: block;
                margin: 10px 0 0 0;
            }
        }
    </style>
</head>
<body>

<div class="error-container">
    <!-- MODIFIED: Error header with code and title
         Reason: Immediately show user the error type
         Original: No structured error display
         New: Clear visual hierarchy for error information -->
    <div class="error-header">
        <div class="error-icon">
            <?php
            // MODIFIED: Show appropriate icon for error type
            // Reason: Visual feedback helps users understand issue
            switch ($error_type) {
                case 'auth':
                    echo '🔐';
                    break;
                case 'permission':
                    echo '🚫';
                    break;
                case 'notfound':
                    echo '❌';
                    break;
                default:
                    echo '⚠️';
            }
            ?>
        </div>
        <div class="error-code"><?php echo $error_code; ?></div>
        <div class="error-title"><?php echo htmlspecialchars($error_title); ?></div>
    </div>

    <!-- MODIFIED: Error details body
         Reason: Provide user-facing error information
         Original: No structured error messages
         New: Clear message + suggestions + technical details -->
    <div class="error-body">
        <div class="error-message">
            <?php echo $error_message; ?>
        </div>

        <?php
        // MODIFIED: Show contextual suggestions based on error type
        // Reason: Help users understand how to resolve the issue
        // Original: No guidance provided
        // New: Actionable suggestions for common errors
        
        if ($error_type === 'auth') {
            echo '
            <div class="error-suggestions">
                <h4>Troubleshooting Steps:</h4>
                <ul>
                    <li>Verify your username and password are correct</li>
                    <li>Check CAPS LOCK is not enabled</li>
                    <li>Ensure your account is active (contact administrator if needed)</li>
                    <li>Clear browser cookies and try again</li>
                    <li>Try a different browser if the issue persists</li>
                </ul>
            </div>
            ';
        } elseif ($error_type === 'permission') {
            echo '
            <div class="error-suggestions">
                <h4>What You Can Do:</h4>
                <ul>
                    <li>Request access from your system administrator</li>
                    <li>Check if you\'re using the correct account</li>
                    <li>Contact your manager to approve necessary permissions</li>
                    <li>Review the module access policy for your role</li>
                </ul>
            </div>
            ';
        } elseif ($error_type === 'notfound') {
            echo '
            <div class="error-suggestions">
                <h4>Possible Causes:</h4>
                <ul>
                    <li>The page has been moved or deleted</li>
                    <li>URL is misspelled or incorrect</li>
                    <li>Feature has not been configured yet</li>
                    <li>Required resource or file is missing</li>
                </ul>
            </div>
            ';
        } else {
            echo '
            <div class="error-suggestions">
                <h4>Recommended Actions:</h4>
                <ul>
                    <li>Refresh the page and try again</li>
                    <li>Return to home and navigate through menu</li>
                    <li>Contact system administrator if issue persists</li>
                    <li>Check system status page for known issues</li>
                </ul>
            </div>
            ';
        }
        ?>

        <?php
        // MODIFIED: Show technical details if provided
        // Reason: Help administrators debug issues
        // Original: Technical details not shown
        // New: Collapsible detail section for debugging
        
        if (!empty($error_details)) {
            echo '
            <div>
                <span class="toggle-details" onclick="document.querySelector(\'.error-details\').classList.toggle(\'show\'); this.textContent = document.querySelector(\'.error-details\').classList.contains(\'show\') ? \'Hide Details ▲\' : \'Show Details ▼\';">Show Details ▼</span>
                <div class="error-details">
                    ' . $error_details . '
                </div>
            </div>
            ';
        }
        ?>
    </div>

    <!-- MODIFIED: Error footer with action buttons
         Reason: Guide user to next action
         Original: No navigation options
         New: Clear navigation buttons -->
    <div class="error-footer">
        <a href="<?php echo $redirect_url; ?>" class="btn-redirect"><?php echo $redirect_text; ?></a>
        <button class="btn-secondary" onclick="window.history.back();">Go Back</button>

        <div class="error-timestamp">
            Error ID: <?php echo date('YmdHis'); ?><br>
            Time: <?php echo date('Y-m-d H:i:s'); ?>
        </div>
    </div>
</div>

<script>
    // MODIFIED: Add error logging capability
    // Reason: Track errors for debugging and monitoring
    // Original: No error logging
    // New: Console logging for developers
    
    console.error({
        code: <?php echo $error_code; ?>,
        type: '<?php echo $error_type; ?>',
        message: '<?php echo addslashes($error_message); ?>',
        timestamp: new Date().toISOString()
    });
</script>

</body>
</html>
