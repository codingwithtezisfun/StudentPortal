<?php
include 'db_connection.php';

// Process AJAX requests
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['fetch_student'])) {
    $regNumber = $_POST['regNumber'];

    // Fetch student details using regNumber
    $sql_student = "SELECT * FROM students WHERE regNumber = '$regNumber'";
    $result_student = $conn->query($sql_student);

    if ($result_student->num_rows == 1) {
        $row_student = $result_student->fetch_assoc();
        $student_id = $row_student['id'];
        $name = $row_student['name'];
        $regNumber = $row_student['regNumber'];
        $intake = ''; // Fetch from your logic or set default
        $response = array('success' => true, 'student_id' => $student_id, 'name' => $name, 'regNumber' => $regNumber, 'intake' => $intake);
    } else {
        $response['message'] = "Student not found with provided Registration Number";
    }
}

$conn->close();

// Send JSON response to AJAX request
header('Content-Type: application/json');
echo json_encode($response);
?>
