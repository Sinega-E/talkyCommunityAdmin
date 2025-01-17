<?php
include 'db.php';
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');


if (isset($_GET['id'])) {
    $trainer_id = intval($_GET['id']); // Sanitize the input

    try {
        // Prepare the SQL query
        $stmt = $pdo->prepare('SELECT * FROM trainer_info WHERE id = :id');
        $stmt->bindParam(':id', $trainer_id, PDO::PARAM_INT);
        $stmt->execute();

        // Fetch trainer details
        $trainer = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($trainer) {
            echo json_encode($trainer);
        } else {
            echo json_encode(['success' => false, 'message' => 'Trainer not found']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error fetching trainer details: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid or missing trainer ID']);
}
?>
