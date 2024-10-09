<?php
require 'session_start.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Units</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        .stage-title {
            background-color: #778899;
            color: white;
            padding: 10px;
            margin-bottom: 15px;
        }

        .semester-title {
            background-color: #add8e6;
            color: blue;
            padding: 8px;
            margin-bottom: 10px;
            font-weight: bold;
        }

        .table-header {
            background-color: #add8e6;
            color: blue;
        }
        
        .card-header {
            background-color: #36454f;
            color: white;
            padding: 10px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="container mt-3">
        <div class="card">
            <div class="card-header text-center">
                <h2 style="font-family: Arial, sans-serif; font-size: 24px;">
                    <i class="fas fa-certificate" style="font-size: 20px; margin-right: 10px;"></i>
                    Course Units
                </h2>
            </div>
            <div class="card-body">
                <div id="unitsTable"></div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script>
        $(document).ready(function() {
            $.ajax({
                url: 'fetch_course_units.php',
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    $('#unitsTable').empty(); // Clear existing table

                    if (data.length > 0) {
                        $.each(data, function(index, semesterData) {
                            var semester = semesterData.semester;
                            var semesterUnits = semesterData.data;

                            $.each(semesterUnits, function(index, stageData) {
                                var stage = stageData.stage;
                                var units = stageData.units;

                                // Create the stage and semester header
                                var stageHtml = '<div class="stage-title"><h3>Stage ' + stage + '</h3></div>';
                                stageHtml += '<div class="semester-title">Semester ' + semester + '</div>';

                                // Create the units table
                                stageHtml += '<table class="table table-bordered">';
                                stageHtml += '<thead class="table-header"><tr><th>Unit Name</th></tr></thead>';
                                stageHtml += '<tbody>';

                                if (units.length > 0) {
                                    $.each(units, function(index, unit) {
                                        stageHtml += '<tr><td>' + unit.unitName + '</td></tr>';
                                    });
                                } else {
                                    stageHtml += '<tr><td colspan="1">No units found for this semester and stage</td></tr>';
                                }

                                stageHtml += '</tbody></table>';

                                // Append the constructed HTML to the unitsTable
                                $('#unitsTable').append(stageHtml);
                            });
                        });
                    } else {
                        $('#unitsTable').html('<p>No units found</p>');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching data:', error);
                    $('#unitsTable').html('<p>Error fetching data</p>');
                }
            });
        });
    </script>
</body>
</html>
