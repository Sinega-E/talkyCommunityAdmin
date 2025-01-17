<?php
// student_submit.php

header('Content-Type: application/json');
include('db.php'); // Include database connection

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate form data
    $fullName = trim($_POST['fullName']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $dob = trim($_POST['dob']);
    $gender = trim($_POST['gender']);
    $course = trim($_POST['course']);
    $startDate = trim($_POST['startDate']);
    $consent = trim($_POST['consent']);

    // Check if startDate is provided and convert it to the MySQL DATETIME format (YYYY-MM-DD HH:MM:SS)
    if (!empty($startDate)) {
        // Convert the datetime-local format (YYYY-MM-DDTHH:MM) to MySQL datetime format (YYYY-MM-DD HH:MM:SS)
        $startDateFormatted = date("Y-m-d H:i:s", strtotime($startDate));
    } else {
        $startDateFormatted = null; // Handle empty or invalid date
    }

    // Check if email already exists
    $emailCheckQuery = "SELECT email FROM students WHERE email = ? LIMIT 1";
    $emailStmt = $conn->prepare($emailCheckQuery);
    $emailStmt->bind_param('s', $email);
    $emailStmt->execute();
    $emailStmt->store_result();
    $emailExists = $emailStmt->num_rows > 0;
    $emailStmt->close();

    if ($emailExists) {
        // Send error response for duplicate email
        echo json_encode([
            'status' => 'error',
            'message' => 'Email already exists'
        ]);
        exit; // Stop further processing if email exists
    }

    // Check if phone number is exactly 10 digits
    if (strlen($phone) != 10) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Phone number should be exactly 10 digits'
        ]);
        exit; // Stop further processing if phone number is invalid
    }

    // Check if phone already exists
    $phoneCheckQuery = "SELECT phone FROM students WHERE phone = ? LIMIT 1";
    $phoneStmt = $conn->prepare($phoneCheckQuery);
    $phoneStmt->bind_param('s', $phone);
    $phoneStmt->execute();
    $phoneStmt->store_result();
    $phoneExists = $phoneStmt->num_rows > 0;
    $phoneStmt->close();

    if ($phoneExists) {
        // Send error response for duplicate phone
        echo json_encode([
            'status' => 'error',
            'message' => 'Phone number already exists'
        ]);
        exit; // Stop further processing if phone exists
    }

    // Insert new student data
    $insertQuery = "INSERT INTO students (fullName, email, phone, dob, gender, course, startDate, consent)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($insertQuery);

    if ($stmt) {
        // Bind parameters and insert data into the database
        $stmt->bind_param('ssissssi', $fullName, $email, $phone, $dob, $gender, $course, $startDateFormatted, $consent);

        if ($stmt->execute()) {
            // Send success response
            echo json_encode([
                'status' => 'success',
                'message' => 'Student registered successfully'
            ]);
        } else {
            // Send error response for database error
            echo json_encode([
                'status' => 'error',
                'message' => 'Failed to register student. Please try again.'
            ]);
        }
        $stmt->close();
    } else {
        // Send error response if statement preparation fails
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to prepare database statement.'
        ]);
    }

    // Close connection
    $conn->close();
} else {
    // If not POST request, send error response
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid request method'
    ]);
}
?>
