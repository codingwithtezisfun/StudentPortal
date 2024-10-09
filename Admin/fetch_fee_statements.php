<?php
header('Content-Type: application/json');

include 'db_connection.php';

// Build SQL query based on filter criteria
$sql = "SELECT fee.year, courses.courseName AS course, fee.intake, fee.fee_amount 
        FROM fee
        JOIN courses ON fee.course = courses.course_id
        WHERE 1=1";

if (isset($_GET['year']) && $_GET['year'] !== '') {
    $year = $conn->real_escape_string($_GET['year']);
    $sql .= " AND fee.year = $year";
}

if (isset($_GET['intake']) && $_GET['intake'] !== '') {
    $intake = $conn->real_escape_string($_GET['intake']);
    $sql .= " AND fee.intake = '$intake'";
}

if (isset($_GET['courseId']) && $_GET['courseId'] !== '') {
    $courseId = $conn->real_escape_string($_GET['courseId']);
    $sql .= " AND fee.course = $courseId";
}

// Add department filter
if (isset($_GET['departmentId']) && $_GET['departmentId'] !== '') {
    $departmentId = $conn->real_escape_string($_GET['departmentId']);
    $sql .= " AND courses.department_id = $departmentId";
}

$result = $conn->query($sql);

if ($result === false) {
    echo json_encode(array('Result' => 'ERROR', 'Message' => 'Query error: ' . $conn->error));
    exit();
}

if ($result->num_rows > 0) {
    $rows = array();
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }
    echo json_encode($rows);
} else {
    echo json_encode(array('Result' => 'ERROR', 'Message' => 'No fee statements found for the selected criteria.'));
}

$conn->close();
?>
