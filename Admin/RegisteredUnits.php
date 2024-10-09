<?php
require 'session_start.php';
require 'db_connection.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Filter Units by Course and Stage</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
</head>
<body>
    <div class="container mt-4">
        <div class="card">
            <div class="card-header text-center" style="background-color: #36454f;">
                <h2 style="font-family: Arial, sans-serif; font-size: 24px; color: white;">Filter Units by Course and Stage</h2>
            </div>
            <div class="card-body" style="color:blue; font-weight:bold;">
                <form id="filter-form" class="border p-4">
                    <div class="form-row mb-3">
                        <div class="col-md-4">
                            <label for="department">Department</label>
                        </div>
                        <div class="col-md-8">
                            <select class="form-control" id="department" name="department" required>
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
                    </div>
                    <div class="form-row mb-3">
                        <div class="col-md-4">
                            <label for="course">Course</label>
                        </div>
                        <div class="col-md-8">
                            <select class="form-control" id="course" name="course" required>
                                <option value="">Select Course</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row mb-3">
                        <div class="col-md-12 text-center">
                            <button type="button" id="fetch-units" class="btn btn-primary">Fetch Units</button>
                        </div>
                    </div>
                </form>

                <div id="units-table" style="display:none;">
                    <h4>Units by Stage</h4>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Course</th>
                                <th>Stage 1 Units</th>
                                <th>Stage 2 Units</th>
                                <th>Stage 3 Units</th>
                            </tr>
                        </thead>
                        <tbody id="units-body">
                            <!-- Units will be populated here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Populate courses based on selected department
            $('#department').on('change', function() {
                var departmentId = $(this).val();

                $.ajax({
                    url: 'fetch_courses.php',
                    type: 'GET',
                    data: { department_id: departmentId },
                    dataType: 'json',
                    success: function(data) {
                        var $courseSelect = $('#course');
                        $courseSelect.empty();
                        $courseSelect.append('<option value="">Select Course</option>');
                        $.each(data, function(index, course) {
                            $courseSelect.append('<option value="' + course.course_id + '">' + course.courseName + '</option>');
                        });
                    },
                    error: function(xhr, status, error) {
                        alert('Error fetching courses: ' + error);
                    }
                });
            });

            // Fetch and display units when the "Fetch Units" button is clicked
            $('#fetch-units').on('click', function() {
                var courseId = $('#course').val();

                if (courseId) {
                    $.ajax({
                        url: 'fetch_registered_units.php',
                        type: 'GET',
                        data: { course_id: courseId },
                        dataType: 'json',
                        success: function(data) {
                            var $unitsBody = $('#units-body');
                            $unitsBody.empty();

                            var stages = ['1', '2', '3'];
                            var row = '<tr><td>' + data.courseName + '</td>';
                            stages.forEach(function(stage) {
                                var units = data.units[stage] ? data.units[stage].join('<br>') : 'No units';
                                row += '<td>' + units + '</td>';
                            });
                            row += '</tr>';

                            $unitsBody.append(row);
                            $('#units-table').show();
                        },
                        error: function(xhr, status, error) {
                            alert('Error fetching units: ' + error);
                        }
                    });
                } else {
                    alert('Please select a course.');
                }
            });
        });
    </script>
</body>
</html>
