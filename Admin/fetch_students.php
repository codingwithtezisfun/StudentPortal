<?php
require 'db_connection.php';

$whereClause = "WHERE 1=1";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!empty($_POST['course'])) {
        $course = $conn->real_escape_string($_POST['course']);
        $whereClause .= " AND s.course = $course";
    }
    if (!empty($_POST['department'])) {
        $department = $conn->real_escape_string($_POST['department']);
        $whereClause .= " AND c.department_id = $department";
    }
    if (!empty($_POST['group'])) {
        $group = $conn->real_escape_string($_POST['group']);
        $whereClause .= " AND s.studentGroup = '$group'";
    }
    if (!empty($_POST['regNumber'])) {
        $regNumber = $conn->real_escape_string($_POST['regNumber']);
        $whereClause .= " AND s.regNumber = '$regNumber'";
    }
    if (!empty($_POST['status'])) {
        $status = $conn->real_escape_string($_POST['status']);
        $whereClause .= " AND s.status = '$status'";
    }
}

$sql = "SELECT s.id, s.name, s.regNumber, s.phoneNumber, s.email, c.courseName, d.departmentName, s.studentGroup, s.gender, s.status, s.image
        FROM students s
        JOIN courses c ON s.course = c.course_id
        JOIN departments d ON c.department_id = d.department_id
        $whereClause";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $i = 1;
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$i}</td>
                <td>{$row['name']}</td>
                <td>{$row['regNumber']}</td>
                <td>{$row['phoneNumber']}</td>
                <td>{$row['email']}</td>
                <td>{$row['courseName']}</td>
                <td>{$row['departmentName']}</td>
                <td>{$row['studentGroup']}</td>
                <td>{$row['gender']}</td>
                <td>{$row['status']}</td>
                <td><img src='../StudentImages/{$row['image']}' alt='Image' style='width: 50px; height: 50px;'></td>
                <td><button class='btn btn-primary' onclick='loadUpdateForm({$row['id']})'>Edit</button></td>
              </tr>";
        $i++;
    }
} else {
    echo "<tr><td colspan='12'>No records found.</td></tr>";
}

$conn->close();
?>
