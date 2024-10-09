<?php
header('Content-Type: application/json');

require 'session_start.php';
require 'db_connection.php';
// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'registerUnit') {
    $unitName = $_POST['unitName'];
    $courseId = $_POST['courseId'];
    $stage = $_POST['stage'];
    $semester = $_POST['semester'];
    $electiveCategory = isset($_POST['electiveCategory']) ? $_POST['electiveCategory'] : null;
    $isElective = ($courseId == '20' && $stage == '3') ? 1 : 0;

    // Check if the unit already exists in the course_units table
    $checkStmt = $conn->prepare("SELECT unit_id FROM course_units WHERE unit_id = ? AND course_id = ? AND stage = ? AND semester = ?");
    $checkStmt->bind_param("iiss", $unitName, $courseId, $stage, $semester);
    $checkStmt->execute();
    $checkStmt->store_result();

    if ($checkStmt->num_rows > 0) {
        echo json_encode(['status' => 'error', 'message' => 'Unit with the same details already exists.']);
    } else {
        // Prepare and bind the insert statement
        $stmt = $conn->prepare("INSERT INTO course_units (unit_id, course_id, stage, semester, isElective, electiveCategory) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iissss", $unitName, $courseId, $stage, $semester, $isElective, $electiveCategory);

        // Execute the statement
        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Unit successfully registered!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error: ' . $stmt->error]);
        }

        $stmt->close();
    }

    $checkStmt->close();
    $conn->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
}
?>
