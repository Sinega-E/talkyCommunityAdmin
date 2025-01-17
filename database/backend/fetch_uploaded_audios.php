<?php
include 'db.php';
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json');

// Query to get the audio paths of all trainers
$query = "SELECT email, intro_audio FROM trainer_info WHERE intro_audio IS NOT NULL";
$result = mysqli_query($conn, $query);

$audios = [];
while ($row = mysqli_fetch_assoc($result)) {
    $audios[] = $row;
}

// Use JSON_UNESCAPED_SLASHES to prevent slashes from being escaped in URLs or paths
echo json_encode($audios, JSON_UNESCAPED_SLASHES);
?>
