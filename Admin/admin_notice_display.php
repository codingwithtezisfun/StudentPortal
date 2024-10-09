<?php
require 'session_start.php';
require 'db_connection.php';

// Query to fetch files from database where target audience is all or students
$sql = "SELECT id, title, filePath FROM noticeboard WHERE targetAudience IN ('all', 'staff')";
$result = $conn->query($sql);

$response = array();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $file = array(
            'id' => $row['id'],
            'title' => htmlspecialchars($row['title']),
            'filePath' => $row['filePath']
        );
        $response[] = $file;
    }
} else {
    $response['error'] = 'No notices found.';
}

$conn->close();

echo json_encode($response);
?>
