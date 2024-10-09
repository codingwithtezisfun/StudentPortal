<?php
require 'session_start.php';
require 'db_connection.php';

$department_id = $_POST['department'];
$courseName = $_POST['courseName'];

if (empty($department_id) || empty($courseName)) {
    http_response_code(400);
    echo json_encode(['message' => 'Invalid input']);
    exit();
}

$sql = "INSERT INTO courses (department_id, courseName) VALUES (?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $department_id, $courseName);

if ($stmt->execute()) {
    echo json_encode(['message' => 'Course registered successfully']);
} else {
    http_response_code(500);
    echo json_encode(['message' => 'Error registering course']);
}

$stmt->close();
$conn->close();
?>
