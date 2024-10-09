<?php
include 'db_connection.php';

$output = array();

// Fetch all departments
$sql_departments = "SELECT department_id, departmentName FROM departments";
$result_departments = $conn->query($sql_departments);

if ($result_departments->num_rows > 0) {
    while ($row_department = $result_departments->fetch_assoc()) {
        $department_id = $row_department['department_id'];
        $departmentName = $row_department['departmentName'];

        // Fetch courses for each department
        $sql_courses = "SELECT course_id, courseName FROM courses WHERE department_id = $department_id";
        $result_courses = $conn->query($sql_courses);

        $courses = array();
        if ($result_courses->num_rows > 0) {
            while ($row_course = $result_courses->fetch_assoc()) {
                $course_id = $row_course['course_id'];
                $courseName = $row_course['courseName'];

                // Fetch units for each course, classified by stage and semester
                $sql_units = "
                    SELECT unitName, stage, semester
                    FROM course_units
                    JOIN units ON course_units.unit_id = units.unit_id
                    WHERE course_id = $course_id
                    ORDER BY stage ASC, semester ASC
                ";
                $result_units = $conn->query($sql_units);

                $units = array();
                if ($result_units->num_rows > 0) {
                    while ($row_unit = $result_units->fetch_assoc()) {
                        $units[] = $row_unit;
                    }
                }

                $courses[] = array(
                    'courseName' => $courseName,
                    'units' => $units
                );
            }
        }

        $output[] = array(
            'departmentName' => $departmentName,
            'courses' => $courses
        );
    }
}

$conn->close();

header('Content-Type: application/json');
echo json_encode($output);
?>
