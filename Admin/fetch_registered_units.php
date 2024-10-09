<?php
require 'db_connection.php';

if (isset($_GET['course_id'])) {
    $courseId = $_GET['course_id'];
    $currentYear = date('Y');
    
    // Determine the current intake based on the current month
    $current_month = date('n'); // Get the current month as an integer (1-12)
    if ($current_month >= 1 && $current_month <= 3) {
        $current_intake = "Jan-Mar";
    } elseif ($current_month >= 4 && $current_month <= 6) {
        $current_intake = "Apr-Jun";
    } elseif ($current_month >= 7 && $current_month <= 9) {
        $current_intake = "Jul-Sep";
    } else {
        $current_intake = "Oct-Dec";
    }
    
    // Modify SQL query to filter by current intake
    $sql = "
        SELECT u.unitName, sur.stage
        FROM student_unit_registrations sur
        INNER JOIN units u ON sur.unit_id = u.unit_id
        WHERE sur.course_id = ? AND YEAR(sur.registration_date) = ? 
        AND (
            (MONTH(sur.registration_date) BETWEEN 1 AND 3 AND '$current_intake' = 'Jan-Mar') OR
            (MONTH(sur.registration_date) BETWEEN 4 AND 6 AND '$current_intake' = 'Apr-Jun') OR
            (MONTH(sur.registration_date) BETWEEN 7 AND 9 AND '$current_intake' = 'Jul-Sep') OR
            (MONTH(sur.registration_date) BETWEEN 10 AND 12 AND '$current_intake' = 'Oct-Dec')
        )
        ORDER BY sur.stage ASC, u.unitName ASC
    ";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('is', $courseId, $currentYear);
    $stmt->execute();
    $result = $stmt->get_result();

    $units = [];
    while ($row = $result->fetch_assoc()) {
        $units[$row['stage']][] = $row['unitName'];
    }

    // Fetch the course name for the table heading
    $courseNameQuery = "SELECT courseName FROM courses WHERE course_id = ?";
    $stmt = $conn->prepare($courseNameQuery);
    $stmt->bind_param('i', $courseId);
    $stmt->execute();
    $stmt->bind_result($courseName);
    $stmt->fetch();

    echo json_encode(['courseName' => $courseName, 'units' => $units]);
    $stmt->close();
}
$conn->close();

