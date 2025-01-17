<?php
// Database connection
include('db.php');

// Allow cross-origin requests
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS, DELETE, PUT");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Handle preflight OPTIONS request (optional)
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

// Get the user ID from the request
$user_id = $_GET['id'];

// Prepare delete query
$query = "DELETE FROM login_info WHERE login_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'User deleted successfully.']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Error deleting user.']);
}

$stmt->close();
$conn->close();
?>
