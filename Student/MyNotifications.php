<?php
require 'session_start.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fetch Notifications</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .card {
            max-width: 800px;
            margin: auto;
        }
        .card-header {
            background-color:  #36454f;
            color: #fff;
            text-align:center;
        }
        .card-body{
            /* background-color:#87cefa; */
        }
        .jumbotron {
            padding: 2rem 1rem;
            margin-bottom: 1rem;
        }
        .alert-primary {
            background-color: #1e90ff;
            color: #ffe4b5;
        }
        .alert-secondary {
            background-color: #6c757d;
            color: #fff;
        }
        .alert-success {
            background-color: #28a745;
            color: #fff;
        }
        .alert-danger {
            background-color: #dc3545;
            color: #fff;
        }
        .alert-warning {
            background-color: #ffc107;
            color: #212529;
        }
        .alert-info {
            background-color: #17a2b8;
            color: #fff;
        }
        .alert-light {
            background-color: #f8f9fa;
            color: #212529;
        }
        .alert-dark {
            background-color: #343a40;
            color: #fff;
        }
        .message{
            color:white;
        }
    </style>
</head>
<body>
    <div class="card mt-5">
        <div class="card-header">
            <h5 class="card-title mb-0"><i class="fas fa-bell"></i> Notifications</h5>
        </div>
        <div class="card-body" id="notificationList">
            <!-- Notifications will be loaded here -->
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            // Function to fetch notifications using AJAX
            function fetchNotifications() {
                $.ajax({
                    url: 'fetch_notifications.php',
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.error) {
                            $('#notificationList').html('<div class="jumbotron alert-primary">You dont have any noticications currently.</div>');
                        } else {
                            var html = '';
                            $.each(response, function(index, notification) {
                                // You can customize alert class based on your requirements
                                var alertClass = 'alert-primary'; // Default alert class

                                html += '<div class="jumbotron ' + alertClass + '">';
                                html += '<h4 style= "color:black;"class="font-weight-bold">' + notification.subject + '</h4>';
                                html += '<h4 class="font-weight-bold">From: ' + notification.from_user + '</h4>';
                                html += '<h4 class="font-weight-bold">To: ' + notification.to_user + '</h4>';
                                html += '<h5 class="font-weight-bold message">' + notification.message + '</h5>';
                                html += '</div>';
                            });
                            $('#notificationList').html(html);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                        $('#notificationList').html('<div class="jumbotron alert-danger">Failed to fetch notifications. Please try again.</div>');
                    }
                });
            }

            // Initial load of notifications
            fetchNotifications();
        });
    </script>
</body>
</html>
