<?php
include('db.php');
error_log(print_r($_POST, true)); // This will log $_POST data to the server error log

// Create necessary directories for file uploads
$uploadDir = __DIR__ . '/uploads/';
$photoDir = $uploadDir . 'photos/';
$audioDir = $uploadDir . 'audio/';

if (!is_dir($photoDir)) {
    mkdir($photoDir, 0755, true);
}
if (!is_dir($audioDir)) {
    mkdir($audioDir, 0755, true);
}

$response = ['status' => 'error', 'message' => 'Unknown error.'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect trainer data from the form
    $fullName = $_POST['tfullName'];
    $dob = $_POST['tdob'];
    $gender = $_POST['tgender'];
    $contact = $_POST['tcontact'];
    $email = $_POST['temail'];
    $specialization = $_POST['tspecialization'];
    $qualification = $_POST['qualification'];
    $otherQualification = isset($_POST['otherQualification']) ? $_POST['otherQualification'] : null;
    $experience = $_POST['texperience'];
    $courses = isset($_POST['courses']) && is_array($_POST['courses']) ? implode(",", $_POST['courses']) : '';
    $profileHeadline = $_POST['headline'];
    $workType = $_POST['workType'];
    $teachingMode = $_POST['teachingMode'];
    $availabilityData = isset($_POST['availabilityData']) ? $_POST['availabilityData'] : null;
    $consent = isset($_POST['consent']) ? 1 : 0;

    // // Validate required fields
    // if (empty($fullName) || empty($dob) || empty($contact) || empty($email) || empty($specialization) || empty($qualification) || empty($experience) || empty($profileHeadline) || empty($workType) || empty($teachingMode) || empty($consent)) {
    //     $response['message'] = `Please fill in all required fields. `;
        
    //     echo json_encode($response);
    //     exit();
    // }



   
     error_log("Availability Data: " . print_r($availabilityData, true));

    // Repeat for other fields to identify the problematic one
    
    // Decode JSON input if it's provided as a string
    if ($availabilityData) {
        // Ensure availabilityData is valid JSON
        $decodedAvailabilityData = json_decode($availabilityData, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            // Re-encode it as clean JSON to remove slashes or unnecessary characters
            $availabilityData = json_encode($decodedAvailabilityData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        } else {
            $response['message'] = "Invalid JSON in availability data.";
            echo json_encode($response);
            exit();
        }
    } else {
        $availabilityData = null; // Ensure null if not provided
    }
    
    // If 'Other' is selected, use the provided qualification
    if ($qualification === 'Other' && $otherQualification) {
        $qualification = $otherQualification;
    }



    // Insert trainer data into the trainers table
    $trainerStmt = $conn->prepare("INSERT INTO trainer_info (fullName, dob, gender, contact, email, specialization, 
                                  qualification, otherQualification, experience, courses, profileHeadline, workType, 
                                  teachingMode, availabilityData, consent) 
                                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $trainerStmt->bind_param(
        "sssssssssssssss",
        $fullName, $dob, $gender, $contact, $email, $specialization,
        $qualification, $otherQualification, $experience, $courses, $profileHeadline, $workType, $teachingMode,
        $availabilityData, $consent
    );

    if (!$trainerStmt->execute()) {
        $response['message'] = "Error inserting trainer data: " . $trainerStmt->error;
        echo json_encode($response);
        exit();
    }

    // Get the inserted trainer_id
    $trainerId = $trainerStmt->insert_id;
    $trainerStmt->close();

    // Handle photo upload
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $photoName = $_FILES['photo']['name'];
        $tempPhotoName = $_FILES['photo']['tmp_name'];
        $photoPath = $photoDir . basename($photoName);

        if (!in_array($_FILES['photo']['type'], ['image/jpeg', 'image/png']) || $_FILES['photo']['size'] > 2 * 1024 * 1024) {
            $response['message'] = "Invalid photo type or size exceeds 2MB.";
            echo json_encode($response);
            exit();
        }

        if (move_uploaded_file($tempPhotoName, $photoPath)) {
            $photoStmt = $conn->prepare("INSERT INTO trainer_photos (id, photo_name, photo_path, file_type, photo_size) 
                                         VALUES (?, ?, ?, ?, ?)");

            $photoMimeType = mime_content_type($photoPath);
            $photoSize = filesize($photoPath);

            $photoStmt->bind_param("isssi", $trainerId, $photoName, $photoPath, $photoMimeType, $photoSize);

            if (!$photoStmt->execute()) {
                $response['message'] = "Error inserting photo metadata: " . $photoStmt->error;
                echo json_encode($response);
                exit();
            }

            $photoStmt->close();
        } else {
            $response['message'] = "Error uploading photo.";
            echo json_encode($response);
            exit();
        }
    }

    // Handle audio note upload
    if (isset($_FILES['audioNote']) && $_FILES['audioNote']['error'] === UPLOAD_ERR_OK) {
        $audioName = $_FILES['audioNote']['name'];
        $tempAudioName = $_FILES['audioNote']['tmp_name'];
        $audioPath = $audioDir . basename($audioName);

        if (!in_array($_FILES['audioNote']['type'], ['audio/mpeg', 'audio/wav']) || $_FILES['audioNote']['size'] > 5 * 1024 * 1024) {
            $response['message'] = "Invalid audio type or size exceeds 5MB.";
            echo json_encode($response);
            exit();
        }

        if (move_uploaded_file($tempAudioName, $audioPath)) {
            $audioStmt = $conn->prepare("INSERT INTO trainer_audio (id, audio_name, audio_path, file_type, audio_size) 
                                         VALUES (?, ?, ?, ?, ?)");

            $audioMimeType = mime_content_type($audioPath);
            $audioSize = filesize($audioPath);

            $audioStmt->bind_param("isssi", $trainerId, $audioName, $audioPath, $audioMimeType, $audioSize);

            if (!$audioStmt->execute()) {
                $response['message'] = "Error inserting audio metadata: " . $audioStmt->error;
                echo json_encode($response);
                exit();
            }

            $audioStmt->close();
        } else {
            $response['message'] = "Error uploading audio note.";
            echo json_encode($response);
            exit();
        }
    }

    // Array of required fields
$requiredFields = [
    'tfullName' => 'Full Name is required.',
    'tdob' => 'Date of Birth is required.',
    'tcontact' => 'Contact is required.',
    'temail' => 'Email is required.',
    'tspecialization' => 'Specialization is required.',
    'qualification' => 'Qualification is required.',
    'texperience' => 'Experience is required.',
    'headline' => 'Profile Headline is required.',
    'workType' => 'Work Type is required.',
    'teachingMode' => 'Teaching Mode is required.'
];

// Check each required field
foreach ($requiredFields as $field => $message) {
    if (empty(trim($_POST[$field] ?? ''))) {
        $response['message'] = $message;
        echo json_encode($response);
        exit();
    }
}
    $response['status'] = 'success';
    $response['message'] = "New trainer record created successfully.";
    echo json_encode($response);

    $conn->close();
}
?>
