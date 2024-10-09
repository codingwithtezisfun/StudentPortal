<?php
include 'db_connection.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $regNumber = $_POST['regNumber'];
    $stage = $_POST['stage'] ?? null;
    $semester = $_POST['semester'] ?? null;
    $unit = $_POST['unit'] ?? null;

    // Fetch the student ID based on registration number
    $studentQuery = "
        SELECT id 
        FROM students 
        WHERE regNumber = ?
    ";
    $stmt = $conn->prepare($studentQuery);
    $stmt->bind_param("s", $regNumber);
    $stmt->execute();
    $stmt->bind_result($student_id);
    $stmt->fetch();
    $stmt->close();

    if (!$student_id) {
        echo json_encode(['error' => 'Student not found']);
        $conn->close();
        exit;
    }

    // Fetch the student's attendance data
    $attendanceQuery = "
        SELECT 
            cu.stage,
            cu.semester,
            u.unitName,
            a.date,
            a.session,
            a.status
        FROM 
            attendance a
        JOIN 
            course_units cu ON a.unit_id = cu.unit_id
        JOIN 
            units u ON cu.unit_id = u.unit_id
        WHERE 
            a.student_id = ?
    ";

    // Add filters to the query if provided
    $params = [$student_id];
    if ($stage) {
        $attendanceQuery .= " AND cu.stage = ?";
        $params[] = $stage;
    }
    if ($semester) {
        $attendanceQuery .= " AND cu.semester = ?";
        $params[] = $semester;
    }
    if ($unit) {
        $attendanceQuery .= " AND u.unitName = ?";
        $params[] = $unit;
    }

    $stmt = $conn->prepare($attendanceQuery);

    if ($stage || $semester || $unit) {
        $types = str_repeat('i', count($params));
        $stmt->bind_param($types, ...$params);
    } else {
        $stmt->bind_param("i", $student_id);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    $attendanceData = [];

    while ($row = $result->fetch_assoc()) {
        $stage = $row['stage'];
        $semester = $row['semester'];
        $unitName = $row['unitName'];
        $date = $row['date'];
        $session = $row['session'];
        $status = $row['status'];

        if (!isset($attendanceData[$stage])) {
            $attendanceData[$stage] = [
                'stage' => $stage,
                'semesters' => [],
            ];
        }

        if (!isset($attendanceData[$stage]['semesters'][$semester])) {
            $attendanceData[$stage]['semesters'][$semester] = [
                'semester' => $semester,
                'units' => [],
            ];
        }

        if (!isset($attendanceData[$stage]['semesters'][$semester]['units'][$unitName])) {
            $attendanceData[$stage]['semesters'][$semester]['units'][$unitName] = [
                'unitName' => $unitName,
                'dates' => [],
            ];
        }

        $attendanceData[$stage]['semesters'][$semester]['units'][$unitName]['dates'][] = [
            'date' => $date,
            'session' => $session,
            'status' => $status,
        ];
    }

    $stmt->close();
    $conn->close();

    // Output the JSON response
    echo json_encode($attendanceData);
} else {
    echo json_encode(['error' => 'Invalid request method']);
}
?>
