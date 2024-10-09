<?php
header('Content-Type: application/json');

// Database connection
require 'db_connection.php';

// Fetch departments
$departments = [];
$sql = "SELECT department_id, departmentName FROM departments";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $departments[$row['department_id']] = $row['departmentName'];
    }
}

// Fetch courses
$courses = [];
$sql = "SELECT course_id, courseName, department_id FROM courses";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $courses[] = $row;
    }
}

echo json_encode(['departments' => $departments, 'courses' => $courses]);

$conn->close();
?>
