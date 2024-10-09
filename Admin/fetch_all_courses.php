<?php
require 'db_connection.php';

$department_id = $_POST['department_id'];

if (empty($department_id)) {
    // Fetch all courses if no department is selected
    $sql = "
        SELECT d.department_id, d.departmentName, c.courseName
        FROM courses c
        JOIN departments d ON c.department_id = d.department_id
        ORDER BY d.departmentName, c.courseName
    ";
} else {
    // Fetch courses for the selected department
    $sql = "
        SELECT d.departmentName, c.courseName
        FROM courses c
        JOIN departments d ON c.department_id = d.department_id
        WHERE d.department_id = ?
        ORDER BY c.courseName
    ";
}

if (!empty($department_id)) {
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $department_id);
} else {
    $stmt = $conn->prepare($sql);
}

$stmt->execute();
$result = $stmt->get_result();

$grouped_courses = [];
while ($row = $result->fetch_assoc()) {
    $department_name = $row['departmentName'];
    $course_name = $row['courseName'];
    
    if (!isset($grouped_courses[$department_name])) {
        $grouped_courses[$department_name] = [];
    }
    $grouped_courses[$department_name][] = $course_name;
}

if (empty($grouped_courses)) {
    echo '<p class="text-warning">No courses found.</p>';
} else {
    foreach ($grouped_courses as $department_name => $courses) {
        echo '<div class="department-header">' . htmlspecialchars($department_name) . '</div>';
        echo '<ul>';
        foreach ($courses as $course) {
            echo '<li>' . htmlspecialchars($course) . '</li>';
        }
        echo '</ul>';
    }
}

$stmt->close();
$conn->close();
?>
