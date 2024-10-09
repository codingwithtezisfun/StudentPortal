<?php
require 'session_start.php';
require 'db_connection.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fetch Fee Statements</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
    <style>
        .text-muted {
            color: #6c757d; /* or any other color you prefer */
            font-size: 0.8rem; /* adjust size as needed */
        }

        .card-header {
            background-color: #343a40;
        }
        .form {
            color: blue;
            font-weight: bold;
        }
        .button {
            width: 100%;
        }
    </style>
</head>
<body>
    <div class="container mt-2">
        <div class="card">
            <div class="card-header text-center">
                <h2 style="font-family: Arial, sans-serif; font-size: 24px; color: white;">
                    <i class="fas fa-money-check-alt" style="color: white; font-size: 20px; margin-right: 10px;"></i>
                    Fetch Fee Statements
                </h2>
            </div>
            <div class="card-body">
                <form id="fetchFeeForm" class="border form p-4">
                    <div class="form-row mb-3">
                        <div class="col-md-4">
                            <label for="fetchYear">Year</label>
                        </div>
                        <div class="col-md-8">
                            <input type="number" class="form-control" id="fetchYear" name="year" value="<?php echo date('Y'); ?>"  placeholder="Enter year">
                        </div>
                    </div>

                    <div class="form-row mb-3">
                        <div class="col-md-4">
                            <label for="fetchIntake">Intake</label>
                        </div>
                        <div class="col-md-8">
                            <select class="form-control" id="fetchIntake" name="intake">
                                <option value="">All</option>
                                <option value="Jan-Mar">Jan-Mar</option>
                                <option value="Apr-Jun">Apr-Jun</option>
                                <option value="Jul-Sep">Jul-Sep</option>
                                <option value="Oct-Dec">Oct-Dec</option>
                            </select>
                        </div>
                    </div>

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
                            <label for="fetchCourse">Course</label>
                        </div>
                        <div class="col-md-8">
                            <select class="form-control" id="fetchCourse" name="courseId">
                                <option value="">All</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row mb-3">
                        <div class="col-md-12 text-center">
                            <button type="button" class="btn button btn-primary" id="fetchFee">Fetch Fee</button>
                        </div>
                    </div>
                </form>
                <div id="fetchFeeTableContainer" class="mt-4"></div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            // Fetch courses based on selected department
            $('#department').on('change', function() {
                var departmentId = $(this).val();

                $.ajax({
                    url: 'fetch_courses.php',
                    type: 'GET',
                    data: { department_id: departmentId },
                    dataType: 'json',
                    success: function(data) {
                        var $courseSelect = $('#fetchCourse');
                        $courseSelect.empty(); // Clear existing options
                        $courseSelect.append('<option value="">All</option>'); // Default option

                        if (data.error) {
                            console.error('Error fetching courses:', data.error);
                        } else {
                            $.each(data, function(index, course) {
                                $courseSelect.append('<option value="' + course.course_id + '">' + course.courseName + '</option>');
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX error fetching courses:', status, error);
                    }
                });
            });

            // Set up event listener for fetch fee button
            $('#fetchFee').click(function() {
                fetchFeeStatements();
            });
        });

        function fetchFeeStatements() {
            var year = $('#fetchYear').val();
            var intake = $('#fetchIntake').val();
            var courseId = $('#fetchCourse').val();
            var departmentId = $('#department').val(); // Add department filter

            $.ajax({
                url: 'fetch_fee_statements.php',
                type: 'GET',
                dataType: 'json',
                data: {
                    year: year,
                    intake: intake,
                    courseId: courseId,
                    departmentId: departmentId // Include department filter
                },
                success: function(data) {
                    displayFeeStatements(data);
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching fee statements:', error);
                }
            });
        }

        function displayFeeStatements(data) {
            if (!Array.isArray(data)) {
                $('#fetchFeeTableContainer').html('<p>No fee statements found for the selected criteria.</p>');
                return;
            }

            var tableHtml = '<table class="table table-bordered"><thead><tr><th>Year</th><th>Course</th><th>Intake</th><th>Fee Amount</th></tr></thead><tbody>';

            data.forEach(function(row) {
                tableHtml += '<tr><td>' + row.year + '</td><td>' + row.course + '</td><td>' + row.intake + '</td><td>' + row.fee_amount + '</td></tr>';
            });

            tableHtml += '</tbody></table>';
            $('#fetchFeeTableContainer').html(tableHtml);
        }
    </script>
</body>
</html>
