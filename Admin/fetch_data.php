<?php
header('Content-Type: application/json');

include 'db_connection.php';

// Fetch data query
$sql = "SELECT fee.id, fee.year, courses.courseName AS course, fee.intake, fee.fee_amount 
        FROM fee
        JOIN courses ON fee.course = courses.course_id
        WHERE fee.year = YEAR(CURDATE()) 
        AND fee.intake = CASE 
                            WHEN MONTH(CURDATE()) BETWEEN 1 AND 3 THEN 'Jan-Mar'
                            WHEN MONTH(CURDATE()) BETWEEN 4 AND 6 THEN 'Apr-Jun'
                            WHEN MONTH(CURDATE()) BETWEEN 7 AND 9 THEN 'Jul-Sep'
                            WHEN MONTH(CURDATE()) BETWEEN 10 AND 12 THEN 'Oct-Dec'
                            ELSE NULL
                         END";

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
    echo json_encode(array('Result' => 'ERROR', 'Message' => 'No fee data found for current year and intake.'));
}

$conn->close();
?>
