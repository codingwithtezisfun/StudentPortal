<?php
require 'session_start.php';
require 'db_connection.php';

$email = $_SESSION['username'];
$oldPassword = $_POST['oldPassword'];
$newPassword = $_POST['newPassword'];

$stmt = $conn->prepare("SELECT password FROM staffLogin WHERE username = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $staff = $result->fetch_assoc();
    $currentPassword = $staff['password'];

    // Verify the old password
    if (password_verify($oldPassword, $currentPassword)) {
        // Hash the new password
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        // Update the password in the database
        $updateStmt = $conn->prepare("UPDATE staffLogin SET password = ? WHERE username = ?");
        $updateStmt->bind_param("ss", $hashedPassword, $email);
        if ($updateStmt->execute()) {
            echo "Password updated successfully!";
        } else {
            http_response_code(500);
            echo "Error updating password.";
        }
        $updateStmt->close();
    } else {
        http_response_code(400);
        echo "Old password is incorrect.";
    }
} else {
    http_response_code(404);
    echo "User not found.";
}

$stmt->close();
$conn->close();
?>
