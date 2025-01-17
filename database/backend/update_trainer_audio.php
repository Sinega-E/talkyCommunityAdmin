<?php
include 'db.php'; // Include your database connection

// Get the POST data (email and audioPath)
$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['email']) && isset($data['audioPath'])) {
    $email = $data['email'];
    $audioPath = $data['audioPath'];

    // Prepare the SQL query to update the trainer's audio path
    $query = "UPDATE trainer_info SET intro_audio = :audioPath WHERE email = :email";

    // Prepare the statement
    $stmt = $pdo->prepare($query);
    
    // Bind the parameters
    $stmt->bindParam(':audioPath', $audioPath);
    $stmt->bindParam(':email', $email);

    // Execute the query
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Audio updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update audio']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
}
?>
