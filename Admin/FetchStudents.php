<?php
include 'db_connection.php';

$course = $_POST['course'];
$group = $_POST['group'];

$sql = "SELECT id, name, regNumber FROM students WHERE course = ? AND studentGroup = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $course, $group);
$stmt->execute();
$result = $stmt->get_result();

$students = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $students[] = $row;
    }
}

$stmt->close();
$conn->close();

echo json_encode($students);
?>
