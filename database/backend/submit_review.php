<?php
header('Content-Type: application/json');

include 'db.php'; // Include your database connection file
$protocol = ($_SERVER['HTTPS'] ?? 'off') === 'on' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
$baseUrl = "{$protocol}://{$host}/tc-final-pro/database/backend";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $reviewType = $_POST['reviewType'];
        $name = $_POST['name'];
        $role = $_POST['role'];
        $text = isset($_POST['text']) ? $_POST['text'] : null;

        // Define base URL for the files
        // $base_url = 'http://localhost/tc-final-pro/database/backend/';

        // Handle file uploads
        $audioFile = null;
        if (isset($_FILES['audioFile']) && $_FILES['audioFile']['error'] == 0) {
            $audioFileName = basename($_FILES['audioFile']['name']);
            $audioFilePath = 'uploads/audio/' . $audioFileName;
            move_uploaded_file($_FILES['audioFile']['tmp_name'], $audioFilePath);
            // Concatenate the base URL and file path correctly
            $audioFile = $base_url . $audioFilePath;
        }

        $videoFile = null;
        if (isset($_FILES['videoFile']) && $_FILES['videoFile']['error'] == 0) {
            $videoFileName = basename($_FILES['videoFile']['name']);
            $videoFilePath = 'uploads/video/' . $videoFileName;
            move_uploaded_file($_FILES['videoFile']['tmp_name'], $videoFilePath);
            // Concatenate the base URL and file path correctly
            $videoFile = $base_url . $videoFilePath;
        }

        // Prepare data for insertion
        $query = "INSERT INTO reviews (reviewType, name, role, text, audioFile, videoFile) 
                  VALUES (:reviewType, :name, :role, :text, :audioFile, :videoFile)";
        $stmt = $pdo->prepare($query);

        $stmt->bindParam(':reviewType', $reviewType);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':role', $role);
        $stmt->bindParam(':text', $text);
        $stmt->bindParam(':audioFile', $audioFile);
        $stmt->bindParam(':videoFile', $videoFile);

        if ($stmt->execute()) {
            $reviewData = [
                "reviewType" => $reviewType,
                "name" => $name,
                "role" => $role,
                "text" => $text,
                "audioFile" => $audioFile,
                "videoFile" => $videoFile
            ];

            // Encode response without escaping slashes
            echo json_encode([
                "success" => true,
                "message" => "Review submitted successfully",
                "data" => $reviewData
            ], JSON_UNESCAPED_SLASHES);
        } else {
            throw new Exception("Failed to execute the query.");
        }
    } catch (Exception $e) {
        echo json_encode([
            "success" => false,
            "message" => "Error submitting review: " . $e->getMessage()
        ], JSON_UNESCAPED_SLASHES);
    }
} else {
    echo json_encode([
        "success" => false,
        "message" => "Invalid request method."
    ], JSON_UNESCAPED_SLASHES);
}
?>
