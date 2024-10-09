<?php
require 'session_start.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Student Details</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
    <style>
        .student-image {
            width: 250px;
            height: 250px;
            object-fit: cover;
            border-radius:5px;
            margin-top:10px;
        }
        .details-box {
            border: 1px solid blue;
            padding: 20px;
        }
        .image-box {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100%; 
            text-align: center;
            margin-bottom:15px;
        }
        
    </style>
</head>
<body>
    <div class="container mt-3">
        <div class="card" style="color: blue;" >
            <div class="card-header text-center" style="background-color: #36454f;" >
                <h2 style="font-family: Arial, sans-serif; font-size: 24px; color: white;">
                    <i class="fas fa-user-graduate" style="color: white; font-size: 20px; margin-right: 10px;"></i>
                    My Profile
                </h2>
            </div>
            <div class="card-body" id="studentDetailsContainer">
                <!-- Details will be loaded here -->
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            // AJAX request to fetch student details
            $.ajax({
                url: 'my_profile.php',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        var data = response.data;
                        var html = '<div class="row">' +
                                   '<div class="col-md-4 image-box">';
                        if (data.image !== null && data.image !== '') {
                            html += '<img src="/StudentPortal/StudentImages/' + data.image + '" alt="Student Image" class="student-image">';
                        } else {
                            html += '<img src="/StudentPortal/path_to_image_placeholder" alt="Your Image" class="student-image">';
                        }
                        html += '</div>' +
                                '<div class="col-md-8">' +
                                '<div class="details-box">' +
                                '<div class="form-group row">' +
                                '<label class="col-sm-4 col-form-label font-weight-bold">Name:</label>' +
                                '<div class="col-sm-8">' +
                                '<p class="form-control-plaintext">' + data.name + '</p>' +
                                '</div>' +
                                '</div>' +
                                '<div class="form-group row">' +
                                '<label class="col-sm-4 col-form-label font-weight-bold">Registration Number:</label>' +
                                '<div class="col-sm-8">' +
                                '<p class="form-control-plaintext">' + data.regNumber + '</p>' +
                                '</div>' +
                                '</div>' +
                                '<div class="form-group row">' +
                                '<label class="col-sm-4 col-form-label font-weight-bold">Phone Number:</label>' +
                                '<div class="col-sm-8">' +
                                '<p class="form-control-plaintext">' + data.phoneNumber + '</p>' +
                                '</div>' +
                                '</div>' +
                                '<div class="form-group row">' +
                                '<label class="col-sm-4 col-form-label font-weight-bold">Email:</label>' +
                                '<div class="col-sm-8">' +
                                '<p class="form-control-plaintext">' + data.email + '</p>' +
                                '</div>' +
                                '</div>' +
                                '<div class="form-group row">' +
                                '<label class="col-sm-4 col-form-label font-weight-bold">Department:</label>' +
                                '<div class="col-sm-8">' +
                                '<p class="form-control-plaintext">' + data.departmentName + '</p>' +
                                '</div>' +
                                '</div>' +
                                '<div class="form-group row">' +
                                '<label class="col-sm-4 col-form-label font-weight-bold">Course:</label>' +
                                '<div class="col-sm-8">' +
                                '<p class="form-control-plaintext">' + data.courseName + '</p>' +
                                '</div>' +
                                '</div>' +
                                '<div class="form-group row">' +
                                '<label class="col-sm-4 col-form-label font-weight-bold">Group:</label>' +
                                '<div class="col-sm-8">' +
                                '<p class="form-control-plaintext">' + data.studentGroup + '</p>' +
                                '</div>' +
                                '</div>' +
                                '<div class="form-group row">' +
                                '<label class="col-sm-4 col-form-label font-weight-bold">Gender:</label>' +
                                '<div class="col-sm-8">' +
                                '<p class="form-control-plaintext">' + data.gender + '</p>' +
                                '</div>' +
                                '</div>' +
                                '<div class="form-group row">' +
                                '<label class="col-sm-4 col-form-label font-weight-bold">National ID:</label>' +
                                '<div class="col-sm-8">' +
                                '<p class="form-control-plaintext">' + data.studentID + '</p>' +
                                '</div>' +
                                '</div>' +
                                '<div class="form-group row">' +
                                '<label class="col-sm-4 col-form-label font-weight-bold">Status:</label>' +
                                '<div class="col-sm-8">' +
                                '<p class="form-control-plaintext">' + data.status + '</p>' +
                                '</div>' +
                                '</div>' +
                                '</div>' +
                                '</div>' +
                                '</div>';
                        $('#studentDetailsContainer').html(html);
                    } else {
                        $('#studentDetailsContainer').html('<div class="alert alert-danger" role="alert">' + response.error + '</div>');
                    }
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                    $('#studentDetailsContainer').html('<div class="alert alert-danger" role="alert">Failed to fetch student details. Please try again.</div>');
                }
            });
        });
    </script>
</body>
</html>
