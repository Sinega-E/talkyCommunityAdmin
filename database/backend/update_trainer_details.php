<?php
include 'db.php'; // Include your DB connection file

header("Access-Control-Allow-Origin: *"); // Allow all origins, or replace '*' with a specific domain like 'http://localhost:3000'
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); // Specify allowed methods
header("Access-Control-Allow-Headers: Content-Type, Authorization"); // Specify allowed headers
header("Content-Type: application/json"); // Set response type to JSON

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ensure 'id' and 'email' are passed in the form data
    if (!isset($_POST['id']) || !isset($_POST['email'])) {
        echo json_encode(['status' => 'error', 'message' => 'ID and email are required.']);
        exit();
    }

    $id = $_POST['id'];  // Trainer ID (used to identify the row to update)
    $email = $_POST['email']; // Trainer email (used to identify the row to update)
    $fullName = $_POST['fullName'] ?? '';  // Default to empty string if not set
    $dob = $_POST['dob'] ?? '';
    $gender = $_POST['gender'] ?? '';
    $contact = $_POST['contact'] ?? '';
    $specialization = $_POST['specialization'] ?? '';
    $qualification = $_POST['qualification'] ?? '';
    $otherQualification = !empty($_POST['otherQualification']) ? $_POST['otherQualification'] : null;  // Set to empty string if not provided
    $experience = $_POST['experience'] ?? '';  // Set to empty string if not provided
    $courses = isset($_POST['courses']) ? implode(",", $_POST['courses']) : '';
    $profileHeadline = $_POST['profileHeadline'] ?? '';  // Set to empty string if not provided
    $workType = $_POST['workType'] ?? '';
    $teachingMode = $_POST['teachingMode'] ?? '';
    $availabilityData = $_POST['availabilityData'] ?? null;
    $consent = isset($_POST['consent']) ? 1 : 0;

    // Update `trainer_info` table
    $query = "UPDATE trainer_info 
              SET fullName=?, dob=?, gender=?, contact=?, specialization=?, qualification=?, 
                  otherQualification=?, experience=?, courses=?, profileHeadline=?, workType=?, teachingMode=?, 
                  availabilityData=?, consent=? 
              WHERE id=? AND email=?";
    $stmt = $conn->prepare($query);

    // Fix bind_param argument count: Ensure the correct number of placeholders
    $stmt->bind_param(
        "ssssssssssssssis",
        $fullName, $dob, $gender, $contact, $specialization, $qualification,
        $otherQualification, $experience, $courses, $profileHeadline, $workType, $teachingMode, $availabilityData, $consent, $id, $email
    );

    if (!$stmt->execute()) {
        echo json_encode(['status' => 'error', 'message' => 'Failed to update trainer details.']);
        exit();
    }
    $stmt->close();

    echo json_encode(['status' => 'success', 'message' => 'Trainer details and photo updated successfully.']);
}

$conn->close();
?>
