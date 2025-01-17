<?php

include('db.php');
header('Content-Type: application/json');

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

// Decode the JSON body
$data = json_decode(file_get_contents('php://input'), true);

// Debugging: Log the received data
file_put_contents('log.txt', print_r($data, true), FILE_APPEND);

// Check if 'email' is present
$email = $data['email'] ?? '';
if (!$email) {
    echo json_encode(['success' => false, 'message' => 'Email is required']);
    exit;
}

// Prepare the query
$query = "DELETE FROM trainer_info WHERE email = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('s', $email);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to delete trainer']);
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>
