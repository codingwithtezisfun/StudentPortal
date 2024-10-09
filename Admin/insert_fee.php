<?php
require 'session_start.php';
require 'db_connection.php';

$email = $_SESSION['username'];

header('Content-Type: application/json');

// Fetch POST data
$year = $_POST['year'];
$course = $_POST['course'];
$intake = $_POST['intake'];
$fee_amount = $_POST['fee_amount'];

// Check if the fee statement already exists
$check_sql = "SELECT * FROM fee WHERE year = ? AND course = ? AND intake = ?";
$stmt = $conn->prepare($check_sql);
$stmt->bind_param("iss", $year, $course, $intake);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo json_encode(['Result' => 'ERROR', 'Message' => 'Duplicate fee statement exists for this year, course, and intake.']);
    exit();
} else {
    // Insert new fee statement
    $insert_sql = "INSERT INTO fee (year, course, intake, fee_amount) VALUES (?, ?, ?, ?)";
    $insert_stmt = $conn->prepare($insert_sql);
    $insert_stmt->bind_param("issd", $year, $course, $intake, $fee_amount);

    if ($insert_stmt->execute()) {
        echo json_encode(['Result' => 'SUCCESS', 'Message' => 'Fee statement added successfully.']);
    } else {
        echo json_encode(['Result' => 'ERROR', 'Message' => 'Failed to add fee statement: ' . $conn->error]);
    }
}

$conn->close();
?>
