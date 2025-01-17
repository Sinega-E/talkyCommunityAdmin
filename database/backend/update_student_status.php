<?php
// Include database connection file
include 'db.php';

// Get the POST data
$data = json_decode(file_get_contents("php://input"));
$studentId = $data->id;
$status = $data->status;

// Update the status in the database
$query = "UPDATE students SET status = :status WHERE id = :id";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':status', $status);
$stmt->bindParam(':id', $studentId);

if ($stmt->execute()) {
    echo json_encode(['message' => 'Status updated successfully']);
} else {
    echo json_encode(['message' => 'Failed to update status']);
}
?>
