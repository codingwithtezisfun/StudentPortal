<?php
require 'session_start.php';
require 'db_connection.php';

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

// Fetch all unique course IDs from the student_unit_registrations table where the intake matches the current intake
$sql = "
    SELECT DISTINCT course_id
    FROM student_unit_registrations
    WHERE
        (MONTH(registration_date) BETWEEN 1 AND 3 AND '$current_intake' = 'Jan-Mar')
        OR (MONTH(registration_date) BETWEEN 4 AND 6 AND '$current_intake' = 'Apr-Jun')
        OR (MONTH(registration_date) BETWEEN 7 AND 9 AND '$current_intake' = 'Jul-Sep')
        OR (MONTH(registration_date) BETWEEN 10 AND 12 AND '$current_intake' = 'Oct-Dec')
";
$result = $conn->query($sql);

$course_ids = [];
while ($row = $result->fetch_assoc()) {
    $course_ids[] = $row['course_id'];
}

// If no courses found, skip the query
if (empty($course_ids)) {
    echo '<p class="text-warning">No courses found for the current intake.</p>';
    exit();
}

// Fetch course details including department
$placeholders = implode(',', array_fill(0, count($course_ids), '?'));
$sql = "
    SELECT c.course_id, c.courseName, d.departmentName
    FROM courses c
    JOIN departments d ON c.department_id = d.department_id
    WHERE c.course_id IN ($placeholders)
    ORDER BY d.departmentName, c.courseName
";

$stmt = $conn->prepare($sql);
$stmt->bind_param(str_repeat('i', count($course_ids)), ...$course_ids);
$stmt->execute();
$result = $stmt->get_result();

$grouped_courses = [];
while ($row = $result->fetch_assoc()) {
    $department_name = $row['departmentName'];
    $course_name = $row['courseName'];

    if (!isset($grouped_courses[$department_name])) {
        $grouped_courses[$department_name] = [];
    }
    $grouped_courses[$department_name][] = $course_name;
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Courses Grouped by Department</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
    <style>
        .container {
            margin-top: 20px;
        }
        .card-header {
            background-color: #6082b6;
            color: white;
            font-weight: bold;
        }
        .table thead th {
            background-color: #f8f9fa;
        }
        .department-header {
            font-weight: bold;
            background-color: #e9ecef;
            padding: 10px;
            margin-top: 10px;
            border-radius: 5px;
        }
        .card-header{
            text-align:center;
            background-color: #343a40;
        }
        .department-header{
            color:blue;
        }
    </style>
</head>
<body>
        <div class="container mt-2">
     <div class="card mb-4">
            <div class="card-header text-white">
                <h4 class="card-title mb-0"><i class="fas fa-book-open"></i> Active Courses</h4>
            </div>
            <div class="card-body">

        <div id="coursesContainer">
            <?php
            if (empty($grouped_courses)) {
                echo '<p class="text-warning">No courses found.</p>';
            } else {
                foreach ($grouped_courses as $department_name => $courses) {
                    echo '<div class="department-header">' . htmlspecialchars($department_name) . '</div>';
                    echo '<ul>';
                    foreach ($courses as $course) {
                        echo '<li>' . htmlspecialchars($course) . '</li>';
                    }
                    echo '</ul>';
                }
            }
            ?>
        </div>
    </div>
        </div>
        </div>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
