<?php
session_start();
header('Content-Type: application/json');

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in.']);
    exit();
}
require 'db_connection.php';
$student_id = $_POST['student_id'];
$course_id = $_POST['course_id'];
$stage = $_POST['stage'];
$semester = $_POST['semester'];
$unit_ids = $_POST['unit_ids'];
$electiveCategory = isset($_POST['electiveCategory']) ? $_POST['electiveCategory'] : null;

// Prepare SQL statement to check if units have already been registered
$checkStmt = $conn->prepare("
    SELECT COUNT(*) FROM student_unit_registrations 
    WHERE student_id = ? AND unit_id = ? AND stage = ? AND semester = ?
");

if ($checkStmt === false) {
    echo json_encode(['success' => false, 'message' => 'Prepare statement failed: ' . $conn->error]);
    $conn->close();
    exit();
}

$unitAlreadyRegistered = false;

// Loop through the submitted units
foreach ($unit_ids as $unit_id) {
    $checkStmt->bind_param("iiii", $student_id, $unit_id, $stage, $semester);
    $checkStmt->execute();
    $checkStmt->bind_result($count);
    $checkStmt->fetch();

    if ($count > 0) {
        $unitAlreadyRegistered = true;
        break;
    }
}

$checkStmt->close(); // Ensure the statement is closed

if ($unitAlreadyRegistered) {
    $conn->close();
    echo json_encode(['success' => false, 'message' => 'Units have already been registered for the semester and stage.']);
    exit();
}

// Prepare SQL statement to register units
$stmt = $conn->prepare("
    INSERT INTO student_unit_registrations (student_id, course_id, unit_id, stage, semester, electiveCategory) 
    VALUES (?, ?, ?, ?, ?, ?)
");

if ($stmt === false) {
    echo json_encode(['success' => false, 'message' => 'Prepare statement failed: ' . $conn->error]);
    $conn->close();
    exit();
}

$success = true;
$message = 'Units registered successfully.';

// Begin transaction
$conn->begin_transaction();

try {
    // Loop through the submitted units
    foreach ($unit_ids as $unit_id) {
        $stmt->bind_param("iiiiis", $student_id, $course_id, $unit_id, $stage, $semester, $electiveCategory);

        if (!$stmt->execute()) {
            $success = false;
            $message = 'Error occurred during registration: ' . $stmt->error;
            throw new Exception($stmt->error);
        }
    }

    // Commit the transaction
    $conn->commit();

} catch (Exception $e) {
    // Rollback transaction if an error occurred
    $conn->rollback();
    $success = false;
    $message = 'Transaction failed: ' . $e->getMessage();
}

// Close statements and connection
$stmt->close();
$conn->close();

// Return the response
echo json_encode(['success' => $success, 'message' => $message]);

?>
