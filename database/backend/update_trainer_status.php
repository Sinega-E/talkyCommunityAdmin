<?php
// Include database connection
include 'db.php';

// Handle OPTIONS request (for preflight)
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');
    header('Content-Type: application/json');
    http_response_code(200); // OK status
    exit;
}

// Get the input data
$data = json_decode(file_get_contents("php://input"), true);

// Validate input data
if (isset($data['email']) && isset($data['status'])) {
    $email = $data['email'];
    $status = $data['status'];

    try {
        // Prepare the query to update the trainer's status based on their email
        $query = "UPDATE trainer_info SET status = :status WHERE email = :email";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        // Check if the update was successful
        if ($stmt->rowCount() > 0) {
            echo json_encode(['message' => 'Status updated successfully']);
        } else {
            echo json_encode(['message' => 'No changes made or email not found']);
        }
    } catch (PDOException $e) {
        // Handle database errors
        http_response_code(500); // Internal Server Error
        echo json_encode(['message' => 'Database error', 'error' => $e->getMessage()]);
    }
} else {
    // Invalid or missing input data
    http_response_code(400); // Bad Request
    echo json_encode(['message' => 'Invalid input data']);
}
?>
