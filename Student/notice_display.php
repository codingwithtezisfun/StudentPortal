<?php
require 'db_connection.php';
$sql = "SELECT id, title, filePath FROM noticeboard WHERE targetAudience IN ('all', 'students')";
$result = $conn->query($sql);

// Prepare response array
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

// Close connection
$conn->close();

// Return JSON response
echo json_encode($response);
?>
