<?php
include 'db_connection.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $department_id = $_POST['department_id'];
    $course_id = $_POST['course_id'];
    $group_id = $_POST['group_id']; // To filter by student group
    $stage = $_POST['stage'];

    // Fetch students from the selected course and student group
    $studentQuery = "
        SELECT s.id AS student_id, s.name, s.regNumber
        FROM students s
        WHERE s.course = ? AND s.studentGroup = ? AND s.department_id = ?
    ";
    $stmt = $conn->prepare($studentQuery);
    $stmt->bind_param("isi", $course_id, $group_id, $department_id);
    $stmt->execute();
    $studentsResult = $stmt->get_result();

    $attendanceData = [];

    while ($student = $studentsResult->fetch_assoc()) {
        $student_id = $student['student_id'];
        $student_name = $student['name'];
        $student_regNumber = $student['regNumber'];

        // Fetch attendance data for the student based on stage and course
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
                a.student_id = ? AND cu.course_id = ? AND cu.stage = ?
            GROUP BY
                cu.stage, cu.semester, u.unitName
        ";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("iii", $student_id, $course_id, $stage);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $stage = $row['stage'];
            $semester = $row['semester'];
            $unitName = $row['unitName'];
            $totalSessions = $row['total_sessions'];
            $presentSessions = $row['present_sessions'];
            $percentage = (float)$row['percentage'];

            if (!isset($attendanceData[$student_id])) {
                $attendanceData[$student_id] = [
                    'name' => $student_name,
                    'regNumber' => $student_regNumber,
                    'stage' => $stage,
                    'stagePercentage' => 0
                ];
            }

            if (!isset($attendanceData[$student_id]['units'])) {
                $attendanceData[$student_id]['units'] = [];
                $attendanceData[$student_id]['stageTotalSessions'] = 0;
                $attendanceData[$student_id]['stagePresentSessions'] = 0;
                $attendanceData[$student_id]['stageUnitCount'] = 0;
                $attendanceData[$student_id]['stagePercentages'] = [];
            }

            // Add unit data
            $attendanceData[$student_id]['units'][] = [
                'unitName' => $unitName,
                'total_sessions' => $totalSessions,
                'present_sessions' => $presentSessions,
                'percentage' => $percentage
            ];

            // Update stage totals
            $attendanceData[$student_id]['stageTotalSessions'] += $totalSessions;
            $attendanceData[$student_id]['stagePresentSessions'] += $presentSessions;
            $attendanceData[$student_id]['stageUnitCount']++;
            $attendanceData[$student_id]['stagePercentages'][] = $percentage;
        }

        // Calculate average percentage for the student
        if (isset($attendanceData[$student_id])) {
            $studentData = &$attendanceData[$student_id];
            $unitCount = count($studentData['stagePercentages']);
            $studentData['stagePercentage'] = ($unitCount > 0)
                ? number_format(array_sum($studentData['stagePercentages']) / $unitCount, 2, '.', '')
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
