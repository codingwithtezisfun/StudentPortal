<?php
require 'session_start.php';
require 'db_connection.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fetch Students</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
         .card-header {
            background-color: #343a40;
            color:white;
        }
        .form-row{
            color:blue;
            font-weight:bold;
        }
        </style>
</head>
<body>
    <div class="container mt-4">
        <div class="card mt-4">
            <div class="card-header text-center">
                <h2 style="font-family: Arial, sans-serif; font-size: 24px;">
                    <i class="fas fa-user-graduate" style="font-size: 20px; margin-right: 10px;"></i>
                    All Students Session Data
                </h2>
            </div>

            <div class="form-row mt-4 ml-3 mr-3">
                <div class="col-md-3">
                    <label for="department">Department</label>
                    <select class="form-control" id="department">
                        <option value="">Select Department</option>
                        <?php
                        require 'db_connection.php';
                        $sql = "SELECT department_id, departmentName FROM departments";
                        $result = $conn->query($sql);
                        if ($result->num_rows > 0) {
                            while($row = $result->fetch_assoc()) {
                                echo '<option value="' . $row['department_id'] . '">' . $row['departmentName'] . '</option>';
                            }
                        }
                        $conn->close();
                        ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="course">Course</label>
                    <select class="form-control" id="course">
                        <!-- Courses will be loaded based on the selected department -->
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="group">Group</label>
                    <input type="text" class="form-control" id="group" placeholder="Enter Group">
                </div>
                <div class="col-md-2">
                    <label for="intake">Intake</label>
                    <select class="form-control" id="intake">
                        <option value="">Select Intake</option>
                        <option value="Jan-Mar">Jan-Mar</option>
                        <option value="Apr-Jun">Apr-Jun</option>
                        <option value="Jul-Sep">Jul-Sep</option>
                        <option value="Oct-Dec">Oct-Dec</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="year">Year</label>
                    <input type="number" class="form-control" id="year" value="<?php echo date("Y"); ?>" placeholder="Enter Year">
                </div>
            </div>

            <div class="card-body">
                <table class="table table-bordered mt-3">
                    <thead>
                        <tr>
                            <th>#</th> <!-- Auto-numbering column -->
                            <th>Name</th>
                            <th>Registration Number</th>
                            <th>Department</th>
                            <th>Course</th>
                            <th>Group</th>
                            <th>Semester</th>
                            <th>Session Status</th>
                        </tr>
                    </thead>
                    <tbody id="studentsTableBody">
                        <!-- Data will be inserted here via AJAX -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script>
        $(document).ready(function() {
            // Function to fetch students based on filters
            function fetchStudents() {
                let department = $('#department').val();
                let course = $('#course').val();
                let group = $('#group').val();
                let intake = $('#intake').val();
                let year = $('#year').val();

                $.ajax({
                    url: 'student_session_status.php',
                    type: 'GET',
                    data: {
                        department: department,
                        course: course,
                        group: group,
                        intake: intake,
                        year: year
                    },
                    success: function(response) {
                        $('#studentsTableBody').html(response);
                        // Add auto-numbering to the table
                        $('#studentsTableBody tr').each(function(index) {
                            $(this).prepend('<td>' + (index + 1) + '</td>');
                        });
                    },
                    error: function() {
                        $('#studentsTableBody').html('<tr><td colspan="8">Error fetching students.</td></tr>');
                    }
                });
            }

            // Fetch students on page load
            fetchStudents();

            // Trigger fetchStudents on any input change
            $('#department, #course, #group, #intake, #year').on('change', fetchStudents);

            // Populate courses dynamically based on department
            $('#department').on('change', function() {
                var departmentId = $(this).val();
                console.log('Department selected:', departmentId); // Log selected department

                $.ajax({
                    url: 'fetch_courses.php',
                    type: 'GET',
                    data: { department_id: departmentId },
                    dataType: 'json',
                    success: function(data) {
                        console.log('Courses data received:', data); // Log the received data
                        var $courseSelect = $('#course');
                        $courseSelect.empty(); // Clear existing options
                        $courseSelect.append('<option value="">Select Course</option>'); // Default option

                        if (data.error) {
                            console.error('Error fetching courses:', data.error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: data.error,
                                showConfirmButton: true
                            });
                        } else {
                            $.each(data, function(index, course) {
                                $courseSelect.append('<option value="' + course.course_id + '">' + course.courseName + '</option>');
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX error fetching courses:', status, error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'An error occurred while fetching courses.',
                            showConfirmButton: true
                        });
                    }
                });
            });
        });
    </script>
</body>
</html>
