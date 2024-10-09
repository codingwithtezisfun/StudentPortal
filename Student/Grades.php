<?php

require 'session_start.php';
require 'db_connection.php';

$email = $_SESSION['email'];
$stmt = $conn->prepare("SELECT course, studentGroup FROM students WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $student = $result->fetch_assoc();
    $courseId = $student['course'];
    $studentGroup = $student['studentGroup'];
} else {
    echo '<div class="alert alert-danger">Student details not found.</div>';
    exit();
}

$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Grades</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
    <style>
        .card-header {
            background-color: #36454f;
        }
        .form-title {
            background-color: #36454f;;
            color: white;
            padding: 10px;
            margin-bottom: 15px;
        }
        .table-header {
            background-color: #add8e6;
            color:blue;
        }
    </style>
</head>
<body>
    <div class="container mt-3">
        <div class="card">
            <div class="card-header text-center">
                <h2 style="font-family: Arial, sans-serif; font-size: 24px; color: white;">
                    <i class="fas fa-book-open" style="color: white; font-size: 20px; margin-right: 10px;"></i>
                    My Grades
                </h2>
            </div>
            <div class="card-body" style="color: blue;">
                <?php
                $gradesFetched = false;

                for ($stage = 1; $stage <= 3; $stage++) {
                    // Fetch units for the course and stage
                    $units_stmt = $conn->prepare("SELECT u.unit_id, u.unitName 
                              FROM units u
                              JOIN course_units cu ON u.unit_id = cu.unit_id
                              WHERE cu.course_id = ? AND cu.stage = ?");
                    $units_stmt->bind_param("ii", $courseId, $stage);

                    $units_stmt->execute();
                    $units_result = $units_stmt->get_result();

                    if ($units_result->num_rows > 0) {
                        $gradesFetched = true;
                        echo '<h3 class ="form-title">Stage ' . $stage . '</h3>';
                        echo '<table class="table table-bordered">';
                        echo '<thead>';
                        echo '<tr>';
                        echo '<th class="table-header">Unit Name</th>';
                        echo '<th class="table-header">Grade</th>';
                        echo '</tr>';
                        echo '</thead>';
                        echo '<tbody>';

                        while ($unit = $units_result->fetch_assoc()) {
                            // Fetch grades for the unit
                            $grades_stmt = $conn->prepare("SELECT grade FROM grades WHERE student_id = (SELECT id FROM students WHERE email = ?) AND unit_id = ?");
                            $grades_stmt->bind_param("si", $email, $unit['unit_id']);

                            $grades_stmt->execute();
                            $grades_result = $grades_stmt->get_result();

                            $grade = $grades_result->num_rows > 0 ? $grades_result->fetch_assoc()['grade'] : 'N/A';

                            echo '<tr>';
                            echo '<td>' . $unit['unitName'] . '</td>';
                            echo '<td>' . $grade . '</td>';
                            echo '</tr>';
                        }

                        echo '</tbody>';
                        echo '</table>';
                    }

                    $units_stmt->close();
                }

                if (!$gradesFetched) {
                    echo '<div class="alert alert-danger">You dont have any grades Currently.</div>';
                }

                $conn->close();
                ?>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
