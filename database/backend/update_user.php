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

// Get JSON input from the request
$data = json_decode(file_get_contents('php://input'), true);

// Extract data
$login_id = $data['login_id'];
$name = $data['name'];
$email = $data['email'];
$role = $data['role'];
$password = $data['password']; // Plain-text password

// Check if the email exists in the database
if (!empty($password)) {
    // If password is provided, use it as is (no hashing)
    $query = "UPDATE login_info SET uname = ?, email = ?, role = ?, password = ? WHERE login_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssi", $name, $email, $role, $password, $login_id);
} else {
    // If no password is provided, do not change the password
    $query = "UPDATE login_info SET uname = ?, email = ?, role = ? WHERE login_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssi", $name, $email, $role, $login_id);
}

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'User updated successfully.']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Error updating user.']);
}

$stmt->close();
$conn->close();
?>
