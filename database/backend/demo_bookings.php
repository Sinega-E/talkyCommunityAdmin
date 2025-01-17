<?php
include 'db.php';
header("Access-Control-Allow-Origin: *"); // Adjust to match your frontend origin
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");
// Get the request data
$data = json_decode(file_get_contents('php://input'), true);

// Extract student and trainer info
$trainerId = $data['trainerId'];
$trainerName = $data['trainerName'];
$studentName = $data['studentName'];
$studentEmail = $data['studentEmail'];
$studentPhone = $data['studentPhone'];

// Insert the demo booking into the database
$query = "INSERT INTO demo_bookings (trainer_id, trainer_name, student_name, student_email, student_phone)
          VALUES ('$trainerId', '$trainerName', '$studentName', '$studentEmail', '$studentPhone')";

if (mysqli_query($conn, $query)) {
    echo json_encode(['message' => 'Demo booking successful']);
} else {
    echo json_encode(['message' => 'Demo booking failed']);
}
?>
