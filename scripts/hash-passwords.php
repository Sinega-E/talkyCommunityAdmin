<?php
include 'database\backend\db.php'; // Make sure to adjust the path if necessary

// Fetch all users from the admin_login_info table
$sql = "SELECT admin_id, admin_password, admin_confirm_password FROM admin_login_info";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $adminId = $row['admin_id'];
        $adminPassword = $row['admin_password'];
        $adminConfirmPassword = $row['admin_confirm_password'];

        // Hash both the admin_password and admin_confirm_password
        $hashedPassword = password_hash($adminPassword, PASSWORD_BCRYPT);
        $hashedConfirmPassword = password_hash($adminConfirmPassword, PASSWORD_BCRYPT);

        // Update the hashed passwords in the database
        $updateSql = "UPDATE admin_login_info SET admin_password = ?, admin_confirm_password = ? WHERE admin_id = ?";
        $stmt = $conn->prepare($updateSql);
        $stmt->bind_param("ssi", $hashedPassword, $hashedConfirmPassword, $adminId);
        $stmt->execute();
    }

    echo "Passwords hashed successfully.";
} else {
    echo "No users found.";
}

$conn->close();
?>
