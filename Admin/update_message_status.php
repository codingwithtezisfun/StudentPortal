<?php
require 'session_start.php';
require 'db_connection.php';

$message_id = $_POST['message_id'];
$status = $_POST['status'];

// Update message status
$sql = "UPDATE messages SET status = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $status, $message_id);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['error' => 'Failed to update message status']);
}

$stmt->close();
$conn->close();
?>
