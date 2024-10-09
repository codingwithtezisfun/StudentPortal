<?php
require 'db_connection.php';

$departmentId = isset($_GET['department']) ? $_GET['department'] : '';
$courseId = isset($_GET['course']) ? $_GET['course'] : '';
$group = isset($_GET['group']) ? $_GET['group'] : '';
$intake = isset($_GET['intake']) ? $_GET['intake'] : '';
$year = isset($_GET['year']) ? $_GET['year'] : '';

$sql = "SELECT s.name, s.regNumber, d.departmentName, c.courseName, s.studentGroup, sess.intake, sess.session_reported 
        FROM students s
        JOIN departments d ON s.department_id = d.department_id
        JOIN courses c ON s.course = c.course_id
        JOIN session sess ON s.id = sess.student_id
        WHERE 1 = 1";

// Apply filters
if (!empty($departmentId)) {
    $sql .= " AND s.department_id = '$departmentId'";
}
if (!empty($courseId)) {
    $sql .= " AND s.course = '$courseId'";
}
if (!empty($group)) {
    $sql .= " AND s.studentGroup = '$group'";
}
if (!empty($intake)) {
    $sql .= " AND sess.intake = '$intake'";
}
if (!empty($year)) {
    $sql .= " AND sess.year = '$year'";
}

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo '<tr>
                <td>' . $row['name'] . '</td>
                <td>' . $row['regNumber'] . '</td>
                <td>' . $row['departmentName'] . '</td>
                <td>' . $row['courseName'] . '</td>
                <td>' . $row['studentGroup'] . '</td>
                <td>' . $row['intake'] . '</td>
                <td>' . ($row['session_reported'] ? 'Reported' : 'Not Reported') . '</td>
              </tr>';
    }
} else {
    echo '<tr><td colspan="7">No students found.</td></tr>';
}

$conn->close();
?>
