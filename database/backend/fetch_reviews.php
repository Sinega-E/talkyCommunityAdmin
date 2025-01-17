<?php
header('Content-Type: application/json');

include 'db.php'; // Include your database connection file

try {
    $query = "SELECT id, reviewType, name, role, text, audioFile, videoFile, status FROM reviews";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "success" => true,
        "data" => $reviews
    ]);
} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "message" => "Error fetching reviews: " . $e->getMessage()
    ]);
}
?>
