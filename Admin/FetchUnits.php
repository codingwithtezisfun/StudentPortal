<?php
require 'session_start.php';
require 'db_connection.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Units Form</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        .form-title {
            color: #ffffff;
            padding: 10px;
            margin-bottom: 15px;
        }
        .card-header{
            background-color: #343a40;
            color:white;
        }

        .table-header {
            background-color: #343a40;
        }

        .department-title {
            font-size: 1.5rem;
            margin-top: 10px;
            background-color: #343a40;
            color: #ffffff;
            padding: 5px;
        }

        .course-title {
            font-size: 1.5rem;
            margin-top: 10px;
            background-color: #007bff; /* Blue background for courses */
            color: #ffffff;
            padding: 5px;
        }

        .stage-title {
            font-size: 1.5rem;
            margin-top: 10px;
            background-color: #a9a9a9; /* Yellow background for stages */
            color: #000000;
            padding: 5px;
            text-align:center;
        }

        .semester-title {
            font-size: 1.5rem;
            margin-top: 10px;
        }

        .semester-column {
            display: flex;
            justify-content: space-between;
            background-color:#a9a9a9;
        }

        .semester-column > .semester {
            width: 50%;
            text-align:center;
        }

        .semester-header-1 {
            background-color: #dcdcdc;
        }

        .semester-header-2 {
            background-color: #dcdcdc;
        }

        .filters {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container mt-1">
        <div class="card">
            <div class="card-header text-center">
                <h2 style="font-family: Arial, sans-serif; font-size: 24px;">
                    <i class="fas fa-certificate" style=" font-size: 20px; margin-right: 10px;"></i>
                    Units Form
                </h2>
            </div>
            <div class="card-body">
                <div class="filters">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="departmentFilter">Department:</label>
                                <select id="departmentFilter" class="form-control">
                                    <option value="">All Departments</option>
                                    <!-- Options will be populated by JavaScript -->
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="courseFilter">Course:</label>
                                <select id="courseFilter" class="form-control" disabled>
                                    <option value="">All Courses</option>
                                    <!-- Options will be populated by JavaScript -->
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row d-none">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="stageFilter">Stage:</label>
                                <select id="stageFilter" class="form-control">
                                    
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="semesterFilter">Semester:</label>
                                <select id="semesterFilter" class="form-control" disabled>
                                    
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                         <div id="unitsTable">
                <!-- AJAX will populate the table here -->
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script>
$(document).ready(function() {
    let data = {};
    let departments = [];
    let courses = [];
    let stages = new Set();
    let semesters = new Set();

    // Fetch data from the server
    $.ajax({
        url: 'fetch_units.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            data = response;

            // Populate department filter
            let departmentOptions = '<option value="">All Departments</option>';
            $.each(data, function(index, department) {
                departmentOptions += `<option value="${department.departmentName}">${department.departmentName}</option>`;
                departments.push(department.departmentName);
            });
            $('#departmentFilter').html(departmentOptions);

            // Handle department change
            $('#departmentFilter').change(function() {
                let selectedDepartment = $(this).val();
                updateCourses(selectedDepartment);
                updateUnits();
            });

            // Handle course change
            $('#courseFilter').change(function() {
                updateUnits();
            });

            // Handle stage change
            $('#stageFilter').change(function() {
                updateUnits();
            });

            // Handle semester change
            $('#semesterFilter').change(function() {
                updateUnits();
            });

            // Initial population and filtering
            updateCourses();
            updateUnits();
        },
        error: function(xhr, status, error) {
            console.error('Error fetching data:', error);
            $('#unitsTable').html('<p>Error fetching data</p>');
        }
    });

    function updateCourses(selectedDepartment = '') {
        let courseFilter = $('#courseFilter');
        let stageFilter = $('#stageFilter');
        let semesterFilter = $('#semesterFilter');
        
        let courseOptions = '<option value="">All Courses</option>';
        let selectedCourses = [];

        if (selectedDepartment) {
            let department = data.find(dep => dep.departmentName === selectedDepartment);
            if (department) {
                $.each(department.courses, function(index, course) {
                    courseOptions += `<option value="${course.courseName}">${course.courseName}</option>`;
                    selectedCourses.push(course.courseName);
                    $.each(course.units, function(index, unit) {
                        stages.add(unit.stage);
                        semesters.add(unit.semester);
                    });
                });
            }
        }
        
        courseFilter.html(courseOptions).prop('disabled', !selectedDepartment);
        stageFilter.prop('disabled', !selectedDepartment);
        semesterFilter.prop('disabled', !selectedDepartment);
        
        let stageOptions = '<option value="">All Stages</option>';
        stages.forEach(stage => {
            stageOptions += `<option value="${stage}">Stage ${stage}</option>`;
        });
        stageFilter.html(stageOptions);

        let semesterOptions = '<option value="">All Semesters</option>';
        semesters.forEach(semester => {
            semesterOptions += `<option value="${semester}">Semester ${semester}</option>`;
        });
        semesterFilter.html(semesterOptions);
    }

    function updateUnits() {
        let departmentFilter = $('#departmentFilter').val();
        let courseFilter = $('#courseFilter').val();
        let stageFilter = $('#stageFilter').val();
        let semesterFilter = $('#semesterFilter').val();

        let filteredData = data;

        if (departmentFilter) {
            filteredData = filteredData.filter(department => department.departmentName === departmentFilter);
        }

        if (filteredData.length > 0) {
            let html = '';
            $.each(filteredData, function(index, department) {
                html += '<div class="department-title">' + department.departmentName + '</div>';

                $.each(department.courses, function(index, course) {
                    if (courseFilter && course.courseName !== courseFilter) {
                        return;
                    }
                    html += '<div class="course-title">' + course.courseName + '</div>';

                    let stages = {};
                   $.each(course.units, function(index, unit) {
                    if (!stages[unit.stage]) {
                        stages[unit.stage] = { '1': [], '2': [] };
                    }
                    if (!stages[unit.stage][unit.semester]) {
                        stages[unit.stage][unit.semester] = []; // Initialize as an empty array
                    }

                    if (!semesterFilter || unit.semester === semesterFilter) {
                        stages[unit.stage][unit.semester].push(unit);
                    }
                });

                    $.each(stages, function(stage, semesters) {
                        html += '<div class="stage-title">Stage ' + stage + '</div>';
                        html += '<div class="semester-column">';

                        html += '<div class="semester semester-header-1"><strong>Semester 1</strong><table class="table table-bordered">';
                        html += '<tbody>';
                        $.each(semesters['1'], function(index, unit) {
                            html += '<tr><td>' + unit.unitName + '</td></tr>';
                        });
                        html += '</tbody></table></div>';

                        html += '<div class="semester semester-header-2"><strong>Semester 2</strong><table class="table table-bordered">';
                        html += '<tbody>';
                        $.each(semesters['2'], function(index, unit) {
                            html += '<tr><td>' + unit.unitName + '</td></tr>';
                        });
                        html += '</tbody></table></div>';

                        html += '</div>';
                    });
                });
            });
            $('#unitsTable').html(html);
        } else {
            $('#unitsTable').html('<p>No units available</p>');
        }
    }
});
</script>