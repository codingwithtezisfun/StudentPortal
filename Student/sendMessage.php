<?php
require 'db_connection.php';

$from = $_POST['from'];
$receiver = $_POST['receiver'];
$subject = $_POST['subject'];
$message = $_POST['message'];

// Prepare and bind SQL statement
$stmt = $conn->prepare("INSERT INTO messages (receiver, sender, subject, message) VALUES (?, ?, ?, ?)");
if ($stmt === false) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Prepare failed: ' . $conn->error
    ]);
    exit;
}

$stmt->bind_param("ssss", $receiver, $from, $subject, $message);

// Execute the statement
if ($stmt->execute()) {
    $response = [
        'status' => 'success',
        'message' => 'Message sent successfully.'
    ];
} else {
    $response = [
        'status' => 'error',
        'message' => 'Failed to send message: ' . $stmt->error
    ];
}

$stmt->close();
$conn->close();

// Send JSON response back to the frontend
header('Content-Type: application/json');
echo json_encode($response);
?>
