<?php
header("Access-Control-Allow-Origin: *"); // Allow all origins
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json"); // Set the response type to JSON

// Include database connection
require_once 'db.php';

// Get the input from the request
$input = json_decode(file_get_contents('php://input'), true);
$role = $input['role'] ?? '';
$username = $input['username'] ?? '';
$password = $input['password'] ?? '';

// Check if inputs are provided
if (empty($role) || empty($username) || empty($password)) {
    echo json_encode([
        "status" => "error",
        "message" => "All fields are required."
    ]);
    exit();
}

// Query to check if user exists with the provided username, password, and role
$sql = "SELECT * FROM login_info WHERE uname = ? AND password = ? AND role = ?";

// Prepare the statement
$stmt = $conn->prepare($sql);

// Bind parameters
$stmt->bind_param("sss", $username, $password, $role);

$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();

    // If the role is 'admin', no further details need to be fetched
    if ($role === 'admin') {
        echo json_encode([
            "status" => "success",
            "message" => "Admin login successful.",
            "user" => [
                "login_id" => $user['login_id'],
                "role" => $user['role'],
                "uname" => $user['uname'],
                "email" => $user['email']
            ]
        ]);
    } else {
        // Fetch additional trainer details from the trainer_info table using the email
        $email = $user['email'];
        $sqlTrainerInfo = "SELECT * FROM trainer_info WHERE email = ?";

        // Prepare the statement for the trainer_info query
        $stmtTrainer = $conn->prepare($sqlTrainerInfo);
        $stmtTrainer->bind_param("s", $email);
        $stmtTrainer->execute();
        $trainerInfoResult = $stmtTrainer->get_result();

        if ($trainerInfoResult->num_rows === 1) {
            $trainerInfo = $trainerInfoResult->fetch_assoc();

            // Merge login info with trainer details and return the response
            $response = [
                "status" => "success",
                "message" => "Login successful.",
                "user" => [
                    "login_id" => $user['login_id'],
                    "role" => $user['role'],
                    "uname" => $user['uname'],
                    "email" => $user['email'],
                    "trainer_details" => $trainerInfo
                ]
            ];
            echo json_encode($response);
        } else {
            echo json_encode([
                "status" => "error",
                "message" => "No trainer details found for the given email."
            ]);
        }

        // Close the trainer statement
        $stmtTrainer->close();
    }

    // Close the login statement
    $stmt->close();
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid username, password, or role."
    ]);
}

// Close the database connection
$conn->close();
?>
