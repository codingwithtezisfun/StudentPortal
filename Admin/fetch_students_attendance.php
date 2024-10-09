<?php
require 'db_connection.php'; // Ensure the database connection file is included

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $course = $_POST['course'];
    $group = $_POST['group'];

    $sql = "SELECT id, name, regNumber FROM students WHERE course = ? AND studentGroup = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $course, $group);
    $stmt->execute();
    $result = $stmt->get_result();

    $students = array();
    while ($row = $result->fetch_assoc()) {
        $students[] = $row;
    }

    echo json_encode($students);

    $stmt->close();
    $conn->close();
}
?>
