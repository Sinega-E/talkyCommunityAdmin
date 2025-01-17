<?php
header("Content-Type: application/json");
require_once 'db.php';

// Get the input data
$data = json_decode(file_get_contents('php://input'), true);
$name = $data['name'] ?? '';
$email = $data['email'] ?? '';
$role = $data['role'] ?? '';
$password = $data['password'] ?? '';

// Validate required fields
if (empty($name) || empty($email) || empty($role) || empty($password)) {
    echo json_encode(["status" => "error", "message" => "All fields are required."]);
    exit();
}

// Check if the username already exists
$sqlCheck = "SELECT COUNT(*) AS count FROM login_info WHERE uname = ?";
$stmtCheck = $conn->prepare($sqlCheck);
$stmtCheck->bind_param("s", $name);
$stmtCheck->execute();
$resultCheck = $stmtCheck->get_result();
$rowCheck = $resultCheck->fetch_assoc();

if ($rowCheck['count'] > 0) {
    echo json_encode(["status" => "error", "message" => "Username already exists. Try creating with another username."]);
    $stmtCheck->close();
    $conn->close();
    exit();
}

// Insert the new user
$sql = "INSERT INTO login_info (uname, email, role, password) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssss", $name, $email, $role, $password);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "User created successfully."]);
} else {
    echo json_encode(["status" => "error", "message" => "Error creating user."]);
}

// Close the statements and connection
$stmtCheck->close();
$stmt->close();
$conn->close();
?>
