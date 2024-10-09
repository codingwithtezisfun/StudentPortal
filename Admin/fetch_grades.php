<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $courseId = $_POST['course'];
    $studentGroup = $_POST['studentGroup'];
    $stage = $_POST['stage'];
    $semester = $_POST['semester']; // Added semester filter

    include 'db_connection.php';

    // Fetch students based on course, studentGroup
    $students_sql = "SELECT id, name, regNumber FROM students 
                     WHERE course = ? AND studentGroup = ?";
    $students_stmt = $conn->prepare($students_sql);
    $students_stmt->bind_param("is", $courseId, $studentGroup);
    $students_stmt->execute();
    $students_result = $students_stmt->get_result();

   // Assuming $includeElective is a boolean that determines if electives should be included
$includeElective = !empty($_POST['elective']) ? true : false;

// Base SQL query to fetch units based on course, stage, and semester
$units_sql = "SELECT u.unit_id, u.unitName 
              FROM course_units cu 
              JOIN units u ON cu.unit_id = u.unit_id 
              WHERE cu.course_id = ? AND cu.stage = ? AND cu.semester = ?";

// If electives are to be included, modify the SQL query to include them
if ($includeElective) {
    $units_sql .= " AND (cu.isElective = 0 OR (cu.isElective = 1 AND cu.electiveCategory = ?))";
}

$units_stmt = $conn->prepare($units_sql);

// Bind parameters based on whether electives are included or not
if ($includeElective) {
    $elective = $_POST['elective']; // Assuming elective category is posted
    $units_stmt->bind_param("iiss", $courseId, $stage, $semester, $elective);
} else {
    $units_stmt->bind_param("iis", $courseId, $stage, $semester);
}

$units_stmt->execute();
$units_result = $units_stmt->get_result();

if ($students_result->num_rows > 0 && $units_result->num_rows > 0) {
    echo '<form id="gradesForm" method="post">';
    echo '<table class="table table-bordered">';
    echo '<thead>';
    echo '<tr>';
    echo '<th>Student Name</th>';
    echo '<th>Reg Number</th>';
    while ($unit = $units_result->fetch_assoc()) {
        echo '<th class="sub-header">' . $unit['unitName'] . '</th>';
    }
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';

    $units_result->data_seek(0); // Reset pointer to the beginning


        while ($student = $students_result->fetch_assoc()) {
            echo '<tr>';
            echo '<td>' . $student['name'] . '</td>';
            echo '<td>' . $student['regNumber'] . '</td>';
            // Fetch units again to match with student rows
            $units_result->data_seek(0);
            while ($unit = $units_result->fetch_assoc()) {
                // Fetch existing grades
                $grades_stmt = $conn->prepare("SELECT grade FROM grades WHERE student_id = ? AND unit_Id = ?");
                $grades_stmt->bind_param("ii", $student['id'], $unit['unit_id']);
                $grades_stmt->execute();
                $grades_result = $grades_stmt->get_result();
                $grade = $grades_result->num_rows > 0 ? $grades_result->fetch_assoc()['grade'] : 0;

                echo '<td><input type="number" step="0.01" class="form-control" name="grades[' . $student['id'] . '][' . $unit['unit_id'] . ']" value="' . $grade . '"></td>';
            }
            echo '</tr>';
        }

        echo '</tbody>';
        echo '</table>';
        echo '<div class="form-row mb-3">';
        echo '<div class="col-md-12 text-center">';
        echo '<button type="submit" name="save" class="btn btn-success">Save</button>';
        echo '</div>';
        echo '</div>';
        echo '</form>';
    } else {
        echo '<p class="text-danger">No students or units found for the selected criteria.</p>';
    }

    $students_stmt->close();
    $units_stmt->close();
    $conn->close();
}
?>
