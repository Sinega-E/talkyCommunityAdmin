<?php
// Include database connection
include('db.php');

// Set headers to handle CORS and JSON responses
header("Access-Control-Allow-Origin: *"); // Adjust this to match your frontend URL
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

// Handle preflight OPTIONS request for CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Check for POST request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Decode JSON data from the request body
    $input = json_decode(file_get_contents('php://input'), true);

    // Validate the input and assign values
    $category = $input['category'] ?? '';
    $subcategory = $input['subcategory'] ?? '';
    $course_name = $input['course_name'] ?? '';
    $course_description = $input['course_description'] ?? '';
    $duration = $input['duration'] ?? '';
    $difficulty = $input['difficulty'] ?? '';
    $price = $input['price'] ?? '';
    $start_date = $input['start_date'] ?? '';
    $format = $input['format'] ?? '';
    $skills_learned = $input['skills_learned'] ?? '';

    // Validate required fields
    if (empty($category) || empty($subcategory) || empty($course_name) || empty($course_description) || empty($duration) || empty($difficulty) || empty($price) || empty($start_date) || empty($format) || empty($skills_learned)) {
        http_response_code(400); // Bad Request
        echo json_encode(["status" => "error", "message" => "All fields are required."]);
        exit();
    }

    // SQL query to insert the data
    $sql = "INSERT INTO courses (category, subcategory, course_name, course_description, duration, difficulty, price, start_date, format, skills_learned)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    // Prepare the statement
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        http_response_code(500); // Internal Server Error
        echo json_encode(["status" => "error", "message" => "Failed to prepare the SQL statement."]);
        exit();
    }

    // Bind parameters
    $stmt->bind_param("ssssssssss", $category, $subcategory, $course_name, $course_description, $duration, $difficulty, $price, $start_date, $format, $skills_learned);

    // Execute the query and check for success
    if ($stmt->execute()) {
        http_response_code(201); // Created
        echo json_encode(["status" => "success", "message" => "New course added successfully!"]);
    } else {
        http_response_code(500); // Internal Server Error
        echo json_encode(["status" => "error", "message" => "Failed to add course: " . $stmt->error]);
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
} else {
    http_response_code(405); // Method Not Allowed
    echo json_encode(["status" => "error", "message" => "Invalid request method!"]);
}
?>
