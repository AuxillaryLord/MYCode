<?php
// Start session to access user data (if required)
session_start();

// Include the database connection
require_once '../includes/db.php';

// Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the raw POST data (JSON)
    $inputData = json_decode(file_get_contents('php://input'), true);

    // Check if the required fields are available
    if (isset($inputData['id'], $inputData['action'])) {
        $requestId = $inputData['id'];
        $action = $inputData['action'];

        // Ensure the action is either 'approve' or 'reject'
        if (in_array($action, ['approve', 'reject'])) {
            // Define the new status based on the action
            $newStatus = ($action === 'approve') ? 'approved' : 'rejected';

            // Prepare the SQL query to update the booking status
            $query = "UPDATE bookings SET status = :status WHERE id = :id";
            $stmt = $pdo->prepare($query);

            // Execute the query with the new status and request ID
            $stmt->execute(['status' => $newStatus, 'id' => $requestId]);

            // Check if the update was successful
            if ($stmt->rowCount() > 0) {
                // Return a success response
                echo json_encode(['status' => 'success', 'message' => "Request $newStatus successfully."]);
            } else {
                // Return a failure response (if no rows were updated)
                echo json_encode(['status' => 'error', 'message' => 'Failed to update the booking status.']);
            }
        } else {
            // Invalid action, return an error response
            echo json_encode(['status' => 'error', 'message' => 'Invalid action specified.']);
        }
    } else {
        // Missing data, return an error response
        echo json_encode(['status' => 'error', 'message' => 'Invalid input data.']);
    }
} else {
    // Return error if the request is not POST
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
?>
