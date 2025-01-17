<?php
include 'db.php'; // Ensure this points to the correct database connection script
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json');

try {
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Parse JSON body
    $inputData = json_decode(file_get_contents('php://input'), true);

    // Retrieve input data
    $photoName = isset($inputData['photoName']) ? $inputData['photoName'] : '';
    $audioName = isset($inputData['audioName']) ? $inputData['audioName'] : '';

    if (empty($photoName) || empty($audioName)) {
        echo json_encode(['status' => 'error', 'message' => 'Both photoName and audioName are required']);
        exit;
    }

    // Query to check if the photoName exists in trainer_photos
    $photoQuery = "SELECT COUNT(*) AS photo_count FROM trainer_photos WHERE photo_name = :photoName";
    $photoStmt = $pdo->prepare($photoQuery);
    $photoStmt->bindParam(':photoName', $photoName, PDO::PARAM_STR);
    $photoStmt->execute();
    $photoResult = $photoStmt->fetch(PDO::FETCH_ASSOC);

    // Query to check if the audioName exists in trainer_audio
    $audioQuery = "SELECT COUNT(*) AS audio_count FROM trainer_audio WHERE audio_name = :audioName";
    $audioStmt = $pdo->prepare($audioQuery);
    $audioStmt->bindParam(':audioName', $audioName, PDO::PARAM_STR);
    $audioStmt->execute();
    $audioResult = $audioStmt->fetch(PDO::FETCH_ASSOC);

    // Determine if matches were found
    $photoExists = $photoResult['photo_count'] > 0;
    $audioExists = $audioResult['audio_count'] > 0;

    $response = [
        'status' => 'success',
        'photoMatch' => $photoExists,
        'audioMatch' => $audioExists,
    ];

    echo json_encode($response);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}
