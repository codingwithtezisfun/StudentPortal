<?php
require 'session_start.php';
require 'db_connection.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register New Course</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        .container {
            margin-top: 20px;
            color:blue;
            font-weight:bold;
        }
        .form-group {
            margin-bottom: 1rem;
        }
        .card-header{
            background-color: #343a40;
            text-align:center;
            padding:15px;
        }
        .button{
            width:100%;
        }
    </style>
</head>
<body>
    <div class="container">
           <div class="card mb-4">
            <div class="card-header text-white">
                <h4 class="card-title mb-0"><i class="fas fa-book"></i> Register Course</h4>
            </div>
            <div class="card-body">
        <form id="registerCourseForm">
            <div class="form-group">
                <label for="department">Department</label>
                <select class="form-control" id="department" name="department" required>
                    <option value="" selected>Select Department</option>
                    <?php
                    require 'db_connection.php';
                    $sql = "SELECT department_id, departmentName FROM departments";
                    $result = $conn->query($sql);
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo '<option value="' . $row['department_id'] . '">' . $row['departmentName'] . '</option>';
                        }
                    }
                    $conn->close();
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="courseName">Course Name</label>
                <input type="text" class="form-control" id="courseName" name="courseName" placeholder="Enter Course Name" required>
            </div>
            <button type="submit" class="btn button btn-primary">Register Course</button>
        </form>
    </div>
                </div>
                </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#registerCourseForm').on('submit', function(event) {
                event.preventDefault();

                let department = $('#department').val();
                let courseName = $('#courseName').val();

                $.ajax({
                    url: 'register_course.php',
                    type: 'POST',
                    data: {
                        department: department,
                        courseName: courseName
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: 'Course registered successfully.'
                        });
                        $('#registerCourseForm')[0].reset();
                    },
                    error: function(xhr, status, error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'An error occurred while registering the course. Please try again.'
                        });
                    }
                });
            });
        });
    </script>
</body>
</html>
