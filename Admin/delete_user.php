<?php
require 'session_start.php';
require 'db_connection.php';

if (!isset($_POST['username'])) {
    echo json_encode(['status' => 'error', 'message' => 'No username provided.']);
    exit();
}

$usernameToDelete = $_POST['username'];

$sql = "DELETE FROM staffLogin WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $usernameToDelete);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo json_encode(['status' => 'success', 'message' => 'User deleted successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'User not found.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Error deleting user.']);
}

$stmt->close();
$conn->close();
?>
