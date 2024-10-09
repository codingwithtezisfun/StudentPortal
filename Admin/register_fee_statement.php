<?php
header('Content-Type: application/json');

include 'db_connection.php';

$year = $_POST['year'];
$course = $_POST['course'];
$intake = $_POST['intake'];
$fee_amount = $_POST['fee_amount'];

$sql = "INSERT INTO fee_statements (year, course, intake, fee_amount) VALUES ('$year', '$course', '$intake', '$fee_amount')";

// Execute SQL statement
if ($conn->query($sql) === TRUE) {
    echo json_encode(array('Result' => 'OK', 'Message' => 'Fee statement registered successfully'));
} else {
    echo json_encode(array('Result' => 'ERROR', 'Message' => 'Error registering fee statement: ' . $conn->error));
}

// Close connection
$conn->close();
?>
