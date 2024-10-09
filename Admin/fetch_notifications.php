<?php
require 'session_start.php';
require 'db_connection.php';

$to_user = 'staff';

$sql = "SELECT subject, from_user, to_user, message 
        FROM notifications 
        WHERE (to_user = ? OR to_user = 'all') 
        AND expiry_time > NOW() 
        ORDER BY send_time DESC";
        
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $to_user);
$stmt->execute();
$result = $stmt->get_result();

$notifications = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $notifications[] = $row;
    }
}

echo json_encode($notifications);

$stmt->close();
$conn->close();
?>
