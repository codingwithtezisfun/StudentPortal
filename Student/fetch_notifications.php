<?php
session_start();
require 'db_connection.php';
$sql = "SELECT subject, from_user, to_user, message 
        FROM notifications 
        WHERE expiry_time > NOW() 
        AND (to_user = 'All' OR to_user = 'Students') 
        ORDER BY send_time DESC";
$result = $conn->query($sql);

$response = array();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $notification = array(
            'subject' => htmlspecialchars($row['subject']),
            'from_user' => htmlspecialchars($row['from_user']),
            'to_user' => htmlspecialchars($row['to_user']),
            'message' => htmlspecialchars($row['message'])
        );
        $response[] = $notification;
    }
} else {
    $response['error'] = 'No notifications found.';
}

$conn->close();

echo json_encode($response);
?>
