<?php
// fetch_login_info.php
include 'db.php'; // Include your database connection

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
// Get the posted data
$data = json_decode(file_get_contents("php://input"));
$email = $data->email;

try {
    // Query the login_info table for the username and password based on the email
    $stmt = $conn->prepare("SELECT uname, password FROM login_info WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($uname, $password);

    if ($stmt->fetch()) {
        // Return username and password if found
        echo json_encode(['uname' => $uname, 'password' => $password]);
    } else {
        // Return empty result if no matching record found
        echo json_encode(['uname' => null, 'password' => null]);
    }

    $stmt->close();
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}

$conn->close();
?>
