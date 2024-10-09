<?php
header('Content-Type: application/json');
require 'db_connection.php'; // Ensure this file contains the correct database connection setup

// Retrieve form data
$courseId = $_POST['course'];
$stage = $_POST['stage'];
$semester = $_POST['semester'];
$elective = isset($_POST['elective']) ? $_POST['elective'] : null; // Handle optional elective

// Check if elective is provided
$electiveCondition = '';
$params = [];

// Prepare SQL based on whether elective is provided
if ($elective) {
    // Query to fetch units with elective category
    $sql = "SELECT u.unit_id, u.unitName 
            FROM course_units cu 
            JOIN units u ON cu.unit_id = u.unit_id 
            WHERE cu.course_id = ? 
            AND cu.stage = ? 
            AND cu.semester = ? 
            AND cu.electiveCategory = ?";
    $params = [$courseId, $stage, $semester, $elective];
} else {
    // Query to fetch units without elective category
    $sql = "SELECT u.unit_id, u.unitName 
            FROM course_units cu 
            JOIN units u ON cu.unit_id = u.unit_id 
            WHERE cu.course_id = ? 
            AND cu.stage = ? 
            AND cu.semester = ?";
    $params = [$courseId, $stage, $semester];
}

// Prepare and execute statement
try {
    $stmt = $conn->prepare($sql);
    
    // Bind parameters based on the number of params
    if ($elective) {
        $stmt->bind_param('isss', ...$params);
    } else {
        $stmt->bind_param('iss', ...$params);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    // Collect results
    $units = [];
    while ($row = $result->fetch_assoc()) {
        $units[] = $row;
    }

    // Output results in JSON format
    echo json_encode($units);

    $stmt->close();
} catch (Exception $e) {
    // Handle any errors
    echo json_encode(['error' => $e->getMessage()]);
}

$conn->close();
?>
