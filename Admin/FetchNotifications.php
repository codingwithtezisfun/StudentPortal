<?php
require 'session_start.php';
require 'db_connection.php';
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
            background-color: #343a40;
            color: #fff;
            text-align:center;
        }
        .alert-primary {
            background-color: #007bff;
            color: #fff;
        }
    </style>
</head>
<body>
    <div class="card mt-2">
        <div class="card-header">
            <h5 class="card-title mb-0"><i class="fas fa-bell"></i> Notifications</h5>
        </div>
        <div class="card-body" id="notifications-container">
            <!-- Notifications will be dynamically loaded here -->
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script>
        $(document).ready(function() {
            fetchNotifications();

            function fetchNotifications() {
                $.ajax({
                    url: 'fetch_notifications.php',
                    method: 'GET',
                    dataType: 'json',
                    success: function(notifications) {
                        const container = $('#notifications-container');
                        container.empty(); // Clear existing content

                        if (notifications.length > 0) {
                            notifications.forEach(notification => {
                                const alertClass = 'alert-primary'; 

                                const notificationHtml = `
                                    <div class="jumbotron ${alertClass}">        
                                        <h5 class="font-weight-bold">From: ${notification.from_user}</h5>
                                        <h5 class="font-weight-bold">To: ${notification.to_user}</h5>
                                        <h3 class="font-weight-bold" style="text-decoration: underline; color: yellow;">${notification.subject}</h3>
                                        <p class="font-weight-bold">${notification.message}</p>
                                    </div>
                                `;

                                container.append(notificationHtml);
                            });
                        } else {
                            container.append('<p>No notifications found.</p>');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error fetching notifications:', error);
                        $('#notifications-container').html('<p>Error fetching notifications. Please try again later.</p>');
                    }
                });
            }
        });
    </script>
</body>
</html>
