<?php
// get_profile_image.php
include 'db.php';

// Get protocol and host dynamically to construct base URL
$protocol = ($_SERVER['HTTPS'] ?? 'off') === 'on' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
$baseUrl = "{$protocol}://{$host}/tc-final-pro/database/backend";

// Check if trainerId is provided
$trainerId = $_GET['id'] ?? null;
if ($trainerId) {
    // Prepare SQL query to get the profile image information
    $sql = "SELECT photo_id, photo_name, CONCAT(:baseUrl, '/uploads/photos/', photo_name) AS photo_path, 
                   file_type, photo_size, uploaded_at 
            FROM trainer_photos WHERE id = :trainerId";
    
    // Prepare the statement
    $stmt = $pdo->prepare($sql);
    
    // Bind parameters
    $stmt->bindParam(':trainerId', $trainerId, PDO::PARAM_INT);
    $stmt->bindParam(':baseUrl', $baseUrl, PDO::PARAM_STR);  // Bind the base URL parameter
    
    // Execute the query
    $stmt->execute();
    
    // Fetch the result
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        echo json_encode(['data' => $result], JSON_UNESCAPED_SLASHES);
    } else {
        echo json_encode(['data' => null], JSON_UNESCAPED_SLASHES);
    }
} else {
    echo json_encode(['error' => 'Trainer ID not provided'], JSON_UNESCAPED_SLASHES);
}
?>
