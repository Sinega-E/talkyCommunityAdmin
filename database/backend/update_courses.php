<?php
include('db.php');

// Set CORS headers for preflight and main requests
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200); // Respond OK to preflight
    exit();
}

// Continue with POST request handling
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    // Validate required fields
    if (isset($input['id'])) {
        $id = $input['id'];
        
        // Dynamically prepare the update fields
        $fields = [
            'category' => $input['category'] ?? null,
            'subcategory' => $input['subcategory'] ?? null,
            'course_name' => $input['course_name'] ?? null,
            'course_description' => $input['course_description'] ?? null,
            'duration' => $input['duration'] ?? null,
            'difficulty' => $input['difficulty'] ?? null,
            'price' => $input['price'] ?? null,
            'start_date' => $input['start_date'] ?? null,
            'format' => $input['format'] ?? null,
            'skills_learned' => $input['skills_learned'] ?? null,
        ];

        // Build the dynamic SQL query
        $set_clause = [];
        $params = [];
        $types = '';

        foreach ($fields as $column => $value) {
            if ($value !== null) {
                $set_clause[] = "$column = ?";
                $params[] = $value;
                $types .= 's';
            }
        }

        // Add id as the last parameter
        $params[] = $id;
        $types .= 'i';

        $sql = "UPDATE courses SET " . implode(', ', $set_clause) . " WHERE id = ?";

        if ($stmt = $conn->prepare($sql)) {
            // Dynamically bind parameters
            $stmt->bind_param($types, ...$params);

            if ($stmt->execute()) {
                echo json_encode(['status' => 'success', 'message' => 'Course updated successfully']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to update course: ' . $stmt->error]);
            }

            $stmt->close();
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to prepare SQL statement']);
        }

        $conn->close();
    } else {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Invalid input: Missing id field']);
    }
} else {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>
