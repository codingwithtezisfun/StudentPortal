<?php
require 'session_start.php';
// Retrieve registration number from session
$email = $_SESSION['email'];

require 'db_connection.php';

// Prepare SQL statement to retrieve student details including department
$sql = "SELECT s.name, s.regNumber, s.phoneNumber, s.email, c.courseName, s.studentGroup, s.gender, s.studentID, s.status, s.image, d.departmentName
        FROM students s 
        JOIN courses c ON s.course = c.course_id
        JOIN departments d ON s.department_id = d.department_id
        WHERE s.email = ?";

// Prepare and bind parameter
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);

// Execute query
$stmt->execute();
$result = $stmt->get_result();

// Prepare response array
$response = array();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $response['success'] = true;
    $response['data'] = $row;
} else {
    $response['error'] = 'Student details not found for Email: ' . htmlspecialchars($email);
}

// Close statement and connection
$stmt->close();
$conn->close();

// Return JSON response
echo json_encode($response);
?>
