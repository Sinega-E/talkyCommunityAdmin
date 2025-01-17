<?php
include 'db.php';
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json');

// Get the JSON input
$data = json_decode(file_get_contents('php://input'));

$email = isset($data->email) ? $data->email : '';
$contact = isset($data->contact) ? $data->contact : '';

if (empty($email) && empty($contact)) {
    echo json_encode(['exists' => false]);
    exit;
}

// Prepare the SQL query
$query = "SELECT * FROM trainer_info WHERE email = ? OR contact = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('ss', $email, $contact);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo json_encode(['exists' => true]);
} else {
    echo json_encode(['exists' => false]);
}

$stmt->close();
$conn->close();
?>
