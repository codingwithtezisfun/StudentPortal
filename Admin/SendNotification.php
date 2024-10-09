<?php
require 'session_start.php';
require 'db_connection.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Send Notification</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
    <style>
        .card {
            max-width: 600px;
            margin: auto;
        }
        .card-header{
            background-color: #343a40;
            text-align:center;
            font-weight:bold;
        }
        .form{
            color:blue;
            font-weight:bold;
        }
        .form .btn {
            width:100%;
            font-weight:bold;
        }
    </style>
</head>
<body>
    <div class="card mt-1">
        <div class="card-header text-white">
            <h5 class="card-title mb-0">
                <i class="fas fa-envelope"></i> Send Notification
            </h5>
        </div>
        <div class="card-body">
            <form class="form" id="notificationForm">
                <div class="form-group">
                    <label for="subject">Subject:</label>
                    <input type="text" id="subject" name="subject" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="from">From:</label>
                    <input type="text" id="from" name="from" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="to">To:</label>
                    <select id="to" name="to" class="form-control" required>
                        <option value="">Select recipient</option>
                        <option value="Students">Students</option>
                        <option value="Staff">Staff</option>
                        <option value="All">All</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="message">Message:</label>
                    <textarea id="message" name="message" rows="4" class="form-control" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Send Notification</button><br>
                <small style="color:red;">* Notifications sent through this channel are valid for 7 days only!</small><br>
                <small style="color:green;">Use the noticeboard for long-term message availability.</small>

            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.1.4/dist/sweetalert2.min.js"></script>
    <script>
        $(document).ready(function() {
            $("#notificationForm").submit(function(event) {
                event.preventDefault();

                var formData = $(this).serialize();

                $.ajax({
                    url: 'Notification.php',
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Notification Sent',
                            text: 'Your notification has been sent successfully!',
                        }).then(function() {
                            $('#notificationForm')[0].reset(); // Reset form fields
                        });
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'An error occurred while sending the notification.',
                        });
                    }
                });
            });
        });
    </script>
</body>
</html>
