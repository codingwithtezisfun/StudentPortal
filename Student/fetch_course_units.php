<?php
require 'session_start.php';

require 'db_connection.php';

$email = $_SESSION['email'];

// Fetch the student ID and course ID based on the email
$stmt = $conn->prepare("SELECT id, course FROM students WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $student = $result->fetch_assoc();
    $studentId = $student['id'];
    $courseId = $student['course'];
} else {
    echo json_encode(array()); // Return empty array if student not found
    exit();
}

$stmt->close();

// Fetch units grouped by semester and stage for the student
$output = array();

$sql_semesters = "SELECT DISTINCT semester FROM course_units WHERE course_id = ? ORDER BY semester ASC";
$stmt_semesters = $conn->prepare($sql_semesters);
$stmt_semesters->bind_param("i", $courseId);
$stmt_semesters->execute();
$result_semesters = $stmt_semesters->get_result();

while ($row_semester = $result_semesters->fetch_assoc()) {
    $semester = $row_semester['semester'];

    // Fetch stages for each semester
    $sql_stages = "SELECT DISTINCT stage FROM course_units WHERE course_id = ? AND semester = ? ORDER BY stage ASC";
    $stmt_stages = $conn->prepare($sql_stages);
    $stmt_stages->bind_param("ii", $courseId, $semester);
    $stmt_stages->execute();
    $result_stages = $stmt_stages->get_result();

    $semester_data = array();

    while ($row_stage = $result_stages->fetch_assoc()) {
        $stage = $row_stage['stage'];

        // Fetch units for each stage and semester
        $sql_units = "
        SELECT DISTINCT u.unitName
        FROM course_units cu
        JOIN units u ON cu.unit_id = u.unit_id
        WHERE cu.course_id = ? AND cu.semester = ? AND cu.stage = ?
        ORDER BY u.unitName ASC";

        $stmt_units = $conn->prepare($sql_units);
        $stmt_units->bind_param("iii", $courseId, $semester, $stage);
        $stmt_units->execute();
        $result_units = $stmt_units->get_result();

        $units = array();
        while ($row_unit = $result_units->fetch_assoc()) {
            $units[] = array(
                'unitName' => $row_unit['unitName']
            );
        }

        $semester_data[] = array(
            'stage' => $stage,
            'units' => $units
        );
    }

    $output[] = array(
        'semester' => $semester,
        'data' => $semester_data
    );
}

$stmt_semesters->close();
$conn->close();

header('Content-Type: application/json');
echo json_encode($output);
?>
