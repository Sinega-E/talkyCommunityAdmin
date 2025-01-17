<?php
include 'db.php';  // Include your database connection file

// Allow cross-origin requests and set headers
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json');

// Query to fetch demo bookings data
$query = "SELECT db.id, db.trainer_id, db.trainer_name, db.student_name, db.student_email, db.student_phone,db.status, db.booking_date, ti.fullName AS trainer_fullname 
          FROM demo_bookings db
          JOIN trainer_info ti ON db.trainer_id = ti.id"; // Join with trainer_info to get trainer name
$result = mysqli_query($conn, $query);

// Initialize an array to store the data
$demoBookings = [];

// Fetch and store the data
while ($row = mysqli_fetch_assoc($result)) {
    $demoBookings[] = $row;
}

// Return the data as JSON
echo json_encode($demoBookings, JSON_UNESCAPED_SLASHES);
?>
