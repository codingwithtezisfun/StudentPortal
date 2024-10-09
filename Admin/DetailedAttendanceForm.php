<?php
require 'session_start.php';
require 'db_connection.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detailed Attendance</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .container{
            color:blue;
            font-weight:bold;
        }
        .card-header{
            background-color: #343a40;
            color: white;
            padding:20px;
            text-align:center;
        }

    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header text-white">
                <h5 class="card-title mb-0"><i class="fas fa-clipboard-check"></i> Detailed Attendance</h5>
            </div>
            <div class="card-body">
                <form id="detailedAttendanceForm">
                    <div class="form-group">
                        <label for="regNumber">Student Registration Number:</label>
                        <input type="text" class="form-control" id="regNumber" name="regNumber" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Fetch Attendance</button>
                </form>
                <hr>
                <div id="filters" style="display: none;">
                    <div class="form-row">
                        <div class="form-group col-md-3">
                            <label for="filterStage">Stage:</label>
                            <select class="form-control" id="filterStage" name="stage">
                                <option value="">Select Stage</option>
                            </select>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="filterSemester">Semester:</label>
                            <select class="form-control" id="filterSemester" name="semester">
                                <option value="">Select Semester</option>
                            </select>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="filterUnit">Unit:</label>
                            <select class="form-control" id="filterUnit" name="unit">
                                <option value="">Select Unit</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div id="attendanceReport" style="display: none; font-weight:normal;">
                    <h5>Detailed Attendance Report</h5>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Stage</th>
                                <th>Semester</th>
                                <th>Unit Name</th>
                                <th>Date</th>
                                <th>Session</th>
                                <th>Status</th>
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
            // Function to fetch data and populate the filters
            function fetchData() {
                let regNumber = $("#regNumber").val();

                $.ajax({
                    url: "detailed_attendance.php",
                    type: "POST",
                    data: { regNumber: regNumber },
                    dataType: "json",
                    success: function (data) {
                        $('#filters').hide(); // Hide filters initially
                        $('#reportTableBody').empty();

                        if (data.error) {
                            $('#attendanceReport').hide();
                            console.error(data.error);
                            return;
                        }

                        // Populate filters based on the fetched data
                        let stages = new Set();
                        let semesters = new Set();
                        let units = new Set();

                        $.each(data, function (stage, stageData) {
                            stages.add(stage);
                            $.each(stageData.semesters, function (semester, semesterData) {
                                semesters.add(semester);
                                $.each(semesterData.units, function (unitName, unitData) {
                                    units.add(unitName);
                                    $.each(unitData.dates, function (index, session) {
                                        $('#reportTableBody').append(
                                            '<tr>' +
                                            '<td>' + stage + '</td>' +
                                            '<td>' + semester + '</td>' +
                                            '<td>' + unitName + '</td>' +
                                            '<td>' + session.date + '</td>' +
                                            '<td>' + session.session + '</td>' +
                                            '<td>' + session.status + '</td>' +
                                            '</tr>'
                                        );
                                    });
                                });
                            });
                        });

                        // Populate stage filter
                        $('#filterStage').empty().append('<option value="">Select Stage</option>');
                        stages.forEach(function (stage) {
                            $('#filterStage').append('<option value="' + stage + '">' + stage + '</option>');
                        });

                        // Populate semester filter
                        $('#filterSemester').empty().append('<option value="">Select Semester</option>');
                        semesters.forEach(function (semester) {
                            $('#filterSemester').append('<option value="' + semester + '">' + semester + '</option>');
                        });

                        // Populate unit filter
                        $('#filterUnit').empty().append('<option value="">Select Unit</option>');
                        units.forEach(function (unit) {
                            $('#filterUnit').append('<option value="' + unit + '">' + unit + '</option>');
                        });

                        $('#filters').show();
                        $('#attendanceReport').show();
                    },
                    error: function (xhr) {
                        console.error('AJAX error:', xhr.responseText);
                        $('#attendanceReport').hide();
                        $('#filters').hide();
                    }
                });
            }

            // Fetch data when the form is submitted
            $("#detailedAttendanceForm").on("submit", function (event) {
                event.preventDefault(); // Prevent the default form submission
                fetchData();
            });

            // Filter functionality
            $("#filterStage, #filterSemester, #filterUnit").on("change", function () {
                let stage = $("#filterStage").val();
                let semester = $("#filterSemester").val();
                let unit = $("#filterUnit").val();

                $("#reportTableBody tr").each(function () {
                    let row = $(this);
                    let rowStage = row.find('td').eq(0).text();
                    let rowSemester = row.find('td').eq(1).text();
                    let rowUnit = row.find('td').eq(2).text();

                    if ((stage === "" || rowStage === stage) &&
                        (semester === "" || rowSemester === semester) &&
                        (unit === "" || rowUnit === unit)) {
                        row.show();
                    } else {
                        row.hide();
                    }
                });
            });

            // Fetch data on page load if regNumber is already provided
            if ($("#regNumber").val()) {
                fetchData();
            }
        });
    </script>
</body>
</html>