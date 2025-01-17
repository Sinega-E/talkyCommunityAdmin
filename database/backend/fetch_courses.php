<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require 'db.php';

$subcategory = $_GET['subcategory'] ?? '';

try {
    $stmt = $pdo->prepare("SELECT * FROM courses WHERE subcategory = ?");
    $stmt->execute([$subcategory]);
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($courses);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
