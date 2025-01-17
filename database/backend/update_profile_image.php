<?php
// upload_profile_image.php
include 'db.php'; // Ensure this file establishes a valid PDO connection as $pdo

// Set headers for CORS
header("Access-Control-Allow-Origin: *"); // Adjust to match your frontend origin
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['photo']) && isset($_POST['trainerId'])) {
    $trainerId = intval($_POST['trainerId']);
    $photo = $_FILES['photo'];

    // Validate the uploaded file
    if ($photo['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(['status' => 'error', 'message' => 'File upload error: ' . $photo['error']]);
        exit();
    }

    // Define the upload directory and ensure it exists
    $targetDir = "uploads/photos/";
    if (!is_dir($targetDir)) {
        if (!mkdir($targetDir, 0777, true) && !is_dir($targetDir)) {
            echo json_encode(['status' => 'error', 'message' => 'Failed to create upload directory.']);
            exit();
        }
    }

    // Create target file path
    $photoName = uniqid() . "_" . basename($photo['name']); // Add unique prefix to avoid conflicts
    $targetFile = $targetDir . $photoName;

    // Move the uploaded file
    if (!move_uploaded_file($photo['tmp_name'], $targetFile)) {
        echo json_encode(['status' => 'error', 'message' => 'Failed to move the uploaded file.']);
        exit();
    }

    // Get MIME type and file size
    $photoMimeType = mime_content_type($targetFile);
    $photoSize = filesize($targetFile);

    // Update the database
    $photoQuery = "UPDATE trainer_photos 
                   SET photo_name = :photoName, 
                       photo_path = :photoPath, 
                       file_type = :fileType, 
                       photo_size = :fileSize, 
                       uploaded_at = NOW() 
                   WHERE id = :trainerId";

    $stmt = $pdo->prepare($photoQuery);
    $stmt->bindParam(':photoName', $photoName, PDO::PARAM_STR);
    $stmt->bindParam(':photoPath', $targetFile, PDO::PARAM_STR);
    $stmt->bindParam(':fileType', $photoMimeType, PDO::PARAM_STR);
    $stmt->bindParam(':fileSize', $photoSize, PDO::PARAM_INT);
    $stmt->bindParam(':trainerId', $trainerId, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Profile image updated successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update profile image in the database.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}
?>
