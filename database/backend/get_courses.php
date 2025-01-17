<?php
// Database connection
include('db.php');

header("Access-Control-Allow-Origin: *"); // Allow access from your React app
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // SQL query to fetch all courses including the ID
    $sql = "SELECT id, category, subcategory, course_name, course_description, duration, difficulty, price, start_date, format, skills_learned FROM courses";
    
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $courses = [];

        // Fetch each course as an associative array
        while ($row = $result->fetch_assoc()) {
            $courses[] = $row;
        }

        // Return courses as JSON
        echo json_encode($courses);
    } else {
        // No courses found
        echo json_encode([]);
    }

    // Close the connection
    $conn->close();
} else {
    // Invalid request method
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
?>
