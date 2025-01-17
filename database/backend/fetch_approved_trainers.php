<?php
include 'db.php';
$protocol = ($_SERVER['HTTPS'] ?? 'off') === 'on' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
$baseUrl = "{$protocol}://{$host}/tc-final-pro/database/backend";

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json');

$query = "
    SELECT ti.id, ti.fullName, ti.email, ti.specialization, ti.experience, ti.courses, 
           ti.profileHeadline, ti.teachingmode, ti.availabilityData, ti.intro_audio, tp.photo_path
    FROM trainer_info ti
    LEFT JOIN trainer_photos tp ON ti.id = tp.id
    WHERE ti.status = 'approved'
";
$result = mysqli_query($conn, $query);

$trainers = [];
while ($row = mysqli_fetch_assoc($result)) {
    if (!empty($row['photo_path'])) {
        if (strpos($row['photo_path'], 'uploads/') === false) {
            $row['photo_path'] = $baseUrl . '/' . ltrim($row['photo_path'], '/');
        } else {
            $row['photo_path'] = preg_replace('/.*?(uploads\/.*)/', $baseUrl . '/$1', $row['photo_path']);
        }
    }
    
    if (!empty($row['intro_audio'])) {
        if (strpos($row['intro_audio'], 'uploads/') === false) {
            $row['intro_audio'] = $baseUrl . '/' . ltrim($row['intro_audio'], '/');
        } else {
            $row['intro_audio'] = preg_replace('/.*?(uploads\/.*)/', $baseUrl . '/$1', $row['intro_audio']);
        }
    }
    
    $trainers[] = $row; 
}
echo json_encode($trainers, JSON_UNESCAPED_SLASHES);
?>
