<?php
header("Content-Type: application/json");
require_once 'db.php';

$email = $_GET['email'] ?? '';

if (empty($email)) {
    echo json_encode(["status" => "error", "message" => "Email is required."]);
    exit();
}

$sql = "SELECT * FROM trainer_info WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $trainerDetails = $result->fetch_assoc();
    echo json_encode($trainerDetails);
} else {
    echo json_encode(["status" => "error", "message" => "Trainer details not found."]);
}

$stmt->close();
$conn->close();
?>
