<?php
require 'session_start.php';
require 'db_connection.php';

$email = $_SESSION['username'];

// Fetch staff ID
$sql = "SELECT id FROM staff WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $staff = $result->fetch_assoc();
    $staff_id = $staff['id'];
} else {
    echo json_encode(['error' => 'Staff ID not found']);
    exit();
}

// Fetch messages
$sql = "SELECT id, sender, subject, message, status FROM messages WHERE receiver = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $staff_id);
$stmt->execute();
$result = $stmt->get_result();

$messages = [];
while ($row = $result->fetch_assoc()) {
    $messages[] = $row;
}

echo json_encode($messages);

$stmt->close();
$conn->close();
?>
