<?php
require 'session_start.php';
require 'db_connection.php';

$message_id = $_POST['message_id'];

// Fetch message details
$sql = "SELECT sender, subject, message FROM messages WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $message_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $message = $result->fetch_assoc();
    echo json_encode($message);
} else {
    echo json_encode(['error' => 'Message not found']);
}

$stmt->close();
$conn->close();
?>
