<?php
include 'db_connection.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $regNumber = $_POST['regNumber'];

    // Fetch the student ID and their course using the registration number
    $studentQuery = "
        SELECT s.id AS student_id, s.course AS course_id
        FROM students s
        WHERE s.regNumber = ?
    ";
    $stmt = $conn->prepare($studentQuery);
    $stmt->bind_param("s", $regNumber);
    $stmt->execute();
    $stmt->bind_result($student_id, $course_id);
    $stmt->fetch();
    $stmt->close();

    if (!$student_id) {
        echo json_encode(['error' => 'Student not found']);
        $conn->close();
        exit;
    }

    // Fetch units and attendance data for the student, filtering by course_id
    $query = "
        SELECT
            cu.stage,
            cu.semester,
            u.unitName,
            COUNT(a.status) AS total_sessions,
            SUM(a.status = 'P') AS present_sessions,
            (SUM(a.status = 'P') / COUNT(a.status)) * 100 AS percentage
        FROM
            attendance a
        JOIN
            course_units cu ON a.unit_id = cu.unit_id
        JOIN
            units u ON cu.unit_id = u.unit_id
        WHERE
            a.student_id = ? AND cu.course_id = ?
        GROUP BY
            cu.stage,
            cu.semester,
            u.unitName
    ";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $student_id, $course_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $attendanceData = [];

    while ($row = $result->fetch_assoc()) {
        $stage = $row['stage'];
        $semester = $row['semester'];
        $unitName = $row['unitName'];
        $totalSessions = $row['total_sessions'];
        $presentSessions = $row['present_sessions'];
        $percentage = (float) $row['percentage'];

        if (!isset($attendanceData[$stage])) {
            $attendanceData[$stage] = [
                'stage' => $stage,
                'semesters' => [],
                'stageTotalSessions' => 0,
                'stagePresentSessions' => 0,
                'stageUnitCount' => 0,
                'stagePercentages' => []
            ];
        }

        if (!isset($attendanceData[$stage]['semesters'][$semester])) {
            $attendanceData[$stage]['semesters'][$semester] = [
                'semester' => $semester,
                'units' => [],
                'semesterTotalSessions' => 0,
                'semesterPresentSessions' => 0,
                'semesterUnitCount' => 0,
                'semesterPercentages' => []
            ];
        }

        // Add unit data
        $attendanceData[$stage]['semesters'][$semester]['units'][] = [
            'unitName' => $unitName,
            'total_sessions' => $totalSessions,
            'present_sessions' => $presentSessions,
            'percentage' => $percentage
        ];

        $attendanceData[$stage]['semesters'][$semester]['semesterTotalSessions'] += $totalSessions;
        $attendanceData[$stage]['semesters'][$semester]['semesterPresentSessions'] += $presentSessions;
        $attendanceData[$stage]['semesters'][$semester]['semesterUnitCount']++;
        $attendanceData[$stage]['semesters'][$semester]['semesterPercentages'][] = $percentage;

        $attendanceData[$stage]['stageTotalSessions'] += $totalSessions;
        $attendanceData[$stage]['stagePresentSessions'] += $presentSessions;
        $attendanceData[$stage]['stageUnitCount']++;
        $attendanceData[$stage]['stagePercentages'][] = $percentage;
    }

    // Calculate average percentage for semesters and stages
    foreach ($attendanceData as &$stageData) {
        $semesterCount = count($stageData['semesters']);
        $stageData['stagePercentage'] = ($semesterCount > 0)
            ? number_format(array_sum($stageData['stagePercentages']) / count($stageData['stagePercentages']), 2, '.', '')
            : 0;

        foreach ($stageData['semesters'] as &$semesterData) {
            $unitCount = count($semesterData['units']);
            $semesterData['semesterPercentage'] = ($unitCount > 0)
                ? number_format(array_sum($semesterData['semesterPercentages']) / $unitCount, 2, '.', '')
                : 0;
        }
    }

    $stmt->close();
    $conn->close();

    // Output the JSON response
    echo json_encode(array_values($attendanceData));
} else {
    echo json_encode(['error' => 'Invalid request method']);
}
?>
