<?php
// Database connection
include 'db.php';
header('Content-Type: application/json');

// Fetch reviews with approved status
$sql = "SELECT * FROM reviews WHERE status = 'approved'";
$result = $conn->query($sql);

$reviews = [
    'text' => [],
    'audio' => [],
    'video' => [],
];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Categorize reviews based on type
        if ($row['reviewType'] === 'text') {
            $reviews['text'][] = $row;
        } elseif ($row['reviewType'] === 'audio') {
            $reviews['audio'][] = $row;
        } elseif ($row['reviewType'] === 'video') {
            $reviews['video'][] = $row;
        }
    }
} else {
    echo json_encode(["error" => "No reviews found."]);
    exit;
}

// Return reviews as JSON
echo json_encode($reviews, JSON_UNESCAPED_SLASHES);
?>
