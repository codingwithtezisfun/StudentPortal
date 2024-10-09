<?php
require 'session_start.php';
require 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $subject = $_POST['subject'];
    $from = $_POST['from'];
    $to = $_POST['to'];
    $message = $_POST['message'];
    
    $expiryTime = date('Y-m-d H:i:s', strtotime('+7 days'));

    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO notifications (subject, from_user, to_user, message, send_time, expiry_time) VALUES (?, ?, ?, ?, NOW(), ?)");
    $stmt->bind_param("sssss", $subject, $from, $to, $message, $expiryTime);

    // Execute and check for errors
    if ($stmt->execute()) {
        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "error", "message" => $stmt->error]);
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();
}
?>
