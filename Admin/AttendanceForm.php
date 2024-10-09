<?php
require 'session_start.php';
require 'db_connection.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Attendance</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .form-group{
            color:blue;
            font-weight:bold;
        }
        .card-header{
            background-color: #343a40;
            text-align:center;
            padding:20px;
        }
        </style>
</head>
<body>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header text-white">
                <h5 class="card-title mb-0"><i class="fas fa-chart-line"></i> Attendance Overview</h5>
            </div>
            <div class="card-body">
                <form id="fetchAttendanceForm">
                    <div class="form-group">
                        <label for="regNumber">Registration Number:</label>
                        <input type="text" id="regNumber" name="regNumber" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Fetch Attendance</button>
                </form>
                <hr>
                <table class="table table-bordered" id="attendanceTable" style="display: none;">
                    <thead>
                        <tr>
                            <th>Unit</th>
                            <th>Present</th>
                            <th>Total</th>
                            <th>Percentage</th>
                        </tr>
                    </thead>
                    <tbody id="attendanceTableBody">
                        <!-- Data will be inserted here via JavaScript -->
                    </tbody>
                </table>
                <div id="noDataMessage" style="display: none;">No attendance records found.</div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#fetchAttendanceForm').on('submit', function(event) {
                event.preventDefault();
                let regNumber = $('#regNumber').val();
                $.ajax({
                    url: 'fetch_attendance.php',
                    type: 'POST',
                    data: { regNumber: regNumber },
                    dataType: 'json',
                    success: function(data) {
                        $('#attendanceTableBody').empty();
                        if (data.error) {
                            $('#attendanceTable').hide();
                            $('#noDataMessage').text(data.error).show();
                            return;
                        }

                        if (data.length > 0) {
                            $('#attendanceTable').show();
                            $('#noDataMessage').hide();
                            data.forEach(function(stage) {
                                $('#attendanceTableBody').append('<tr class="table-secondary"><td colspan="4">Stage ' + stage.stage + '</td></tr>');
                                $.each(stage.semesters, function(semester, semesterData) {
                                    $('#attendanceTableBody').append('<tr class="table-secondary"><td colspan="4">Semester ' + semester + '</td></tr>');
                                    $.each(semesterData.units, function(index, unit) {
                                        $('#attendanceTableBody').append(
                                            '<tr>' +
                                            '<td>' + unit.unitName + '</td>' +
                                            '<td>' + unit.present_sessions + '</td>' +
                                            '<td>' + unit.total_sessions + '</td>' +
                                            '<td>' + parseFloat(unit.percentage).toFixed(2) + '%</td>' +
                                            '</tr>'
                                        );
                                    });

                                    $('#attendanceTableBody').append('<tr class="font-weight-bold"><td colspan="3">Total for Semester ' + semester + '</td><td>' + parseFloat(semesterData.semesterPercentage).toFixed(2) + '%</td></tr>');
                                });

                                $('#attendanceTableBody').append('<tr class="font-weight-bold"><td colspan="3">Total Attendance for Stage ' + stage.stage + '</td><td>' + parseFloat(stage.stagePercentage).toFixed(2) + '%</td></tr>');
                            });
                        } else {
                            $('#attendanceTable').hide();
                            $('#noDataMessage').show();
                        }
                    },
                    error: function(xhr) {
                        console.error('AJAX error:', xhr.responseText);
                        $('#attendanceTable').hide();
                        $('#noDataMessage').text('Error fetching attendance data').show();
                    }
                });
            });
        });
    </script>
</body>
</html>
