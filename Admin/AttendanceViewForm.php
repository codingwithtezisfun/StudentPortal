<?php
require 'session_start.php';
require 'db_connection.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Report</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <style>
        .card-header{
            background-color: #343a40;
            text-align:center;
            padding:20px;
        }
        .container{
            color:blue;
            font-weight:bold;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header text-white">
                <h5 class="card-title mb-0"> <i class="fas fa-chart-line"> </i> Attendance Report</h5>
            </div>
            <div class="card-body">
                <form id="attendanceForm">
                    <div class="form-row">
                        <div class="form-group col-md-3">
                            <label for="department">Department:</label>
                            <select class="form-control" id="department" name="department">
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
                        <div class="form-group col-md-3">
                            <label for="course">Course:</label>
                            <select class="form-control" id="course" name="course">
                                <option value="" selected>Select Course</option>
                            </select>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="group">Group:</label>
                            <input type="text" class="form-control" id="group" name="group_id">
                        </div>
                        <div class="form-group col-md-3">
                            <label for="stage">Stage:</label>
                            <select class="form-control" id="stage" name="stage">
                                <option value="" selected>Select Stage</option>
                                <option value="1">Stage 1</option>
                                <option value="2">Stage 2</option>
                                <option value="3">Stage 3</option>
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary col-md-12">Fetch Attendance</button>
                </form>
                <hr>
                <div id="attendanceReport" style="display: none; font-weight:normal;">
                    <h5>Attendance Report</h5>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Student Name</th>
                                <th>Registration Number</th>
                                <th>Stage</th>
                                <th>Percentage</th>
                            </tr>
                        </thead>
                        <tbody id="reportTableBody">
                            <!-- Data will be inserted here via JavaScript -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
    $("#department").on("change", function () {
        let departmentId = $(this).val();
        $.ajax({
            url: "fetch_courses.php",
            type: "GET",
            data: { department_id: departmentId },
            dataType: "json",
            success: function (data) {
                let $courseSelect = $("#course");
                $courseSelect.empty();
                $courseSelect.append('<option value="">Select Course</option>');
                $.each(data, function (index, course) {
                    $courseSelect.append(
                        '<option value="' + course.course_id + '">' + course.courseName + '</option>'
                    );
                });
            },
            error: function (xhr, status, error) {
                console.error("AJAX Error:", status, error);
            },
        });
    });

     $("#attendanceForm").on("submit", function (event) {
        event.preventDefault(); // Prevent the default form submission

        let courseId = $("#course").val();
        let stage = $("#stage").val();
        let groupId = $("#group").val(); // Get the selected group

        if (!courseId || !stage) {
            alert("Please select both course and stage.");
            return;
        }
        $.ajax({
            url: "fetch_attendance_report.php",
            type: "POST",
            data: {
                department_id: $("#department").val(),
                course_id: courseId,
                group_id: groupId,
                stage: stage
            },
            dataType: "json",
            success: function (data) {
                $('#reportTableBody').empty();
                if (data.error) {
                    $('#attendanceReport').hide();
                    console.error(data.error);
                    return;
                }

                $('#attendanceReport').show();
                $.each(data, function (index, student) {
                    // Ensure percentage is a valid number before displaying
                    let percentage = isNaN(student.stagePercentage) ? 'N/A' : student.stagePercentage;

                    $('#reportTableBody').append(
                        '<tr>' +
                        '<td>' + student.name + '</td>' +
                        '<td>' + student.regNumber + '</td>' +
                        '<td>' + student.stage + '</td>' +
                        '<td>' + percentage + '%</td>' +
                        '</tr>'
                    );
                });
            },
            error: function (xhr) {
                console.error('AJAX error:', xhr.responseText);
                $('#attendanceReport').hide();
                $('#noDataMessage').text('Error fetching attendance data').show();
            }
        });
    });
});

    </script>
</body>
</html>
