<?php
include('db.php');

// Handle POST request for course deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (isset($input['id'])) {
        $id = $input['id'];

        // SQL to delete the course based on the provided ID
        $sql = "DELETE FROM courses WHERE id = ?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Course deleted successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to delete course']);
        }

        $stmt->close();
        $conn->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid input']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>
