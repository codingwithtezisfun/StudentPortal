<?php
require 'session_start.php';
require 'db_connection.php';

// Fetch all departments
$departments_sql = "SELECT department_id, departmentName FROM departments";
$departments_result = $conn->query($departments_sql);

// Fetch all courses grouped by department
$courses_sql = "
    SELECT d.department_id, d.departmentName, c.courseName
    FROM courses c
    JOIN departments d ON c.department_id = d.department_id
    ORDER BY d.departmentName, c.courseName
";
$courses_result = $conn->query($courses_sql);

$grouped_courses = [];
while ($row = $courses_result->fetch_assoc()) {
    $department_id = $row['department_id'];
    $department_name = $row['departmentName'];
    $course_name = $row['courseName'];
    
    if (!isset($grouped_courses[$department_id])) {
        $grouped_courses[$department_id] = [
            'departmentName' => $department_name,
            'courses' => []
        ];
    }
    $grouped_courses[$department_id]['courses'][] = $course_name;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fetch All Courses</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
    <style>
        .container {
            margin-top: 20px;
        }
        .card-header {
            color: white;
            font-weight: bold;
            text-align:center;
            background-color: #343a40;
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
            color:blue;
        }
        .form-group{
            color:blue;
            font-weight:bold;
        }
    </style>
</head>
<body>
    <div class="container">
           <div class="card mb-4">
            <div class="card-header text-white">
                <h2 class="card-title mb-0"><i class="fas fa-book"></i> All Courses</h2>
            </div>
            <div class="card-body">
        <form id="fetchCoursesForm">
            <div class="form-group">
                <label for="department">Filter by Department:</label>
                <select class="form-control" id="department" name="department">
                    <option value="" selected>All Departments</option>
                    <?php
                    while ($row = $departments_result->fetch_assoc()) {
                        echo '<option value="' . $row['department_id'] . '">' . $row['departmentName'] . '</option>';
                    }
                    ?>
                </select>
            </div>
        </form>
        <div id="coursesContainer">
            <!-- Courses will be loaded here -->
            <?php
            if (empty($_POST['department'])) {
                // Show all courses by default
                foreach ($grouped_courses as $department_id => $department_data) {
                    echo '<div class="department-header">' . htmlspecialchars($department_data['departmentName']) . '</div>';
                    echo '<ul>';
                    foreach ($department_data['courses'] as $course) {
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
    <script>
        $(document).ready(function() {
            $('#department').on('change', function() {
                let departmentId = $(this).val();
                $.ajax({
                    url: 'fetch_all_courses.php',
                    type: 'POST',
                    data: { department_id: departmentId },
                    success: function(response) {
                        $('#coursesContainer').html(response);
                    },
                    error: function(xhr, status, error) {
                        $('#coursesContainer').html('<p class="text-danger">Error fetching courses. Please try again.</p>');
                    }
                });
            });
        });
    </script>
</body>
</html>
