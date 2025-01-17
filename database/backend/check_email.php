<?php
header("Content-Type: application/json");
require_once 'db.php';

$data = json_decode(file_get_contents('php://input'), true);
$email = $data['email'] ?? '';

if (empty($email)) {
    echo json_encode(["exists" => false]);
    exit();
}

$sql = "SELECT COUNT(*) AS count FROM login_info WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

echo json_encode(["exists" => $row['count'] > 0]);

$stmt->close();
$conn->close();
?>
