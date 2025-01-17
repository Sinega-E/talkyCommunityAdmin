<?php
header('Content-Type: application/json');

include 'db.php'; // Include your database connection file

$data = json_decode(file_get_contents('php://input'), true);
$reviewId = $data['reviewId'];

try {
    $query = "DELETE FROM reviews WHERE id = :id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':id', $reviewId);
    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Review deleted"]);
    } else {
        throw new Exception('Failed to delete the review');
    }
} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
?>
