<?php
if (isset($_GET['department_id'])) {
    require 'db_connection.php';

    // Retrieve department_id from GET request
    $department_id = $_GET['department_id'];

    // Fetch courses based on department_id
    $sql = "SELECT course_id, courseName FROM courses WHERE department_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $department_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Prepare data for JSON response
    $courses = array();
    while ($row = $result->fetch_assoc()) {
        $courses[] = $row;
    }

    // Output JSON
    echo json_encode($courses);

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(array("error" => "Department ID is missing"));
}
?>
