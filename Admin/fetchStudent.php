<?php
require 'db_connection.php';

if (isset($_GET['regNumber'])) {
    $regNumber = $_GET['regNumber'];

    $sql = "SELECT students.*, departments.department_id, courses.course_id, courses.courseName 
            FROM students 
            JOIN departments ON students.department_id = departments.department_id
            JOIN courses ON students.course = courses.course_id
            WHERE students.regNumber = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $regNumber);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        // Return student details including department and course
        echo json_encode([
            'id' => $row['id'],
            'name' => $row['name'],
            'regNumber' => $row['regNumber'],
            'phoneNumber' => $row['phoneNumber'],
            'email' => $row['email'],
            'studentGroup' => $row['studentGroup'],
            'gender' => $row['gender'],
            'status' => $row['status'],
            'department_id' => $row['department_id'],
            'course_id' => $row['course_id'],
            'courseName' => $row['courseName'],
            'image' => $row['image'],
            'studentID' => $row['studentID'] 
        ]);
    } else {
        echo json_encode(['error' => 'Student not found']);
    }
} else {
    echo json_encode(['error' => 'Invalid request']);
}

$conn->close();
?>
