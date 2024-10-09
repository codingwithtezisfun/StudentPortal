<?php
require 'session_start.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Session Form</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <style>
      .container{
        max-width:800px;
      }
        </style>
</head>
<body>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header text-center" style="background-color: #36454f;">
                <h2 style="font-family: Arial, sans-serif; font-size: 24px; color: white;">
                <i class="fa-solid fa-circle-check" style="color: white; font-size: 20px margin-right: 10px;"></i>
                Report Session
            </h2>
            </div>
            <div class="card-body" style="color:blue; font-weight:bold;">
                <div id="alert-message" class="alert" style="display:none;"></div>
                
                <div id="report-form" style="display:none;">
                    <form class="border p-4" id="session-form">
                        <div class="form-row mb-3">
                            <div class="col-md-4">
                                <label for="regNo">Registration Number</label>
                            </div>
                            <div class="col-md-8">
                            <input type="text" class="form-control" id="regNo" name="reg_no" value="" readonly>
                            </div>
                        </div>
                        <div class="form-row mb-3">
                            <div class="col-md-4">
                                <label for="intake">Semester</label>
                            </div>
                            <div class="col-md-8">
                                <select class="form-control" id="intake" name="intake" disabled>
                                    <option>Jan-Mar</option>
                                    <option>Apr-Jun</option>
                                    <option>Jul-Sep</option>
                                    <option>Oct-Dec</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-row mb-3">
                            <div class="col-md-4">
                                <label for="year">Year</label>
                            </div>
                            <div class="col-md-8">
                                <input type="number" class="form-control" id="year" name="year" value="<?php echo date('Y'); ?>" placeholder="Enter year" disabled>
                            </div>
                        </div>
                        <div class="form-row mb-3">
                            <div class="col-md-12 text-center">
                                <button style="width:100%; padding:10px; font-weight:bold;" type="submit" class="btn btn-primary">Report Session</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
      $(document).ready(function() {
    // Fetch student info on page load
    $.ajax({
        url: 'Session.php',
        type: 'POST',
        data: { action: 'fetch_student_info' },
        dataType: 'json',
        success: function(response) {
            if (response.regNumber) {
                $('#regNo').val(response.regNumber);
                $('#intake').val(response.intake); // Set current intake by default
                $('#year').val(new Date().getFullYear()); // Set current year by default

                // Check if session is reported for the current intake and year
                $.ajax({
                    url: 'Session.php',
                    type: 'POST',
                    data: {
                        action: 'check_session',
                        intake: response.intake,
                        year: $('#year').val()
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'reported') {
                            $('#alert-message').addClass('alert-success').text('Session already reported.').show();
                        } else {
                            $('#report-form').show(); // Show form if session is not reported
                        }
                    },
                    error: function() {
                        $('#alert-message').addClass('alert-danger').text('Error checking session status.').show();
                    }
                });
            } else {
                $('#alert-message').addClass('alert-danger').text('Error fetching student information.').show();
            }
        },
        error: function() {
            $('#alert-message').addClass('alert-danger').text('Error fetching student information.').show();
        }
    });

    // Handle form submission
    $('#session-form').submit(function(e) {
        e.preventDefault();
        $.ajax({
            url: 'Session.php',
            type: 'POST',
            data: {
                action: 'report_session',
                intake: $('#intake').val(),
                year: $('#year').val()
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    $('#alert-message').removeClass('alert-danger').addClass('alert-success').text(response.message).show();
                    $('#report-form').hide(); // Hide form after successful submission
                } else if (response.status === 'reported') {
                    $('#alert-message').removeClass('alert-danger').addClass('alert-success').text(response.message).show();
                    $('#report-form').hide(); // Hide form if already reported
                } else {
                    $('#alert-message').removeClass('alert-success').addClass('alert-danger').text(response.message).show();
                }
            },
            error: function() {
                $('#alert-message').removeClass('alert-success').addClass('alert-danger').text('Error reporting session.').show();
            }
        });
    });
});

    </script>
</body>
</html>

