<?php
header("Content-Type: application/json");
require_once 'db.php';

// Fetch all records from login_info
$sql = "SELECT * FROM login_info";
$result = $conn->query($sql);

$users = [];
while ($row = $result->fetch_assoc()) {
    $users[] = [
        "login_id" => $row["login_id"],
        "uname" => $row["uname"], // Explicitly include the uname column
        "email" => $row["email"],
        "role" => $row["role"],
        "password" => $row["password"],
        "created_at" => $row["created_at"],
        "updated_at" => $row["updated_at"]
    ];
}

// Return the data as JSON
echo json_encode(["users" => $users]);

$conn->close();
?>
