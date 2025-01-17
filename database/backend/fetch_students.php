<?php
// Fetch students data from database
include 'db.php'; // Include your database connection file

// Update the query to fetch the 'status' column
$query = "SELECT id, fullName, email, phone, dob, gender, course, startDate, consent, created_at, status FROM students";
$stmt = $pdo->query($query);

$students = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    // You can perform any additional logic for 'status' if needed
    // For now, the 'status' will be fetched directly from the database
    $students[] = $row;
}

// Return the data as JSON
echo json_encode($students);
?>
