<?php
// Include the database connection file
include 'db.php';

// Allow all origins to access this resource
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204); // No Content
    exit();
}

// Base URLs for media files
$photo_base_url = $REACT_APP_API_BASE_URL.'database/backend/uploads/photos/';
$audio_base_url = $REACT_APP_API_BASE_URL.'database/backend/uploads/audio/';

// SQL query to fetch all trainer information with photos, audio, and status
$sql = "
  SELECT 
    trainer_info.*, 
    trainer_photos.photo_id, trainer_photos.photo_name, 
    REPLACE(CONCAT('$photo_base_url', trainer_photos.photo_name), '\\\\', '/') AS photo_path, 
    trainer_photos.file_type AS photo_file_type, trainer_photos.photo_size, trainer_photos.uploaded_at AS photo_uploaded_at,
    trainer_audio.audio_id, trainer_audio.audio_name, 
    REPLACE(CONCAT('$audio_base_url', trainer_audio.audio_name), '\\\\', '/') AS audio_path, 
    trainer_audio.file_type AS audio_file_type, trainer_audio.audio_size, trainer_audio.uploaded_at AS audio_uploaded_at
  FROM 
    trainer_info 
  LEFT JOIN 
    trainer_photos ON trainer_info.id = trainer_photos.id
  LEFT JOIN 
    trainer_audio ON trainer_info.id = trainer_audio.id
  ORDER BY 
    trainer_info.created_at DESC
";

// Execute the query
$result = $conn->query($sql);

if ($result) {
    $trainers = [];

    while ($row = $result->fetch_assoc()) {
        $trainerId = $row['id'];

        if (!isset($trainers[$trainerId])) {
            $trainers[$trainerId] = [
                'id' => $row['id'],
                'fullName' => $row['fullName'],
                'dob' => $row['dob'],
                'gender' => $row['gender'],
                'contact' => $row['contact'],
                'email' => $row['email'],
                'specialization' => $row['specialization'],
                'qualification' => $row['qualification'],
                'otherQualification' => $row['otherQualification'],
                'experience' => $row['experience'],
                'courses' => $row['courses'],
                'profileHeadline' => $row['profileHeadline'],
                'workType' => $row['workType'],
                'teachingMode' => $row['teachingMode'],
                'availabilityData' => $row['availabilityData'],
                'consent' => $row['consent'],
                'status' => $row['status'], // Add the status field
                'created_at' => $row['created_at'],
                'updated_at' => $row['updated_at'],
                'photos' => [],
                'audio' => []
            ];
        }

        if ($row['photo_id']) {
            $trainers[$trainerId]['photos'][] = [
                'photo_id' => $row['photo_id'],
                'photo_name' => $row['photo_name'],
                'photo_path' => $row['photo_path'],
                'file_type' => $row['photo_file_type'],
                'photo_size' => $row['photo_size'],
                'uploaded_at' => $row['photo_uploaded_at']
            ];
        }

        if ($row['audio_id']) {
            $trainers[$trainerId]['audio'][] = [
                'audio_id' => $row['audio_id'],
                'audio_name' => $row['audio_name'],
                'audio_path' => $row['audio_path'],
                'file_type' => $row['audio_file_type'],
                'audio_size' => $row['audio_size'],
                'uploaded_at' => $row['audio_uploaded_at']
            ];
        }
    }

    // Output JSON with unescaped slashes
    echo json_encode(array_values($trainers), JSON_UNESCAPED_SLASHES);
} else {
    echo json_encode(['error' => $conn->error], JSON_UNESCAPED_SLASHES);
}

$conn->close();
?>
