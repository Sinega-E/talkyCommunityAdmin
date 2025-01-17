<?php
include 'db.php';
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $targetDir = "uploads/audio/intro_audio/";
    $email = $_POST['email'];
    $file = $_FILES['audio'];

    // Generate a unique file name
    $fileName = uniqid() . "_" . basename($file['name']);
    $targetFilePath = $targetDir . $fileName;

    // Move the uploaded file to the target directory
    if (move_uploaded_file($file['tmp_name'], $targetFilePath)) {
        // Update the intro_audio column in the database
        $stmt = $conn->prepare("UPDATE trainer_info SET intro_audio = ? WHERE email = ?");
        $stmt->bind_param("ss", $targetFilePath, $email);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'audioPath' => $targetFilePath]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update database']);
        }

        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to upload audio']);
    }

    $conn->close();
}
?>
