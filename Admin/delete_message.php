<?php
require 'session_start.php';
require 'db_connection.php';

$message_id = $_POST['message_id'] ?? '';

if (empty($message_id)) {
    echo json_encode(['error' => 'Missing message ID']);
    exit();
}

// Delete the message based on the message_id
$sql = "DELETE FROM messages WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $message_id);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['error' => 'Failed to delete message.']);
}

$stmt->close();
$conn->close();
?>
