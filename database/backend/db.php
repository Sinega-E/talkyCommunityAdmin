<?php
header("Access-Control-Allow-Origin: *"); // Allow all origins for development
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");  // Set the response type to JSON
// Get the current host dynamically (e.g., localhost or domain)
$protocol = ($_SERVER['HTTPS'] ?? 'off') === 'on' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];


// Database credentials
$servername = "localhost";
$username = "talky_user";
$password = "TalkyComm@1";
$dbname = "talky_community";
$REACT_APP_API_BASE_URL =  "{$protocol}://{$host}/tc-final-pro/";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

try {
    // Create a PDO instance
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // Set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    die(); // Exit the script if database connection fails
}
// Check connection
if ($conn->connect_error) {
    // Return a JSON response if the connection fails
    echo json_encode([
        "status" => "error",
        "message" => "Database connection failed: " . $conn->connect_error
    ]);
    exit(); // Stop script execution
}

// If connection is successful, no output is necessary
?>
