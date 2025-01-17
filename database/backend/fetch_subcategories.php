<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require 'db.php';

$category = $_GET['category'] ?? '';

try {
    $stmt = $pdo->prepare("SELECT DISTINCT subcategory FROM courses WHERE category = ?");
    $stmt->execute([$category]);
    $subcategories = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo json_encode($subcategories);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
